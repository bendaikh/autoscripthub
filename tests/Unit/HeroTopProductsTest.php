<?php

namespace Tests\Unit;

use Fickrr\Support\HeroTopProducts;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2).'/app/Support/HeroTopProducts.php';

class HeroTopProductsTest extends TestCase
{
    public function test_slides_mix_categories_and_item_types_without_duplicates(): void
    {
        $categories = [
            ['id' => 22, 'name' => 'Games', 'slug' => 'games'],
            ['id' => 26, 'name' => 'AI Licenses', 'slug' => 'ai-licenses'],
            ['id' => 19, 'name' => 'Office Licenses', 'slug' => 'office'],
            ['id' => 4, 'name' => 'Graphics & Design', 'slug' => 'graphics'],
            ['id' => 1, 'name' => 'Scripts', 'slug' => 'scripts'],
        ];
        $types = [
            ['name' => 'Scripts', 'slug' => 'scripts'],
            ['name' => 'Themes', 'slug' => 'themes'],
            ['name' => 'Apps', 'slug' => 'apps'],
            ['name' => 'AI', 'slug' => 'ai'],
        ];

        $slides = HeroTopProducts::slides($categories, $types, [
            22 => 40, 26 => 12, 19 => 30, 4 => 8, 1 => 5,
        ], [
            'scripts' => 90, 'themes' => 18, 'apps' => 7, 'ai' => 3,
        ]);

        $names = [];
        foreach ($slides as $slide) {
            foreach ($slide as $row) {
                $names[] = $row['name'];
            }
        }

        $this->assertSame(['Games', 'Office Licenses', 'Themes', 'AI Licenses'], $slides[0] ? array_column($slides[0], 'name') : []);
        $this->assertContains('Themes', $names);
        $this->assertContains('Apps', $names);
        $this->assertNotContains('AI', $names);
        $this->assertSame(1, array_count_values($names)['Scripts']);
        $this->assertLessThanOrEqual(4, count($slides[0]));
    }

    public function test_empty_groups_are_omitted_and_the_rest_follow_count(): void
    {
        $slides = HeroTopProducts::slides([
            ['id' => 26, 'name' => 'AI Licenses', 'slug' => 'ai-licenses'],
            ['id' => 22, 'name' => 'Games', 'slug' => 'games'],
            ['id' => 19, 'name' => 'Office Licenses', 'slug' => 'office'],
            ['id' => 4, 'name' => 'Graphics & Design', 'slug' => 'graphics'],
            ['id' => 8, 'name' => 'Print', 'slug' => 'print'],
        ], [
            ['name' => 'Plugins', 'slug' => 'plugins'],
            ['name' => 'Themes', 'slug' => 'themes'],
        ], [
            26 => 40, 22 => 10, 19 => 25, 4 => 0, 8 => 0,
        ], [
            'plugins' => 0, 'themes' => 0,
        ]);

        $names = [];
        foreach ($slides as $slide) {
            foreach ($slide as $row) {
                $names[] = $row['name'];
                $this->assertGreaterThan(0, $row['count']);
            }
        }

        $this->assertSame(['AI Licenses', 'Office Licenses', 'Games'], $names);
    }

    public function test_known_names_get_distinct_icons(): void
    {
        $this->assertSame('fa-gamepad', HeroTopProducts::icon('Games')['icon']);
        $this->assertSame('fa-magic', HeroTopProducts::icon('AI Tools')['icon']);
        $this->assertSame('fa-file-text-o', HeroTopProducts::icon('Office')['icon']);
        $this->assertSame('fa-desktop', HeroTopProducts::icon('Operating Systems')['icon']);
        $this->assertSame('fa-shield', HeroTopProducts::icon('Antivirus')['icon']);
        $this->assertSame('fa-lock', HeroTopProducts::icon('VPN')['icon']);
        $this->assertSame('fa-picture-o', HeroTopProducts::icon('Graphics & Design')['icon']);
        $this->assertSame('fa-code', HeroTopProducts::icon('Scripts')['icon']);
        $this->assertSame('fa-building', HeroTopProducts::icon('Corporate Software')['icon']);
        $this->assertNotSame('fa-magic', HeroTopProducts::icon('Email Marketing')['icon']);
    }
}
