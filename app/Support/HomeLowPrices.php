<?php

namespace Fickrr\Support;

use Illuminate\Support\Facades\DB;
use Fickrr\Models\Items;
use Fickrr\Helpers\Helper;

/**
 * ash_fix8: cheapest published, buyable products for the homepage row.
 * Read-only. Does not change prices or item flags.
 */
class HomeLowPrices
{
    public const LIMIT = 16;
    public const POOL = 120;

    public static function items(bool $subscriptionMode, string $today, bool $localStorage, int $limit = self::LIMIT)
    {
        $limit = max(1, min(24, $limit));
        $q = Items::with('ratings')->select(
            'items.item_id', 'items.item_liked', 'items.item_slug', 'items.item_preview',
            'items.item_name', 'items.item_type', 'items.item_type_id', 'users.user_photo',
            'users.username', 'users.user_document_verified', 'items.updated_item', 'items.item_sold',
            'items.free_download', 'items.item_flash', 'items.regular_price', 'items.extended_price',
            'items.item_token', 'items.user_id', 'items.file_type', 'items.item_delimiter',
            'items.item_serials_list', 'items.item_category', 'items.item_category_parent',
            'items.item_category_type'
        )->leftJoin('users', 'users.id', '=', 'items.user_id')
            ->where('items.item_status', '=', 1)
            ->where('items.drop_status', '=', 'no')
            ->where(function ($w) {
                $w->where('items.free_download', '=', 0)->orWhereNull('items.free_download');
            })
            ->where(function ($w) {
                $w->where('items.regular_price', '>', 0)->orWhere('items.extended_price', '>', 0);
            });
        if ($subscriptionMode) {
            $q->where('users.user_subscr_date', '>=', $today)
                ->where('users.user_subscr_payment_status', '=', 'completed');
        }
        $pool = $q->orderByRaw(
            'LEAST(CASE WHEN items.regular_price > 0 THEN items.regular_price ELSE 999999 END, CASE WHEN items.extended_price > 0 THEN items.extended_price ELSE 999999 END) ASC'
        )->orderBy('items.item_id', 'asc')->take(self::POOL)->get();

        $priced = [];
        foreach ($pool as $item) {
            if ((int) ($item->free_download ?? 0) === 1) {
                continue;
            }
            $price = 0.0;
            try {
                $price = (float) Helper::catalog_price($item);
            } catch (\Throwable $e) {
                continue;
            }
            if ($price <= 0) {
                continue;
            }
            $img = '';
            try {
                $img = Helper::card_image($item);
            } catch (\Throwable $e) {
                $img = '';
            }
            if ($img !== '' && class_exists(ProductImage::class) && !ProductImage::isUsable($img, $localStorage)) {
                continue;
            }
            $priced[] = ['item' => $item, 'price' => $price];
        }
        usort($priced, function ($a, $b) {
            if ($a['price'] === $b['price']) {
                return ((int) $a['item']->item_id) <=> ((int) $b['item']->item_id);
            }
            return $a['price'] <=> $b['price'];
        });
        $priced = array_slice($priced, 0, $limit);
        return collect(array_map(function ($row) {
            return $row['item'];
        }, $priced));
    }

    /**
     * ash_fix12: hero tile sets from the same cheapest-item query.
     * 15 items in groups of 5 (3 visible + 2 spare). If there are at least
     * 6 items, the first visible trio starts at #4 so the hero does not
     * repeat the first three cards of the Lowest prices row; the cheapest
     * three still appear in a later rotation.
     */
    public const HERO_LIMIT = 15;
    public const HERO_CHUNK = 5;
    public const HERO_OFFSET = 3;

    public static function heroSets(bool $subscriptionMode, string $today, bool $localStorage): array
    {
        $pool = self::items($subscriptionMode, $today, $localStorage, self::HERO_LIMIT);
        $cards = [];
        foreach ($pool as $item) {
            $image = '';
            try {
                $image = trim((string) Helper::card_image($item));
            } catch (\Throwable $e) {
                $image = '';
            }
            if ($image === '') {
                continue;
            }
            if (class_exists(ProductImage::class) && !ProductImage::isUsable($image, $localStorage)) {
                continue;
            }
            $cards[] = [
                'name' => (string) $item->item_name,
                'slug' => (string) $item->item_slug,
                'image' => $image,
                'regular_price' => $item->regular_price,
                'extended_price' => $item->extended_price,
                'item_flash' => $item->item_flash,
                'free_download' => $item->free_download,
                'item_category' => $item->item_category,
                'item_category_parent' => $item->item_category_parent,
                'item_category_type' => $item->item_category_type,
            ];
        }
        if (count($cards) >= self::HERO_OFFSET * 2) {
            $cards = array_merge(
                array_slice($cards, self::HERO_OFFSET),
                array_slice($cards, 0, self::HERO_OFFSET)
            );
        }
        $sets = [];
        foreach (array_chunk($cards, self::HERO_CHUNK) as $chunk) {
            if (!$chunk) {
                continue;
            }
            $sets[] = [
                'name' => 'Lowest prices',
                'slug' => '',
                'id' => 0,
                'url' => '/shop?sort=price_asc',
                'trending' => false,
                'items' => array_values($chunk),
            ];
        }
        return $sets;
    }
}
