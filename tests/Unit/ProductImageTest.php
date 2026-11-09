<?php

namespace Tests\Unit;

use Fickrr\Support\ProductImage;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2).'/app/Support/ProductImage.php';

class ProductImageTest extends TestCase
{
    public function test_large_images_scale_down_and_keep_aspect_ratio(): void
    {
        $this->assertSame([1200, 720], ProductImage::outputSize(2000, 1200));
        $this->assertSame([1200, 675], ProductImage::outputSize(1920, 1080));
    }

    public function test_small_images_are_not_upscaled(): void
    {
        $this->assertSame([80, 80], ProductImage::outputSize(80, 80));
        $this->assertSame([361, 230], ProductImage::outputSize(361, 230));
        $this->assertSame([400, 250], ProductImage::outputSize(400, 250));
        $this->assertSame([1200, 800], ProductImage::outputSize(1200, 800));
    }

    public function test_sharpest_file_wins_and_unknown_sizes_keep_the_first_candidate(): void
    {
        $widths = [
            'preview.jpg' => 361,
            'shot.jpg' => 750,
            'thumb.jpg' => 80,
        ];
        $picked = ProductImage::pickSharpest(
            ['thumb.jpg', 'preview.jpg', 'shot.jpg'],
            function ($name) use ($widths) {
                return $widths[$name];
            }
        );
        $this->assertSame('shot.jpg', $picked);

        $remote = ProductImage::pickSharpest(
            ['preview.jpg', 'shot.jpg'],
            function () {
                return null;
            }
        );
        $this->assertSame('preview.jpg', $remote);

        $this->assertNull(ProductImage::pickSharpest(['', '  '], function () {
            return 100;
        }));
    }
}
