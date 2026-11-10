<?php

namespace Fickrr\Support;

/**
 * Home "Popular Items": Games, AI and Office, interleaved so one
 * category does not fill the slider before the others appear.
 */
class PopularMix
{
    public const AI_PARENT = 26;

    public const GAMES_PARENT = 22;

    /** Office Licenses and Corporate Software Licenses. */
    public const OFFICE_PARENTS = [19, 24];

    public static function parents(): array
    {
        return [self::OFFICE_PARENTS[0], self::GAMES_PARENT, self::OFFICE_PARENTS[1], self::AI_PARENT];
    }

    /**
     * @param  object|array  $item
     */
    public static function group($item): ?string
    {
        $name = strtolower((string) (is_array($item) ? ($item['item_name'] ?? '') : ($item->item_name ?? '')));
        $parent = (int) (is_array($item) ? ($item['item_category_parent'] ?? 0) : ($item->item_category_parent ?? 0));

        if (self::mentions($name, ['chatgpt', 'gemini', 'google']) || $parent === self::AI_PARENT) {
            return 'ai';
        }
        if (self::mentions($name, ['office']) || in_array($parent, self::OFFICE_PARENTS, true)) {
            return 'office';
        }
        if ($parent === self::GAMES_PARENT) {
            return 'games';
        }

        return null;
    }

    /**
     * Round-robin AI, Games, Office. Each list is already ordered.
     *
     * @param  iterable<int, object|array>  $items
     * @return array<int, object|array>
     */
    public static function interleave(iterable $items, int $limit): array
    {
        $groups = ['ai' => [], 'games' => [], 'office' => []];
        foreach ($items as $item) {
            $group = self::group($item);
            if ($group !== null) {
                $groups[$group][] = $item;
            }
        }

        $mixed = [];
        $index = 0;
        while (count($mixed) < $limit) {
            $added = false;
            foreach (['ai', 'games', 'office'] as $key) {
                if (!isset($groups[$key][$index])) {
                    continue;
                }
                $mixed[] = $groups[$key][$index];
                $added = true;
                if (count($mixed) >= $limit) {
                    break;
                }
            }
            if (!$added) {
                break;
            }
            $index++;
        }

        return $mixed;
    }

    private static function mentions(string $name, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && str_contains($name, $needle)) {
                return true;
            }
        }

        return false;
    }
}
