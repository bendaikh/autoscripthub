<?php

namespace Fickrr\Support;

/**
 * Related products on an item page stay inside that item's category.
 * Same subcategory first, then the parent category, and nothing else.
 */
class RelatedItems
{
    public const LIMIT = 4;

    /**
     * @param  object|array  $current
     * @param  iterable<int, object|array>  $candidates
     * @return array<int, object|array>
     */
    public static function choose($current, iterable $candidates, int $limit = self::LIMIT): array
    {
        $limit = max(0, $limit);
        $currentId = self::id($current);
        $categoryId = self::text(self::value($current, 'item_category'));
        $categoryType = self::type($current);
        $parentId = self::text(self::value($current, 'item_category_parent'));

        $same = [];
        $parent = [];
        $seen = [];
        foreach ($candidates as $row) {
            $rowId = self::id($row);
            if (! self::isListable($row) || $rowId === $currentId || isset($seen[$rowId])) {
                continue;
            }
            $seen[$rowId] = true;
            if ($categoryId !== '' && self::text(self::value($row, 'item_category')) === $categoryId && self::type($row) === $categoryType) {
                $same[] = $row;
                continue;
            }
            if ($parentId !== '' && $parentId !== '0' && self::text(self::value($row, 'item_category_parent')) === $parentId) {
                $parent[] = $row;
            }
        }

        self::newestFirst($same);
        self::newestFirst($parent);

        $picked = array_slice($same, 0, $limit);
        if (count($picked) < $limit) {
            foreach ($parent as $row) {
                if (count($picked) >= $limit) {
                    break;
                }
                $picked[] = $row;
            }
        }

        return $picked;
    }

    /**
     * @param  object|array  $row
     */
    private static function isListable($row): bool
    {
        return (string) self::value($row, 'item_status') === '1'
            && (string) self::value($row, 'drop_status') === 'no';
    }

    /**
     * @param  object|array  $row
     */
    private static function id($row): int
    {
        return (int) self::value($row, 'item_id');
    }

    /**
     * @param  object|array  $row
     */
    private static function type($row): string
    {
        return strtolower(trim((string) self::value($row, 'item_category_type')));
    }

    /**
     * @param  object|array  $row
     * @return mixed
     */
    private static function value($row, string $field)
    {
        if (is_array($row)) {
            return $row[$field] ?? null;
        }
        if (is_object($row)) {
            return $row->{$field} ?? null;
        }

        return null;
    }

    private static function text($value): string
    {
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }

    /**
     * @param  array<int, object|array>  $rows
     */
    private static function newestFirst(array &$rows): void
    {
        usort($rows, function ($left, $right) {
            return self::id($right) <=> self::id($left);
        });
    }
}
