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
        $admin = false;
        try {
            $admin = auth()->check() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin();
        } catch (\Throwable $e) {
            $admin = false;
        }
        $path = VisitRules::cleanPath((string) $request->input('path', '/'));
        if ($admin || VisitRules::isBot($request->userAgent()) || VisitRules::skipPath($path)) {
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
        $referrer = substr(trim((string) $request->input('ref', '')), 0, 191);
        DB::table('site_visits')->insert([
            'visited_at' => date('Y-m-d H:i:s'),
            'path' => $path,
            'visitor_hash' => VisitRules::hashId((string) config('app.key'), $visitor),
            'country_code' => CountryResolver::resolve($flat, $ip, (string) config('visits.geoip_path')),
            'referrer' => $referrer !== '' ? $referrer : null,
            'device' => VisitRules::device($request->userAgent()),
        ]);
    }
}
