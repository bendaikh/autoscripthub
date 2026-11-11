<?php

namespace Tests\Unit;

use Fickrr\Support\Visits\ClientAddress;
use Fickrr\Support\Visits\CountryResolver;
use Fickrr\Support\Visits\VisitRange;
use Fickrr\Support\Visits\VisitRules;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2).'/app/Support/Visits/VisitRules.php';
require_once dirname(__DIR__, 2).'/app/Support/Visits/ClientAddress.php';
require_once dirname(__DIR__, 2).'/app/Support/Visits/CountryResolver.php';
require_once dirname(__DIR__, 2).'/app/Support/Visits/VisitRange.php';

class VisitTrackingTest extends TestCase
{
    public function test_bots_assets_and_admin_pages_are_skipped(): void
    {
        $this->assertTrue(VisitRules::isBot('Mozilla/5.0 (compatible; Googlebot/2.1)'));
        $this->assertFalse(VisitRules::isBot('Mozilla/5.0 Chrome/120.0'));
        $this->assertTrue(VisitRules::skipPath('/admin/items'));
        $this->assertTrue(VisitRules::skipPath('/theme/css/style.css'));
        $this->assertFalse(VisitRules::skipPath('/shop/category/games'));
        $this->assertSame('mobile', VisitRules::device('Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X)'));
    }

    public function test_country_header_and_cdn_ip_are_preferred(): void
    {
        $headers = [
            'cf-ipcountry' => 'MA',
            'cf-connecting-ip' => '1.1.1.1',
            'x-forwarded-for' => '8.8.8.8, 10.0.0.4',
        ];

        $this->assertSame('MA', CountryResolver::resolve($headers, '8.8.8.8', null));
        $this->assertSame('1.1.1.1', ClientAddress::ip($headers, '10.0.0.8'));
        $this->assertSame('8.8.8.8', ClientAddress::ip(['x-forwarded-for' => '8.8.8.8, 10.1.1.1'], '127.0.0.1'));
    }

    public function test_a_single_day_is_hourly_and_a_week_is_daily(): void
    {
        $now = new \DateTimeImmutable('2026-11-11 15:00:00');
        $today = VisitRange::resolve('today', null, null, $now);
        $week = VisitRange::resolve('7', null, null, $now);
        $custom = VisitRange::resolve('custom', '2026-11-01', '2026-11-03', $now);

        $this->assertTrue($today['hourly']);
        $this->assertCount(24, VisitRange::keys($today['start'], $today['end'], true));
        $this->assertFalse($week['hourly']);
        $this->assertSame('2026-11-05', $week['from']);
        $this->assertSame('2026-11-11', $week['to']);
        $this->assertSame(['2026-11-01', '2026-11-02', '2026-11-03'], VisitRange::keys($custom['start'], $custom['end'], false));

        $filled = VisitRange::align(['00:00', '01:00'], [
            ['bucket' => 1, 'visits' => 4, 'uniques' => 2],
        ], true);
        $this->assertSame([0, 4], $filled['visits']);
        $this->assertSame([0, 2], $filled['uniques']);
    }

    public function test_visitor_hash_does_not_keep_the_raw_id(): void
    {
        $hash = VisitRules::hashId('app-key', 'visitor-123');

        $this->assertSame(64, strlen($hash));
        $this->assertStringNotContainsString('visitor-123', $hash);
        $this->assertSame($hash, VisitRules::hashId('app-key', 'visitor-123'));
    }
}
