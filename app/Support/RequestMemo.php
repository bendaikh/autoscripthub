<?php

namespace Fickrr\Support;

/**
 * Tiny per-request memo used only while Blade views are being rendered.
 *
 * The view composers and partials (product cards etc.) ask for the same
 * read-only settings / counts hundreds of times per page. While a GET page is
 * rendering nothing writes to the database, so those reads can be answered
 * once and reused. Controllers (which may read after a write) are never
 * affected: memoising is only switched on by the first view composer, and it is
 * ignored for non-GET requests and for the console.
 */
class RequestMemo
{
    protected static $enabled = false;
    protected static $store = [];

    public static function enable()
    {
        if (app()->runningInConsole() && !defined('REQUEST_MEMO_FORCE')) {
            return;
        }
        if (function_exists('request') && request()->isMethod('GET')) {
            static::$enabled = true;
        }
    }

    public static function remember($key, \Closure $callback)
    {
        if (!static::$enabled) {
            return $callback();
        }
        if (!array_key_exists($key, static::$store)) {
            static::$store[$key] = $callback();
        }
        return static::$store[$key];
    }
}
