<?php

namespace Tests\Feature;

use Fickrr\Services\Physical\CheckoutValidationException;
use Fickrr\Services\Physical\InsufficientStockException;
use Fickrr\Services\Physical\InvalidStatusTransitionException;
use Fickrr\Services\Physical\OnlinePaymentService;
use Fickrr\Services\Physical\PayPalChargePolicy;
use Fickrr\Services\Physical\PhysicalOrderService;
use Fickrr\Services\Physical\StripeChargePolicy;
use Fickrr\Mail\PhysicalOrderMail;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PhysicalStoreOrderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->bootPhysicalSchema();
        config([
            'store.mode' => 'physical',
            'store.stripe_enabled' => true,
            'store.stripe_charges_enabled' => false,
            'store.paypal_enabled' => true,
            'store.paypal_charges_enabled' => false,
            'store.shipping.flat_fee' => 4,
            'store.shipping.free_over' => null,
            'store.currency' => 'USD',
        ]);
        Mail::fake();
        OnlinePaymentService::$liveApiCalls = 0;
    }

    protected function tearDown(): void
    {
        Schema::dropAllTables();
        parent::tearDown();
    }

    public function test_card_checkout_stays_unpaid_and_does_not_reduce_stock(): void
    {
        $itemId = $this->item('Mug', 12.5, 5, 1.5, 200);

        $order = $this->orders()->place($this->customer(['payment_method' => 'stripe']), [[
            'item_id' => $itemId,
            'quantity' => 2,
        ]]);
        $started = app(OnlinePaymentService::class)->begin($order);

        $this->assertNull($started['redirect']);
        $this->assertSame(0, OnlinePaymentService::$liveApiCalls);
        $this->assertFalse(StripeChargePolicy::chargesEnabled());
        $this->assertSame('pending', $order->fulfillment_status);
        $this->assertSame('stripe', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame(0, (int) $order->stock_committed);
        $this->assertSame('Ada Lovelace', $order->customer_name);
        $this->assertSame('1 Market Street', $order->address);
        $this->assertSame(5, (int) DB::table('items')->where('item_id', $itemId)->value('stock_quantity'));
        $this->assertEquals(25.0, (float) $order->subtotal);
        $this->assertEquals(7.0, (float) $order->shipping_fee);
        $this->assertEquals(32.0, (float) $order->total);
        Mail::assertSent(PhysicalOrderMail::class, function (PhysicalOrderMail $mail) {
            return $mail->kind === 'confirmation';
        });
    }

    public function test_paypal_checkout_stays_unpaid_without_calling_paypal(): void
    {
        $itemId = $this->item('Bowl', 9, 3, 0, 100);
        $order = $this->orders()->place($this->customer(['payment_method' => 'paypal']), [[
            'item_id' => $itemId,
            'quantity' => 1,
        ]]);
        $started = app(OnlinePaymentService::class)->begin($order);

        $this->assertNull($started['redirect']);
        $this->assertSame(0, OnlinePaymentService::$liveApiCalls);
        $this->assertFalse(PayPalChargePolicy::chargesEnabled());
        $this->assertSame('paypal', $order->payment_method);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame(3, (int) DB::table('items')->where('item_id', $itemId)->value('stock_quantity'));
    }

    public function test_cash_on_delivery_is_rejected(): void
    {
        $itemId = $this->item('Plate', 4, 2, 0, 50);
        try {
            $this->orders()->place($this->customer(['payment_method' => 'cod']), [[
                'item_id' => $itemId,
                'quantity' => 1,
            ]]);
            $this->fail('Cash on delivery must be rejected.');
        } catch (CheckoutValidationException $e) {
            $this->assertStringContainsString('card or PayPal', $e->getMessage());
        }
        $this->assertSame(0, DB::table('physical_orders')->count());
    }

    public function test_confirming_an_order_decrements_stock_and_cannot_go_negative(): void
    {
        $itemId = $this->item('Notebook', 8, 2, 0, 100);
        $orders = $this->orders();
        $order = $orders->place($this->customer(), [[
            'item_id' => $itemId,
            'quantity' => 2,
        ]]);
        $second = $orders->place($this->customer(), [[
            'item_id' => $itemId,
            'quantity' => 1,
        ]]);
        $this->assertSame('pending', $second->payment_status);
        $this->assertSame(2, (int) DB::table('items')->where('item_id', $itemId)->value('stock_quantity'));

        try {
            $orders->transition((int) $order->id, 'confirmed');
            $this->fail('Unpaid orders cannot be confirmed.');
        } catch (InvalidStatusTransitionException $e) {
            $this->assertStringContainsString('Online payment is required', $e->getMessage());
        }

        $confirmed = $orders->recordPayment((int) $order->id, 'pi_test_notebook');
        $this->assertSame('confirmed', $confirmed->fulfillment_status);
        $this->assertSame(1, (int) $confirmed->stock_committed);
        $this->assertSame(0, (int) DB::table('items')->where('item_id', $itemId)->value('stock_quantity'));

        try {
            $orders->recordPayment((int) $second->id, 'pi_test_notebook_late');
            $this->fail('The second payment cannot confirm more stock than exists.');
        } catch (InsufficientStockException $e) {
            $this->assertStringContainsString('Not enough stock', $e->getMessage());
        }
        $this->assertSame('paid', DB::table('physical_orders')->where('id', $second->id)->value('payment_status'));
        $this->assertSame('pending', DB::table('physical_orders')->where('id', $second->id)->value('fulfillment_status'));

        $orders->transition((int) $order->id, 'cancelled');
        $this->assertSame(2, (int) DB::table('items')->where('item_id', $itemId)->value('stock_quantity'));
    }

    public function test_status_must_follow_the_physical_sequence(): void
    {
        $itemId = $this->item('Lamp', 20, 4, 0, 500);
        $orders = $this->orders();
        $order = $orders->place($this->customer(), [[
            'item_id' => $itemId,
            'quantity' => 1,
        ]]);

        try {
            $orders->transition((int) $order->id, 'shipped', 'TRACK1');
            $this->fail('Pending orders cannot skip to shipped.');
        } catch (InvalidStatusTransitionException $e) {
            $this->assertStringContainsString('pending', $e->getMessage());
        }

        $this->assertSame('pending', DB::table('physical_orders')->where('id', $order->id)->value('fulfillment_status'));

        $orders->recordPayment((int) $order->id, 'pi_test_lamp');
        $shipped = $orders->transition((int) $order->id, 'shipped', 'TRACK1');
        $this->assertSame('shipped', $shipped->fulfillment_status);
        $this->assertSame('TRACK1', $shipped->tracking_number);
        Mail::assertSent(PhysicalOrderMail::class, function (PhysicalOrderMail $mail) {
            return $mail->kind === 'shipped';
        });

        $delivered = $orders->transition((int) $order->id, 'delivered');
        $this->assertSame('delivered', $delivered->fulfillment_status);
        $this->assertSame('paid', $delivered->payment_status);
    }

    public function test_variant_stock_is_used_when_an_option_is_selected(): void
    {
        $itemId = $this->item('Shirt', 18, 9, 0, 150);
        $variantId = DB::table('item_variants')->insertGetId([
            'item_id' => $itemId,
            'lp_id' => null,
            'sku' => 'SHIRT-M',
            'name' => 'Medium',
            'price' => 21,
            'stock_quantity' => 1,
            'weight_grams' => 180,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $orders = $this->orders();
        $order = $orders->place($this->customer(), [[
            'item_id' => $itemId,
            'variant_id' => $variantId,
            'quantity' => 1,
        ]]);
        $orders->recordPayment((int) $order->id, 'pi_test_shirt');

        $this->assertSame(0, (int) DB::table('item_variants')->where('id', $variantId)->value('stock_quantity'));
        $this->assertSame(9, (int) DB::table('items')->where('item_id', $itemId)->value('stock_quantity'));
        $this->assertEquals(21.0, (float) DB::table('physical_order_items')->where('physical_order_id', $order->id)->value('unit_price'));

        $returned = $orders->transition((int) $order->id, 'shipped', 'T');
        $orders->transition((int) $returned->id, 'returned');
        $this->assertSame(1, (int) DB::table('item_variants')->where('id', $variantId)->value('stock_quantity'));
    }

    public function test_disabled_stripe_does_not_call_the_gateway(): void
    {
        config([
            'store.stripe_charges_enabled' => true,
            'store.stripe_secret' => '',
            'store.stripe_key' => '',
        ]);
        $itemId = $this->item('Cup', 6, 3, 0, 80);
        $order = $this->orders()->place($this->customer(['payment_method' => 'stripe']), [[
            'item_id' => $itemId,
            'quantity' => 1,
        ]]);
        app(OnlinePaymentService::class)->begin($order);

        $this->assertFalse(StripeChargePolicy::chargesEnabled());
        $this->assertSame(0, OnlinePaymentService::$liveApiCalls);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame(3, (int) DB::table('items')->where('item_id', $itemId)->value('stock_quantity'));
    }

    public function test_landing_page_order_collects_address_and_does_not_require_a_file(): void
    {
        $lpId = DB::table('landing_pages')->insertGetId([
            'lp_title' => 'Ceramic cup',
            'lp_slug' => 'ceramic-cup',
            'lp_price' => 15,
            'lp_currency' => 'USD',
            'lp_status' => 1,
            'lp_product_kind' => 'script',
            'lp_delivery_method' => 'upload',
            'lp_product_file' => null,
            'lp_product_link' => null,
            'lp_sku' => 'CUP-1',
            'lp_stock_quantity' => 4,
            'lp_weight_grams' => 300,
            'lp_shipping_fee' => 3,
            'lp_track_inventory' => 1,
        ]);
        $page = DB::table('landing_pages')->where('lp_id', $lpId)->first();

        $order = $this->orders()->placeLanding($page, $this->customer(['quantity' => 2]));

        $this->assertSame('landing', $order->source);
        $this->assertSame('pending', $order->fulfillment_status);
        $this->assertNotEmpty($order->landing_order_token);
        $this->assertSame(4, (int) DB::table('landing_pages')->where('lp_id', $lpId)->value('lp_stock_quantity'));

        $landingOrder = DB::table('landing_page_orders')->where('order_token', $order->landing_order_token)->first();
        $this->assertNotNull($landingOrder);
        $this->assertSame('pending', $landingOrder->payment_status);
        $this->assertSame('Ada Lovelace', $landingOrder->customer_name);
        $this->assertSame('Lisbon', $landingOrder->city);
        $this->assertSame(2, (int) $landingOrder->quantity);
        $this->assertNull($page->lp_product_file);

        $this->orders()->recordPayment((int) $order->id, 'pi_test_landing');
        $this->assertSame(2, (int) DB::table('landing_pages')->where('lp_id', $lpId)->value('lp_stock_quantity'));
        $this->assertSame('confirmed', DB::table('landing_page_orders')->where('order_token', $order->landing_order_token)->value('fulfillment_status'));
    }

    private function orders(): PhysicalOrderService
    {
        return app(PhysicalOrderService::class);
    }

    private function customer(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Ada Lovelace',
            'customer_phone' => '+1 555 010 2030',
            'customer_email' => 'ada@example.com',
            'address' => '1 Market Street',
            'city' => 'Lisbon',
            'country' => 'Portugal',
            'payment_method' => 'stripe',
        ], $overrides);
    }

    private function item(string $name, float $price, int $stock, float $shipping, int $weight): int
    {
        return DB::table('items')->insertGetId([
            'item_name' => $name,
            'item_token' => 'tok-' . $name,
            'regular_price' => $price,
            'extended_price' => 0,
            'item_status' => 1,
            'drop_status' => 'no',
            'sku' => strtoupper($name),
            'stock_quantity' => $stock,
            'weight_grams' => $weight,
            'shipping_fee' => $shipping,
            'requires_shipping' => 1,
            'track_inventory' => 1,
            'product_type' => 'physical',
        ]);
    }

    private function bootPhysicalSchema(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('item_id');
            $table->string('item_name');
            $table->string('item_token')->nullable();
            $table->decimal('regular_price', 12, 2)->default(0);
            $table->decimal('extended_price', 12, 2)->default(0);
            $table->unsignedTinyInteger('item_status')->default(1);
            $table->string('drop_status')->default('no');
        });

        Schema::create('landing_pages', function (Blueprint $table) {
            $table->increments('lp_id');
            $table->string('lp_title');
            $table->string('lp_slug');
            $table->decimal('lp_price', 12, 2);
            $table->string('lp_currency', 10)->default('USD');
            $table->unsignedTinyInteger('lp_status')->default(1);
            $table->string('lp_product_kind', 20)->nullable();
            $table->string('lp_delivery_method', 20)->nullable();
            $table->string('lp_product_file')->nullable();
            $table->string('lp_product_link')->nullable();
        });

        Schema::create('landing_page_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lp_id');
            $table->string('customer_email');
            $table->string('order_token')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10);
            $table->string('payment_method');
            $table->string('payment_status')->default('pending');
            $table->text('payment_details')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        $migration = require base_path('database/migrations/2026_11_05_180000_add_physical_store_tables.php');
        $migration->up();
        $reference = require base_path('database/migrations/2026_11_05_190000_add_physical_payment_reference.php');
        $reference->up();
    }
}
