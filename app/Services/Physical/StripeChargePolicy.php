<?php

namespace Fickrr\Services\Physical;

class StripeChargePolicy
{
    /**
     * A live Stripe charge is allowed only in production, only when the
     * deployment env opts in, and only when both API keys are present.
     */
    public static function chargesEnabled(): bool
    {
        if (!config('store.stripe_enabled')) {
            return false;
        }

        if (!config('store.stripe_charges_enabled')) {
            return false;
        }

        if (!app()->environment('production')) {
            return false;
        }

        return trim((string) config('store.stripe_secret')) !== ''
            && trim((string) config('store.stripe_key')) !== '';
    }

    public static function refusalMessage(): string
    {
        return 'Card payment is not turned on, so no charge was created. The order stays unpaid until a card payment succeeds.';
    }
}
