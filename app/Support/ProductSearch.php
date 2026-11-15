<?php

namespace Fickrr\Support;

/**
 * Product search used by the home page search, the shop search and the
 * search suggestions.
 *
 * Every word must appear in the title, tags, short description or SEO
 * keywords (in any order), and common variants also match:
 * "6" <-> "VI", "gta" <-> "grand theft auto", "gta6" -> "gta 6".
 */
class ProductSearch
{
    private const COLUMNS = ['items.item_name', 'items.item_tags', 'items.item_shortdesc', 'items.item_seo_keyword'];

    /** Short words that only narrow results when matched in the title or tags. */
    private const NAME_ONLY_MAX_LEN = 2;

    private const STOP = ['the', 'a', 'an', 'and', 'or', 'for', 'of', 'to', 'in', 'on', 'with', '&', '-', '|'];

    private const ROMAN = [1 => 'i', 2 => 'ii', 3 => 'iii', 4 => 'iv', 5 => 'v', 6 => 'vi', 7 => 'vii', 8 => 'viii', 9 => 'ix', 10 => 'x'];

    private const ALIASES = [
        'gta' => ['grand theft auto'],
        'grand theft auto' => ['gta'],
        'ms' => ['microsoft'],
        'win' => ['windows'],
        'ai' => ['artificial intelligence'],
    ];

    /** Cleans the keyword typed by the visitor. */
    public static function clean($keyword): string
    {
        $k = mb_strtolower(trim((string) $keyword));
        $k = preg_replace('/[^\p{L}\p{N}\s.+#\-]/u', ' ', $k);
        return trim(preg_replace('/\s+/u', ' ', $k));
    }

    /**
     * Word groups: each group lists the spellings that count as a match for one word.
     *
     * @return array<int, array{alts: string[], short: bool}>
     */
    public static function groups($keyword): array
    {
        $k = self::clean($keyword);
        if ($k === '') {
            return [];
        }
        // phrase aliases first (e.g. "grand theft auto" is one word group)
        $groups = [];
        if (preg_match('/\bgrand theft auto\b/u', $k)) {
            $groups[] = ['alts' => ['grand theft auto', 'gta'], 'short' => false];
            $k = trim(preg_replace('/\bgrand theft auto\b/u', ' ', $k));
        }
        $words = [];
        foreach (preg_split('/\s+/u', $k) as $w) {
            if ($w === '') {
                continue;
            }
            // "gta6" -> "gta" + "6"
            if (preg_match('/^(\p{L}{2,})(\d{1,2})$/u', $w, $m)) {
                $words[] = $m[1];
                $words[] = $m[2];
                continue;
            }
            $words[] = $w;
        }
        $words = array_values(array_unique(array_filter($words, fn ($w) => !in_array($w, self::STOP, true))));
        foreach (array_slice($words, 0, 6) as $w) {
            $alts = [$w];
            if (ctype_digit($w) && isset(self::ROMAN[(int) $w])) {
                $alts[] = self::ROMAN[(int) $w];
            } elseif (($n = array_search($w, self::ROMAN, true)) !== false && strlen($w) >= 2) {
                $alts[] = (string) $n;
            }
            foreach (self::ALIASES[$w] ?? [] as $alias) {
                $alts[] = $alias;
            }
            $groups[] = ['alts' => array_values(array_unique($alts)), 'short' => mb_strlen($w) <= self::NAME_ONLY_MAX_LEN];
        }
        return $groups;
    }

    /** Adds the search conditions to an items query (call inside or outside a where group). */
    public static function apply($query, $keyword)
    {
        $groups = self::groups($keyword);
        if ($groups === []) {
            return $query;
        }
        foreach ($groups as $g) {
            // very short words ("6", "vi", "pc") would match almost any description
            $cols = $g['short'] ? ['items.item_name', 'items.item_tags'] : self::COLUMNS;
            $query->where(function ($q) use ($g, $cols) {
                foreach ($g['alts'] as $alt) {
                    foreach ($cols as $col) {
                        $q->orWhere($col, 'LIKE', '%' . self::escape($alt) . '%');
                    }
                }
            });
        }
        return $query;
    }

    /** Best matches first: whole phrase in the title, then titles with every word. */
    public static function orderByRelevance($query, $keyword)
    {
        $k = self::clean($keyword);
        if ($k === '') {
            return $query;
        }
        $first = self::groups($k)[0]['alts'][0] ?? $k;
        return $query->orderByRaw(
            'CASE WHEN items.item_name LIKE ? THEN 0 WHEN items.item_tags LIKE ? THEN 1 WHEN items.item_name LIKE ? THEN 2 ELSE 3 END',
            ['%' . self::escape($k) . '%', '%' . self::escape($k) . '%', '%' . self::escape($first) . '%']
        );
    }

    private static function escape(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
    }
}
