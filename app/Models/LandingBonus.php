<?php

namespace Fickrr\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Free bonus items of a landing page (Admin > Landing Pages > edit > Bonuses).
 *
 * Cover and banner images: public/storage/landing-bonuses (plus a resized
 * .webp copy). Bonus files: storage/app/landing-bonuses (private, only
 * reachable through signed download links of a paid order).
 */
class LandingBonus
{
    public const MAX_ITEMS = 6;
    public const IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'webp'];
    public const FILE_TYPES = ['pdf', 'zip', 'png', 'jpg', 'jpeg'];
    public const IMAGE_MAX_KB = 5120;
    public const FILE_MAX_KB = 102400;

    public static function ready(): bool
    {
        try {
            return Schema::hasTable('landing_page_bonuses');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function forPage($lpId, bool $activeOnly = false)
    {
        if (!self::ready() || !$lpId) {
            return collect();
        }
        $q = DB::table('landing_page_bonuses')->where('landing_page_id', (int) $lpId);
        if ($activeOnly) {
            $q->where('is_active', 1);
        }
        return $q->orderBy('sort_order')->orderBy('id')->get();
    }

    /** Active bonuses that can be shown (cover image) and delivered (file or link). */
    public static function shownFor($page)
    {
        if (!$page || (int) ($page->bonus_section_enabled ?? 0) !== 1) {
            return collect();
        }
        return self::forPage($page->lp_id, true)->filter(fn ($b) => self::imageUrl($b->image_path) && self::deliverable($b))->values();
    }

    public static function deliverable($b): bool
    {
        if (($b->delivery_method ?? 'upload') === 'link') {
            return (bool) preg_match('#^https?://#i', (string) $b->external_link);
        }
        return !empty($b->file_path) && is_file(self::fileDir() . '/' . basename($b->file_path));
    }

    /* ------------------------------------------------------------ storage */

    public static function imageDir(): string
    {
        $dir = public_path('storage/landing-bonuses');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }

    public static function fileDir(): string
    {
        $dir = storage_path('app/landing-bonuses');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }

    public static function imageUrl(?string $name): ?string
    {
        $name = basename((string) $name);
        if ($name === '' || !is_file(self::imageDir() . '/' . $name)) {
            return null;
        }
        return url('/public/storage/landing-bonuses/' . $name);
    }

    /** Resized WebP copy (max 1200px wide), when one could be made. */
    public static function webpUrl(?string $name): ?string
    {
        $webp = self::webpName($name);
        return $webp && is_file(self::imageDir() . '/' . $webp) ? url('/public/storage/landing-bonuses/' . $webp) : null;
    }

    private static function webpName(?string $name): ?string
    {
        $name = basename((string) $name);
        return $name === '' ? null : pathinfo($name, PATHINFO_FILENAME) . '-1200.webp';
    }

    public static function storeImage(UploadedFile $file, string $prefix = 'bonus'): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $ext = $ext === 'jpeg' ? 'jpg' : $ext;
        $name = $prefix . '-' . Str::random(20) . '.' . $ext;
        $file->move(self::imageDir(), $name);
        self::makeWebp($name);
        return $name;
    }

    /** @return array{0:string,1:string,2:int} stored name, original name, size */
    public static function storeFile(UploadedFile $file): array
    {
        $original = self::cleanName($file->getClientOriginalName());
        $size = (int) $file->getSize();
        $ext = strtolower($file->getClientOriginalExtension());
        $name = 'file-' . Str::random(32) . '.' . $ext;
        $file->move(self::fileDir(), $name);
        return [$name, $original, $size];
    }

    public static function deleteImage(?string $name): void
    {
        $name = basename((string) $name);
        if ($name === '') {
            return;
        }
        @unlink(self::imageDir() . '/' . $name);
        if ($webp = self::webpName($name)) {
            @unlink(self::imageDir() . '/' . $webp);
        }
    }

    public static function deleteFile(?string $name): void
    {
        $name = basename((string) $name);
        if ($name !== '') {
            @unlink(self::fileDir() . '/' . $name);
        }
    }

    public static function copyImage(?string $name): ?string
    {
        $name = basename((string) $name);
        $src = self::imageDir() . '/' . $name;
        if ($name === '' || !is_file($src)) {
            return null;
        }
        $new = 'bonus-' . Str::random(20) . '.' . pathinfo($name, PATHINFO_EXTENSION);
        if (!@copy($src, self::imageDir() . '/' . $new)) {
            return null;
        }
        self::makeWebp($new);
        return $new;
    }

    public static function copyFile(?string $name): ?string
    {
        $name = basename((string) $name);
        $src = self::fileDir() . '/' . $name;
        if ($name === '' || !is_file($src)) {
            return null;
        }
        $new = 'file-' . Str::random(32) . '.' . pathinfo($name, PATHINFO_EXTENSION);
        return @copy($src, self::fileDir() . '/' . $new) ? $new : null;
    }

    /** Deletes all bonus rows and files of a landing page. */
    public static function deleteForPage($page): void
    {
        if (!self::ready() || !$page) {
            return;
        }
        foreach (self::forPage($page->lp_id) as $b) {
            self::deleteImage($b->image_path);
            self::deleteFile($b->file_path);
        }
        DB::table('landing_page_bonuses')->where('landing_page_id', (int) $page->lp_id)->delete();
        if (!empty($page->bonus_banner_image)) {
            self::deleteImage($page->bonus_banner_image);
        }
    }

    /** Copies the bonus section and items (with their own copies of the files) to another page. */
    public static function duplicate($from, int $toLpId): void
    {
        if (!self::ready() || !$from) {
            return;
        }
        $update = [
            'bonus_section_enabled' => (int) ($from->bonus_section_enabled ?? 0),
            'bonus_section_title' => $from->bonus_section_title ?? null,
            'bonus_banner_image' => self::copyImage($from->bonus_banner_image ?? null),
        ];
        DB::table('landing_pages')->where('lp_id', $toLpId)->update($update);
        foreach (self::forPage($from->lp_id) as $b) {
            DB::table('landing_page_bonuses')->insert([
                'landing_page_id' => $toLpId,
                'title' => $b->title,
                'description' => $b->description,
                'image_path' => self::copyImage($b->image_path),
                'delivery_method' => $b->delivery_method,
                'file_path' => self::copyFile($b->file_path),
                'file_original_name' => $b->file_original_name,
                'file_size' => $b->file_size,
                'external_link' => $b->external_link,
                'value_label' => $b->value_label,
                'badge_text' => $b->badge_text,
                'sort_order' => $b->sort_order,
                'is_active' => $b->is_active,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /* ----------------------------------------------------------- delivery */

    /**
     * Download entries for a paid order: title, name, size label, url, external.
     */
    public static function downloadsFor($order, int $days = 7): array
    {
        if (!$order || ($order->payment_status ?? '') !== 'completed') {
            return [];
        }
        $page = DB::table('landing_pages')->where('lp_id', $order->lp_id)->first();
        $list = [];
        foreach (self::shownFor($page) as $i => $b) {
            $isLink = $b->delivery_method === 'link';
            $list[] = [
                'id' => (int) $b->id,
                'title' => $b->title,
                'badge' => $b->badge_text ?: 'BONUS #' . ($i + 1),
                'name' => $isLink ? null : ($b->file_original_name ?: basename($b->file_path)),
                'size' => $isLink ? null : self::sizeLabel((int) $b->file_size),
                'external' => $isLink,
                'url' => self::signedUrl($order->order_token, (int) $b->id, $days),
            ];
        }
        return $list;
    }

    /** Expiring download link for one bonus of one order (checked by verifySignature). */
    public static function signedUrl(string $orderToken, int $bonusId, int $days): string
    {
        $expires = time() + max(1, $days) * 86400;
        return url('/landing/bonus/' . rawurlencode($orderToken) . '/' . $bonusId)
            . '?expires=' . $expires . '&sig=' . self::signature($orderToken, $bonusId, $expires);
    }

    public static function verifySignature(string $orderToken, int $bonusId, $expires, $sig): bool
    {
        if (!ctype_digit((string) $expires) || (int) $expires < time() || !is_string($sig)) {
            return false;
        }
        return hash_equals(self::signature($orderToken, $bonusId, (int) $expires), $sig);
    }

    private static function signature(string $orderToken, int $bonusId, int $expires): string
    {
        return hash_hmac('sha256', $orderToken . '|' . $bonusId . '|' . $expires, (string) config('app.key'));
    }

    public static function sizeLabel(int $bytes): string
    {
        if ($bytes <= 0) {
            return '';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = min(3, (int) floor(log($bytes, 1024)));
        return round($bytes / (1024 ** $i), $i > 1 ? 1 : 0) . ' ' . $units[$i];
    }

    private static function cleanName(string $name): string
    {
        $name = preg_replace('/[^\pL\pN ._()\-]+/u', '', basename($name));
        return mb_substr(trim($name) !== '' ? trim($name) : 'bonus-file', 0, 180);
    }

    /** Saves a resized WebP copy next to the image (needs GD with WebP support). */
    private static function makeWebp(string $name): void
    {
        try {
            if (!function_exists('imagewebp')) {
                return;
            }
            $path = self::imageDir() . '/' . $name;
            $info = @getimagesize($path);
            if (!$info) {
                return;
            }
            $src = match ($info[2]) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
                IMAGETYPE_PNG => @imagecreatefrompng($path),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
                default => false,
            };
            if (!$src) {
                return;
            }
            [$w, $h] = [$info[0], $info[1]];
            $nw = min(1200, $w);
            $nh = (int) round($h * $nw / max(1, $w));
            $dst = imagecreatetruecolor($nw, $nh);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
            @imagewebp($dst, self::imageDir() . '/' . self::webpName($name), 82);
            imagedestroy($src);
            imagedestroy($dst);
        } catch (\Throwable $e) {
            // the original image is used instead
        }
    }
}
