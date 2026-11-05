<?php

namespace Fickrr\Support;

class Store
{
    public static function mode(): string
    {
        return strtolower((string) config('store.mode', 'physical'));
    }

    public static function isPhysical(): bool
    {
        return self::mode() === 'physical';
    }

    public static function name(): string
    {
        $name = trim((string) config('store.name', 'ESIPRES'));

        return $name !== '' ? $name : 'ESIPRES';
    }

    public static function url(): string
    {
        return rtrim((string) config('store.url', 'https://esipres.com'), '/');
    }

    public static function supportEmail(): string
    {
        $email = trim((string) config('store.support_email', 'support@esipres.com'));

        return $email !== '' ? $email : 'support@esipres.com';
    }

    public static function looksLikeLegacyBrand(string $value): bool
    {
        $value = strtolower($value);

        foreach (['autoscript', 'auto script', 'auto-script', 'digital pro', 'fickrr', 'script hub'] as $needle) {
            if (str_contains($value, $needle)) {
                return true;
            }
        }

        return false;
    }

    public static function publicName(?string $siteTitle = null): string
    {
        if (!self::isPhysical()) {
            $title = trim((string) $siteTitle);

            return $title !== '' ? $title : self::name();
        }

        $title = trim((string) $siteTitle);
        if ($title === '' || self::looksLikeLegacyBrand($title)) {
            return self::name();
        }

        return $title;
    }

    public static function metaDescription(?string $current = null): string
    {
        $fallback = (string) config('store.meta_description');
        if (!self::isPhysical()) {
            $current = trim((string) $current);

            return $current !== '' ? $current : $fallback;
        }

        $current = trim((string) $current);
        if ($current === '' || self::looksLikeLegacyBrand($current) || self::looksDigitalCopy($current)) {
            return $fallback;
        }

        return $current;
    }

    public static function looksDigitalCopy(string $value): bool
    {
        $value = strtolower($value);

        foreach (['digital marketplace', 'instant download', 'license key', 'serial key', 'download your files'] as $needle) {
            if (str_contains($value, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Public newsletter helper text. Physical mode never repeats a stored
     * line about scripts, themes, or templates.
     */
    public static function newsletterText(?string $stored): string
    {
        $stored = trim((string) $stored);
        if (!self::isPhysical()) {
            return $stored;
        }

        if ($stored === '' || self::looksDigitalCopy($stored) || self::mentionsDigitalCatalog($stored)) {
            return (string) __('Product news and delivery updates. Nothing to download.');
        }

        return $stored;
    }

    public static function mentionsDigitalCatalog(string $value): bool
    {
        $value = strtolower($value);

        foreach (['script', 'theme', 'template'] as $needle) {
            if (str_contains($value, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Customer-facing download routes return this redirect in physical mode.
     */
    public static function denyDigitalFulfillment()
    {
        if (!self::isPhysical()) {
            return null;
        }

        return redirect('/')->with('error', __('This store ships physical products. There is no file to download.'));
    }
}
