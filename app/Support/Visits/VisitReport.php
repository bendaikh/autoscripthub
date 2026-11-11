<?php

namespace Fickrr\Support\Visits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitReport
{
    public static function forRequest(Request $request): array
    {
        $range = VisitRange::resolve(
            (string) $request->query('visit_range', '7'),
            $request->query('visit_from'),
            $request->query('visit_to'),
            new \DateTimeImmutable('now')
        );

        return self::build($range);
    }

    /**
     * @param  array{preset:string,start:\DateTimeImmutable,end:\DateTimeImmutable,hourly:bool,from:string,to:string}  $range
     */
    public static function build(array $range): array
    {
        $start = $range['start']->format('Y-m-d H:i:s');
        $end = $range['end']->format('Y-m-d H:i:s');
        $driver = DB::connection()->getDriverName();
        $bucket = $range['hourly']
            ? ($driver === 'sqlite' ? "cast(strftime('%H', visited_at) as integer)" : 'HOUR(visited_at)')
            : ($driver === 'sqlite' ? "strftime('%Y-%m-%d', visited_at)" : 'DATE(visited_at)');
        $rows = DB::table('site_visits')
            ->selectRaw($bucket.' as bucket, COUNT(*) as visits, COUNT(DISTINCT visitor_hash) as uniques')
            ->whereBetween('visited_at', [$start, $end])
            ->groupBy(DB::raw($bucket))
            ->get();
        $series = VisitRange::align(VisitRange::keys($range['start'], $range['end'], $range['hourly']), array_map(function ($row) {
            return ['bucket' => $row->bucket, 'visits' => $row->visits, 'uniques' => $row->uniques];
        }, $rows->all()), $range['hourly']);
        $totalVisits = (int) DB::table('site_visits')->whereBetween('visited_at', [$start, $end])->count();
        $totalUniques = (int) DB::table('site_visits')->whereBetween('visited_at', [$start, $end])->distinct()->count('visitor_hash');
        $countries = DB::table('site_visits')
            ->selectRaw('country_code, COUNT(*) as visits, COUNT(DISTINCT visitor_hash) as uniques')
            ->whereBetween('visited_at', [$start, $end])
            ->groupBy('country_code')
            ->orderByDesc('visits')
            ->limit(20)
            ->get();
        $max = 0;
        foreach ($countries as $country) {
            $max = max($max, (int) $country->visits);
        }
        $countryRows = [];
        foreach ($countries as $country) {
            $code = strtoupper((string) $country->country_code);
            $countryRows[] = [
                'code' => $code,
                'name' => $code === '' ? 'Unknown' : \Helper::countryNameFromCode($code),
                'flag' => \Helper::countryFlagEmoji($code),
                'visits' => (int) $country->visits,
                'uniques' => (int) $country->uniques,
                'percent' => $max > 0 ? (int) round(((int) $country->visits / $max) * 100) : 0,
            ];
        }

        return [
            'preset' => $range['preset'],
            'from' => $range['from'],
            'to' => $range['to'],
            'hourly' => $range['hourly'],
            'labels' => $series['labels'],
            'visits' => $series['visits'],
            'uniques' => $series['uniques'],
            'total_visits' => $totalVisits,
            'total_uniques' => $totalUniques,
            'countries' => $countryRows,
            'realtime' => self::realtime(),
        ];
    }

    public static function realtime(): array
    {
        $minutes = max(1, (int) config('visits.realtime_minutes', 5));
        $since = date('Y-m-d H:i:s', time() - ($minutes * 60));
        $rows = DB::table('site_visits')
            ->where('visited_at', '>=', $since)
            ->orderBy('visited_at', 'desc')
            ->limit(400)
            ->get(['visitor_hash', 'path', 'country_code', 'visited_at']);
        $seen = [];
        $visitors = [];
        foreach ($rows as $row) {
            if (isset($seen[$row->visitor_hash])) {
                continue;
            }
            $seen[$row->visitor_hash] = true;
            $code = strtoupper((string) $row->country_code);
            $visitors[] = [
                'path' => (string) $row->path,
                'country' => $code,
                'name' => $code === '' ? 'Unknown' : \Helper::countryNameFromCode($code),
                'flag' => \Helper::countryFlagEmoji($code),
                'seen' => (string) $row->visited_at,
            ];
            if (count($visitors) >= 12) {
                break;
            }
        }

        return [
            'minutes' => $minutes,
            'count' => count($seen),
            'visitors' => $visitors,
        ];
    }
}
