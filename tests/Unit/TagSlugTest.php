<?php

namespace Tests\Unit;

use Fickrr\Support\LegacyItemSlugs;
use Fickrr\Support\TagSlug;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2).'/app/Support/TagSlug.php';
require_once dirname(__DIR__, 2).'/app/Support/LegacyItemSlugs.php';

class TagSlugTest extends TestCase
{
    public function test_slash_tags_round_trip_and_old_urls_decode(): void
    {
        $tag = 'xbox / console';

        $this->assertSame('xbox-~-console', TagSlug::encode($tag));
        $this->assertSame($tag, TagSlug::decode(TagSlug::encode($tag)));
        $this->assertSame('xbox / console', TagSlug::decode('xbox-/-console'));
        $this->assertSame('google map data extractor / scrapper', TagSlug::decode('-google-map-data-extractor-/-scrapper'));
    }

    public function test_legacy_item_slugs_point_at_the_live_urls(): void
    {
        $this->assertSame(
            'justfans-premium-content-creators-saas-platform-all-versions-available',
            LegacyItemSlugs::target('justfans-v810-premium-content-creators-saas-platform-nulled')
        );
        $this->assertNull(LegacyItemSlugs::target('a-current-slug'));
        $this->assertCount(7, LegacyItemSlugs::MAP);
    }
}
