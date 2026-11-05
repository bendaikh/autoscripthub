<?php

namespace Tests\Unit;

use Fickrr\Helpers\LandingPageContent;
use Fickrr\Services\Physical\CheckoutValidationException;
use Fickrr\Services\Physical\PhysicalOrderService;
use Fickrr\Services\Physical\ShippingCalculator;
use Fickrr\Services\Physical\StripeChargePolicy;
use Fickrr\Support\Store;
use Fickrr\Support\StoreDatabaseGuard;
use Tests\TestCase;

class PhysicalStoreFoundationTest extends TestCase
{
    public function test_checkout_rules_require_delivery_details(): void
    {
        $service = app(PhysicalOrderService::class);

        try {
            $service->validateCustomer([
                'customer_name' => 'A',
                'customer_phone' => '12',
                'customer_email' => 'not-an-email',
                'address' => 'x',
                'city' => '',
                'country' => '',
                'payment_method' => 'stripe',
            ]);
            $this->fail('Expected validation to fail.');
        } catch (CheckoutValidationException $e) {
            $this->assertArrayHasKey('customer_name', $e->errors);
            $this->assertArrayHasKey('customer_phone', $e->errors);
            $this->assertArrayHasKey('customer_email', $e->errors);
            $this->assertArrayHasKey('address', $e->errors);
            $this->assertArrayHasKey('city', $e->errors);
            $this->assertArrayHasKey('country', $e->errors);
        }
    }

    public function test_shipping_adds_flat_and_per_unit_fees_and_can_be_free(): void
    {
        config(['store.shipping.flat_fee' => 5, 'store.shipping.free_over' => null]);
        $calc = new ShippingCalculator();
        $quote = $calc->quote([
            ['quantity' => 3, 'unit_price' => 10, 'shipping_fee' => 2, 'weight_grams' => 100, 'requires_shipping' => true],
        ]);
        $this->assertSame(30.0, $quote['subtotal']);
        $this->assertSame(11.0, $quote['shipping_fee']);
        $this->assertSame(41.0, $quote['total']);
        $this->assertSame(300, $quote['weight_grams']);

        config(['store.shipping.free_over' => 20]);
        $free = $calc->quote([
            ['quantity' => 3, 'unit_price' => 10, 'shipping_fee' => 2, 'requires_shipping' => true],
        ]);
        $this->assertSame(0.0, $free['shipping_fee']);
        $this->assertSame(30.0, $free['total']);
    }

    public function test_stripe_charges_stay_off_outside_production(): void
    {
        config(['store.stripe_enabled' => true, 'store.stripe_charges_enabled' => true]);
        $this->assertFalse(StripeChargePolicy::chargesEnabled());
    }

    public function test_database_guard_refuses_the_digital_store_database(): void
    {
        $message = StoreDatabaseGuard::violation('autoscripthub', false);
        $this->assertNotNull($message);
        $this->assertStringContainsString('auto-script-hub', $message);

        $this->assertNull(StoreDatabaseGuard::violation('esipres', true));
        $this->assertNotNull(StoreDatabaseGuard::violation('homestead', true));
        $this->assertNotNull(StoreDatabaseGuard::violation('', true));
    }

    public function test_landing_pages_default_to_physical_products(): void
    {
        $page = (object) ['lp_product_kind' => 'script'];
        $this->assertSame('physical', LandingPageContent::productKind($page));
    }

    public function test_physical_newsletter_drops_script_theme_template_wording(): void
    {
        config(['store.mode' => 'physical']);
        $text = Store::newsletterText('Want more script,themes & templates? Subscribe');

        $this->assertStringNotContainsString('script', strtolower($text));
        $this->assertStringNotContainsString('theme', strtolower($text));
        $this->assertStringNotContainsString('template', strtolower($text));
        $this->assertStringContainsString('delivery', strtolower($text));

        config(['store.mode' => 'digital']);
        $this->assertSame(
            'Want more script,themes & templates? Subscribe',
            Store::newsletterText('Want more script,themes & templates? Subscribe')
        );
    }

