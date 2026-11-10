<?php

namespace Fickrr\Support;

/**
 * Tag URLs cannot contain a raw slash. Spaces become hyphens and "/"
 * becomes "~" in the link; both forms turn back into the stored tag text.
 */
class TagSlug
{
    public static function encode(string $tag): string
    {
        $tag = trim($tag);
        $tag = str_replace('/', '~', $tag);
        $tag = preg_replace('/\s+/', '-', $tag) ?? $tag;

        return strtolower($tag);
    }

    public static function decode(string $slug): string
    {
        $slug = rawurldecode($slug);
        $slug = str_replace('~', '/', $slug);

        return trim(str_replace('-', ' ', $slug));
    }
}
