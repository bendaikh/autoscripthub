<?php

namespace Fickrr\Support\Visits;

class VisitRange
{
    /**
     * @return array{preset:string,start:\DateTimeImmutable,end:\DateTimeImmutable,hourly:bool,from:string,to:string}
     */
    public static function resolve(string $preset, ?string $from, ?string $to, \DateTimeImmutable $now): array
    {
        $preset = strtolower(trim($preset));
        $end = $now->setTime(23, 59, 59);
        $start = $now->setTime(0, 0, 0);

        if ($preset === 'today') {
            return self::pack('today', $start, $end, true);
        }
        if ($preset === 'yesterday') {
            $day = $now->modify('-1 day');

            return self::pack('yesterday', $day->setTime(0, 0, 0), $day->setTime(23, 59, 59), true);
        }
        if ($preset === '30') {
            return self::pack('30', $start->modify('-29 days'), $end, false);
        }
        if ($preset === 'month') {
            return self::pack('month', $start->modify('first day of this month')->setTime(0, 0, 0), $end, false);
        }
        if ($preset === 'custom') {
            $fromDay = self::day($from) ?? $start->modify('-6 days');
            $toDay = self::day($to) ?? $end;
            if ($fromDay > $toDay) {
                [$fromDay, $toDay] = [$toDay, $fromDay];
            }
            if ($fromDay->diff($toDay)->days > 366) {
                $fromDay = $toDay->modify('-366 days');
            }
            $hourly = $fromDay->format('Y-m-d') === $toDay->format('Y-m-d');

            return self::pack('custom', $fromDay->setTime(0, 0, 0), $toDay->setTime(23, 59, 59), $hourly);
        }

        return self::pack('7', $start->modify('-6 days'), $end, false);
    }

    /**
     * @return array<int, string>
     */
    public static function keys(\DateTimeImmutable $start, \DateTimeImmutable $end, bool $hourly): array
    {
        if ($hourly) {
            $keys = [];
            for ($hour = 0; $hour < 24; $hour++) {
                $keys[] = sprintf('%02d:00', $hour);
            }

            return $keys;
        }
        $keys = [];
        $cursor = $start->setTime(0, 0, 0);
        $last = $end->setTime(0, 0, 0);
        while ($cursor <= $last) {
            $keys[] = $cursor->format('Y-m-d');
            $cursor = $cursor->modify('+1 day');
        }

        return $keys;
    }

    /**
     * @param  array<int, string>  $keys
     * @param  array<int, array{bucket:mixed,visits:mixed,uniques:mixed}>  $rows
     * @return array{labels:array<int, string>,visits:array<int, int>,uniques:array<int, int>}
     */
    public static function align(array $keys, array $rows, bool $hourly): array
    {
        $visits = array_fill_keys($keys, 0);
        $uniques = array_fill_keys($keys, 0);
        foreach ($rows as $row) {
            $bucket = $hourly
                ? sprintf('%02d:00', (int) $row['bucket'])
                : substr((string) $row['bucket'], 0, 10);
            if (! array_key_exists($bucket, $visits)) {
                continue;
            }
            $visits[$bucket] = (int) $row['visits'];
            $uniques[$bucket] = (int) $row['uniques'];
        }

        return [
            'labels' => array_keys($visits),
            'visits' => array_values($visits),
            'uniques' => array_values($uniques),
        ];
    }

    private static function day(?string $value): ?\DateTimeImmutable
    {
        $value = trim((string) $value);
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }
        $day = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $day instanceof \DateTimeImmutable ? $day : null;
    }

    /**
     * @return array{preset:string,start:\DateTimeImmutable,end:\DateTimeImmutable,hourly:bool,from:string,to:string}
     */
    private static function pack(string $preset, \DateTimeImmutable $start, \DateTimeImmutable $end, bool $hourly): array
    {
        return [
            'preset' => $preset,
            'start' => $start,
            'end' => $end,
            'hourly' => $hourly,
            'from' => $start->format('Y-m-d'),
            'to' => $end->format('Y-m-d'),
        ];
    }
}
