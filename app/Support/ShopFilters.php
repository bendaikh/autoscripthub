<?php

namespace Fickrr\Support;

/**
 * Parses and applies the shop sidebar filters (POST /shop).
 *
 * Every value from the request is checked against a strict format before it
 * reaches the query, and the query only ever receives bound parameters, so a
 * crafted item_type[] / category_names[] value cannot change the SQL.
 */
class ShopFilters
{
    /**
     * item_type[] values are item_type_slug values such as "scripts" or
     * "graphics-templates".
     *
     * @return array{0: string[], 1: bool} [valid slugs, whether the filter was submitted]
     */
    public static function itemTypes($input): array
    {
        $values = self::strings($input);
        $types = [];
        foreach ($values as $value) {
            if (preg_match('/^[A-Za-z0-9_-]{1,100}$/', $value)) {
                $types[$value] = $value;
            }
        }

        return [array_values($types), $values !== []];
    }

    /**
     * category_names[] values are "category_<id>" or "subcategory_<id>".
     *
     * @return array{categories: int[], subcategories: string[], submitted: bool}
     *         categories:    parent category ids (matched against items.item_category_parent)
     *         subcategories: "subcategory_<id>" names (matched against items.item_type_cat_id)
     */
    public static function categories($input): array
    {
        $values = self::strings($input);
        $categories = [];
        $subcategories = [];
        foreach ($values as $value) {
            if (!preg_match('/^(category|subcategory)_([1-9][0-9]{0,9})$/', $value, $m)) {
                continue;
            }
            if ($m[1] === 'category') {
                $categories[(int) $m[2]] = (int) $m[2];
            } else {
                $subcategories[$value] = $value;
            }
        }

        return [
            'categories' => array_values($categories),
            'subcategories' => array_values($subcategories),
            'submitted' => $values !== [],
        ];
    }

    /** "asc" or "desc"; anything else falls back to "desc" (the old default). */
    public static function direction($input): string
    {
        $dir = is_string($input) ? strtolower(trim($input)) : '';

        return in_array($dir, ['asc', 'desc'], true) ? $dir : 'desc';
    }

    /**
     * Adds the type and category conditions to $query (an Eloquent or query
     * builder) using whereIn, i.e. bound parameters only.
     *
     * A filter that was submitted but contains no valid value matches nothing,
     * like the old FIND_IN_SET against an unknown value did.
     */
    public static function apply($query, array $types, bool $typesSubmitted, array $cats): void
    {
        if ($typesSubmitted) {
            if ($types === []) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('items.item_type', $types);
            }
        }

        if ($cats['submitted']) {
            if ($cats['categories'] === [] && $cats['subcategories'] === []) {
                $query->whereRaw('1 = 0');
            } else {
                // A ticked category shows everything under it; a ticked
                // subcategory shows the items filed in that subcategory.
                $query->where(function ($q) use ($cats) {
                    if ($cats['categories'] !== []) {
                        $q->orWhereIn('items.item_category_parent', $cats['categories']);
                    }
                    if ($cats['subcategories'] !== []) {
                        $q->orWhereIn('items.item_type_cat_id', $cats['subcategories']);
                    }
                });
            }
        }
    }

    /** @return string[] non-empty trimmed strings; nested arrays and other types are dropped */
    private static function strings($input): array
    {
        if ($input === null || $input === '') {
            return [];
        }
        $out = [];
        foreach ((array) $input as $value) {
            if (is_string($value) || is_int($value)) {
                $value = trim((string) $value);
                if ($value !== '') {
                    $out[] = $value;
                }
            }
        }

        return $out;
    }
}
