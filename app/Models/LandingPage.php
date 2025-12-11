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
        'lp_slug',
        'lp_description',
        'lp_what_you_get',
        'lp_banner_image',
        'lp_features',
        'lp_youtube_url',
        'lp_price',
        'lp_extended_price',
        'lp_currency',
        'lp_meta_title',
        'lp_meta_description',
        'lp_meta_keywords',
        'lp_status',
        'lp_product_id',
        'lp_product_file',
        'lp_product_file_type',
        'lp_product_link',
        'lp_delivery_method'
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
     * Generate unique slug
     */
    public static function generateSlug($title)
    {
        $slug = Str::slug($title);
        $count = DB::table('landing_pages')
            ->where('lp_slug', 'like', $slug . '%')
            ->count();
        
        return $count > 0 ? $slug . '-' . ($count + 1) : $slug;
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

