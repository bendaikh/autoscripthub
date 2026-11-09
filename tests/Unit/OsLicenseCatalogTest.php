<?php

namespace Tests\Unit;

use Fickrr\Support\OsLicenseCatalog;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2).'/app/Support/OsLicenseCatalog.php';

class OsLicenseCatalogTest extends TestCase
{
    private function tree(): array
    {
        $categories = [
            ['cat_id' => 4, 'category_slug' => 'scripts', 'category_name' => 'Scripts'],
            ['cat_id' => 9, 'category_slug' => 'operating-system-licenses', 'category_name' => 'Operating System Licenses'],
            ['cat_id' => 15, 'category_slug' => 'game-keys', 'category_name' => 'Game Keys'],
        ];
        $subcategories = [
            ['subcat_id' => 21, 'cat_id' => 9, 'subcategory_slug' => 'windows-11-license', 'subcategory_name' => 'Windows 11 License'],
            ['subcat_id' => 22, 'cat_id' => 9, 'subcategory_slug' => 'windows-10-license', 'subcategory_name' => 'Windows 10 License'],
            ['subcat_id' => 40, 'cat_id' => 15, 'subcategory_slug' => 'steam-keys', 'subcategory_name' => 'Steam Keys'],
            ['subcat_id' => 41, 'cat_id' => 4, 'subcategory_slug' => 'php-scripts', 'subcategory_name' => 'PHP Scripts'],
        ];

        return [$categories, $subcategories];
    }

    private function resolved(array $config = []): array
    {
        [$categories, $subcategories] = $this->tree();
        $config = array_merge([
            'slugs' => ['operating-system-licenses'],
            'names' => ['Operating System Licenses'],
            'ids' => [],
        ], $config);

        return OsLicenseCatalog::resolve($categories, $subcategories, $config);
    }

    public function test_parent_matched_by_slug_includes_every_child(): void
    {
        $resolved = $this->resolved();

        $this->assertSame([9], $resolved['category_ids']);
        $this->assertEqualsCanonicalizing([21, 22], $resolved['subcategory_ids']);
        $this->assertContains('category_9', $resolved['selection_values']);
        $this->assertContains('subcategory_21', $resolved['selection_values']);
        $this->assertNotContains('category_15', $resolved['selection_values']);
        $this->assertNotContains('subcategory_40', $resolved['selection_values']);
    }

    public function test_fallback_names_are_exact_and_cover_the_license_parents(): void
    {
        $names = OsLicenseCatalog::FALLBACK_PARENT_NAMES;
        foreach ([
            'Operating System Licenses',
            '  office licenses ',
            'Antivirus Licenses',
            'Graphics & Design Licenses',
            'Games',
            'vpn licenses',
        ] as $name) {
            $this->assertTrue(OsLicenseCatalog::isFallbackParentName($name, $names), $name);
        }
        $this->assertFalse(OsLicenseCatalog::isFallbackParentName('Operating System License Keys', $names));
        $this->assertFalse(OsLicenseCatalog::isFallbackParentName('Scripts', $names));
        $this->assertFalse(OsLicenseCatalog::isFallbackParentName('Themes', $names));
        $this->assertFalse(OsLicenseCatalog::isFallbackParentName('Game Keys', $names));

        [$categories, $subcategories] = $this->tree();
        $categories[1]['category_slug'] = 'os-keys';
        $categories[1]['category_name'] = '  Operating System Licenses  ';
        $categories[] = ['cat_id' => 16, 'category_slug' => 'office', 'category_name' => 'Office Licenses'];
        $categories[] = ['cat_id' => 17, 'category_slug' => 'themes', 'category_name' => 'Themes'];

        $resolved = OsLicenseCatalog::resolve($categories, $subcategories, [
            'slugs' => [],
            'names' => $names,
            'ids' => [],
        ]);

        $this->assertContains(9, $resolved['category_ids']);
        $this->assertContains(16, $resolved['category_ids']);
        $this->assertNotContains(17, $resolved['category_ids']);
        $this->assertNotContains(4, $resolved['category_ids']);
        $this->assertContains(21, $resolved['subcategory_ids']);
        $this->assertNotContains(41, $resolved['subcategory_ids']);
    }

