<?php

/*
 * Single-price categories have one price and no Regular / Extended choice.
 *
 * The admin toggle (category.single_price / subcategory.single_price) is the
 * main switch. A parent that is on covers every sub-category. A sub-category
 * can also be switched on by itself.
 *
 * names and slugs are an exact fallback for license parents, including ones
 * created after the backfill migration. Matching ignores case and surrounding
 * spaces. It does not match a longer or different name.
 *
 * Set OS_LICENSE_CATEGORY_IDS (comma-separated parent cat_id values) to force
 * extra parents without renaming them.
 */

use Fickrr\Support\OsLicenseCatalog;

$rawIds = (string) env('OS_LICENSE_CATEGORY_IDS', '');
$ids = [];
foreach (preg_split('/\s*,\s*/', $rawIds, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $id) {
    $id = (int) $id;
    if ($id > 0) {
        $ids[] = $id;
    }
}

return [
    'slugs' => OsLicenseCatalog::FALLBACK_PARENT_SLUGS,
    'names' => OsLicenseCatalog::FALLBACK_PARENT_NAMES,
    'ids' => $ids,
    // Upload/edit attribute labels that are optional only for these categories.
    'optional_attribute_labels' => [
        'Games',
    ],
];
