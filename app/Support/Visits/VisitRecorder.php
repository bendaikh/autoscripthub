<?php

namespace Fickrr\Support\Visits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitRecorder
{
    public static function capture(Request $request): void
    {
        if (! config('visits.enabled', true)) {
            return;
        }
        if ($request->ajax() || $request->headers->has('X-Requested-With')) {
            return;
        }
        $path = VisitRules::cleanPath((string) $request->input('path', '/'));
        if (VisitRules::isBot($request->userAgent()) || VisitRules::skipPath($path)) {
            return;
        }
        $headers = array_change_key_case($request->headers->all(), CASE_LOWER);
        $flat = [];
        foreach ($headers as $name => $values) {
            $flat[$name] = is_array($values) ? (string) ($values[0] ?? '') : (string) $values;
        }
        $ip = ClientAddress::ip($flat, (string) $request->ip());
        $visitor = trim((string) $request->input('vid', ''));
        if ($visitor === '') {
            $visitor = $ip !== '' ? $ip : 'unknown';
        }
        $visitor = substr($visitor, 0, 80);
        // the site owner (admin, owner's devices, excluded IPs) is not counted
        if (AnalyticsExclusion::isExcluded($request, $visitor, $ip)) {
            return;
        }
        $referrer = substr(trim((string) $request->input('ref', '')), 0, 191);
        DB::table('site_visits')->insert([
            'visited_at' => date('Y-m-d H:i:s'),
            'path' => $path,
            'visitor_hash' => VisitRules::hashId((string) config('app.key'), $visitor),
            'country_code' => CountryResolver::resolve($flat, $ip, (string) config('visits.geoip_path')),
            'referrer' => $referrer !== '' ? $referrer : null,
            'device' => VisitRules::device($request->userAgent()),
        ] + ($ip !== '' && self::hasIpHash() ? ['ip_hash' => AnalyticsExclusion::ipHash($ip)] : []));
    }

    private static function hasIpHash(): bool
    {
        try {
            return (bool) \Illuminate\Support\Facades\Cache::remember('site_visits.has_ip_hash', 3600, fn () => \Illuminate\Support\Facades\Schema::hasColumn('site_visits', 'ip_hash') ? 1 : 0);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