    public function test_admin_toggle_covers_a_parent_or_one_subcategory(): void
    {
        [$categories, $subcategories] = $this->tree();
        foreach ($categories as &$category) {
            $category['category_slug'] = 'custom-'.$category['cat_id'];
            $category['category_name'] = 'Custom '.$category['cat_id'];
            $category['single_price'] = $category['cat_id'] === 4 ? 1 : 0;
        }
        unset($category);
        $subcategories[] = [
            'subcat_id' => 77,
            'cat_id' => 15,
            'subcategory_slug' => 'one-antivirus',
            'subcategory_name' => 'One Antivirus',
            'single_price' => 1,
        ];

        $resolved = OsLicenseCatalog::resolve($categories, $subcategories, [
            'slugs' => [],
            'names' => [],
            'ids' => [],
        ]);

        $this->assertSame([4], $resolved['category_ids']);
        $this->assertContains(41, $resolved['subcategory_ids']);
        $this->assertContains(77, $resolved['subcategory_ids']);
        $this->assertNotContains(40, $resolved['subcategory_ids']);
        $this->assertNotContains(21, $resolved['subcategory_ids']);

        $scriptChild = (object) [
            'item_category_type' => 'subcategory',
            'item_category' => 41,
            'item_category_parent' => 4,
        ];
        $loneSub = (object) [
            'item_category_type' => 'subcategory',
            'item_category' => 77,
            'item_category_parent' => 15,
        ];
        $steam = (object) [
            'item_category_type' => 'subcategory',
            'item_category' => 40,
            'item_category_parent' => 15,
        ];
        $this->assertTrue(OsLicenseCatalog::matchesItem($scriptChild, $resolved));
        $this->assertTrue(OsLicenseCatalog::matchesItem($loneSub, $resolved));
        $this->assertFalse(OsLicenseCatalog::matchesItem($steam, $resolved));
        $this->assertTrue(OsLicenseCatalog::matchesSelection('subcategory_77', $resolved));
        $this->assertFalse(OsLicenseCatalog::matchesSelection('category_15', $resolved));
    }

    public function test_explicit_category_id_is_included_with_children(): void
    {
        [$categories, $subcategories] = $this->tree();
        $categories[1]['category_slug'] = 'utilities';
        $categories[1]['category_name'] = 'Utilities';

        $resolved = OsLicenseCatalog::resolve($categories, $subcategories, [
            'slugs' => [],
            'names' => [],
            'ids' => [15],
        ]);

        $this->assertSame([15], $resolved['category_ids']);
        $this->assertSame([40], $resolved['subcategory_ids']);
        $this->assertNotContains(9, $resolved['category_ids']);
    }

    public function test_windows_child_matches_and_other_serials_do_not(): void
    {
        $resolved = $this->resolved();

        $windows = (object) [
            'item_type' => 'serials',
            'item_category_type' => 'subcategory',
            'item_category' => 21,
            'item_category_parent' => 9,
        ];
        $otherSerial = (object) [
            'item_type' => 'serials',
            'item_category_type' => 'subcategory',
            'item_category' => 40,
            'item_category_parent' => 15,
        ];
        $script = (object) [
            'item_type' => 'scripts',
            'item_category_type' => 'subcategory',
            'item_category' => 41,
            'item_category_parent' => 4,
        ];
        $scriptFiledUnderOs = (object) [
            'item_type' => 'scripts',
            'item_category_type' => 'category',
            'item_category' => 9,
            'item_category_parent' => 9,
        ];

        $this->assertTrue(OsLicenseCatalog::matchesItem($windows, $resolved));
        $this->assertFalse(OsLicenseCatalog::matchesItem($otherSerial, $resolved));
        $this->assertFalse(OsLicenseCatalog::matchesItem($script, $resolved));
        $this->assertTrue(OsLicenseCatalog::matchesItem($scriptFiledUnderOs, $resolved));
    }

    public function test_parent_link_matches_even_if_child_id_is_unknown(): void
    {
        $resolved = $this->resolved();
        $item = (object) [
            'item_category_type' => 'subcategory',
            'item_category' => 999,
            'item_category_parent' => 9,
        ];

        $this->assertTrue(OsLicenseCatalog::matchesItem($item, $resolved));
        $this->assertTrue(OsLicenseCatalog::matchesSelection('subcategory_21', $resolved));
        $this->assertTrue(OsLicenseCatalog::matchesSelection('category_9', $resolved));
        $this->assertFalse(OsLicenseCatalog::matchesSelection('subcategory_40', $resolved));
        $this->assertFalse(OsLicenseCatalog::matchesSelection('category_4', $resolved));
    }

    public function test_games_label_is_exact_and_prices_keep_cents_only_when_asked(): void
    {
        $this->assertTrue(OsLicenseCatalog::isOptionalAttributeLabel('Games', ['Games']));
        $this->assertTrue(OsLicenseCatalog::isOptionalAttributeLabel(' games ', ['Games']));
        $this->assertFalse(OsLicenseCatalog::isOptionalAttributeLabel('Game Assets', ['Games']));
        $this->assertFalse(OsLicenseCatalog::isOptionalAttributeLabel('Compatible Games', ['Games']));

        $this->assertSame('$12.99', OsLicenseCatalog::formatTaggedPrice('left', '$', 12.99, true));
        $this->assertSame('$13', OsLicenseCatalog::formatTaggedPrice('left', '$', 12.99, false));
        $this->assertSame('12.99$', OsLicenseCatalog::formatTaggedPrice('right', '$', 12.99, true));
        $this->assertSame(12.99, OsLicenseCatalog::roundAmount(12.99, true));
        $this->assertSame(13.0, OsLicenseCatalog::roundAmount(12.99, false));
    }
}
