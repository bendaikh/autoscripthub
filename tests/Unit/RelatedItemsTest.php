<?php

namespace Tests\Unit;

use Fickrr\Support\RelatedItems;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2).'/app/Support/RelatedItems.php';

class RelatedItemsTest extends TestCase
{
    public function test_same_subcategory_comes_first_then_the_parent_category(): void
    {
        $current = $this->item(166, 21, 'subcategory', 9);
        $candidates = [
            $this->item(10, 21, 'subcategory', 9, 'Windows 11 Home'),
            $this->item(30, 40, 'subcategory', 15, 'Steam key'),
            $this->item(12, 22, 'subcategory', 9, 'Windows 10'),
            $this->item(11, 21, 'subcategory', 9, 'Windows 11 Pro OEM'),
            $this->item(166, 21, 'subcategory', 9, 'Current'),
            $this->item(13, 9, 'category', 9, 'Operating System Licenses'),
            $this->item(14, 21, 'subcategory', 9, 'Deleted', 'no', 1),
        ];
        $candidates[6] = $this->item(14, 21, 'subcategory', 9, 'Deleted', 'yes', 1);
        $candidates[] = $this->item(15, 21, 'subcategory', 9, 'Unapproved', 'no', 0);

        $picked = RelatedItems::choose($current, $candidates, 4);
        $names = array_map(function ($row) {
            return $row->item_name;
        }, $picked);

        $this->assertSame(
            ['Windows 11 Pro OEM', 'Windows 11 Home', 'Operating System Licenses', 'Windows 10'],
            $names
        );
    }

    public function test_a_full_subcategory_does_not_pull_in_siblings(): void
    {
        $current = $this->item(1, 21, 'subcategory', 9);
        $candidates = [];
        foreach ([8, 7, 6, 5, 4] as $id) {
            $candidates[] = $this->item($id, 21, 'subcategory', 9, 'same-'.$id);
        }
        $candidates[] = $this->item(99, 22, 'subcategory', 9, 'sibling');

        $picked = RelatedItems::choose($current, $candidates, 4);

        $this->assertSame([8, 7, 6, 5], array_map(function ($row) {
            return $row->item_id;
        }, $picked));
    }

    public function test_other_parents_are_never_shown_and_an_empty_match_stays_empty(): void
    {
        $current = $this->item(1, 21, 'subcategory', 9);
        $picked = RelatedItems::choose($current, [
            $this->item(2, 40, 'subcategory', 15, 'Other'),
            $this->item(3, 21, 'category', 15, 'Id collision'),
        ], 4);

        $this->assertSame([], $picked);

        $top = $this->item(50, 9, 'category', 9, 'Parent category');
        $fromParent = RelatedItems::choose($top, [
            $this->item(60, 9, 'category', 9, 'Also the parent'),
            $this->item(61, 22, 'subcategory', 9, 'Child'),
            $this->item(62, 40, 'subcategory', 15, 'Elsewhere'),
        ], 4);

        $this->assertSame(['Also the parent', 'Child'], array_map(function ($row) {
            return $row->item_name;
        }, $fromParent));
    }

    private function item($id, $category, $type, $parent, $name = '', $drop = 'no', $status = 1)
    {
        return (object) [
            'item_id' => $id,
            'item_name' => $name,
            'item_category' => $category,
            'item_category_type' => $type,
            'item_category_parent' => $parent,
            'drop_status' => $drop,
            'item_status' => $status,
        ];
    }
}
