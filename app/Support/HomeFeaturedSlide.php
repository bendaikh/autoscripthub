<?php

namespace Fickrr\Support;

use Fickrr\Models\Items;
use Fickrr\Helpers\Helper;

/**
 * ash_fix9: homepage Featured Products row only.
 * Picks four specific published items in a fixed order.
 * Does not change item_featured flags or the /featured-items listing.
 */
class HomeFeaturedSlide
{
    public const SLUGS = [
        'stockifly-saas-410-multitenant-billing-inventory-and-pos-with-online-shop-laravel-12-and-vue-3',
        'snapshop-101-nextjs-16-ecommerce-platform-with-mongodb-admin-dashboard-and-multipayment',
        'infixedu-school-erp-960-laravel-13-school-management-system-with-admin-teacher-and-parent-portals',
        'elmapicms-40-selfhosted-headless-cms-with-rest-api-laravel-13-and-react-admin-panel',
    ];

    public static function pick($existing, bool $subscriptionMode, string $today, bool $localStorage)
    {
        $bySlug = [];
        if ($existing) {
            foreach ($existing as $item) {
                $s = (string) ($item->item_slug ?? '');
                if ($s !== '') {
                    $bySlug[$s] = $item;
                }
            }
        }

        $missing = [];
        foreach (self::SLUGS as $slug) {
            if (!isset($bySlug[$slug])) {
                $missing[] = $slug;
            }
        }

        if ($missing) {
            $q = Items::with('ratings')->select(
                'items.item_id', 'items.item_liked', 'items.item_slug', 'items.item_preview',
                'items.item_name', 'items.item_type', 'items.item_type_id', 'users.user_photo',
                'users.username', 'users.user_document_verified', 'items.updated_item', 'items.item_sold',
                'items.free_download', 'items.item_flash', 'items.regular_price', 'items.extended_price',
                'items.item_token', 'items.user_id', 'items.file_type', 'items.item_delimiter',
                'items.item_serials_list'
            )->leftJoin('users', 'users.id', '=', 'items.user_id')
                ->where('items.item_status', '=', 1)
                ->where('items.drop_status', '=', 'no')
                ->whereIn('items.item_slug', $missing);
            if ($subscriptionMode) {
                $q->where('users.user_subscr_date', '>=', $today)
                    ->where('users.user_subscr_payment_status', '=', 'completed');
            }
            foreach ($q->get() as $item) {
                $bySlug[(string) $item->item_slug] = $item;
            }
        }

        $out = [];
        foreach (self::SLUGS as $slug) {
            if (!isset($bySlug[$slug])) {
                continue;
            }
            $item = $bySlug[$slug];
            $img = '';
            try {
                $img = Helper::card_image($item);
            } catch (\Throwable $e) {
                $img = '';
            }
            if ($img !== '' && class_exists(ProductImage::class) && !ProductImage::isUsable($img, $localStorage)) {
                continue;
            }
            $out[] = $item;
        }
        return collect($out);
    }
}
