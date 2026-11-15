<?php

namespace Fickrr\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The product in the bottom-right "Trending now" popup on storefront pages.
 *
 * Shows the product pinned as "#1 Trending" (items.item_hero_pin, the
 * "Home page hero" checkbox in Admin > Items > edit), which is the GTA 6
 * listing. If nothing is pinned it falls back to the GTA 6 listing by name.
 * Never shows any other product: no match means no popup.
 */
class TrendingPopup
{
    /** @return array<int, object> zero or one item */
    public static function items(bool $subscriptionMode): array
    {
        $key = 'trending_popup_items_v2_'.($subscriptionMode ? 'sub' : 'all');

        try {
            return Cache::remember($key, 600, function () use ($subscriptionMode) {
                return self::load($subscriptionMode);
            });
        } catch (\Throwable $e) {
            // A popup must never break the page it sits on.
            report($e);

            return [];
        }
    }

    /** "GTA 6", "gta-6", "GTA VI", "Grand Theft Auto VI" / "6". */
    public static function isGta6($text): bool
    {
        $text = html_entity_decode((string) $text);

        return (bool) preg_match('/\bgta[\s_-]*(6|vi)\b|grand[\s_-]+theft[\s_-]+auto[\s_-]*(6|vi)\b/i', $text);
    }

    private static function load(bool $subscriptionMode): array
    {
        $base = function () use ($subscriptionMode) {
            $query = DB::table('items')
                ->leftJoin('users', 'users.id', '=', 'items.user_id')
                ->select(
                    'items.item_id', 'items.item_slug', 'items.item_name', 'items.item_preview', 'items.item_thumbnail',
                    'items.regular_price', 'items.extended_price', 'items.item_flash', 'items.free_download',
                    'items.item_category', 'items.item_category_parent', 'items.item_category_type', 'items.item_views'
                )
                ->where('items.item_status', '=', 1)
                ->where('items.drop_status', '=', 'no');
            if ($subscriptionMode) {
                $query->where('users.user_subscr_date', '>=', date('Y-m-d'))
                    ->where('users.user_subscr_payment_status', '=', 'completed');
            }

            return $query;
        };

        if (Schema::hasColumn('items', 'item_hero_pin')) {
            $pinned = $base()->where('items.item_hero_pin', '=', 1)->orderBy('items.item_id', 'desc')->first();
            if ($pinned) {
                return [$pinned];
            }
        }

        $candidates = $base()
            ->where(function ($q) {
                $q->where('items.item_name', 'LIKE', '%gta%')
                    ->orWhere('items.item_name', 'LIKE', '%grand theft auto%')
                    ->orWhere('items.item_slug', 'LIKE', '%gta%');
            })
            ->orderBy('items.item_views', 'desc')->orderBy('items.item_id', 'desc')
            ->limit(50)->get();
        foreach ($candidates as $item) {
            if (self::isGta6($item->item_name) || self::isGta6(str_replace('-', ' ', (string) $item->item_slug))) {
                return [$item];
            }
        }

        return [];
    }
}
