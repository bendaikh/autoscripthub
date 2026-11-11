<?php

namespace Fickrr\Support\Visits;

class VisitRules
{
    public static function isBot(?string $userAgent): bool
    {
        $agent = strtolower(trim((string) $userAgent));
        if ($agent === '') {
            return true;
        }

        return (bool) preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternal|wget|curl\/|python-requests|headless|semrush|ahrefs|petalbot|bytespider|gptbot|claudebot|amazonbot|duckduckbot|yandex|baiduspider|sogou|scrapy|lighthouse|pagespeed|uptimerobot|previewbot|httpclient/', $agent);
    }

    public static function skipPath(string $path): bool
    {
        $onlyPath = parse_url($path, PHP_URL_PATH);
        $path = '/'.ltrim(strtolower((string) ($onlyPath !== null && $onlyPath !== false ? $onlyPath : $path)), '/');
        if ($path === '/visit' || str_starts_with($path, '/admin') || str_starts_with($path, '/api')) {
            return true;
        }
        if (preg_match('#\.(css|js|map|png|jpe?g|gif|svg|ico|webp|woff2?|ttf|eot|xml|txt|json)$#', $path)) {
            return true;
        }

        return (bool) preg_match('#^/(storage|assets|theme|public|images|vendor|favicon\.ico|robots\.txt|sitemap)#', $path);
    }

    public static function device(?string $userAgent): string
    {
        $agent = strtolower((string) $userAgent);
        if (preg_match('/ipad|tablet|kindle/', $agent)) {
            return 'tablet';
        }
        if (preg_match('/mobile|iphone|android|ipod/', $agent)) {
            return 'mobile';
        }

        return 'desktop';
    }

    public static function hashId(string $key, string $id): string
    {
        return hash('sha256', $key.'|visit|'.$id);
    }

    public static function cleanPath(string $path): string
    {
        $onlyPath = parse_url($path, PHP_URL_PATH);
        $path = '/'.ltrim((string) ($onlyPath !== null && $onlyPath !== false ? $onlyPath : $path), '/');
        $path = preg_replace('/[^\x20-\x7E]/', '', $path) ?? '/';
        if ($path === '') {
            $path = '/';
        }

        return substr($path, 0, 191);
    }
}
