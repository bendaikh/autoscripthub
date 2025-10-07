<?php

namespace Fickrr\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/razorpay',
		'/subscription-razorpay',
		'/deposit-razorpay',
		'/payu_success',
		'/deposit_payu_success',
		'/payu_subscription',
		'/payhere-success',
		'/deposit-payhere-success',
		'/iyzico-success/*',
		'/deposit-iyzico-success/*',
		'/subscription-iyzico/*',
		'/coingate',
		'/deposit-coingate',
		'/admin/upload',
		'/upload',
		'/subscription-sslcommerz',
		'/subscription-sslcommerz-failure',
		'/subscription-sslcommerz-cancel',
		'/subscription-sslcommerz-ipn',
		'/sslcommerz-success',
		'/sslcommerz-failure',
		'/sslcommerz-cancel',
		'/sslcommerz-ipn',
		'/deposit-sslcommerz-success',
		'/aamarpay/*',
		'/subscription-aamarpay/*',
		'/deposit-aamarpay/*',
		'/deposit-mercadopago',
		'/deposit-mercadopago/*',
		'/subscription-mercadopago',
		'/subscription-mercadopago/*',
		'/mercadopago-success',
		'/mercadopago-success/*',
		'/subscription-coinbase',
		'/subscription-coinbase/*',
		'/deposit-coinbase',
		'/deposit-coinbase/*',
		'/coinbase',
		'/coinbase/*',
		'/webhooks/coinbase-checkout',
		'/webhooks/coinbase-deposit',
		'/webhooks/coinbase-subscription',
		'paytm/status',
		
		
    ];
}
