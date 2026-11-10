<?php

namespace Fickrr\Support;

/**
 * Home hero "Top Products" slides: real parent categories plus item
 * types that are not already represented by a category name.
 */
class HeroTopProducts
{
    public const PER_SLIDE = 4;

    /**
     * Longer names first so "operating" wins before a shorter neighbour.
     *
     * @var array<int, array{0:string,1:string,2:string}>
     */
    private const ICONS = [
        ['chatgpt', 'fa-magic', '#7c3aed'],
        ['gemini', 'fa-magic', '#8b5cf6'],
        ['antivirus', 'fa-shield', '#ef4444'],
        ['operating', 'fa-desktop', '#2563eb'],
        ['windows', 'fa-windows', '#2563eb'],
        ['corporate', 'fa-building', '#ea580c'],
        ['office', 'fa-file-text-o', '#f97316'],
        ['vpn', 'fa-lock', '#0d9488'],
        ['graphic', 'fa-picture-o', '#f59e0b'],
        ['design', 'fa-paint-brush', '#f59e0b'],
        ['script', 'fa-code', '#4f46e5'],
        ['theme', 'fa-paint-brush', '#0ea5e9'],
        ['game', 'fa-gamepad', '#10b981'],
        ['plugin', 'fa-plug', '#6366f1'],
        ['app', 'fa-th-large', '#059669'],
        ['ai', 'fa-magic', '#7c3aed'],
    ];

    /**
     * @param  iterable<int, array{id:int,name:string,slug:string}>  $categories
     * @param  iterable<int, array{name:string,slug:string}>  $types
     * @param  array<int, int>  $categoryCounts
     * @param  array<string, int>  $typeCounts
     * @return array<int, array<int, array{name:string,slug:string,kind:string,count:int,icon:string,color:string}>>
     */
    public static function slides(iterable $categories, iterable $types, array $categoryCounts, array $typeCounts): array
    {
        $rows = [];
        $names = [];

        foreach ($categories as $category) {
            $slug = trim((string) ($category['slug'] ?? ''));
            $name = trim((string) ($category['name'] ?? ''));
            if ($slug === '' || $name === '') {
                continue;
            }
            $names[] = self::norm($name);
            $rows[] = self::row($name, $slug, 'category', (int) ($categoryCounts[(int) ($category['id'] ?? 0)] ?? 0));
        }

        foreach ($types as $type) {
            $slug = trim((string) ($type['slug'] ?? ''));
            $name = trim((string) ($type['name'] ?? ''));
            if ($slug === '' || $name === '' || self::covered($name, $names)) {
                continue;
            }
            $rows[] = self::row($name, $slug, 'item-type', (int) ($typeCounts[$slug] ?? 0));
        }

        usort($rows, function (array $a, array $b) {
            if ($a['count'] !== $b['count']) {
                return $b['count'] <=> $a['count'];
            }

            return strcasecmp($a['name'], $b['name']);
        });

        if ($rows === []) {
            return [];
        }

        return array_chunk($rows, self::PER_SLIDE);
    }

    /**
     * @return array{icon:string,color:string}
     */
    public static function icon(string $name): array
    {
        foreach (self::ICONS as $icon) {
            if (self::mentions($name, $icon[0])) {
                return ['icon' => $icon[1], 'color' => $icon[2]];
            }
        }

        $palette = [
            ['fa-folder-open', '#6366f1'],
            ['fa-cube', '#8b5cf6'],
            ['fa-tag', '#0ea5e9'],
            ['fa-star', '#f59e0b'],
        ];
        $pick = $palette[abs(crc32(strtolower($name))) % count($palette)];

        return ['icon' => $pick[0], 'color' => $pick[1]];
    }

    /**
     * @return array{name:string,slug:string,kind:string,count:int,icon:string,color:string}
     */
    private static function row(string $name, string $slug, string $kind, int $count): array
    {
        $icon = self::icon($name);

        return [
            'name' => $name,
            'slug' => $slug,
            'kind' => $kind,
            'count' => $count,
            'icon' => $icon['icon'],
            'color' => $icon['color'],
        ];
    }

    /**
     * @param  array<int, string>  $categoryNames
     */
    private static function covered(string $name, array $categoryNames): bool
    {
        $type = self::norm($name);
        if ($type === '') {
            return true;
        }
        foreach ($categoryNames as $category) {
            if ($category === $type || str_starts_with($category, $type.' ') || str_starts_with($type, $category.' ')) {
                return true;
            }
            $short = strlen($type) <= strlen($category) ? $type : $category;
            $long = $short === $type ? $category : $type;
            if (strlen($short) >= 4 && str_contains($long, $short)) {
                return true;
            }
        }

        return false;
    }

    private static function mentions(string $name, string $needle): bool
    {
        return (bool) preg_match('/(?:^|[^a-z0-9])'.preg_quote($needle, '/').'/i', self::norm($name));
    }

    private static function norm(string $name): string
    {
        $name = strtolower($name);
        $name = preg_replace('/[^a-z0-9]+/', ' ', $name) ?? $name;

        return trim($name);
    }
}
