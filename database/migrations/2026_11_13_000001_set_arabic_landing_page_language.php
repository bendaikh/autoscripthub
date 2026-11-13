<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The landing language column already exists (lp_content_language, default en).
 * This only sets landing page 49 to Arabic when that row is still English and
 * its own copy is Arabic. No other rows are updated.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('landing_pages') || !Schema::hasColumn('landing_pages', 'lp_content_language')) {
            return;
        }

        $row = DB::table('landing_pages')->where('lp_id', 49)->first();
        if (!$row) {
            return;
        }

        $saved = strtolower(trim((string) ($row->lp_content_language ?? '')));
        if ($saved !== '' && $saved !== 'en') {
            return;
        }

        $sample = trim(
            ($row->lp_title ?? '') . ' ' .
            ($row->lp_tagline ?? '') . ' ' .
            ($row->lp_cta_text ?? '') . ' ' .
            strip_tags((string) ($row->lp_description ?? '')) . ' ' .
            strip_tags((string) ($row->lp_about_description ?? '')) . ' ' .
            strip_tags((string) ($row->lp_what_you_get ?? ''))
        );
        preg_match_all('/\p{Arabic}/u', $sample, $matches);
        if (count($matches[0] ?? []) < 12) {
            return;
        }

        DB::table('landing_pages')->where('lp_id', 49)->update([
            'lp_content_language' => 'ar',
        ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('landing_pages') || !Schema::hasColumn('landing_pages', 'lp_content_language')) {
            return;
        }

        DB::table('landing_pages')
            ->where('lp_id', 49)
            ->where('lp_content_language', 'ar')
            ->update(['lp_content_language' => 'en']);
    }
};
