<?php

namespace Fickrr\Support\Visits;

class ClientAddress
{
    public static function ip(array $headers, string $fallback): string
    {
        foreach (['cf-connecting-ip', 'true-client-ip', 'x-real-ip'] as $name) {
            $ip = self::publicIp(self::first($headers[$name] ?? ''));
            if ($ip !== null) {
                return $ip;
            }
        }
        $forwarded = (string) ($headers['x-forwarded-for'] ?? '');
        foreach (explode(',', $forwarded) as $part) {
            $ip = self::publicIp($part);
            if ($ip !== null) {
                return $ip;
            }
        }
        $fallbackIp = self::publicIp($fallback);

        return $fallbackIp ?? trim($fallback);
    }

    public static function countryFromHeaders(array $headers): ?string
    {
        foreach (['cf-ipcountry', 'x-country-code', 'cloudfront-viewer-country', 'x-vercel-ip-country', 'x-appengine-country', 'x-geo-country'] as $name) {
            $code = self::code($headers[$name] ?? '');
            if ($code !== null) {
                return $code;
            }
        }

        return null;
    }

    private static function first(string $value): string
    {
        return trim(explode(',', $value)[0] ?? '');
    }

    private static function publicIp(string $value): ?string
    {
        $value = trim($value);
        if (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return null;
        }

        return $value;
    }

    private static function code($value): ?string
    {
        $code = strtoupper(trim((string) $value));
        if (strlen($code) !== 2 || ! ctype_alpha($code) || in_array($code, ['XX', 'T1', 'A1', 'A2', 'O1'], true)) {
            return null;
        }

        return $code;
    }
}
