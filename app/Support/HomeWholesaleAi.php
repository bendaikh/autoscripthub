<?php

namespace Fickrr\Support;

use Illuminate\Support\Facades\DB;
use Fickrr\Models\Category;

/**
 * ash_fix11: data-driven homepage "Wholesale products" tiles.
 * Parent category slug is ai-licenses. Brand subcategories under that
 * parent appear automatically. A small list of external tiles can link
 * to an already-indexed category (e.g. Office Licenses) without moving items.
 */
class HomeWholesaleAi
{
    public const PARENT_SLUG = 'ai-licenses';

    /** Always visible, even with zero products. */
    public const ALWAYS_SHOW = ['gemini', 'chatgpt', 'muse-ai'];

    public const ICONS = [
        'gemini' => ['bg' => '#E8F0FE', 'fg' => '#1A73E8'],
        'chatgpt' => ['bg' => '#E7F6F2', 'fg' => '#10A37F'],
        'muse-ai' => ['bg' => '#F3E8FF', 'fg' => '#6D28D9'],
        'claude' => ['bg' => '#FCEEE8', 'fg' => '#D97757'],
        'elevenlabs' => ['bg' => '#F3F4F6', 'fg' => '#111827'],
        'antigravity' => ['bg' => '#EEF2FF', 'fg' => '#3730A3'],
        'scraiber' => ['bg' => '#ECFDF5', 'fg' => '#047857'],
        'ai-tools' => ['bg' => '#EEF2FF', 'fg' => '#4338CA'],
        'capcut' => ['bg' => '#E6FFFB', 'fg' => '#0F766E'],
        'udemy' => ['bg' => '#F3E8FF', 'fg' => '#A435F0'],
        'proton-vpn' => ['bg' => '#EEE9FF', 'fg' => '#6D4AFF'],
        'nordvpn' => ['bg' => '#E8F1FF', 'fg' => '#1A6DFF'],
        'apple-music' => ['bg' => '#FFE8EC', 'fg' => '#FA233B'],
        'zoom' => ['bg' => '#E8F1FF', 'fg' => '#0B5CFF'],
        'office' => ['bg' => '#FFF1E8', 'fg' => '#D83B01'],
    ];

    /**
     * Tiles that point at an existing indexed category/subcategory
     * instead of a subcategory under the wholesale parent.
     * count_parent: published items with this item_category_parent.
     */
    public const EXTERNAL_TILES = [
        [
            'slug' => 'office',
            'name' => 'Office',
            'path' => '/shop/category/office-licenses',
            'order' => 270,
            'count_parent' => 19,
        ],
    ];

    public static function parent()
    {
        return Category::where('category_slug', self::PARENT_SLUG)
            ->where('category_status', 1)
            ->where('drop_status', 'no')
            ->first();
    }

    public static function tiles(): array
    {
        $parent = self::parent();
        if (!$parent) {
            return ['parent' => null, 'tiles' => []];
        }
        $parentId = (int) $parent->cat_id;

        $subs = DB::table('subcategory')
            ->where('cat_id', $parentId)
            ->where('subcategory_status', 1)
            ->where('drop_status', 'no')
            ->orderBy('subcategory_order', 'asc')
            ->orderBy('subcategory_name', 'asc')
            ->get();

        $counts = DB::table('items')
            ->where('item_status', 1)
            ->where('drop_status', 'no')
            ->where('item_category_type', 'subcategory')
            ->where('item_category_parent', $parentId)
            ->select('item_category', DB::raw('count(*) as c'))
            ->groupBy('item_category')
            ->pluck('c', 'item_category')
            ->all();

        $tiles = [];
        foreach ($subs as $sub) {
            $slug = (string) $sub->subcategory_slug;
            $id = (int) $sub->subcat_id;
            $count = (int) ($counts[(string) $id] ?? $counts[$id] ?? 0);
            $forced = in_array($slug, self::ALWAYS_SHOW, true);
            if ($count < 1 && !$forced) {
                continue;
            }
            $tiles[] = self::decorateTile([
                'id' => $id,
                'name' => (string) $sub->subcategory_name,
                'slug' => $slug,
                'href' => url('/shop/subcategory/'.$slug),
                'count' => $count,
                'coming_soon' => $count < 1,
                'order' => (int) $sub->subcategory_order,
            ]);
        }

        foreach (self::EXTERNAL_TILES as $ext) {
            $count = 0;
            if (!empty($ext['count_parent'])) {
                $count = (int) DB::table('items')
                    ->where('item_status', 1)
                    ->where('drop_status', 'no')
                    ->where('item_category_parent', (int) $ext['count_parent'])
                    ->count();
            }
            $tiles[] = self::decorateTile([
                'id' => 0,
                'name' => (string) $ext['name'],
                'slug' => (string) $ext['slug'],
                'href' => url($ext['path']),
                'count' => $count,
                'coming_soon' => $count < 1,
                'order' => (int) ($ext['order'] ?? 999),
            ]);
        }

        usort($tiles, function ($a, $b) {
            if ($a['order'] === $b['order']) {
                return strcasecmp($a['name'], $b['name']);
            }
            return $a['order'] <=> $b['order'];
        });

        return [
            'parent' => $parent,
            'parent_href' => url('/shop/category/'.self::PARENT_SLUG),
            'tiles' => $tiles,
        ];
    }

    private static function decorateTile(array $tile): array
    {
        $style = self::ICONS[$tile['slug']] ?? ['bg' => '#F3F4F6', 'fg' => '#374151'];
        $tile['bg'] = $style['bg'];
        $tile['fg'] = $style['fg'];
        $tile['icon'] = self::iconFile($tile['slug']);
        return $tile;
    }

    public static function iconFile(string $slug): string
    {
        $base = public_path('assets/ai-brands');
        $try = $base.'/'.$slug.'.svg';
        if (is_file($try)) {
            return 'assets/ai-brands/'.$slug.'.svg';
        }
        return 'assets/ai-brands/_generic.svg';
    }
}
