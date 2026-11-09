<?php

/*
 * Operating System Licenses sell as one price (no Regular / Extended choice).
 *
 * Parents are resolved from the category table by slug, name, or explicit id.
 * Every subcategory of a matched parent is included, so "Windows 11 License"
 * follows "Operating System Licenses" without hard-coding child ids.
 *
 * Set OS_LICENSE_CATEGORY_IDS (comma-separated parent cat_id values) if the
 * live category name or slug does not match the lists below.
 */

$rawIds = (string) env('OS_LICENSE_CATEGORY_IDS', '');
$ids = [];
foreach (preg_split('/\s*,\s*/', $rawIds, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $id) {
    $id = (int) $id;
    if ($id > 0) {
        $ids[] = $id;
    }
}

return [
    'slugs' => [
        'operating-system-licenses',
        'operating-system-license',
    ],
    'names' => [
        'Operating System Licenses',
        'Operating System License',
    ],
    'ids' => $ids,
    // Upload/edit attribute labels that are optional only for these categories.
    'optional_attribute_labels' => [
        'Games',
    ],
];
