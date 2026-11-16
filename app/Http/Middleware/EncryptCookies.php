<?php

namespace Fickrr\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        'translate',
		'referral',
		'multicurrency',
		'ash_noanalytics', // set by JavaScript on the owner's devices (analytics exclusion)
    ];
}
