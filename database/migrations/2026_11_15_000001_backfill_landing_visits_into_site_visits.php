<?php

use Fickrr\Support\Visits\VisitRules;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Copies landing page visits recorded before landing pages sent visits to the
 * admin dashboard (site_visits), so the dashboard Visits chart, Countries and
 * Realtime include past landing page traffic too. Visits from now on are
 * recorded directly by the visit tracker on the landing pages.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('landing_page_visits') || !Schema::hasTable('site_visits') || !Schema::hasTable('landing_pages')) {
            return;
        }

        $cutoff = date('Y-m-d H:i:s');
        $key = (string) config('app.key');
        $slugs = DB::table('landing_pages')->pluck('lp_slug', 'lp_id');

        DB::table('landing_page_visits')
            ->where('created_at', '<', $cutoff)
            ->chunkById(1000, function ($visits) use ($key, $slugs) {
                $rows = [];
                foreach ($visits as $v) {
                    if (VisitRules::isBot($v->lpv_user_agent)) {
                        continue;
                    }
                    $slug = $slugs[$v->lp_id] ?? null;
                    $visitedAt = $v->lpv_started_at ?: $v->created_at;
                    if ($slug === null || !$visitedAt) {
                        continue;
                    }
                    $referrer = substr(trim((string) $v->lpv_referrer), 0, 191);
                    $country = strtoupper(substr((string) $v->lpv_country_code, 0, 2));
                    $rows[] = [
                        'visited_at' => $visitedAt,
                        'path' => VisitRules::cleanPath('/landing/'.$slug),
                        'visitor_hash' => VisitRules::hashId($key, (string) $v->lpv_visitor_id),
                        'country_code' => strlen($country) === 2 ? $country : null,
                        'referrer' => $referrer !== '' ? $referrer : null,
                        'device' => VisitRules::device($v->lpv_user_agent),
                    ];
                }
                if ($rows !== []) {
                    DB::table('site_visits')->insert($rows);
                }
            }, 'lpv_id');
    }

    public function down(): void
    {
        // Copied rows cannot be told apart from live ones; nothing to undo.
    }
};
