<?php

namespace Fickrr\Services\Physical;

class StripeChargePolicy
{
    /**
     * A live Stripe charge is allowed only in production, and only when the
     * deployment env opts in. Development and tests never reach the Stripe API.
     */
    public static function chargesEnabled(): bool
    {
        if (!config('store.stripe_enabled')) {
            return false;
        }

        if (!config('store.stripe_charges_enabled')) {
            return false;
        }

        return app()->environment('production');
    }

    public static function refusalMessage(): string
    {
        return 'Card payment is turned off, so no charge was created. Choose cash on delivery.';
    }
}
