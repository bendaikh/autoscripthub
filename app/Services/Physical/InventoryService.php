<?php

namespace Fickrr\Services\Physical;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventoryService
{
    public function tracksItem(object $item): bool
    {
        if (!Schema::hasColumn('items', 'track_inventory')) {
            return true;
        }

        return (int) ($item->track_inventory ?? 1) === 1;
    }

    public function tracksLanding(object $page): bool
    {
        if (!Schema::hasColumn('landing_pages', 'lp_track_inventory')) {
            return true;
        }

        return (int) ($page->lp_track_inventory ?? 1) === 1;
    }

    public function availableForItem(int $itemId, ?int $variantId, bool $track): int
    {
        if (!$track) {
            return PHP_INT_MAX;
        }

        $onHand = $this->onHand('items', 'item_id', $itemId, 'item_variants', 'item_id', $variantId, 'stock_quantity');
        $reserved = $this->reserved(function ($query) use ($itemId, $variantId) {
            if ($variantId) {
                $query->where('i.variant_id', $variantId);
            } else {
                $query->where('i.item_id', $itemId)->whereNull('i.variant_id');
            }
        });

        return max(0, $onHand - $reserved);
    }

    public function availableForLanding(int $lpId, ?int $variantId, bool $track): int
    {
        if (!$track) {
            return PHP_INT_MAX;
        }

        $onHand = $variantId
            ? $this->variantOnHand($variantId)
            : $this->columnOnHand('landing_pages', 'lp_id', $lpId, 'lp_stock_quantity');

        $reserved = $this->reserved(function ($query) use ($lpId, $variantId) {
            if ($variantId) {
                $query->where('i.variant_id', $variantId);
            } else {
                $query->where('i.lp_id', $lpId)->whereNull('i.variant_id');
            }
        });

        return max(0, $onHand - $reserved);
    }

    public function commitItem(int $itemId, ?int $variantId, int $qty, bool $track): void
    {
        if (!$track || $qty < 1) {
            return;
        }

        if ($variantId) {
            $this->decrementColumn('item_variants', 'id', $variantId, 'stock_quantity', $qty);
            return;
        }

        $this->decrementColumn('items', 'item_id', $itemId, 'stock_quantity', $qty);
    }

    public function releaseItem(int $itemId, ?int $variantId, int $qty, bool $track): void
    {
        if (!$track || $qty < 1) {
            return;
        }

        if ($variantId) {
            $this->incrementColumn('item_variants', 'id', $variantId, 'stock_quantity', $qty);
            return;
        }

        $this->incrementColumn('items', 'item_id', $itemId, 'stock_quantity', $qty);
    }

    public function commitLanding(int $lpId, ?int $variantId, int $qty, bool $track): void
    {
        if (!$track || $qty < 1) {
            return;
        }

        if ($variantId) {
            $this->decrementColumn('item_variants', 'id', $variantId, 'stock_quantity', $qty);
            return;
        }

        $this->decrementColumn('landing_pages', 'lp_id', $lpId, 'lp_stock_quantity', $qty);
    }

    public function releaseLanding(int $lpId, ?int $variantId, int $qty, bool $track): void
    {
        if (!$track || $qty < 1) {
            return;
        }

        if ($variantId) {
            $this->incrementColumn('item_variants', 'id', $variantId, 'stock_quantity', $qty);
            return;
        }

        $this->incrementColumn('landing_pages', 'lp_id', $lpId, 'lp_stock_quantity', $qty);
    }

    private function onHand(string $parent, string $parentKey, int $parentId, string $variantTable, string $variantParentKey, ?int $variantId, string $column): int
    {
        if ($variantId) {
            return $this->variantOnHand($variantId);
        }

        return $this->columnOnHand($parent, $parentKey, $parentId, $column);
    }

    private function variantOnHand(int $variantId): int
    {
        return $this->columnOnHand('item_variants', 'id', $variantId, 'stock_quantity');
    }

    private function columnOnHand(string $table, string $key, int $id, string $column): int
    {
        $row = DB::table($table)->where($key, $id)->first();
        if (!$row || !isset($row->{$column})) {
            return 0;
        }

        return (int) $row->{$column};
    }

    private function reserved(callable $constrain): int
    {
        if (!Schema::hasTable('physical_orders') || !Schema::hasTable('physical_order_items')) {
            return 0;
        }

        $query = DB::table('physical_order_items as i')
            ->join('physical_orders as o', 'o.id', '=', 'i.physical_order_id')
            ->where('o.payment_status', 'paid')
            ->where('o.fulfillment_status', 'pending')
            ->where('o.stock_committed', 0);

        $constrain($query);

        return (int) $query->sum('i.quantity');
    }

    private function decrementColumn(string $table, string $key, int $id, string $column, int $qty): void
    {
        DB::transaction(function () use ($table, $key, $id, $column, $qty) {
            $row = DB::table($table)->where($key, $id)->lockForUpdate()->first();
            if (!$row) {
                throw new InsufficientStockException('The product is no longer available.');
            }

            $affected = DB::table($table)
                ->where($key, $id)
                ->where($column, '>=', $qty)
                ->decrement($column, $qty);

            if ($affected !== 1) {
                throw new InsufficientStockException('Not enough stock to confirm this order.');
            }
        });
    }

    private function incrementColumn(string $table, string $key, int $id, string $column, int $qty): void
    {
        DB::table($table)->where($key, $id)->increment($column, $qty);
    }
}
