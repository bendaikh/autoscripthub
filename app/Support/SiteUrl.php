<?php

namespace Fickrr\Support;

class SiteUrl
{
    public const BASE = 'https://auto-script-hub.com';

    public static function to(string $path = '/'): string
    {
        $path = trim($path);
        if ($path === '' || $path === '/') {
            return self::BASE.'/';
        }
        if (preg_match('#^https?://#i', $path)) {
            $parts = parse_url($path);
            $host = strtolower((string) ($parts['host'] ?? ''));
            if ($host === 'auto-script-hub.com' || $host === 'www.auto-script-hub.com' || $host === '') {
                $local = ($parts['path'] ?? '/');
                if (!empty($parts['query'])) {
                    $local .= '?'.$parts['query'];
                }
                return self::to($local);
            }
            return $path;
        }
        return self::BASE.'/'.ltrim($path, '/');
    }

    public static function absolute(string $url): string
    {
        return self::to($url);
    }

    public static function lastmod($value): string
    {
        $stamp = $value ? strtotime((string) $value) : false;
        if ($stamp === false || $stamp <= 0) {
            return date('Y-m-d');
        }
        return date('Y-m-d', $stamp);
    }
}
