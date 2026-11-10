<?php

namespace Fickrr\Support;

/**
 * Hero visual: a few popular products for each major category, ready
 * for the spotlight, orbiting thumbs, and the thumbnail strip.
 */
class HeroShowcase
{
    public const PER_GROUP = 3;

    /** @var array<string, array<int, string>> */
    private const NEEDLES = [
        'antivirus' => ['antivirus'],
        'vpn' => ['vpn'],
        'os' => ['operating', 'windows'],
        'office' => ['office', 'corporate'],
        'graphics' => ['graphic'],
        'scripts' => ['script'],
        'games' => ['game'],
        'ai' => ['chatgpt', 'gemini', 'google', 'ai'],
    ];

    /** @var array<int, string> */
    private const ORDER = ['games', 'ai', 'office', 'os', 'antivirus', 'vpn', 'graphics', 'scripts'];

    /** @var array<string, string> */
    private const LABELS = [
        'games' => 'Games',
        'ai' => 'AI',
        'office' => 'Office',
        'os' => 'Operating Systems',
        'antivirus' => 'Antivirus',
        'vpn' => 'VPN',
        'graphics' => 'Graphics & Design',
        'scripts' => 'Scripts',
    ];

    public static function groupKey(string $name): ?string
    {
        foreach (self::NEEDLES as $key => $needles) {
            foreach ($needles as $needle) {
                if (self::mentions($name, $needle)) {
                    return $key;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<int, array{id:int,name:string,slug:string}>  $categories
     * @param  array<int, array{name:string,slug:string}>  $types
     * @param  array<int, array<string, mixed>>  $items
     * @return array{groups: array<int, array<string, mixed>>, marquee: array<int, array<string, mixed>>}
     */
    public static function present(array $categories, array $types, array $items): array
    {
        $parentGroup = [];
        $typeGroup = [];
        $meta = [];

        foreach ($categories as $category) {
            $key = self::groupKey((string) ($category['name'] ?? ''));
            $id = (int) ($category['id'] ?? 0);
            if ($key === null || $id <= 0) {
                continue;
            }
            $parentGroup[$id] = $key;
            if (! isset($meta[$key])) {
                $meta[$key] = self::meta($key, (string) $category['name'], (string) ($category['slug'] ?? ''), 'category');
            }
        }

        foreach ($types as $type) {
            $key = self::groupKey((string) ($type['name'] ?? ''));
            $slug = trim((string) ($type['slug'] ?? ''));
            if ($key === null || $slug === '') {
                continue;
            }
            $typeGroup[$slug] = $key;
            if (! isset($meta[$key])) {
                $meta[$key] = self::meta($key, (string) $type['name'], $slug, 'item-type');
            }
        }

        $seen = [];
        foreach ($items as $item) {
            $id = (int) ($item['id'] ?? 0);
            if ($id <= 0 || isset($seen[$id])) {
                continue;
            }
            $preview = trim((string) ($item['preview'] ?? ''));
            $thumb = trim((string) ($item['thumb'] ?? ''));
            if ($preview === '' && $thumb === '') {
                continue;
            }
            $key = $parentGroup[(int) ($item['parent'] ?? 0)] ?? ($typeGroup[(string) ($item['type'] ?? '')] ?? null);
            if ($key === null) {
                $key = self::groupKey((string) ($item['name'] ?? ''));
            }
            if ($key === null) {
                continue;
            }
            if (! isset($meta[$key])) {
                $meta[$key] = self::meta($key, self::LABELS[$key] ?? $key, '', 'category');
            }
            $seen[$id] = true;
            $meta[$key]['items'][] = $item;
        }

        $groups = [];
        foreach (self::ORDER as $key) {
            if (empty($meta[$key]['items'])) {
                continue;
            }
            usort($meta[$key]['items'], function (array $a, array $b) {
                $views = ((int) ($b['views'] ?? 0)) <=> ((int) ($a['views'] ?? 0));
                if ($views !== 0) {
                    return $views;
                }

                return ((int) ($b['id'] ?? 0)) <=> ((int) ($a['id'] ?? 0));
            });
            $meta[$key]['items'] = array_slice($meta[$key]['items'], 0, self::PER_GROUP);
            $groups[] = $meta[$key];
        }

        return ['groups' => $groups, 'marquee' => self::marquee($groups)];
    }

    /**
     * @param  array<int, array<string, mixed>>  $groups
     * @return array<int, array<string, mixed>>
     */
    public static function marquee(array $groups): array
    {
        $tiles = [];
        foreach ($groups as $group) {
            foreach ($group['items'] ?? [] as $item) {
                $id = (int) ($item['id'] ?? 0);
                if ($id > 0) {
                    $tiles[$id] = $item;
                }
            }
        }
        $tiles = array_values($tiles);
        if ($tiles === []) {
            return [];
        }
        $base = $tiles;
        while (count($tiles) < 8) {
            $tiles = array_merge($tiles, $base);
        }

        return $tiles;
    }

    /**
     * @return array{key:string,name:string,slug:string,kind:string,icon:string,color:string,items:array}
     */
    private static function meta(string $key, string $name, string $slug, string $kind): array
    {
        $icon = HeroTopProducts::icon($name);

        return [
            'key' => $key,
            'name' => $name,
            'slug' => $slug,
            'kind' => $kind,
            'icon' => $icon['icon'],
            'color' => $icon['color'],
            'items' => [],
        ];
    }

    private static function mentions(string $name, string $needle): bool
    {
        $name = strtolower($name);
        $name = trim((string) (preg_replace('/[^a-z0-9]+/', ' ', $name) ?? $name));

        return (bool) preg_match('/(?:^|[^a-z0-9])'.preg_quote($needle, '/').'/i', $name);
    }
}