    public function test_product_cards_show_the_full_photo(): void
    {
        $shared = file_get_contents(public_path('assets/product-cards.css'));
        $home = file_get_contents(public_path('assets/home-theme.css'));

        $this->assertStringContainsString('aspect-ratio: 4 / 3', $shared);
        $this->assertStringContainsString('object-fit: contain', $shared);
        $this->assertStringNotContainsString('object-fit: cover', $shared);
        $this->assertStringContainsString('object-fit: contain', $home);
        $this->assertDoesNotMatchRegularExpression(
            '/product-thumb img \{[^}]*object-fit:\s*cover/s',
            $home
        );
        $this->assertStringContainsString("asset('assets/product-cards.css')", file_get_contents(base_path('resources/views/style.blade.php')));
    }

    public function test_physical_homepage_hides_invented_digital_claims(): void
    {
        $src = file_get_contents(base_path('resources/views/pages/index.blade.php'));

        $this->assertStringContainsString(
            'Pay by card or PayPal before the order ships.',
            $src
        );
        $this->assertStringContainsString('hv-hero--shop', $src);
        $this->assertStringContainsString('assets/esipres/hero-still.svg', $src);
        $this->assertStringNotContainsString('No file to download', $src);
        $this->assertStringNotContainsString('nothing to download', strtolower($src));
        $this->assertStringNotContainsString('Physical products', $src);
        $this->assertStringNotContainsString('Shipping updates by email', $src);
        $this->assertFileExists(public_path('assets/esipres/hero-still.svg'));
        $this->assertStringContainsString('$hvFallbackReviews = $hvPhysicalStore ? [] : [', $src);

        $uptime = strpos($src, '99.9%');
        $digitalPitch = strpos($src, 'premium digital products');
        $this->assertNotFalse($uptime);
        $this->assertNotFalse($digitalPitch);
        foreach ([$uptime, $digitalPitch] as $pos) {
            $before = substr($src, 0, $pos);
            $this->assertNotFalse(strrpos($before, '@else'));
            $this->assertGreaterThan(
                strrpos($before, 'Store::isPhysical()'),
                strrpos($before, '@else')
            );
        }

        $fallback = strpos($src, '$hvFallbackReviews = $hvPhysicalStore ? [] : [');
        foreach (['John D.', 'Sarah K.', 'Michael T.', 'marketplace for digital products'] as $name) {
            $this->assertGreaterThan($fallback, strpos($src, $name));
        }
    }

    public function test_deployment_env_requires_a_separate_database(): void
    {
        $path = base_path('.env.esipres.example');
        $contents = file_get_contents($path);
        $this->assertNotFalse($contents);
        $this->assertStringContainsString('APP_URL=https://esipres.com', $contents);
        $this->assertStringContainsString('STORE_MODE=physical', $contents);
        $this->assertStringContainsString('DB_DATABASE=esipres', $contents);
        $this->assertStringContainsString('STORE_ENFORCE_DATABASE=true', $contents);
        $this->assertStringContainsString('STRIPE_CHARGES_ENABLED=false', $contents);
        $this->assertStringContainsString('PAYPAL_CHARGES_ENABLED=false', $contents);
        $this->assertStringContainsString('PAYPAL_CLIENT_ID=', $contents);
        $this->assertDoesNotMatchRegularExpression('/^PAYPAL_CLIENT_SECRET=.+$/m', $contents);
        $this->assertDoesNotMatchRegularExpression('/^STRIPE_SECRET=.+$/m', $contents);
        $this->assertStringNotContainsString('DB_DATABASE=autoscripthub', $contents);
        $this->assertStringNotContainsString('sk_live_', $contents);
        $this->assertDoesNotMatchRegularExpression('/^DB_PASSWORD=.+$/m', $contents);
    }
}
