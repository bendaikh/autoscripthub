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
 *  - it comes from an excluded IP address, or from an IP the owner used the
 *    admin panel from in the last AUTO_IP_DAYS days (covers private /
 *    incognito windows and other browsers on the same connection).
 */
class AnalyticsExclusion
{
    public const COOKIE = 'ash_noanalytics';
    public const AUTO_IP_DAYS = 3;

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
            $hasSeen = Schema::hasColumn('analytics_exclusions', 'last_seen_at');
            $rows = DB::table('analytics_exclusions')->get($hasSeen ? ['type', 'value', 'last_seen_at'] : ['type', 'value']);
            $cut = time() - self::AUTO_IP_DAYS * 86400;
            $auto = $hasSeen ? $rows->where('type', 'autoip')->filter(fn ($r) => $r->last_seen_at && strtotime((string) $r->last_seen_at) >= $cut)->pluck('value')->all() : [];
            return [
                'ip' => array_values(array_unique(array_merge($rows->where('type', 'ip')->pluck('value')->all(), $auto))),
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

    /**
     * Remembers the IP the owner is using the admin panel from (excluded for
     * AUTO_IP_DAYS days after the last admin visit) and removes that IP's
     * visits from the last AUTO_IP_DAYS days.
     */
    public static function learnIp(string $ip): void
    {
        $ip = trim($ip);
        if (!self::ready() || !filter_var($ip, FILTER_VALIDATE_IP) || !Schema::hasColumn('analytics_exclusions', 'last_seen_at')) {
            return;
        }
        DB::table('analytics_exclusions')->updateOrInsert(
            ['type' => 'autoip', 'value' => $ip],
            ['label' => 'Learned from admin panel', 'last_seen_at' => now(), 'created_at' => now()]
        );
        self::forget();
        self::purgeIp($ip, now()->subDays(self::AUTO_IP_DAYS));
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

    /** Deletes past visits from this IP (optionally only since $since). */
    public static function purgeIp(string $ip, $since = null): int
    {
        try {
            $hash = self::ipHash($ip);
            $removed = 0;
            if (Schema::hasTable('site_visits') && Schema::hasColumn('site_visits', 'ip_hash')) {
                $q = DB::table('site_visits')->where('ip_hash', $hash);
                if ($since) {
                    $q->where('visited_at', '>=', $since);
                }
                $removed += $q->delete();
            }
            if (!Schema::hasTable('landing_page_visits')) {
                return $removed;
            }
            $vq = DB::table('landing_page_visits')->where('lpv_ip_hash', $hash);
            if ($since) {
                $vq->where('lpv_last_seen_at', '>=', $since);
            }
            $visits = $vq->pluck('lpv_id', 'lpv_visitor_id');
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
            return $removed + DB::table('landing_page_visits')->whereIn('lpv_id', $visits->values()->all())->delete();
        } catch (\Throwable $e) {
            report($e);
            return 0;
        }
    }
}
