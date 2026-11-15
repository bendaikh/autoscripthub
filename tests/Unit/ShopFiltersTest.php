<?php

namespace Tests\Unit;

use Fickrr\Support\ShopFilters;
use Illuminate\Database\Capsule\Manager as Capsule;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2).'/app/Support/ShopFilters.php';

class ShopFiltersTest extends TestCase
{
    public function test_item_types_keep_valid_slugs_and_drop_injection(): void
    {
        $this->assertSame([['scripts', 'graphics-templates'], true], ShopFilters::itemTypes(['scripts', 'graphics-templates', 'scripts']));
        $this->assertSame([[], true], ShopFilters::itemTypes(['x") OR 1=1 -- ', "a'b", ['nested']]));
        $this->assertSame([['ok'], true], ShopFilters::itemTypes(['ok', 'bad"),SLEEP(5)#']));
        $this->assertSame([[], false], ShopFilters::itemTypes(null));
        $this->assertSame([[], false], ShopFilters::itemTypes(['', '  ']));
    }

    public function test_categories_split_into_parent_ids_and_subcategory_names(): void
    {
        $this->assertSame(
            ['categories' => [3, 9], 'subcategories' => ['subcategory_21'], 'submitted' => true],
            ShopFilters::categories(['category_3', 'subcategory_21', 'category_9', 'category_3'])
        );
        $this->assertSame(
            ['categories' => [], 'subcategories' => [], 'submitted' => true],
            ShopFilters::categories(['category_1") OR 1=1#', 'subcategory_x', 'category_0', 'foo_5', 'category_'])
        );
        $this->assertSame(['categories' => [], 'subcategories' => [], 'submitted' => false], ShopFilters::categories(['']));
    }

    public function test_direction_only_allows_asc_or_desc(): void
    {
        $this->assertSame('asc', ShopFilters::direction('ASC'));
        $this->assertSame('desc', ShopFilters::direction('desc'));
        $this->assertSame('desc', ShopFilters::direction('desc; DROP TABLE items'));
        $this->assertSame('desc', ShopFilters::direction(null));
    }

    public function test_filters_select_the_expected_items(): void
    {
        if (!class_exists(Capsule::class) || !extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('illuminate/database or pdo_sqlite not available');
        }

        $db = new Capsule();
        $db->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $schema = $db->getConnection()->getSchemaBuilder();
        $schema->create('items', function ($t) {
            $t->integer('item_id');
            $t->string('item_type');
            $t->integer('item_category_parent');
            $t->string('item_type_cat_id');
        });
        $db->getConnection()->table('items')->insert([
            ['item_id' => 1, 'item_type' => 'scripts', 'item_category_parent' => 3, 'item_type_cat_id' => 'category_3'],
            ['item_id' => 2, 'item_type' => 'scripts', 'item_category_parent' => 3, 'item_type_cat_id' => 'subcategory_21'],
            ['item_id' => 3, 'item_type' => 'graphics', 'item_category_parent' => 9, 'item_type_cat_id' => 'subcategory_40'],
            ['item_id' => 4, 'item_type' => 'plugins', 'item_category_parent' => 9, 'item_type_cat_id' => 'category_9'],
        ]);

        $run = function ($type, $cats) use ($db) {
            [$types, $typesSubmitted] = ShopFilters::itemTypes($type);
            $q = $db->getConnection()->table('items');
            ShopFilters::apply($q, $types, $typesSubmitted, ShopFilters::categories($cats));

            return $q->orderBy('item_id')->pluck('item_id')->all();
        };

        $this->assertSame([1, 2, 3, 4], $run(null, null));
        $this->assertSame([1, 2, 3], $run(['scripts', 'graphics'], null));
        $this->assertSame([3, 4], $run(null, ['category_9']));
        $this->assertSame([2, 3], $run(null, ['subcategory_21', 'subcategory_40']));
        $this->assertSame([2, 3, 4], $run(null, ['subcategory_21', 'category_9']));
        $this->assertSame([3], $run(['graphics'], ['category_9']));
        // tampered input never widens the result set
        $this->assertSame([], $run(['x") OR "1"="1'], null));
        $this->assertSame([], $run(null, ['category_1") OR 1=1 -- ']));
        $this->assertSame([1, 2], $run(['scripts', 'x") OR 1=1#'], null));
    }
}
