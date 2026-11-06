<?php

namespace Fickrr\Services\Physical;

class PayPalChargePolicy
{
    /**
     * A live PayPal payment is allowed only in production, only when the
     * deployment env opts in, and only when the REST app credentials exist.
     */
    public static function chargesEnabled(): bool
    {
        if (!config('store.paypal_enabled')) {
            return false;
        }

        if (!config('store.paypal_charges_enabled')) {
            return false;
        }

        if (!app()->environment('production')) {
            return false;
        }

        return trim((string) config('store.paypal.client_id')) !== ''
            && trim((string) config('store.paypal.secret')) !== '';
    }

    public static function apiBase(): string
    {
        return config('store.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    public static function refusalMessage(): string
    {
        return 'PayPal is not turned on, so no payment was created. The order stays unpaid until PayPal confirms payment.';
    }
}
