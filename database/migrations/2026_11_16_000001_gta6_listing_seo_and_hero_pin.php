<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 1. items.item_hero_pin: products pinned to the home page hero ("#1 Trending").
 *    Admin > Items > edit > "Pin to the home page hero".
 * 2. One-time update of the GTA 6 listing: "GTA 6" in the title, tags, short
 *    description, description and SEO fields, and pinned to the hero.
 *    Only fields that do not mention "GTA 6" yet are changed; the URL (slug) stays.
 */
return new class extends Migration
{
    private const KEYWORDS = ['GTA 6', 'GTA VI', 'Grand Theft Auto 6', 'Grand Theft Auto VI', 'GTA 6 PC', 'Rockstar Games'];

    public function up(): void
    {
        if (!Schema::hasTable('items')) {
            return;
        }
        if (!Schema::hasColumn('items', 'item_hero_pin')) {
            Schema::table('items', function (Blueprint $table) {
                $table->tinyInteger('item_hero_pin')->default(0)->index();
            });
        }

        $candidates = DB::table('items')
            ->where('drop_status', '=', 'no')
            ->where(function ($q) {
                foreach (['item_name', 'item_slug', 'item_tags'] as $col) {
                    $q->orWhere($col, 'LIKE', '%gta%')->orWhere($col, 'LIKE', '%grand%theft%');
                }
            })
            ->get(['item_id', 'item_name', 'item_slug', 'item_tags', 'item_shortdesc', 'item_desc', 'item_seo_keyword', 'item_seo_desc', 'item_views', 'item_status']);

        $isGta6 = fn ($text) => (bool) preg_match('/(gta|grand[\s_-]*theft[\s_-]*auto)[\s_-]*(6|vi)(?![a-z0-9])/i', (string) $text);
        $matches = $candidates->filter(fn ($i) => $isGta6($i->item_name) || $isGta6($i->item_slug) || $isGta6($i->item_tags))->values();

        if ($matches->isEmpty()) {
            echo "  GTA 6 listing not found. Items mentioning GTA: "
                . ($candidates->isEmpty() ? 'none' : $candidates->map(fn ($i) => '#' . $i->item_id . ' ' . $i->item_name)->implode(' | ')) . "\n";
            return;
        }

        $has = fn ($text) => (bool) preg_match('/gta[\s_-]*6(?![0-9])/i', html_entity_decode((string) $text));
        $pinId = (int) $matches->sortByDesc(fn ($i) => (int) $i->item_status * 1000000 + (int) $i->item_views)->first()->item_id;

        foreach ($matches as $item) {
            $data = [];
            if (!$has($item->item_name)) {
                $data['item_name'] = mb_substr('GTA 6 – ' . $item->item_name, 0, 190);
            }
            $data['item_tags'] = $this->mergeList($item->item_tags, self::KEYWORDS);
            if (!$has($item->item_shortdesc)) {
                $short = trim((string) $item->item_shortdesc);
                $data['item_shortdesc'] = 'GTA 6 (Grand Theft Auto VI)' . ($short !== '' ? ' – ' . $short : ' – ' . $item->item_name);
            }
            if (!$has($item->item_desc)) {
                $data['item_desc'] = htmlentities('<p><strong>GTA 6 (Grand Theft Auto VI)</strong></p>') . (string) $item->item_desc;
            }
            $data['item_allow_seo'] = 1;
            $data['item_seo_keyword'] = mb_substr($this->mergeList($item->item_seo_keyword, self::KEYWORDS), 0, 1000);
            $seoDesc = trim((string) $item->item_seo_desc);
            if ($seoDesc === '') {
                $base = trim(strip_tags(html_entity_decode((string) ($item->item_shortdesc ?: $item->item_name))));
                $seoDesc = 'GTA 6 (Grand Theft Auto VI) – ' . $base;
            } elseif (!$has($seoDesc)) {
                $seoDesc = 'GTA 6 – ' . $seoDesc;
            }
            $data['item_seo_desc'] = mb_substr($seoDesc, 0, 300);
            if ((int) $item->item_id === $pinId) {
                $data['item_hero_pin'] = 1;
            }
            foreach ($data as $col => $value) {
                if (is_string($value) && ($max = $this->maxLength($col)) !== null) {
                    $data[$col] = mb_substr($value, 0, $max);
                }
            }
            try {
                DB::table('items')->where('item_id', $item->item_id)->update($data);
            } catch (\Throwable $e) {
                echo '  Could not update #' . $item->item_id . ': ' . $e->getMessage() . "\n";
                continue;
            }
            echo '  Updated #' . $item->item_id . ' "' . ($data['item_name'] ?? $item->item_name) . '"'
                . ((int) $item->item_id === $pinId ? ' (pinned to home hero)' : '') . "\n";
        }
    }

    /** Character limit of a varchar column (null for text columns). */
    private function maxLength(string $col): ?int
    {
        static $cols = null;
        if ($cols === null) {
            $cols = [];
            try {
                foreach (Schema::getColumns('items') as $c) {
                    $cols[$c['name']] = $c['type'] ?? '';
                }
            } catch (\Throwable $e) {
            }
        }
        if (preg_match('/char\((\d+)\)/i', (string) ($cols[$col] ?? ''), $m)) {
            return (int) $m[1];
        }
        return null;
    }

    private function mergeList($existing, array $add): string
    {
        $list = array_values(array_filter(array_map('trim', explode(',', (string) $existing)), 'strlen'));
        $lower = array_map('mb_strtolower', $list);
        foreach ($add as $word) {
            if (!in_array(mb_strtolower($word), $lower, true)) {
                $list[] = $word;
                $lower[] = mb_strtolower($word);
            }
        }
        return implode(',', $list);
    }

    public function down(): void
    {
        if (Schema::hasColumn('items', 'item_hero_pin')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('item_hero_pin');
            });
        }
    }
};
