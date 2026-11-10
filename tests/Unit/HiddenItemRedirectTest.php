<?php

namespace Tests\Unit;

use Fickrr\Support\HiddenItemRedirect;
use Fickrr\Support\ProductImage;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2).'/app/Support/HiddenItemRedirect.php';
require_once dirname(__DIR__, 2).'/app/Support/ProductImage.php';

class HiddenItemRedirectTest extends TestCase
{
    public function test_disabled_or_dropped_items_go_to_the_parent_category(): void
    {
        $disabled = (object) [
            'item_status' => 0,
            'drop_status' => 'no',
            'item_category_parent' => 22,
            'item_category_type' => 'subcategory',
            'item_category' => 67,
        ];

        $this->assertTrue(HiddenItemRedirect::isHidden($disabled));
        $this->assertSame(22, HiddenItemRedirect::parentId($disabled));
        $this->assertSame('/shop/category/games', HiddenItemRedirect::path((object) ['category_slug' => 'games']));
        $this->assertSame('/shop', HiddenItemRedirect::path(null));
        $this->assertFalse(HiddenItemRedirect::isHidden((object) ['item_status' => 1, 'drop_status' => 'no']));
    }

    public function test_gallery_rows_without_a_file_are_skipped(): void
    {
        $rows = [
            (object) ['item_image' => 'present.jpg'],
            (object) ['item_image' => 'missing.jpg'],
            (object) ['item_image' => ''],
        ];

        $kept = ProductImage::keepExisting($rows, function (string $name) {
            return $name === 'present.jpg';
        });

        $this->assertCount(1, $kept);
        $this->assertSame('present.jpg', $kept[0]->item_image);
    }
}
