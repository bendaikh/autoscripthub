<?php

namespace Fickrr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LandingPage extends Model
{
    protected $table = 'landing_pages';
    protected $primaryKey = 'lp_id';
    
    protected $fillable = [
        'lp_title',
        'lp_tagline',
        'lp_cta_text',
        'lp_accent_color',
        'lp_slug',
        'lp_description',
        'lp_about_description',
        'lp_what_you_get',
        'lp_banner_image',
        'lp_product_video',
        'lp_features',
        'lp_reviews',
        'lp_youtube_url',
        'lp_price',
        'lp_extended_price',
        'lp_currency',
        'lp_meta_title',
        'lp_meta_description',
        'lp_meta_keywords',
        'lp_status',
        'lp_ads_status',
        'lp_source_url',
        'lp_product_id',
        'lp_product_file',
        'lp_product_file_type',
        'lp_product_link',
        'lp_delivery_method',
        'lp_product_kind',
        'lp_content_language',
        'lp_target_country',
    ];

    /**
     * Get all landing pages
     */
    public static function getAllLandingPages()
    {
        return DB::table('landing_pages')
            ->orderBy('lp_id', 'desc')
            ->get();
    }

    /**
     * Get landing page by slug
     */
    public static function getBySlug($slug)
    {
        return DB::table('landing_pages')
            ->where('lp_slug', $slug)
            ->where('lp_status', 1)
            ->first();
    }

    /**
     * Get landing page by ID
     */
    public static function getById($id)
    {
        return DB::table('landing_pages')
            ->where('lp_id', $id)
            ->first();
    }

    /**
     * Save new landing page
     */
    public static function saveLandingPage($data)
    {
        DB::table('landing_pages')->insert($data);
    }

    /**
     * Update landing page
     */
    public static function updateLandingPage($id, $data)
    {
        DB::table('landing_pages')
            ->where('lp_id', $id)
            ->update($data);
    }

    /**
     * Delete landing page
     */
    public static function deleteLandingPage($id)
    {
        // Delete gallery images first
        DB::table('landing_page_gallery')
            ->where('lp_id', $id)
            ->delete();
            
        // Delete landing page
        DB::table('landing_pages')
            ->where('lp_id', $id)
            ->delete();
    }

    /**
     * Get gallery images
     */
    public static function getGalleryImages($lp_id)
    {
        return DB::table('landing_page_gallery')
            ->where('lp_id', $lp_id)
            ->orderBy('lpg_order', 'asc')
            ->get();
    }

    /**
     * Save gallery image
     */
    public static function saveGalleryImage($data)
    {
        DB::table('landing_page_gallery')->insert($data);
    }

    /**
     * Delete gallery image
     */
    public static function deleteGalleryImage($lpg_id)
    {
        DB::table('landing_page_gallery')
            ->where('lpg_id', $lpg_id)
            ->delete();
    }

    /**
     * Normalize a landing page URL slug (English, lowercase, hyphens).
     */
    public static function normalizeSlug(?string $raw): string
    {
        $slug = Str::slug($raw ?? '', '-', 'en');
        $slug = strtolower(preg_replace('/[^a-z0-9-]/', '', $slug));
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        if (strlen($slug) > 60) {
            $slug = rtrim(substr($slug, 0, 60), '-');
        }

        return $slug;
    }

    /**
     * Generate unique slug
     */
    public static function generateSlug($title)
    {
        $slug = self::normalizeSlug($title);
        if ($slug === '') {
            $slug = 'landing-page';
        }

        return self::ensureUniqueSlug($slug);
    }

    /**
     * Append -2, -3, … until the slug is unique.
     */
    public static function ensureUniqueSlug(string $slug, $exclude_id = null): string
    {
        $base = self::normalizeSlug($slug);
        if ($base === '') {
            $base = 'landing-page';
        }

        $candidate = $base;
        $i = 2;
        while (self::slugExists($candidate, $exclude_id)) {
            $candidate = $base . '-' . $i;
            $i++;
        }

        return $candidate;
    }

    /**
     * Check if slug exists
     */
    public static function slugExists($slug, $exclude_id = null)
    {
        $query = DB::table('landing_pages')->where('lp_slug', $slug);
        
        if ($exclude_id) {
            $query->where('lp_id', '!=', $exclude_id);
        }
        
        return $query->count() > 0;
    }
}

