<?php

namespace Fickrr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LandingPixel extends Model
{
    protected $table = 'landing_pixels';

    protected $fillable = [
        'platform',
        'pixel_id',
        'label',
        'is_active',
    ];

    public static function platforms(): array
    {
        return ['facebook', 'tiktok'];
    }

    public static function allPixels()
    {
        return DB::table('landing_pixels')
            ->orderBy('platform')
            ->orderBy('id', 'desc')
            ->get();
    }

    public static function activePixels()
    {
        return DB::table('landing_pixels')
            ->where('is_active', 1)
            ->orderBy('id')
            ->get();
    }

    public static function activeByPlatform(): array
    {
        $grouped = [
            'facebook' => [],
            'tiktok' => [],
        ];

        foreach (self::activePixels() as $pixel) {
            if (!isset($grouped[$pixel->platform])) {
                continue;
            }
            $id = trim((string) $pixel->pixel_id);
            if ($id === '') {
                continue;
            }
            $grouped[$pixel->platform][] = $id;
        }

        $grouped['facebook'] = array_values(array_unique($grouped['facebook']));
        $grouped['tiktok'] = array_values(array_unique($grouped['tiktok']));

        return $grouped;
    }
}
