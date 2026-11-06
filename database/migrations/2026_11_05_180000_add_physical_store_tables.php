<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensure('items', [
            'sku' => function (Blueprint $table) {
                $table->string('sku', 80)->nullable()->index();
            },
            'stock_quantity' => function (Blueprint $table) {
                $table->unsignedInteger('stock_quantity')->default(0);
            },
            'weight_grams' => function (Blueprint $table) {
                $table->unsignedInteger('weight_grams')->nullable();
            },
            'shipping_fee' => function (Blueprint $table) {
                $table->decimal('shipping_fee', 12, 2)->default(0);
            },
            'requires_shipping' => function (Blueprint $table) {
                $table->boolean('requires_shipping')->default(true);
            },
            'track_inventory' => function (Blueprint $table) {
                $table->boolean('track_inventory')->default(true);
            },
            'product_type' => function (Blueprint $table) {
                $table->string('product_type', 20)->default('physical');
            },
        ]);

        if (!Schema::hasTable('item_variants')) {
            Schema::create('item_variants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id')->nullable()->index();
                $table->unsignedBigInteger('lp_id')->nullable()->index();
                $table->string('sku', 80)->nullable();
                $table->string('name');
                $table->decimal('price', 12, 2)->nullable();
                $table->unsignedInteger('stock_quantity')->default(0);
                $table->unsignedInteger('weight_grams')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('physical_orders')) {
            Schema::create('physical_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number', 40)->unique();
                $table->string('source', 20)->default('checkout');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('lp_id')->nullable();
                $table->string('landing_order_token')->nullable()->index();
                $table->string('customer_name');
                $table->string('customer_email');
                $table->string('customer_phone', 40);
                $table->text('address');
                $table->string('city');
                $table->string('country', 80);
                $table->string('payment_method', 20);
                $table->string('payment_status', 20)->default('pending');
                $table->string('fulfillment_status', 20)->default('pending')->index();
                $table->decimal('subtotal', 12, 2);
                $table->decimal('shipping_fee', 12, 2)->default(0);
                $table->decimal('total', 12, 2);
                $table->string('currency', 10)->default('USD');
                $table->string('tracking_number')->nullable();
                $table->boolean('stock_committed')->default(false);
                $table->text('admin_note')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->timestamp('shipped_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->timestamp('returned_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('physical_order_items')) {
            Schema::create('physical_order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('physical_order_id')->index();
                $table->unsignedBigInteger('item_id')->nullable()->index();
                $table->unsignedBigInteger('variant_id')->nullable()->index();
                $table->unsignedBigInteger('lp_id')->nullable()->index();
                $table->string('sku', 80)->nullable();
                $table->string('name');
                $table->string('variant_name')->nullable();
                $table->unsignedInteger('quantity');
                $table->decimal('unit_price', 12, 2);
                $table->decimal('shipping_fee', 12, 2)->default(0);
                $table->unsignedInteger('weight_grams')->nullable();
                $table->timestamps();
            });
        }

        $this->ensure('landing_pages', [
            'lp_sku' => function (Blueprint $table) {
                $table->string('lp_sku', 80)->nullable();
            },
            'lp_stock_quantity' => function (Blueprint $table) {
                $table->unsignedInteger('lp_stock_quantity')->default(0);
            },
            'lp_weight_grams' => function (Blueprint $table) {
                $table->unsignedInteger('lp_weight_grams')->nullable();
            },
            'lp_shipping_fee' => function (Blueprint $table) {
                $table->decimal('lp_shipping_fee', 12, 2)->default(0);
            },
            'lp_track_inventory' => function (Blueprint $table) {
                $table->boolean('lp_track_inventory')->default(true);
            },
        ]);

        $this->ensure('landing_page_orders', [
            'customer_name' => function (Blueprint $table) {
                $table->string('customer_name')->nullable();
            },
            'customer_phone' => function (Blueprint $table) {
                $table->string('customer_phone', 40)->nullable();
            },
            'address' => function (Blueprint $table) {
                $table->text('address')->nullable();
            },
            'city' => function (Blueprint $table) {
                $table->string('city')->nullable();
            },
            'country' => function (Blueprint $table) {
                $table->string('country', 80)->nullable();
            },
            'quantity' => function (Blueprint $table) {
                $table->unsignedInteger('quantity')->default(1);
            },
            'shipping_fee' => function (Blueprint $table) {
                $table->decimal('shipping_fee', 12, 2)->default(0);
            },
            'fulfillment_status' => function (Blueprint $table) {
                $table->string('fulfillment_status', 20)->nullable();
            },
            'variant_id' => function (Blueprint $table) {
                $table->unsignedBigInteger('variant_id')->nullable();
            },
            'physical_order_id' => function (Blueprint $table) {
                $table->unsignedBigInteger('physical_order_id')->nullable()->index();
            },
            'stock_committed' => function (Blueprint $table) {
                $table->boolean('stock_committed')->default(false);
            },
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_order_items');
        Schema::dropIfExists('physical_orders');
        Schema::dropIfExists('item_variants');

        $this->dropColumns('items', [
            'sku', 'stock_quantity', 'weight_grams', 'shipping_fee',
            'requires_shipping', 'track_inventory', 'product_type',
        ]);
        $this->dropColumns('landing_pages', [
            'lp_sku', 'lp_stock_quantity', 'lp_weight_grams', 'lp_shipping_fee', 'lp_track_inventory',
        ]);
        $this->dropColumns('landing_page_orders', [
            'customer_name', 'customer_phone', 'address', 'city', 'country',
            'quantity', 'shipping_fee', 'fulfillment_status', 'variant_id',
            'physical_order_id', 'stock_committed',
        ]);
    }

    private function ensure(string $table, array $definitions): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $missing = [];
        foreach ($definitions as $name => $define) {
            if (!Schema::hasColumn($table, $name)) {
                $missing[] = $define;
            }
        }

        if ($missing === []) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($missing) {
            foreach ($missing as $define) {
                $define($blueprint);
            }
        });
    }

    private function dropColumns(string $table, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $present = array_values(array_filter($columns, function ($column) use ($table) {
            return Schema::hasColumn($table, $column);
        }));

        if ($present === []) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($present) {
            $blueprint->dropColumn($present);
        });
    }
};
