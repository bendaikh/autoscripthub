<?php

namespace Fickrr\Services\Physical;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PhysicalCatalog
{
    /**
     * Published catalog products a landing page may use.
     * Approval and "not deleted" are the only gates. File type, featured flag,
     * vendor, and a digital download file are not.
     */
    public static function publishedProducts()
    {
        if (!Schema::hasTable('items')) {
            return collect();
        }

        return DB::table('items')
            ->where('drop_status', 'no')
            ->where('item_status', 1)
            ->orderBy('item_name')
            ->get();
    }

    /**
     * When a landing page is tied to a catalog product, sell that product's
     * current price and stock instead of a separate copy.
     */
    public static function applyCatalogItem(object $page): object
    {
        $id = (int) ($page->lp_product_id ?? 0);
        if ($id < 1 || !Schema::hasTable('items')) {
            return $page;
        }

        $item = self::publishedProducts()->first(function ($row) use ($id) {
            return (int) $row->item_id === $id;
        });
        if (!$item) {
            return $page;
        }

        $page->lp_price = $item->regular_price;
        if (isset($item->sku)) {
            $page->lp_sku = $item->sku;
        }
        if (isset($item->stock_quantity)) {
            $page->lp_stock_quantity = $item->stock_quantity;
        }
        if (isset($item->shipping_fee)) {
            $page->lp_shipping_fee = $item->shipping_fee;
        }
        if (isset($item->weight_grams)) {
            $page->lp_weight_grams = $item->weight_grams;
        }
        if (isset($item->track_inventory)) {
            $page->lp_track_inventory = $item->track_inventory;
        }
        $page->catalog_item_id = (int) $item->item_id;

        return $page;
    }

    public static function applyItemRequest(?string $itemToken, Request $request): void
    {
        if (!\Fickrr\Support\Store::isPhysical() || !$request->boolean('physical_fields_present')) {
            return;
        }

        if ($itemToken === null || $itemToken === '' || !Schema::hasTable('items')) {
            return;
        }

        $item = DB::table('items')->where('item_token', $itemToken)->first();
        if (!$item) {
            return;
        }

        $update = array_filter([
            'sku' => self::nullableString($request->input('sku')),
            'stock_quantity' => $request->has('stock_quantity') ? max(0, (int) $request->input('stock_quantity')) : null,
            'weight_grams' => $request->filled('weight_grams') ? max(0, (int) $request->input('weight_grams')) : null,
            'shipping_fee' => $request->has('shipping_fee') ? round((float) $request->input('shipping_fee'), 2) : null,
            'requires_shipping' => 1,
            'track_inventory' => $request->boolean('track_inventory') ? 1 : 0,
            'product_type' => 'physical',
            'free_download' => 0,
            'subscription_item' => 0,
            'extended_price' => 0,
            'item_support' => 0,
        ], function ($value) {
            return $value !== null;
        });

        self::updateExisting('items', 'item_id', (int) $item->item_id, $update);
        self::syncVariants((int) $item->item_id, null, $request);
    }

    public static function syncLanding(int $lpId, Request $request): void
    {
        if (!\Fickrr\Support\Store::isPhysical() || !Schema::hasTable('landing_pages')) {
            return;
        }

        $update = [
            'lp_product_kind' => 'physical',
            'lp_delivery_method' => 'shipping',
            'lp_product_file' => null,
            'lp_product_link' => null,
            'lp_extended_price' => 0,
            'lp_sku' => self::nullableString($request->input('lp_sku')),
            'lp_stock_quantity' => max(0, (int) $request->input('lp_stock_quantity', 0)),
            'lp_weight_grams' => $request->filled('lp_weight_grams') ? max(0, (int) $request->input('lp_weight_grams')) : null,
            'lp_shipping_fee' => round((float) $request->input('lp_shipping_fee', 0), 2),
            'lp_track_inventory' => $request->has('lp_track_inventory') ? ($request->boolean('lp_track_inventory') ? 1 : 0) : 1,
        ];

        self::updateExisting('landing_pages', 'lp_id', $lpId, $update);
        self::syncVariants(null, $lpId, $request);
    }

    private static function syncVariants(?int $itemId, ?int $lpId, Request $request): void
    {
        if (!Schema::hasTable('item_variants')) {
            return;
        }

        $query = DB::table('item_variants');
        if ($itemId) {
            $query->where('item_id', $itemId);
        } else {
            $query->where('lp_id', $lpId);
        }
        $query->delete();

        $names = (array) $request->input('variant_name', []);
        $skus = (array) $request->input('variant_sku', []);
        $prices = (array) $request->input('variant_price', []);
        $stocks = (array) $request->input('variant_stock', []);
        $weights = (array) $request->input('variant_weight', []);

        foreach ($names as $index => $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }

            DB::table('item_variants')->insert([
                'item_id' => $itemId,
                'lp_id' => $lpId,
                'sku' => self::nullableString($skus[$index] ?? null),
                'name' => $name,
                'price' => ($prices[$index] ?? '') === '' ? null : round((float) $prices[$index], 2),
                'stock_quantity' => max(0, (int) ($stocks[$index] ?? 0)),
                'weight_grams' => ($weights[$index] ?? '') === '' ? null : max(0, (int) $weights[$index]),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private static function updateExisting(string $table, string $key, int $id, array $data): void
    {
        $columns = array_flip(Schema::getColumnListing($table));
        $data = array_intersect_key($data, $columns);
        if ($data === []) {
            return;
        }

        DB::table($table)->where($key, $id)->update($data);
    }

    private static function nullableString($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
