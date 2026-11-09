<?php

namespace Fickrr\Support;

use Fickrr\Helpers\Helper;
use Fickrr\Models\Settings;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Resolves the Operating System Licenses category tree from config + the database.
 */
class OsLicense
{
    /** @var array<string, mixed>|null */
    private static $resolved;

    /** @var array<int, int>|null */
    private static $itemIds;

    public static function reset(): void
    {
        self::$resolved = null;
        self::$itemIds = null;
    }

    public static function resolved(): array
    {
        if (self::$resolved !== null) {
            return self::$resolved;
        }

        try {
            $categories = DB::table('category')
                ->where('drop_status', '=', 'no')
                ->get(['cat_id', 'category_slug', 'category_name']);
            $subcategories = DB::table('subcategory')
                ->where('drop_status', '=', 'no')
                ->get(['subcat_id', 'cat_id', 'subcategory_slug', 'subcategory_name']);
            $config = config('os_licenses', []);
        } catch (Throwable $e) {
            return self::$resolved = [
                'category_ids' => [],
                'subcategory_ids' => [],
                'selection_values' => [],
            ];
        }

        return self::$resolved = OsLicenseCatalog::resolve($categories, $subcategories, is_array($config) ? $config : []);
    }

    public static function selectionValues(): array
    {
        return self::resolved()['selection_values'];
    }

    public static function isSelection($value): bool
    {
        return OsLicenseCatalog::matchesSelection($value, self::resolved());
    }

    public static function isItem($item): bool
    {
        if (! is_object($item)) {
            return false;
        }
        if (self::hasCategoryFields($item)) {
            return OsLicenseCatalog::matchesItem($item, self::resolved());
        }
        $id = (int) ($item->item_id ?? 0);
        if ($id <= 0) {
            return false;
        }

        return in_array($id, self::itemIds(), true);
    }

    public static function isOptionalAttributeLabel($label): bool
    {
        $configured = config('os_licenses.optional_attribute_labels', ['Games']);
        if (! is_array($configured)) {
            $configured = ['Games'];
        }

        return OsLicenseCatalog::isOptionalAttributeLabel($label, $configured);
    }

    /**
     * Pending cart rows for these items always carry the regular price.
     */
    public static function alignPendingCart($rows): void
    {
        if ($rows === null || count($rows) === 0) {
            return;
        }

        try {
            $additional = Settings::editAdditional();
            $duration = $additional->regular_license ?? '';
        } catch (Throwable $e) {
            return;
        }

        foreach ($rows as $row) {
            if (! self::isItem($row) || empty($row->ord_id)) {
                continue;
            }
            $base = Helper::price_info($row->item_flash ?? 0, $row->regular_price ?? 0);
            $current = (float) ($row->item_single_price ?? 0);
            if (($row->license ?? '') === 'regular' && abs($current - (float) $base) < 0.001) {
                continue;
            }
            $update = [
                'license' => 'regular',
                'item_single_price' => $base,
            ];
            if ($duration !== '') {
                $update['end_date'] = date('Y-m-d', strtotime($duration));
            }
            try {
                DB::table('item_order')
                    ->where('ord_id', $row->ord_id)
                    ->where('order_status', 'pending')
                    ->update($update);
            } catch (Throwable $e) {
                continue;
            }
            $row->license = 'regular';
            $row->item_single_price = $base;
            if (isset($update['end_date'])) {
                $row->end_date = $update['end_date'];
            }
        }
    }

    private static function hasCategoryFields($item): bool
    {
        return property_exists($item, 'item_category')
            || property_exists($item, 'item_category_type')
            || property_exists($item, 'item_category_parent');
    }

    /**
     * @return array<int, int>
     */
    private static function itemIds(): array
    {
        if (self::$itemIds !== null) {
            return self::$itemIds;
        }
        $resolved = self::resolved();
        $categoryIds = $resolved['category_ids'];
        $subcategoryIds = $resolved['subcategory_ids'];
        if ($categoryIds === [] && $subcategoryIds === []) {
            return self::$itemIds = [];
        }

        try {
            $ids = DB::table('items')
                ->where('drop_status', '=', 'no')
                ->where(function ($query) use ($categoryIds, $subcategoryIds) {
                    if ($categoryIds !== []) {
                        $query->where(function ($inner) use ($categoryIds) {
                            $inner->where('item_category_type', '=', 'category')
                                ->whereIn('item_category', $categoryIds);
                        })->orWhereIn('item_category_parent', $categoryIds);
                    }
                    if ($subcategoryIds !== []) {
                        $query->orWhere(function ($inner) use ($subcategoryIds) {
                            $inner->where('item_category_type', '=', 'subcategory')
                                ->whereIn('item_category', $subcategoryIds);
                        });
                    }
                })
                ->pluck('item_id');
        } catch (Throwable $e) {
            return self::$itemIds = [];
        }

        $mapped = [];
        foreach ($ids as $id) {
            $mapped[] = (int) $id;
        }

        return self::$itemIds = $mapped;
    }
}
