<?php

namespace Tests\Unit;

use Fickrr\Support\HeroShowcase;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2).'/app/Support/HeroTopProducts.php';
require_once dirname(__DIR__, 2).'/app/Support/HeroShowcase.php';

class HeroShowcaseTest extends TestCase
{
    public function test_groups_keep_three_popular_items_and_merge_office(): void
    {
        $shown = HeroShowcase::present([
            ['id' => 22, 'name' => 'Games', 'slug' => 'games'],
            ['id' => 26, 'name' => 'AI Licenses', 'slug' => 'ai-licenses'],
            ['id' => 19, 'name' => 'Office Licenses', 'slug' => 'office'],
            ['id' => 24, 'name' => 'Corporate Software Licenses', 'slug' => 'corporate'],
            ['id' => 7, 'name' => 'Email Marketing', 'slug' => 'email'],
        ], [
            ['name' => 'Scripts', 'slug' => 'scripts'],
        ], [
            $this->item(1, 'Low game', 22, '', 1),
            $this->item(2, 'Top game', 22, '', 50),
            $this->item(3, 'Mid game', 22, '', 20),
            $this->item(4, 'Extra game', 22, '', 5),
            $this->item(5, 'Office box', 19, '', 12),
            $this->item(6, 'Corporate key', 24, '', 30),
            $this->item(7, 'ChatGPT Plus', 8, '', 15, 'chatgpt-plus.png'),
            $this->item(8, 'Laravel script', 3, 'scripts', 9, 'script.png'),
            $this->item(9, 'No art', 22, '', 99, ''),
        ]);

        $byKey = [];
        foreach ($shown['groups'] as $group) {
            $byKey[$group['key']] = $group;
        }

        $this->assertSame(['games', 'ai', 'office', 'scripts'], array_keys($byKey));
        $this->assertSame([2, 3, 4], array_column($byKey['games']['items'], 'id'));
        $this->assertSame([6, 5], array_column($byKey['office']['items'], 'id'));
        $this->assertSame('Office Licenses', $byKey['office']['name']);
        $this->assertSame([7], array_column($byKey['ai']['items'], 'id'));
        $this->assertSame([8], array_column($byKey['scripts']['items'], 'id'));
        $this->assertSame('item-type', $byKey['scripts']['kind']);
        $this->assertNotContains(9, array_column($shown['marquee'], 'id'));
        $this->assertGreaterThanOrEqual(8, count($shown['marquee']));
    }

    private function item(int $id, string $name, int $parent, string $type, int $views, string $preview = 'cover.png'): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'slug' => 'item-'.$id,
            'preview' => $preview,
            'thumb' => $preview === '' ? '' : 'thumb-'.$id.'.png',
            'parent' => $parent,
            'type' => $type,
            'views' => $views,
        ];
    }
}
