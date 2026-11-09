<?php

namespace Fickrr\Support;

/**
 * Pure matching rules for the Operating System Licenses category tree.
 * Database rows are passed in so this can be tested without a connection.
 */
class OsLicenseCatalog
{
    /**
     * Parent names that sell as one price even when the admin toggle was not
     * stored yet. Matching is exact after trim and case-folding.
     */
    public const FALLBACK_PARENT_NAMES = [
        'Operating System Licenses',
        'Operating System License',
        'Office Licenses',
        'Antivirus Licenses',
        'Graphics & Design Licenses',
        'Games',
        'VPN Licenses',
    ];

    public const FALLBACK_PARENT_SLUGS = [
        'operating-system-licenses',
        'operating-system-license',
        'office-licenses',
        'antivirus-licenses',
        'graphics-design-licenses',
        'games',
        'vpn-licenses',
    ];

    public static function normalizeKey($value): string
    {
        $value = strtolower(trim((string) $value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);

        return trim((string) $value, '-');
    }

    public static function normalizeLabel($value): string
    {
        $value = strtolower(trim((string) $value));
        $value = preg_replace('/\s+/', ' ', $value);

        return (string) $value;
    }

    /**
     * @param  iterable<int, object|array>  $categories
     * @param  iterable<int, object|array>  $subcategories
     * @param  array{slugs?:array,names?:array,ids?:array}  $config
     * @return array{category_ids:array<int,int>,subcategory_ids:array<int,int>,selection_values:array<int,string>}
     */
    public static function resolve(iterable $categories, iterable $subcategories, array $config): array
    {
        $needles = [];
        foreach (array_merge($config['slugs'] ?? [], $config['names'] ?? []) as $needle) {
            $key = self::normalizeKey($needle);
            if ($key !== '') {
                $needles[$key] = true;
            }
        }

        $explicit = [];
        foreach ($config['ids'] ?? [] as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $explicit[$id] = true;
            }
        }

        $categoryIds = [];
        foreach ($categories as $row) {
            $row = (array) $row;
            $id = (int) ($row['cat_id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            if (isset($explicit[$id]) || self::flagOn($row['single_price'] ?? null) || self::matchesRoot($row, $needles)) {
                $categoryIds[$id] = $id;
            }
        }

        $subcategoryIds = [];
        foreach ($subcategories as $row) {
            $row = (array) $row;
            $id = (int) ($row['subcat_id'] ?? 0);
            $parent = (int) ($row['cat_id'] ?? 0);
            if ($id > 0 && (isset($categoryIds[$parent]) || self::flagOn($row['single_price'] ?? null))) {
                $subcategoryIds[$id] = $id;
            }
        }

        $selections = [];
        foreach ($categoryIds as $id) {
            $selections[] = 'category_'.$id;
        }
        foreach ($subcategoryIds as $id) {
            $selections[] = 'subcategory_'.$id;
        }

        return [
            'category_ids' => array_values($categoryIds),
            'subcategory_ids' => array_values($subcategoryIds),
            'selection_values' => $selections,
        ];
    }

    public static function matchesItem($item, array $resolved): bool
    {
        if (! is_object($item) && ! is_array($item)) {
            return false;
        }
        $item = (array) $item;
        $type = strtolower(trim((string) ($item['item_category_type'] ?? '')));
        $categoryId = (int) ($item['item_category'] ?? 0);
        $parentId = (int) ($item['item_category_parent'] ?? 0);
        $categoryIds = array_map('intval', $resolved['category_ids'] ?? []);
        $subcategoryIds = array_map('intval', $resolved['subcategory_ids'] ?? []);

        if ($parentId > 0 && in_array($parentId, $categoryIds, true)) {
            return true;
        }

        if ($type === 'subcategory' || $type === 'sub_category') {
            return in_array($categoryId, $subcategoryIds, true);
        }

        if (in_array($categoryId, $categoryIds, true)) {
            return true;
        }

        return in_array($categoryId, $subcategoryIds, true);
    }

    public static function matchesSelection($value, array $resolved): bool
    {
        $value = trim((string) $value);
        if ($value === '') {
            return false;
        }
        if (in_array($value, $resolved['selection_values'] ?? [], true)) {
            return true;
        }
        if (! preg_match('/^(category|subcategory)_(\d+)$/', $value, $match)) {
            return false;
        }
        $id = (int) $match[2];
        if ($match[1] === 'category') {
            return in_array($id, array_map('intval', $resolved['category_ids'] ?? []), true);
        }

        return in_array($id, array_map('intval', $resolved['subcategory_ids'] ?? []), true);
    }

    public static function isOptionalAttributeLabel($label, array $configuredLabels): bool
    {
        $label = self::normalizeLabel($label);
        if ($label === '') {
            return false;
        }
        foreach ($configuredLabels as $configured) {
            if ($label === self::normalizeLabel($configured)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whole dollars for the rest of the site. Two decimals for a single-price item.
     */
    public static function formatTaggedPrice($position, $symbol, $amount, bool $exact): string
    {
        if ($exact) {
            $shown = number_format(round((float) $amount, 2), 2, '.', '');
        } else {
            $shown = (string) round((float) $amount);
        }
        $symbol = (string) $symbol;
        if ($position === 'left') {
            return $symbol.$shown;
        }

        return $shown.$symbol;
    }

    public static function roundAmount($amount, bool $exact): float
    {
        if ($exact) {
            return round((float) $amount, 2);
        }

        return (float) round((float) $amount);
    }

    public static function flagOn($value): bool
    {
        return $value === 1 || $value === '1' || $value === true;
    }

    public static function isFallbackParentName($name, array $names): bool
    {
        $key = self::normalizeKey($name);
        if ($key === '') {
            return false;
        }
        foreach ($names as $needle) {
            if ($key === self::normalizeKey($needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, bool>  $needles
     */
    private static function matchesRoot(array $row, array $needles): bool
    {
        foreach (['category_slug', 'category_name'] as $field) {
            $key = self::normalizeKey($row[$field] ?? '');
            if ($key !== '' && isset($needles[$key])) {
                return true;
            }
        }

        return false;
    }
}
