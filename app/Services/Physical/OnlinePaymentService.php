<?php

namespace Fickrr\Services\Physical;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

/**
 * Starts Stripe Checkout or a PayPal order only when that provider is
 * explicitly enabled with credentials. Otherwise it records nothing and
 * leaves the order unpaid.
 */
class OnlinePaymentService
{
    public static int $liveApiCalls = 0;

    /**
     * @return array{redirect: ?string}
     */
    public function begin(object $order): array
    {
        if (($order->payment_status ?? '') === 'paid') {
            return ['redirect' => null];
        }

        if ($order->payment_method === 'stripe' && StripeChargePolicy::chargesEnabled()) {
            return ['redirect' => $this->stripeCheckoutUrl($order)];
        }

        if ($order->payment_method === 'paypal' && PayPalChargePolicy::chargesEnabled()) {
            return ['redirect' => $this->paypalApprovalUrl($order)];
        }

        return ['redirect' => null];
    }

    private function stripeCheckoutUrl(object $order): string
    {
        self::$liveApiCalls++;
        \Stripe\Stripe::setApiKey((string) config('store.stripe_secret'));
        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'client_reference_id' => $order->order_number,
            'customer_email' => $order->customer_email,
            'success_url' => url('/checkout/stripe/return') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('physical.order.show', ['orderNumber' => $order->order_number]),
            'metadata' => [
                'order_number' => $order->order_number,
                'source' => 'physical',
            ],
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower((string) ($order->currency ?: 'usd')),
                    'unit_amount' => (int) round(((float) $order->total) * 100),
                    'product_data' => [
                        'name' => 'Order ' . $order->order_number,
                    ],
                ],
            ]],
        ]);

        $this->rememberReference($order, (string) ($session->id ?? ''));

        return (string) $session->url;
    }

    private function paypalApprovalUrl(object $order): string
    {
        self::$liveApiCalls++;
        $token = $this->paypalAccessToken();
        $response = Http::withToken($token)
            ->acceptJson()
            ->post(PayPalChargePolicy::apiBase() . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $order->order_number,
                    'custom_id' => $order->order_number,
                    'amount' => [
                        'currency_code' => strtoupper((string) ($order->currency ?: 'USD')),
                        'value' => number_format((float) $order->total, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url' => url('/checkout/paypal/return'),
                    'cancel_url' => route('physical.order.show', ['orderNumber' => $order->order_number]),
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                ],
            ]);

        $body = $response->json();
        $paypalId = (string) ($body['id'] ?? '');
        $this->rememberReference($order, $paypalId);

        foreach ($body['links'] ?? [] as $link) {
            if (($link['rel'] ?? '') === 'approve' && !empty($link['href'])) {
                return (string) $link['href'];
            }
        }

        throw new CheckoutValidationException([
            'payment_method' => ['PayPal did not return a payment page. No capture was recorded.'],
        ]);
    }

    private function paypalAccessToken(): string
    {
        $response = Http::asForm()
            ->withBasicAuth(
                (string) config('store.paypal.client_id'),
                (string) config('store.paypal.secret')
            )
            ->post(PayPalChargePolicy::apiBase() . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        $token = (string) ($response->json('access_token') ?? '');
        if ($token === '') {
            throw new CheckoutValidationException([
                'payment_method' => ['PayPal did not authorize the request. No payment was created.'],
            ]);
        }

        return $token;
    }

    private function rememberReference(object $order, string $reference): void
    {
        if ($reference === '' || !Schema::hasColumn('physical_orders', 'payment_reference')) {
            return;
        }

        DB::table('physical_orders')->where('id', $order->id)->update([
            'payment_reference' => $reference,
            'updated_at' => now(),
        ]);
    }
}
