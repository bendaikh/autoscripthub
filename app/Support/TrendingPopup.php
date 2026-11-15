<?php

namespace Fickrr\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Items for the bottom-right "Trending now" popup shown on every storefront page.
 * Same pool as the home "Popular Items" slider: Games, AI and Office products
 * ordered by views, interleaved so one group does not take every slot.
 */
class TrendingPopup
{
    public const LIMIT = 6;

    /** @return array<int, object> */
    public static function items(bool $subscriptionMode): array
    {
        $key = 'trending_popup_items_v1_'.($subscriptionMode ? 'sub' : 'all');

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

    private static function load(bool $subscriptionMode): array
    {
        $parents = PopularMix::parents();
        $query = DB::table('items')
            ->leftJoin('users', 'users.id', '=', 'items.user_id')
            ->select(
                'items.item_id', 'items.item_slug', 'items.item_name', 'items.item_preview', 'items.item_thumbnail',
                'items.regular_price', 'items.extended_price', 'items.item_flash', 'items.free_download',
                'items.item_category', 'items.item_category_parent', 'items.item_category_type', 'items.item_views'
            )
            ->where('items.item_status', '=', 1)
            ->where('items.drop_status', '=', 'no')
            ->where(function ($q) {
                $q->where('items.item_preview', '!=', '')->orWhere('items.item_thumbnail', '!=', '');
            })
            ->where(function ($filter) use ($parents) {
                $filter->whereIn('items.item_category_parent', $parents)
                    ->orWhere('items.item_name', 'LIKE', '%gemini%')
                    ->orWhere('items.item_name', 'LIKE', '%google%')
                    ->orWhere('items.item_name', 'LIKE', '%chatgpt%')
                    ->orWhere('items.item_name', 'LIKE', '%office%');
            });
        if ($subscriptionMode) {
            $query->where('users.user_subscr_date', '>=', date('Y-m-d'))
                ->where('users.user_subscr_payment_status', '=', 'completed');
        }
        $rows = $query->orderBy('items.item_views', 'desc')->orderBy('items.item_id', 'desc')
            ->limit(60)->get()->all();

        return PopularMix::interleave($rows, self::LIMIT);
    }
}
