<?php

namespace Fickrr\Support\Visits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Keeps the site owner out of the analytics (visits chart, countries,
 * realtime, landing page stats, Google Analytics and ad pixels).
 *
 * A visit is left out when:
 *  - an admin is logged in, or
 *  - the browser has the "ash_noanalytics" cookie (set on every device that
 *    opens the admin panel), or
 *  - its visitor id belongs to an excluded device, or
 *  - it comes from an excluded IP address.
 */
class AnalyticsExclusion
{
    public const COOKIE = 'ash_noanalytics';

    public static function ready(): bool
    {
        try {
            return Schema::hasTable('analytics_exclusions');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function isExcluded(Request $request, ?string $visitorId = null, ?string $ip = null): bool
    {
        if ((string) $request->cookies->get(self::COOKIE) === '1') {
            return true;
        }
        if (self::isAdmin()) {
            return true;
        }
        $lists = self::lists();
        if ($ip !== null && $ip !== '' && in_array($ip, $lists['ip'], true)) {
            return true;
        }
        if ($visitorId !== null && $visitorId !== '' && in_array(self::deviceHash($visitorId), $lists['device'], true)) {
            return true;
        }
        return false;
    }

    public static function isAdmin(): bool
    {
        try {
            $user = auth()->user();
            return $user && (($user->user_type ?? '') === 'admin' || (int) ($user->id ?? 0) === 1);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** @return array{ip: string[], device: string[]} */
    public static function lists(): array
    {
        if (!self::ready()) {
            return ['ip' => [], 'device' => []];
        }
        return Cache::remember('analytics.exclusions.v1', 60, function () {
            $rows = DB::table('analytics_exclusions')->get(['type', 'value']);
            return [
                'ip' => $rows->where('type', 'ip')->pluck('value')->values()->all(),
                'device' => $rows->where('type', 'device')->pluck('value')->values()->all(),
            ];
        });
    }

    public static function forget(): void
    {
        Cache::forget('analytics.exclusions.v1');
    }

    public static function deviceHash(string $visitorId): string
    {
        return VisitRules::hashId((string) config('app.key'), substr(trim($visitorId), 0, 80));
    }

    public static function ipHash(string $ip): string
    {
        // same hash the landing page tracker stores (lpv_ip_hash)
        return hash('sha256', $ip . '|' . (string) config('app.key', 'app-key'));
    }

    /**
     * Excludes this browser's visitor ids and removes their past visits.
     * Returns how many past visits were removed.
     */
    public static function excludeDevice(array $visitorIds, string $label = ''): int
    {
        if (!self::ready()) {
            return 0;
        }
        $ids = array_values(array_unique(array_filter(array_map(fn ($v) => substr(trim((string) $v), 0, 80), $visitorIds))));
        if (!$ids) {
            return 0;
        }
        foreach ($ids as $id) {
            DB::table('analytics_exclusions')->updateOrInsert(
                ['type' => 'device', 'value' => self::deviceHash($id)],
                ['label' => $label !== '' ? mb_substr($label, 0, 100) : null, 'created_at' => now()]
            );
        }
        self::forget();
        return self::purgeVisitors($ids);
    }

    public static function addIp(string $ip, string $label = ''): bool
    {
        $ip = trim($ip);
        if (!self::ready() || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }
        DB::table('analytics_exclusions')->updateOrInsert(
            ['type' => 'ip', 'value' => $ip],
            ['label' => $label !== '' ? mb_substr($label, 0, 100) : null, 'created_at' => now()]
        );
        self::forget();
        self::purgeIp($ip);
        return true;
    }

    public static function remove(int $id): void
    {
        if (self::ready()) {
            DB::table('analytics_exclusions')->where('id', $id)->delete();
            self::forget();
        }
    }

    /** Deletes past visits of these visitor ids (site visits + landing page stats). */
    public static function purgeVisitors(array $ids): int
    {
        $removed = 0;
        try {
            $hashes = array_map([self::class, 'deviceHash'], $ids);
            if (Schema::hasTable('site_visits')) {
                $removed += DB::table('site_visits')->whereIn('visitor_hash', $hashes)->delete();
            }
            if (Schema::hasTable('landing_page_visits')) {
                $removed += DB::table('landing_page_visits')->whereIn('lpv_visitor_id', $ids)->delete();
            }
            if (Schema::hasTable('landing_page_events')) {
                DB::table('landing_page_events')->whereIn('lpe_visitor_id', $ids)->delete();
            }
        } catch (\Throwable $e) {
            report($e);
        }
        return $removed;
    }

    /** Deletes past landing page visits from this IP (the site visits table keeps no IPs). */
    public static function purgeIp(string $ip): int
    {
        try {
            if (!Schema::hasTable('landing_page_visits')) {
                return 0;
            }
            $hash = self::ipHash($ip);
            $visits = DB::table('landing_page_visits')->where('lpv_ip_hash', $hash)->pluck('lpv_id', 'lpv_visitor_id');
            if ($visits->isEmpty()) {
                return 0;
            }
            // the same visitors' site visits go too
            if (Schema::hasTable('site_visits')) {
                DB::table('site_visits')->whereIn('visitor_hash', $visits->keys()->map(fn ($v) => self::deviceHash((string) $v))->all())->delete();
            }
            if (Schema::hasTable('landing_page_events')) {
                DB::table('landing_page_events')->whereIn('lpv_id', $visits->values()->all())->delete();
            }
            return DB::table('landing_page_visits')->where('lpv_ip_hash', $hash)->delete();
        } catch (\Throwable $e) {
            report($e);
            return 0;
        }
    }
}
