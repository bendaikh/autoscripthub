<?php

namespace Tests\Unit;

use Fickrr\Support\PopularMix;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2).'/app/Support/PopularMix.php';

class PopularMixTest extends TestCase
{
    public function test_round_robin_mixes_ai_games_and_office(): void
    {
        $items = [
            ['item_name' => 'Office 2019', 'item_category_parent' => 19],
            ['item_name' => 'Office 365', 'item_category_parent' => 19],
            ['item_name' => 'Football Manager', 'item_category_parent' => 22],
            ['item_name' => 'ChatGPT Plus', 'item_category_parent' => 26],
            ['item_name' => 'Google Gemini Pro', 'item_category_parent' => 8],
            ['item_name' => 'Windows key sitting in scripts', 'item_category_parent' => 8],
            ['item_name' => 'Corporate Office 2016', 'item_category_parent' => 24],
        ];

        $mixed = PopularMix::interleave($items, 6);

        $this->assertSame(
            ['ChatGPT Plus', 'Football Manager', 'Office 2019', 'Google Gemini Pro', 'Office 365', 'Corporate Office 2016'],
            array_column($mixed, 'item_name')
        );
    }
}
