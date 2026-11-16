<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - analytics_exclusions.last_seen_at: IPs learned from the owner's admin
 *   visits (type "autoip") are excluded while recently seen.
 * - site_visits.ip_hash: lets the visits chart drop visits from excluded IPs.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('analytics_exclusions') && !Schema::hasColumn('analytics_exclusions', 'last_seen_at')) {
            Schema::table('analytics_exclusions', function (Blueprint $table) {
                $table->timestamp('last_seen_at')->nullable()->index();
            });
        }
        if (Schema::hasTable('site_visits') && !Schema::hasColumn('site_visits', 'ip_hash')) {
            Schema::table('site_visits', function (Blueprint $table) {
                $table->char('ip_hash', 64)->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('analytics_exclusions', 'last_seen_at')) {
            Schema::table('analytics_exclusions', function (Blueprint $table) {
                $table->dropColumn('last_seen_at');
            });
        }
        if (Schema::hasColumn('site_visits', 'ip_hash')) {
            Schema::table('site_visits', function (Blueprint $table) {
                $table->dropColumn('ip_hash');
            });
        }
    }
};
