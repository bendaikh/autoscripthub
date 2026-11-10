<?php

namespace Fickrr\Support;

/**
 * Product photos are stored once and later chosen as thumbnail, preview, or gallery.
 * Cap width only. Never enlarge a small file, and never re-encode when the pixels already fit.
 */
class ProductImage
{
    /** Preview and gallery files are kept up to this width. */
    public const MAX_WIDTH = 1200;

    /** Thumbnails should be uploaded at least this wide. Smaller files are not enlarged. */
    public const THUMBNAIL_MIN_WIDTH = 400;

    /**
     * @return array{0:int,1:int}
     */
    public static function outputSize(int $width, int $height, int $maxWidth = self::MAX_WIDTH): array
    {
        if ($width <= 0 || $height <= 0 || $maxWidth <= 0 || $width <= $maxWidth) {
            return [$width, $height];
        }
        $newHeight = (int) max(1, round($height * ($maxWidth / $width)));

        return [$maxWidth, $newHeight];
    }

    /**
     * @param  array<int, mixed>  $filenames
     * @param  callable(string): ?int  $widthOf
     */
    public static function pickSharpest(array $filenames, callable $widthOf): ?string
    {
        $best = null;
        $bestWidth = -1;
        foreach ($filenames as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }
            $width = $widthOf($name);
            $width = $width === null ? -1 : (int) $width;
            if ($best === null || $width > $bestWidth) {
                $best = $name;
                $bestWidth = $width;
            }
        }

        return $best;
    }

    /**
     * Extra gallery rows whose file is gone should not be rendered.
     * The callback decides whether that filename is on disk.
     *
     * @param  iterable<int, object|array>  $images
     * @param  callable(string): bool  $exists
     * @return array<int, object|array>
     */
    public static function keepExisting(iterable $images, callable $exists): array
    {
        $kept = [];
        foreach ($images as $image) {
            $name = trim((string) (is_object($image) ? ($image->item_image ?? '') : ($image['item_image'] ?? '')));
            if ($name !== '' && $exists($name)) {
                $kept[] = $image;
            }
        }

        return $kept;
    }

    public static function storedFileExists(string $filename): bool
    {
        $safe = basename(str_replace('\\', '/', $filename));
        if ($safe === '' || $safe === '.' || $safe === '..') {
            return false;
        }

        return is_file(base_path('public/storage/items/'.$safe));
    }

    public static function localWidth(string $absolutePath): ?int
    {
        if (! is_file($absolutePath)) {
            return null;
        }
        $info = @getimagesize($absolutePath);
        if (! is_array($info) || empty($info[0])) {
            return null;
        }

        return (int) $info[0];
    }

    /**
     * Downscale a stored image in place when it is wider than the cap.
     * Returns true when the file was rewritten.
     */
    public static function limitFile(string $absolutePath, int $quality = 100): bool
    {
        $size = self::readSize($absolutePath);
        if ($size === null) {
            return false;
        }
        [$width, $height] = $size;
        [$newWidth, $newHeight] = self::outputSize($width, $height, self::MAX_WIDTH);
        if ($newWidth === $width && $newHeight === $height) {
            return false;
        }
        $image = self::load($absolutePath);
        $image->save($absolutePath, $quality > 0 ? $quality : 100);

        return true;
    }

    /**
     * Open an image and shrink it to the cap before a watermark is drawn.
     */
    public static function load(string $absolutePath)
    {
        $image = \Image::make($absolutePath);
        $width = (int) $image->width();
        $height = (int) $image->height();
        [$newWidth, $newHeight] = self::outputSize($width, $height, self::MAX_WIDTH);
        if ($newWidth !== $width || $newHeight !== $height) {
            $image->resize($newWidth, $newHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        return $image;
    }

    /**
     * @return array{0:int,1:int}|null
     */
    private static function readSize(string $absolutePath): ?array
    {
        if (! is_file($absolutePath)) {
            return null;
        }
        $info = @getimagesize($absolutePath);
        if (! is_array($info) || empty($info[0]) || empty($info[1])) {
            return null;
        }

        return [(int) $info[0], (int) $info[1]];
    }
}
