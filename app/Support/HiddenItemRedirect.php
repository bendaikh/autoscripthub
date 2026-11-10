<?php

namespace Fickrr\Support;

/**
 * A product that is turned off or dropped should leave the item URL
 * and land on its parent category, or the shop if that category is gone.
 */
class HiddenItemRedirect
{
    public static function isHidden($item): bool
    {
        $status = (int) (is_object($item) ? ($item->item_status ?? 1) : ($item['item_status'] ?? 1));
        $dropped = (string) (is_object($item) ? ($item->drop_status ?? 'no') : ($item['drop_status'] ?? 'no'));

        return $status !== 1 || $dropped !== 'no';
    }

    public static function parentId($item): int
    {
        $parent = (int) (is_object($item) ? ($item->item_category_parent ?? 0) : ($item['item_category_parent'] ?? 0));
        if ($parent > 0) {
            return $parent;
        }
        $type = (string) (is_object($item) ? ($item->item_category_type ?? '') : ($item['item_category_type'] ?? ''));
        if ($type === 'category') {
            return (int) (is_object($item) ? ($item->item_category ?? 0) : ($item['item_category'] ?? 0));
        }

        return 0;
    }

    public static function path($category): string
    {
        $slug = trim((string) (is_object($category) ? ($category->category_slug ?? '') : ($category['category_slug'] ?? '')));
        if ($slug !== '') {
            return '/shop/category/'.$slug;
        }

        return '/shop';
    }
}
