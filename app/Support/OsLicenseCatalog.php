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
        // Always union the built-in names. A cached config file from before
        // VPN Licenses (or any later name) was added must not hide that parent
        // when its single_price column is still null or 0.
        foreach (array_merge(
            self::FALLBACK_PARENT_SLUGS,
            self::FALLBACK_PARENT_NAMES,
            $config['slugs'] ?? [],
            $config['names'] ?? []
        ) as $needle) {
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

    /**
     * Home-page slides: single-price parents, plus a sub-category only when
     * it is switched on by itself and its parent is not already a slide.
     *
     * @param  iterable<int, object|array>  $parents
     * @return array<int, array{kind:string,id:int,name:string,slug:string,image:?string}>
     */
    public static function homeSlides(iterable $parents, array $config): array
    {
        $parentRows = [];
        $subRows = [];
        foreach ($parents as $parent) {
            $row = self::record($parent);
            if (! self::isActiveCategory($row)) {
                continue;
            }
            $subs = self::childRecords($parent, $row);
            unset($row['subcategories'], $row['subcategory'], $row['sub_category']);
            $parentRows[] = $row;
            foreach ($subs as $sub) {
                $subRow = self::record($sub);
                if (! self::isActiveSubcategory($subRow)) {
                    continue;
                }
                $subRow['cat_id'] = $subRow['cat_id'] ?? $row['cat_id'];
                $subRows[] = $subRow;
            }
        }

        $resolved = self::resolve($parentRows, $subRows, $config);
        $parentOn = array_flip($resolved['category_ids']);
        $subOn = array_flip($resolved['subcategory_ids']);
        $slides = [];
        $shownParents = [];

        foreach ($parentRows as $row) {
            $id = (int) ($row['cat_id'] ?? 0);
            if ($id <= 0 || ! isset($parentOn[$id])) {
                continue;
            }
            $slides[] = self::slide('category', $id, (string) ($row['category_name'] ?? ''), (string) ($row['category_slug'] ?? ''), self::imageFrom($row));
            $shownParents[$id] = true;
        }

        foreach ($subRows as $row) {
            $id = (int) ($row['subcat_id'] ?? 0);
            $parentId = (int) ($row['cat_id'] ?? 0);
            if ($id <= 0 || ! isset($subOn[$id]) || isset($shownParents[$parentId])) {
                continue;
            }
            $slides[] = self::slide('subcategory', $id, (string) ($row['subcategory_name'] ?? ''), (string) ($row['subcategory_slug'] ?? ''), self::imageFrom($row));
        }

        return $slides;
    }

    /**
     * Parent ids and lone subcategory ids represented by home slides.
     *
     * @param  array<int, array{kind?:string,id?:int}>  $slides
     * @return array{parents:array<int,int>,subcategories:array<int,int>}
     */
    public static function slideBuckets(array $slides): array
    {
        $parents = [];
        $subs = [];
        foreach ($slides as $slide) {
            $id = (int) ($slide['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }
            if (($slide['kind'] ?? '') === 'subcategory') {
                $subs[$id] = $id;
            } else {
                $parents[$id] = $id;
            }
        }

        return ['parents' => array_values($parents), 'subcategories' => array_values($subs)];
    }

    /**
     * An item belongs on the license product row when it sits in a slide
     * parent (including that parent's sub-categories) or in a lone sub-category slide.
     *
     * @param  object|array  $item
     * @param  array<int, array{kind?:string,id?:int}>  $slides
     */
    public static function itemInSlides($item, array $slides): bool
    {
        $buckets = self::slideBuckets($slides);
        $parents = array_flip($buckets['parents']);
        $subs = array_flip($buckets['subcategories']);
        $row = self::record($item);
        $parent = (int) ($row['item_category_parent'] ?? 0);
        $category = (int) ($row['item_category'] ?? 0);
        $type = (string) ($row['item_category_type'] ?? '');
        if ($parent > 0 && isset($parents[$parent])) {
            return true;
        }
        if ($type === 'category' && isset($parents[$category])) {
            return true;
        }
        if ($type === 'subcategory' && isset($subs[$category])) {
            return true;
        }

        return false;
    }

    /**
     * @param  object|array  $value
     * @return array<string, mixed>
     */
    private static function record($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (! is_object($value)) {
            return [];
        }
        // Eloquent keeps columns in a protected $attributes array. get_object_vars()
        // from here only sees public flags, and isset() is false for null columns
        // such as single_price, so a new "VPN Licenses" row would lose its name.
        if (method_exists($value, 'getAttributes')) {
            $row = $value->getAttributes();
        } else {
            $row = get_object_vars($value);
        }
        foreach (['category_image', 'image', 'category_icon', 'subcategory_image', 'single_price', 'cat_id', 'subcat_id', 'category_name', 'category_slug', 'category_status', 'drop_status', 'subcategory_name', 'subcategory_slug', 'subcategory_status', 'item_category', 'item_category_parent', 'item_category_type'] as $field) {
            if (! array_key_exists($field, $row) && isset($value->{$field})) {
                $row[$field] = $value->{$field};
            }
        }

        return $row;
    }

    /**
     * @param  object|array  $parent
     * @param  array<string, mixed>  $row
     * @return array<int, object|array>
     */
    private static function childRecords($parent, array $row): array
    {
        if (is_object($parent) && method_exists($parent, 'SubCategory')) {
            $children = $parent->subcategory;
            if ($children instanceof \Traversable) {
                return iterator_to_array($children);
            }
            if (is_array($children)) {
                return $children;
            }
        }
        foreach (['subcategories', 'subcategory', 'sub_category'] as $key) {
            if (isset($row[$key]) && is_iterable($row[$key])) {
                return is_array($row[$key]) ? $row[$key] : iterator_to_array($row[$key]);
            }
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private static function isActiveCategory(array $row): bool
    {
        $status = (string) ($row['category_status'] ?? '1');
        $drop = (string) ($row['drop_status'] ?? 'no');

        return $status !== '0' && $drop === 'no' && (int) ($row['cat_id'] ?? 0) > 0;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private static function isActiveSubcategory(array $row): bool
    {
        $status = (string) ($row['subcategory_status'] ?? '1');
        $drop = (string) ($row['drop_status'] ?? 'no');

        return $status !== '0' && $drop === 'no' && (int) ($row['subcat_id'] ?? 0) > 0;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private static function imageFrom(array $row): ?string
    {
        foreach (['category_image', 'subcategory_image', 'image', 'category_icon'] as $field) {
            $value = trim((string) ($row[$field] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @return array{kind:string,id:int,name:string,slug:string,image:?string}
     */
    private static function slide(string $kind, int $id, string $name, string $slug, ?string $image): array
    {
        return [
            'kind' => $kind,
            'id' => $id,
            'name' => $name,
            'slug' => $slug,
            'image' => $image,
        ];
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
