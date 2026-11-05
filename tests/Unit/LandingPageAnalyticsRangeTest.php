<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\LandingPageController;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LandingPageAnalyticsRangeTest extends TestCase
{
    private function resolve(array $q): array
    {
        $c = (new \ReflectionClass(LandingPageController::class))->newInstanceWithoutConstructor();
        return $c->resolveAnalyticsRange(Request::create('/x', 'GET', $q));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_preset_days_kept(): void
    {
        $r = $this->resolve(['days' => 14]);
        $this->assertSame('preset', $r['mode']);
        $this->assertSame(14, $r['days']);
        $this->assertSame(['days' => 14], $r['params']);
    }

    public function test_single_day_is_inclusive_whole_day(): void
    {
        $r = $this->resolve(['from' => '2026-10-03', 'to' => '2026-10-03']);
        $this->assertSame('custom', $r['mode']);
        $this->assertTrue($r['single_day']);
        $this->assertSame('2026-10-03 00:00:00', $r['from']->format('Y-m-d H:i:s'));
        $this->assertSame('2026-10-03 23:59:59', $r['to']->format('Y-m-d H:i:s'));
        $this->assertSame(1, $r['days']);
    }

    public function test_only_from_means_single_day(): void
    {
        $r = $this->resolve(['from' => '2026-10-03']);
        $this->assertSame('2026-10-03', $r['to_date']);
    }

    public function test_range_inclusive(): void
    {
        $r = $this->resolve(['from' => '2026-10-01', 'to' => '2026-10-10']);
        $this->assertSame(10, $r['days']);
        $this->assertSame('2026-10-10 23:59:59', $r['to']->format('Y-m-d H:i:s'));
    }

    public function test_from_after_to_falls_back(): void
    {
        $r = $this->resolve(['from' => '2026-10-10', 'to' => '2026-10-01']);
        $this->assertSame('preset', $r['mode']);
        $this->assertSame(30, $r['days']);
        $this->assertNotSame('', $r['error']);
    }

    public function test_invalid_date_falls_back(): void
    {
        $r = $this->resolve(['from' => '2026-02-31', 'to' => 'abc']);
        $this->assertSame('preset', $r['mode']);
        $this->assertNotSame('', $r['error']);
    }

    public function test_today_and_yesterday_presets(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-05 10:00:00', config('app.timezone')));
        $this->assertSame('2026-11-05', $this->resolve(['days' => 'today'])['from_date']);
        $y = $this->resolve(['days' => 'yesterday']);
        $this->assertSame('2026-11-04', $y['from_date']);
        $this->assertSame('2026-11-04', $y['to_date']);
    }
}
