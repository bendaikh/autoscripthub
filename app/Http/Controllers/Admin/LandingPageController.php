<?php

namespace Fickrr\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\LandingPage;
use Fickrr\Models\LandingPageMessage;
use Fickrr\Models\Settings;
use Fickrr\Helpers\Helper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Storage;
use Session;
use Illuminate\Support\Facades\DB;

class LandingPageController extends Controller
{
    /**
     * Display all landing pages
     */
    public function index()
    {
        $data['landing_pages'] = LandingPage::getAllLandingPages();
        $data['catalog_products'] = $this->catalogProducts();
        $sid = 1;
        $data['setting'] = Settings::editGeneral($sid);
        
        return view('admin.landing-pages.index', $data);
    }

    /**
     * Update ads publish status from the landing pages table.
     */
    public function updateAdsStatus(Request $request, $lp_id)
    {
        $landing_page = LandingPage::getById($lp_id);

        if (!$landing_page) {
            return response()->json(['success' => false, 'message' => 'Landing page not found'], 404);
        }

        $status = strtolower(trim((string) $request->input('ads_status', '')));
        $allowed = ['off', 'published', 'paused'];

        if (!in_array($status, $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'Invalid ads status'], 422);
        }

        LandingPage::updateLandingPage($lp_id, [
            'lp_ads_status' => $status,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'ads_status' => $status,
        ]);
    }

    /**
     * Show form to create new landing page
     */
    public function create()
    {
        $sid = 1;
        $data['setting'] = Settings::editGeneral($sid);
        $data['ai'] = Settings::editAiSettings();
        $data['openai_ready'] = (int) ($data['ai']->openai_enabled ?? 0) === 1
            && !empty($data['ai']->openai_api_key);
        $data['catalog_products'] = $this->catalogProducts();
        $requested = (int) request()->query('item', 0);
        $data['catalog_prefill'] = $data['catalog_products']->first(function ($item) use ($requested) {
            return (int) $item->item_id === $requested;
        });
        
        return view('admin.landing-pages.create', $data);
    }

    /**
     * Store new landing page
     */
    public function store(Request $request)
    {
        $request->merge([
            'lp_slug' => LandingPage::normalizeSlug($request->input('lp_slug')),
        ]);

        $validator = Validator::make($request->all(), [
            'lp_title' => 'required|max:255',
            'lp_slug' => 'required|string|max:60|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'lp_description' => 'required',
            'lp_price' => 'required|numeric|min:0',
            'lp_banner_image' => 'nullable|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'lp_product_file' => 'nullable',
            'gallery_images' => 'nullable|array|max:20',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'lp_product_kind' => 'nullable|in:script,sheet,product,physical',
            'lp_content_language' => 'required|string|max:10',
            'lp_target_country' => 'required|string|size:2',
            'lp_source_url' => 'nullable|url|max:2048',
            'lp_product_video' => 'nullable|file|max:51200',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $slugError = $this->validateLandingSlug($request->input('lp_slug'));
        if ($slugError !== null) {
            return redirect()->back()->withErrors(['lp_slug' => $slugError])->withInput();
        }

        $contentLanguage = $this->resolveContentLanguage($request);

        $slug = LandingPage::ensureUniqueSlug(
            LandingPage::normalizeSlug($request->input('lp_slug'))
        );

        // Handle banner image upload
        $banner_image = null;
        if ($request->hasFile('lp_banner_image')) {
            $image = $request->file('lp_banner_image');
            $bannerError = \Fickrr\Helpers\SecureUpload::validateImage($image);
            if ($bannerError !== null) {
                return redirect()->back()->with('error', $bannerError)->withInput();
            }
            $banner_image = \Fickrr\Helpers\SecureUpload::safeFilename(\Fickrr\Helpers\SecureUpload::extension($image));
            $image->move(public_path('storage/landing-pages'), $banner_image);
        }

        // Process features
        $features = [];
        if ($request->has('features') && is_array($request->features)) {
            foreach ($request->features as $feature) {
                if (!empty($feature)) {
                    $features[] = $feature;
                }
            }
        }

        // Handle product delivery method
        $product_file = null;
        $product_file_type = 'file';
        $product_link = null;
        $delivery_method = $request->lp_delivery_method ?? 'upload';

        if (\Fickrr\Support\Store::isPhysical()) {
            $delivery_method = 'shipping';
            $product_file = null;
            $product_link = null;
        } elseif ($delivery_method == 'link') {
            // Save product link
            $product_link = $request->lp_product_link;
        } else {
            // Handle product file/folder upload
            if ($request->hasFile('lp_product_file')) {
                $files = $request->file('lp_product_file');
                $productAllowed = ['zip', 'rar', '7z', 'tar', 'gz', 'pdf', 'jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'mp3', 'xlsx', 'xls', 'csv', 'ods'];
                
                if (is_array($files) && count($files) > 1) {
                    // Multiple files - folder upload
                    $product_file_type = 'folder';
                    $folderName = 'landing_product_' . time();
                    $folderPath = public_path('storage/landing-products/' . $folderName);
                    
                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0755, true);
                    }
                    
                    foreach ($files as $file) {
                        $err = \Fickrr\Helpers\SecureUpload::validateItemFile($file, $productAllowed);
                        if ($err !== null) {
                            return redirect()->back()->with('error', $err)->withInput();
                        }
                        $fileName = \Fickrr\Helpers\SecureUpload::safeFilename(\Fickrr\Helpers\SecureUpload::extension($file));
                        $file->move($folderPath, $fileName);
                    }
                    
                    $product_file = $folderName;
                } else {
                    // Single file upload
                    $product_file_type = 'file';
                    $file = is_array($files) ? $files[0] : $files;
                    $err = \Fickrr\Helpers\SecureUpload::validateItemFile($file, $productAllowed);
                    if ($err !== null) {
                        return redirect()->back()->with('error', $err)->withInput();
                    }
                    $product_file = \Fickrr\Helpers\SecureUpload::safeFilename(\Fickrr\Helpers\SecureUpload::extension($file));
                    $file->move(public_path('storage/landing-products'), $product_file);
                }
            }
        }

        $product_video = $this->storeProductVideo($request);
        if ($product_video instanceof \Illuminate\Http\RedirectResponse) {
            return $product_video;
        }

        $data = [
            'lp_title' => $request->lp_title,
            'lp_tagline' => $request->lp_tagline,
            'lp_cta_text' => $request->lp_cta_text,
            'lp_accent_color' => \Fickrr\Helpers\LandingPageContent::sanitizeAccent($request->lp_accent_color),
            'lp_slug' => $slug,
            'lp_description' => $request->lp_description,
            'lp_about_description' => $request->lp_about_description,
            'lp_what_you_get' => $request->lp_what_you_get,
            'lp_banner_image' => $banner_image,
            'lp_product_video' => $product_video,
            'lp_features' => json_encode($features),
            'lp_reviews' => $this->encodeReviewsInput($request->input('lp_reviews')),
            'lp_youtube_url' => $request->lp_youtube_url,
            'lp_price' => $request->lp_price,
            'lp_extended_price' => $request->lp_extended_price,
            'lp_currency' => $request->lp_currency ?? 'USD',
            'lp_meta_title' => $request->lp_meta_title,
            'lp_meta_description' => $request->lp_meta_description,
            'lp_meta_keywords' => $request->lp_meta_keywords,
            'lp_status' => $request->lp_status ?? 1,
            'lp_ads_status' => 'off',
            'lp_product_file' => $product_file,
            'lp_product_file_type' => $product_file_type,
            'lp_product_link' => $product_link,
            'lp_delivery_method' => $delivery_method,
            'lp_product_kind' => $this->normalizeProductKind($request->input('lp_product_kind')),
            'lp_content_language' => $contentLanguage,
            'lp_target_country' => strtoupper((string) $request->input('lp_target_country', '')),
            'lp_source_url' => $this->normalizeSourceUrl($request->input('lp_source_url')),
            'lp_product_id' => $this->publishedCatalogId($request),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Prefer AI-generated banner filename when no upload was provided
        if (empty($banner_image) && $request->filled('ai_banner_image')) {
            $aiBanner = basename($request->input('ai_banner_image'));
            if (file_exists(public_path('storage/landing-pages/' . $aiBanner))) {
                $data['lp_banner_image'] = $aiBanner;
            }
        }

        LandingPage::saveLandingPage($data);

        // Get the last inserted ID
        $landing_page_id = \DB::getPdo()->lastInsertId();
        \Fickrr\Services\Physical\PhysicalCatalog::syncLanding((int) $landing_page_id, $request);

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            $order = 0;
            $gallery_images = $request->file('gallery_images');
            
            foreach ($gallery_images as $gallery_image) {
                if ($gallery_image && $gallery_image->isValid()) {
                    $gallery_filename = \Fickrr\Helpers\SecureUpload::safeFilename(
                        \Fickrr\Helpers\SecureUpload::extension($gallery_image)
                    );
                    $gallery_image->move(public_path('storage/landing-pages'), $gallery_filename);
                    
                    LandingPage::saveGalleryImage([
                        'lp_id' => $landing_page_id,
                        'lpg_image' => $gallery_filename,
                        'lpg_description' => null,
                        'lpg_order' => $order++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        Session::flash('success', 'Landing page created successfully! URL: /landing/' . $slug);
        return redirect()->route('admin.landing-pages');
    }

    /**
     * Show form to edit landing page
     */
    public function edit($id)
    {
        $data['landing_page'] = LandingPage::getById($id);
        
        if (!$data['landing_page']) {
            Session::flash('error', 'Landing page not found');
            return redirect()->route('admin.landing-pages');
        }

        $data['gallery_images'] = LandingPage::getGalleryImages($id);
        $sid = 1;
        $data['setting'] = Settings::editGeneral($sid);
        $data['ai'] = Settings::editAiSettings();
        $data['openai_ready'] = (int) ($data['ai']->openai_enabled ?? 0) === 1
            && !empty($data['ai']->openai_api_key);
        $data['catalog_products'] = $this->catalogProducts();
        
        return view('admin.landing-pages.edit', $data);
    }

    /**
     * Update landing page
     */
    public function update(Request $request)
    {
        $request->merge([
            'lp_slug' => LandingPage::normalizeSlug($request->input('lp_slug')),
        ]);

        $validator = Validator::make($request->all(), [
            'lp_id' => 'required|exists:landing_pages,lp_id',
            'lp_title' => 'required|max:255',
            'lp_slug' => 'required|string|max:60|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'lp_description' => 'required',
            'lp_price' => 'required|numeric|min:0',
            'lp_banner_image' => 'nullable|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'lp_product_file' => 'nullable',
            'gallery_images' => 'nullable|array|max:20',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'lp_product_kind' => 'nullable|in:script,sheet,product,physical',
            'lp_content_language' => 'required|string|max:10',
            'lp_target_country' => 'required|string|size:2',
            'lp_source_url' => 'nullable|url|max:2048',
            'lp_product_video' => 'nullable|file|max:51200',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $landing_page = LandingPage::getById($request->lp_id);
        
        if (!$landing_page) {
            Session::flash('error', 'Landing page not found');
            return redirect()->route('admin.landing-pages');
        }

        $slugError = $this->validateLandingSlug($request->input('lp_slug'));
        if ($slugError !== null) {
            return redirect()->back()->withErrors(['lp_slug' => $slugError])->withInput();
        }

        $contentLanguage = $this->resolveContentLanguage($request);

        // Handle banner image upload
        $banner_image = $landing_page->lp_banner_image;
        if ($request->hasFile('lp_banner_image')) {
            // Delete old image
            if ($banner_image && file_exists(public_path('storage/landing-pages/' . $banner_image))) {
                File::delete(public_path('storage/landing-pages/' . $banner_image));
            }
            
            $image = $request->file('lp_banner_image');
            $bannerError = \Fickrr\Helpers\SecureUpload::validateImage($image);
            if ($bannerError !== null) {
                return redirect()->back()->with('error', $bannerError)->withInput();
            }
            $banner_image = \Fickrr\Helpers\SecureUpload::safeFilename(\Fickrr\Helpers\SecureUpload::extension($image));
            $image->move(public_path('storage/landing-pages'), $banner_image);
        }

        // Process features
        $features = [];
        if ($request->has('features') && is_array($request->features)) {
            foreach ($request->features as $feature) {
                if (!empty($feature)) {
                    $features[] = $feature;
                }
            }
        }

        // Handle product delivery method
        $product_file = $landing_page->lp_product_file;
        $product_file_type = $landing_page->lp_product_file_type ?? 'file';
        $product_link = $landing_page->lp_product_link;
        $delivery_method = $request->lp_delivery_method ?? 'upload';

        if (\Fickrr\Support\Store::isPhysical()) {
            $delivery_method = 'shipping';
            $product_file = null;
            $product_link = null;
        } elseif ($delivery_method == 'link') {
            // Update product link
            $product_link = $request->lp_product_link;
            
            // Delete old uploaded files if switching from upload to link
            if ($landing_page->lp_delivery_method == 'upload' && $product_file) {
                if ($product_file_type == 'folder') {
                    $folderPath = public_path('storage/landing-products/' . $product_file);
                    if (file_exists($folderPath)) {
                        File::deleteDirectory($folderPath);
                    }
                } else {
                    $filePath = public_path('storage/landing-products/' . $product_file);
                    if (file_exists($filePath)) {
                        File::delete($filePath);
                    }
                }
                $product_file = null;
            }
        } else {
            // Handle product file/folder upload
            if ($request->hasFile('lp_product_file')) {
                // Delete old file/folder
                if ($product_file) {
                    if ($product_file_type == 'folder') {
                        $folderPath = public_path('storage/landing-products/' . $product_file);
                        if (file_exists($folderPath)) {
                            File::deleteDirectory($folderPath);
                        }
                    } else {
                        $filePath = public_path('storage/landing-products/' . $product_file);
                        if (file_exists($filePath)) {
                            File::delete($filePath);
                        }
                    }
                }
                
                $files = $request->file('lp_product_file');
                $productAllowed = ['zip', 'rar', '7z', 'tar', 'gz', 'pdf', 'jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'mp3', 'xlsx', 'xls', 'csv', 'ods'];
                
                if (is_array($files) && count($files) > 1) {
                    // Multiple files - folder upload
                    $product_file_type = 'folder';
                    $folderName = 'landing_product_' . time();
                    $folderPath = public_path('storage/landing-products/' . $folderName);
                    
                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0755, true);
                    }
                    
                    foreach ($files as $file) {
                        $err = \Fickrr\Helpers\SecureUpload::validateItemFile($file, $productAllowed);
                        if ($err !== null) {
                            return redirect()->back()->with('error', $err)->withInput();
                        }
                        $fileName = \Fickrr\Helpers\SecureUpload::safeFilename(\Fickrr\Helpers\SecureUpload::extension($file));
                        $file->move($folderPath, $fileName);
                    }
                    
                    $product_file = $folderName;
                } else {
                    // Single file upload
                    $product_file_type = 'file';
                    $file = is_array($files) ? $files[0] : $files;
                    $err = \Fickrr\Helpers\SecureUpload::validateItemFile($file, $productAllowed);
                    if ($err !== null) {
                        return redirect()->back()->with('error', $err)->withInput();
                    }
                    $product_file = \Fickrr\Helpers\SecureUpload::safeFilename(\Fickrr\Helpers\SecureUpload::extension($file));
                    $file->move(public_path('storage/landing-products'), $product_file);
                }
            }
            
            // Clear link if switching from link to upload
            if ($landing_page->lp_delivery_method == 'link') {
                $product_link = null;
            }
        }

        $slug = LandingPage::ensureUniqueSlug(
            LandingPage::normalizeSlug($request->input('lp_slug')),
            $request->lp_id
        );

        $product_video = $landing_page->lp_product_video ?? null;
        if ($product_video && $request->has('remove_product_video') && $request->input('remove_product_video')) {
            $this->deleteProductVideoFile($product_video);
            $product_video = null;
        }
        $uploadedVideo = $this->storeProductVideo($request);
        if ($uploadedVideo instanceof \Illuminate\Http\RedirectResponse) {
            return $uploadedVideo;
        }
        if ($uploadedVideo) {
            if ($product_video) {
                $this->deleteProductVideoFile($product_video);
            }
            $product_video = $uploadedVideo;
        }

        $data = [
            'lp_title' => $request->lp_title,
            'lp_tagline' => $request->lp_tagline,
            'lp_cta_text' => $request->lp_cta_text,
            'lp_accent_color' => \Fickrr\Helpers\LandingPageContent::sanitizeAccent($request->lp_accent_color),
            'lp_slug' => $slug,
            'lp_description' => $request->lp_description,
            'lp_about_description' => $request->lp_about_description,
            'lp_what_you_get' => $request->lp_what_you_get,
            'lp_banner_image' => $banner_image,
            'lp_product_video' => $product_video,
            'lp_features' => json_encode($features),
            'lp_reviews' => $this->encodeReviewsInput($request->input('lp_reviews')),
            'lp_youtube_url' => $request->lp_youtube_url,
            'lp_price' => $request->lp_price,
            'lp_extended_price' => $request->lp_extended_price,
            'lp_currency' => $request->lp_currency ?? 'USD',
            'lp_meta_title' => $request->lp_meta_title,
            'lp_meta_description' => $request->lp_meta_description,
            'lp_meta_keywords' => $request->lp_meta_keywords,
            'lp_status' => $request->lp_status ?? 1,
            'lp_product_file' => $product_file,
            'lp_product_file_type' => $product_file_type,
            'lp_product_link' => $product_link,
            'lp_delivery_method' => $delivery_method,
            'lp_product_kind' => $this->normalizeProductKind($request->input('lp_product_kind')),
            'lp_content_language' => $contentLanguage,
            'lp_target_country' => strtoupper((string) $request->input('lp_target_country', '')),
            'lp_source_url' => $this->normalizeSourceUrl($request->input('lp_source_url')),
            'lp_product_id' => $this->publishedCatalogId($request),
            'updated_at' => now(),
        ];

        if (($banner_image === ($landing_page->lp_banner_image ?? null) || empty($request->file('lp_banner_image'))) && $request->filled('ai_banner_image')) {
            $aiBanner = basename($request->input('ai_banner_image'));
            if (file_exists(public_path('storage/landing-pages/' . $aiBanner))) {
                $data['lp_banner_image'] = $aiBanner;
            }
        }

        LandingPage::updateLandingPage($request->lp_id, $data);
        \Fickrr\Services\Physical\PhysicalCatalog::syncLanding((int) $request->lp_id, $request);

        // Handle new gallery images
        if ($request->hasFile('gallery_images')) {
            $existing_images = LandingPage::getGalleryImages($request->lp_id);
            $order = count($existing_images);
            $gallery_images = $request->file('gallery_images');
            
            foreach ($gallery_images as $gallery_image) {
                if ($gallery_image && $gallery_image->isValid()) {
                    $gallery_filename = \Fickrr\Helpers\SecureUpload::safeFilename(
                        \Fickrr\Helpers\SecureUpload::extension($gallery_image)
                    );
                    $gallery_image->move(public_path('storage/landing-pages'), $gallery_filename);
                    
                    LandingPage::saveGalleryImage([
                        'lp_id' => $request->lp_id,
                        'lpg_image' => $gallery_filename,
                        'lpg_description' => null,
                        'lpg_order' => $order++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        Session::flash('success', 'Landing page updated successfully!');
        return redirect()->route('admin.edit-landing-page', $request->lp_id);
    }

    /**
     * Duplicate landing page
     */
    public function duplicate($id)
    {
        $landing_page = LandingPage::getById($id);
        
        if (!$landing_page) {
            Session::flash('error', 'Landing page not found');
            return redirect()->route('admin.landing-pages');
        }

        // Generate new title and slug
        $new_title = 'Copy of ' . $landing_page->lp_title;
        $new_slug = LandingPage::ensureUniqueSlug(
            LandingPage::normalizeSlug($landing_page->lp_slug . '-copy')
        );

        // Copy banner image
        $new_banner_image = null;
        if ($landing_page->lp_banner_image && file_exists(public_path('storage/landing-pages/' . $landing_page->lp_banner_image))) {
            $extension = pathinfo($landing_page->lp_banner_image, PATHINFO_EXTENSION);
            $new_banner_image = time() . '_' . rand(1000, 9999) . '_copy.' . $extension;
            File::copy(
                public_path('storage/landing-pages/' . $landing_page->lp_banner_image),
                public_path('storage/landing-pages/' . $new_banner_image)
            );
        }

        $new_product_video = null;
        if (!empty($landing_page->lp_product_video) && file_exists(public_path('storage/landing-pages/' . $landing_page->lp_product_video))) {
            $extension = pathinfo($landing_page->lp_product_video, PATHINFO_EXTENSION);
            $new_product_video = time() . '_' . rand(1000, 9999) . '_copy_video.' . $extension;
            File::copy(
                public_path('storage/landing-pages/' . $landing_page->lp_product_video),
                public_path('storage/landing-pages/' . $new_product_video)
            );
        }

        // Copy product file/folder
        $new_product_file = null;
        $product_file_type = $landing_page->lp_product_file_type ?? 'file';
        if ($landing_page->lp_product_file && $landing_page->lp_delivery_method == 'upload') {
            if ($product_file_type == 'folder') {
                // Copy folder
                $new_folder_name = 'landing_product_' . time() . '_copy';
                $source_folder = public_path('storage/landing-products/' . $landing_page->lp_product_file);
                $dest_folder = public_path('storage/landing-products/' . $new_folder_name);
                
                if (file_exists($source_folder)) {
                    File::copyDirectory($source_folder, $dest_folder);
                    $new_product_file = $new_folder_name;
                }
            } else {
                // Copy single file
                $extension = pathinfo($landing_page->lp_product_file, PATHINFO_EXTENSION);
                $new_product_file = time() . '_' . rand(1000, 9999) . '_copy.' . $extension;
                if (file_exists(public_path('storage/landing-products/' . $landing_page->lp_product_file))) {
                    File::copy(
                        public_path('storage/landing-products/' . $landing_page->lp_product_file),
                        public_path('storage/landing-products/' . $new_product_file)
                    );
                }
            }
        }

        // Prepare data for new landing page
        $data = [
            'lp_title' => $new_title,
            'lp_slug' => $new_slug,
            'lp_tagline' => $landing_page->lp_tagline ?? null,
            'lp_cta_text' => $landing_page->lp_cta_text ?? null,
            'lp_accent_color' => $landing_page->lp_accent_color ?? null,
            'lp_description' => $landing_page->lp_description,
            'lp_about_description' => $landing_page->lp_about_description ?? null,
            'lp_what_you_get' => $landing_page->lp_what_you_get,
            'lp_banner_image' => $new_banner_image,
            'lp_product_video' => $new_product_video,
            'lp_features' => $landing_page->lp_features,
            'lp_youtube_url' => $landing_page->lp_youtube_url,
            'lp_price' => $landing_page->lp_price,
            'lp_extended_price' => $landing_page->lp_extended_price,
            'lp_currency' => $landing_page->lp_currency ?? 'USD',
            'lp_meta_title' => $landing_page->lp_meta_title,
            'lp_meta_description' => $landing_page->lp_meta_description,
            'lp_meta_keywords' => $landing_page->lp_meta_keywords,
            'lp_status' => 0, // Set to inactive by default
            'lp_ads_status' => 'off',
            'lp_product_file' => $new_product_file,
            'lp_product_file_type' => $product_file_type,
            'lp_product_link' => $landing_page->lp_product_link,
            'lp_delivery_method' => $landing_page->lp_delivery_method ?? 'upload',
            'lp_product_kind' => $landing_page->lp_product_kind ?? 'script',
            'lp_content_language' => $landing_page->lp_content_language ?? 'en',
            'lp_target_country' => $landing_page->lp_target_country ?? null,
            'lp_source_url' => $landing_page->lp_source_url ?? null,
            'lp_product_id' => $landing_page->lp_product_id ?? null,
            'lp_reviews' => $landing_page->lp_reviews ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        LandingPage::saveLandingPage($data);

        // Get the last inserted ID
        $new_landing_page_id = \DB::getPdo()->lastInsertId();

        // Copy gallery images
        $gallery_images = LandingPage::getGalleryImages($id);
        $order = 0;
        foreach ($gallery_images as $image) {
            if (file_exists(public_path('storage/landing-pages/' . $image->lpg_image))) {
                $extension = pathinfo($image->lpg_image, PATHINFO_EXTENSION);
                $new_gallery_filename = time() . '_' . rand(1000, 9999) . '_copy_' . $order . '.' . $extension;
                File::copy(
                    public_path('storage/landing-pages/' . $image->lpg_image),
                    public_path('storage/landing-pages/' . $new_gallery_filename)
                );
                
                LandingPage::saveGalleryImage([
                    'lp_id' => $new_landing_page_id,
                    'lpg_image' => $new_gallery_filename,
                    'lpg_description' => $image->lpg_description,
                    'lpg_order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Session::flash('success', 'Landing page duplicated successfully!');
        return redirect()->route('admin.landing-pages');
    }

    /**
     * Delete landing page
     */
    public function delete($id)
    {
        if (!$this->destroyLandingPage($id)) {
            Session::flash('error', 'Landing page not found');
            return redirect()->route('admin.landing-pages');
        }

        Session::flash('success', 'Landing page deleted successfully!');
        return redirect()->route('admin.landing-pages');
    }

    /**
     * Delete multiple landing pages at once.
     */
    public function bulkDelete(Request $request)
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', (array) $request->input('ids', [])))));

        if (empty($ids)) {
            Session::flash('error', 'Please select at least one landing page');
            return redirect()->route('admin.landing-pages');
        }

        $deleted = 0;
        foreach ($ids as $id) {
            if ($this->destroyLandingPage($id)) {
                $deleted++;
            }
        }

        if ($deleted === 0) {
            Session::flash('error', 'No landing pages were deleted');
        } else {
            Session::flash('success', $deleted . ' landing page(s) deleted successfully!');
        }

        return redirect()->route('admin.landing-pages');
    }

    /**
     * Remove one landing page and its stored assets.
     */
    protected function destroyLandingPage($id): bool
    {
        $landing_page = LandingPage::getById($id);

        if (!$landing_page) {
            return false;
        }

        if ($landing_page->lp_banner_image && file_exists(public_path('storage/landing-pages/' . $landing_page->lp_banner_image))) {
            File::delete(public_path('storage/landing-pages/' . $landing_page->lp_banner_image));
        }
        if (!empty($landing_page->lp_product_video)) {
            $this->deleteProductVideoFile($landing_page->lp_product_video);
        }

        $gallery_images = LandingPage::getGalleryImages($id);
        foreach ($gallery_images as $image) {
            if (file_exists(public_path('storage/landing-pages/' . $image->lpg_image))) {
                File::delete(public_path('storage/landing-pages/' . $image->lpg_image));
            }
        }

        LandingPage::deleteLandingPage($id);

        return true;
    }

    /**
     * Delete gallery image
     */
    public function deleteGalleryImage($lpg_id)
    {
        $image = \DB::table('landing_page_gallery')->where('lpg_id', $lpg_id)->first();
        
        if ($image) {
            if (file_exists(public_path('storage/landing-pages/' . $image->lpg_image))) {
                File::delete(public_path('storage/landing-pages/' . $image->lpg_image));
            }
            LandingPage::deleteGalleryImage($lpg_id);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Display all landing page messages
     */
    public function messages()
    {
        $data['messages'] = LandingPageMessage::getAllMessages();
        $data['unread_count'] = LandingPageMessage::getUnreadCount();
        $sid = 1;
        $data['setting'] = Settings::editGeneral($sid);
        
        return view('admin.landing-pages.messages', $data);
    }

    /**
     * Mark message as read
     */
    public function markAsRead($lpm_id)
    {
        LandingPageMessage::updateStatus($lpm_id, 1);
        Session::flash('success', 'Message marked as read');
        return redirect()->back();
    }

    /**
     * Mark message as replied
     */
    public function markAsReplied($lpm_id)
    {
        LandingPageMessage::updateStatus($lpm_id, 2);
        Session::flash('success', 'Message marked as replied');
        return redirect()->back();
    }

    /**
     * Delete message
     */
    public function deleteMessage($lpm_id)
    {
        LandingPageMessage::deleteMessage($lpm_id);
        Session::flash('success', 'Message deleted successfully');
        return redirect()->back();
    }

    /**
     * Delete multiple messages
     */
    public function deleteMultipleMessages(Request $request)
    {
        $message_ids = $request->input('message_ids', []);
        
        if (empty($message_ids)) {
            Session::flash('error', 'Please select at least one message');
            return redirect()->back();
        }

        foreach ($message_ids as $lpm_id) {
            LandingPageMessage::deleteMessage($lpm_id);
        }

        Session::flash('success', 'Messages deleted successfully');
        return redirect()->back();
    }

    /**
     * Landing page analytics dashboard
     */
    public function analytics(Request $request, $lp_id)
    {
        $lp_id = (int) $lp_id;
        $landingPage = LandingPage::getById($lp_id);
        if (!$landingPage) {
            Session::flash('error', 'Landing page not found');
            return redirect()->route('admin.landing-pages');
        }

        $range = $this->resolveAnalyticsRange($request);
        $country = $this->parseAnalyticsCountryFilter($request);
        $analytics = $this->buildLandingPageAnalytics($lp_id, $range['from'], $range['days'], $country, $range['to']);
        $analytics['range'] = $range;
        $analytics['range_params'] = $range['params'];
        $analytics['landing_page'] = $landingPage;

        $sid = 1;
        $analytics['setting'] = Settings::editGeneral($sid);

        return view('admin.landing-pages.analytics', $analytics);
    }

    /**
     * Live visitor stats for polling (JSON).
     */
    public function analyticsLive(Request $request, $lp_id)
    {
        $lp_id = (int) $lp_id;
        $landingPage = LandingPage::getById($lp_id);
        if (!$landingPage) {
            return response()->json(['success' => false, 'message' => 'Landing page not found'], 404);
        }

        $country = $this->parseAnalyticsCountryFilter($request);

        return response()->json([
            'success' => true,
            'data' => $this->getLiveVisitorStats($lp_id, $country),
        ]);
    }

    /**
     * Resolve the analytics period from the request: a preset (?days=N) or a
     * custom inclusive range (?from=Y-m-d&to=Y-m-d, single day when equal or
     * when only one is given). Dates are interpreted in the app timezone.
     */
    public function resolveAnalyticsRange(Request $request): array
    {
        $tz = config('app.timezone') ?: 'UTC';
        $fromRaw = trim((string) $request->get('from', ''));
        $toRaw = trim((string) $request->get('to', ''));
        $error = '';
        $daysRaw = (string) $request->get('days', '');
        if ($fromRaw === '' && $toRaw === '' && in_array($daysRaw, ['today', 'yesterday'], true)) {
            $fromRaw = $toRaw = now($tz)->subDays($daysRaw === 'yesterday' ? 1 : 0)->format('Y-m-d');
        }

        if ($fromRaw !== '' || $toRaw !== '') {
            $parse = function ($v) use ($tz) {
                if ($v === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
                    return null;
                }
                try {
                    $d = \Carbon\Carbon::createFromFormat('!Y-m-d', $v, $tz);
                } catch (\Throwable $e) {
                    return null;
                }
                return ($d && $d->format('Y-m-d') === $v) ? $d : null;
            };
            $f = $parse($fromRaw);
            $t = $parse($toRaw);
            if (($fromRaw !== '' && !$f) || ($toRaw !== '' && !$t)) {
                $error = __('Invalid date. Showing the last 30 days instead.');
            } else {
                $f = $f ?: $t->copy();
                $t = $t ?: $f->copy();
                if ($f->gt($t)) {
                    $error = __('The start date must be on or before the end date. Showing the last 30 days instead.');
                } elseif ($f->diffInDays($t) > 730) {
                    $error = __('Please choose a range of at most 2 years. Showing the last 30 days instead.');
                } else {
                    $from = $f->copy()->startOfDay();
                    $to = $t->copy()->endOfDay();
                    $days = (int) $f->diffInDays($t) + 1;
                    $single = $f->isSameDay($t);
                    return [
                        'mode' => 'custom',
                        'from' => $from,
                        'to' => $to,
                        'days' => $days,
                        'from_date' => $f->format('Y-m-d'),
                        'to_date' => $t->format('Y-m-d'),
                        'single_day' => $single,
                        'label' => $single ? $f->format('M j, Y') : $f->format('M j, Y') . ' – ' . $t->format('M j, Y'),
                        'error' => '',
                        'params' => ['from' => $f->format('Y-m-d'), 'to' => $t->format('Y-m-d')],
                    ];
                }
            }
        }

        $days = $error === '' ? (int) $request->get('days', 30) : 30;
        if ($days <= 0) {
            $days = 30;
        }
        if ($days > 365) {
            $days = 365;
        }
        $now = now($tz);

        return [
            'mode' => 'preset',
            'from' => $now->copy()->subDays($days),
            'to' => $now,
            'days' => $days,
            'from_date' => $now->copy()->subDays($days)->format('Y-m-d'),
            'to_date' => $now->format('Y-m-d'),
            'single_day' => false,
            'label' => __('Last') . ' ' . $days . ' ' . __('days'),
            'error' => $error,
            'params' => ['days' => $days],
        ];
    }

    /**
     * Normalize optional country filter from query string (ISO 3166-1 alpha-2).
     */
    protected function parseAnalyticsCountryFilter(Request $request): string
    {
        $country = strtoupper(trim((string) $request->get('country', '')));
        if ($country !== '' && !preg_match('/^[A-Z]{2}$/', $country)) {
            return '';
        }

        return $country;
    }

    /**
     * Scope a visit query to a single country when a filter is active.
     */
    protected function applyAnalyticsCountryToVisits($query, string $country, string $column = 'lpv_country_code')
    {
        if ($country !== '') {
            $query->where($column, $country);
        }

        return $query;
    }

    /**
     * Base query for landing page events, optionally scoped by visitor country.
     */
    protected function landingPageEventsQuery(int $lp_id, $from, string $country = '', $to = null)
    {
        $query = DB::table('landing_page_events')
            ->where('landing_page_events.lp_id', $lp_id)
            ->whereBetween('landing_page_events.created_at', [$from, $to ?: now()]);

        if ($country !== '') {
            $query->join(
                'landing_page_visits',
                'landing_page_events.lpv_id',
                '=',
                'landing_page_visits.lpv_id'
            )->where('landing_page_visits.lpv_country_code', $country);
        }

        return $query;
    }

    /**
     * Base query for completed orders, optionally scoped by purchase visit country.
     */
    protected function landingPageOrdersQuery(int $lp_id, $from, string $country = '', $to = null)
    {
        $query = DB::table('landing_page_orders')
            ->where('landing_page_orders.lp_id', $lp_id)
            ->whereBetween('landing_page_orders.created_at', [$from, $to ?: now()])
            ->where('landing_page_orders.payment_status', 'completed');

        if ($country !== '') {
            $query->whereExists(function ($sub) use ($lp_id, $country) {
                $sub->select(DB::raw(1))
                    ->from('landing_page_events as lpe')
                    ->join('landing_page_visits as lpv', 'lpe.lpv_id', '=', 'lpv.lpv_id')
                    ->whereColumn(
                        DB::raw("JSON_UNQUOTE(JSON_EXTRACT(lpe.lpe_properties, '$.order_id'))"),
                        'landing_page_orders.order_token'
                    )
                    ->where('lpe.lp_id', $lp_id)
                    ->where('lpe.lpe_name', 'purchase_completed')
                    ->where('lpv.lpv_country_code', $country);
            });
        }

        return $query;
    }

    /**
     * Aggregate analytics metrics for a landing page over a date range.
     */
    protected function buildLandingPageAnalytics(int $lp_id, $from, int $days, string $country = '', $to = null): array
    {
        $to = $to ?: now();
        $visitsBase = DB::table('landing_page_visits')
            ->where('lp_id', $lp_id)
            ->whereBetween('created_at', [$from, $to]);
        $this->applyAnalyticsCountryToVisits($visitsBase, $country);

        $totalVisits = (clone $visitsBase)->count();
        $uniqueVisitors = (clone $visitsBase)->distinct('lpv_visitor_id')->count('lpv_visitor_id');
        $pageviews = (int) ((clone $visitsBase)->sum('lpv_pageviews') ?? 0);
        $bounceVisits = (clone $visitsBase)->where('lpv_pageviews', '<=', 1)->count();

        $ordersBase = $this->landingPageOrdersQuery($lp_id, $from, $country, $to);

        $completedOrders = (clone $ordersBase)->count();
        $revenueTotal = (float) ((clone $ordersBase)->sum('landing_page_orders.amount') ?? 0);

        $eventsQuery = $this->landingPageEventsQuery($lp_id, $from, $country, $to);

        $pageViewsCount = (int) (clone $eventsQuery)
            ->where('landing_page_events.lpe_name', 'page_view')
            ->count();

        $purchaseEvents = (int) (clone $eventsQuery)
            ->where('landing_page_events.lpe_name', 'purchase_completed')
            ->count();

        $checkoutStarts = (int) (clone $eventsQuery)
            ->where('landing_page_events.lpe_name', 'initiate_checkout')
            ->count();

        $buyClicks = (int) (clone $eventsQuery)
            ->where('landing_page_events.lpe_name', 'buy_now_click')
            ->count();

        $contactSubmissions = (int) (clone $eventsQuery)
            ->where('landing_page_events.lpe_name', 'chat_message_submitted')
            ->count();

        $contactSent = (int) (clone $eventsQuery)
            ->where('landing_page_events.lpe_name', 'chat_message_sent')
            ->count();

        $conversionRate = $uniqueVisitors > 0
            ? round(($completedOrders / $uniqueVisitors) * 100, 2)
            : 0;

        $avgPagesPerVisit = $totalVisits > 0 ? round($pageviews / $totalVisits, 2) : 0;
        $bounceRate = $totalVisits > 0 ? round(($bounceVisits / $totalVisits) * 100, 1) : 0;

        $dailyTrends = DB::table('landing_page_visits')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as visits'),
                DB::raw('COUNT(DISTINCT lpv_visitor_id) as unique_visitors'),
                DB::raw('COALESCE(SUM(lpv_pageviews), 0) as pageviews')
            )
            ->where('lp_id', $lp_id)
            ->whereBetween('created_at', [$from, $to]);
        $this->applyAnalyticsCountryToVisits($dailyTrends, $country);
        $dailyTrends = $dailyTrends
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $dailyOrders = (clone $this->landingPageOrdersQuery($lp_id, $from, $country, $to))
            ->select(
                DB::raw('DATE(landing_page_orders.created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('COALESCE(SUM(landing_page_orders.amount), 0) as revenue')
            )
            ->groupBy(DB::raw('DATE(landing_page_orders.created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $dailyChart = [];
        foreach ($dailyTrends as $row) {
            $orderRow = $dailyOrders->get($row->date);
            $dailyChart[] = [
                'date' => $row->date,
                'visits' => (int) $row->visits,
                'unique_visitors' => (int) $row->unique_visitors,
                'pageviews' => (int) $row->pageviews,
                'orders' => $orderRow ? (int) $orderRow->orders : 0,
                'revenue' => $orderRow ? (float) $orderRow->revenue : 0,
            ];
        }

        $funnelSteps = [
            ['key' => 'page_view', 'label' => 'Page Views', 'count' => $pageViewsCount],
            ['key' => 'scroll_depth', 'label' => 'Scrolled 25%+', 'count' => 0],
            ['key' => 'buy_now_click', 'label' => 'Buy Now Clicks', 'count' => $buyClicks],
            ['key' => 'initiate_checkout', 'label' => 'Checkout Started', 'count' => $checkoutStarts],
            ['key' => 'purchase_completed', 'label' => 'Purchases', 'count' => max($purchaseEvents, $completedOrders)],
        ];

        $scrollDepthCounts = ['25' => 0, '50' => 0, '75' => 0, '90' => 0];
        $durationTotal = 0;
        $durationCount = 0;

        $propertyEvents = (clone $this->landingPageEventsQuery($lp_id, $from, $country, $to))
            ->select('landing_page_events.lpe_name', 'landing_page_events.lpe_properties')
            ->whereIn('landing_page_events.lpe_name', ['scroll_depth', 'page_exit'])
            ->get();

        foreach ($propertyEvents as $event) {
            $props = json_decode((string) $event->lpe_properties, true);
            if (!is_array($props)) {
                continue;
            }

            if ($event->lpe_name === 'scroll_depth' && isset($props['percent'])) {
                $pct = (string) $props['percent'];
                if (isset($scrollDepthCounts[$pct])) {
                    $scrollDepthCounts[$pct]++;
                }
            }

            if ($event->lpe_name === 'page_exit' && isset($props['duration_ms'])) {
                $durationTotal += (int) $props['duration_ms'];
                $durationCount++;
            }
        }

        $funnelSteps[1]['count'] = $scrollDepthCounts['25'];

        $avgDurationSeconds = $durationCount > 0
            ? (int) round(($durationTotal / $durationCount) / 1000)
            : 0;

        $countries = DB::table('landing_page_visits')
            ->select(
                'lpv_country_code',
                DB::raw('COUNT(*) as visits'),
                DB::raw('COUNT(DISTINCT lpv_visitor_id) as unique_visitors'),
                DB::raw('COALESCE(SUM(lpv_pageviews),0) as pageviews')
            )
            ->where('lp_id', $lp_id)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('lpv_country_code')
            ->where('lpv_country_code', '!=', '')
            ->groupBy('lpv_country_code')
            ->orderByDesc('unique_visitors')
            ->orderByDesc('visits')
            ->get();

        $countriesEnriched = $this->enrichCountryStats($countries);
        $availableCountries = $countriesEnriched;
        $liveStats = $this->getLiveVisitorStats($lp_id, $country);

        $utmSources = DB::table('landing_page_visits')
            ->select(
                DB::raw("COALESCE(NULLIF(lpv_utm_source, ''), '(direct / none)') as source"),
                DB::raw('COUNT(*) as visits'),
                DB::raw('COUNT(DISTINCT lpv_visitor_id) as unique_visitors')
            )
            ->where('lp_id', $lp_id)
            ->whereBetween('created_at', [$from, $to]);
        $this->applyAnalyticsCountryToVisits($utmSources, $country);
        $utmSources = $utmSources
            ->groupBy(DB::raw("COALESCE(NULLIF(lpv_utm_source, ''), '(direct / none)')"))
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        $utmCampaigns = DB::table('landing_page_visits')
            ->select(
                DB::raw("COALESCE(NULLIF(lpv_utm_campaign, ''), '(none)') as campaign"),
                DB::raw("COALESCE(NULLIF(lpv_utm_medium, ''), '(none)') as medium"),
                DB::raw('COUNT(*) as visits')
            )
            ->where('lp_id', $lp_id)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('lpv_utm_campaign')
            ->where('lpv_utm_campaign', '!=', '');
        $this->applyAnalyticsCountryToVisits($utmCampaigns, $country);
        $utmCampaigns = $utmCampaigns
            ->groupBy(
                DB::raw("COALESCE(NULLIF(lpv_utm_campaign, ''), '(none)')"),
                DB::raw("COALESCE(NULLIF(lpv_utm_medium, ''), '(none)')")
            )
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        $referrers = DB::table('landing_page_visits')
            ->select(
                DB::raw("CASE WHEN lpv_referrer IS NULL OR lpv_referrer = '' THEN '(direct)' ELSE SUBSTRING(lpv_referrer, 1, 120) END as referrer"),
                DB::raw('COUNT(*) as visits'),
                DB::raw('COUNT(DISTINCT lpv_visitor_id) as unique_visitors')
            )
            ->where('lp_id', $lp_id)
            ->whereBetween('created_at', [$from, $to]);
        $this->applyAnalyticsCountryToVisits($referrers, $country);
        $referrers = $referrers
            ->groupBy(DB::raw("CASE WHEN lpv_referrer IS NULL OR lpv_referrer = '' THEN '(direct)' ELSE SUBSTRING(lpv_referrer, 1, 120) END"))
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        $devices = ['desktop' => 0, 'mobile' => 0, 'tablet' => 0, 'other' => 0];
        $userAgentsQuery = DB::table('landing_page_visits')
            ->where('lp_id', $lp_id)
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('lpv_user_agent');
        $this->applyAnalyticsCountryToVisits($userAgentsQuery, $country);
        $userAgents = $userAgentsQuery->pluck('lpv_user_agent');

        foreach ($userAgents as $ua) {
            $devices[$this->detectDeviceType((string) $ua)]++;
        }

        $events = (clone $this->landingPageEventsQuery($lp_id, $from, $country, $to))
            ->select('landing_page_events.lpe_name', DB::raw('COUNT(*) as total'))
            ->groupBy('landing_page_events.lpe_name')
            ->orderByDesc('total')
            ->get();

        $paymentMethods = (clone $this->landingPageOrdersQuery($lp_id, $from, $country, $to))
            ->select(
                'landing_page_orders.payment_method',
                DB::raw('COUNT(*) as total'),
                DB::raw('COALESCE(SUM(landing_page_orders.amount), 0) as revenue')
            )
            ->groupBy('landing_page_orders.payment_method')
            ->orderByDesc('total')
            ->get();

        $recentOrders = (clone $this->landingPageOrdersQuery($lp_id, $from, $country, $to))
            ->select(
                'landing_page_orders.customer_email',
                'landing_page_orders.amount',
                'landing_page_orders.currency',
                'landing_page_orders.payment_method',
                'landing_page_orders.completed_at',
                'landing_page_orders.created_at'
            )
            ->orderByDesc('landing_page_orders.completed_at')
            ->limit(8)
            ->get();

        $topFunnelCount = max(1, (int) ($funnelSteps[0]['count'] ?? 0));
        foreach ($funnelSteps as &$step) {
            $step['rate'] = round(((int) $step['count'] / $topFunnelCount) * 100, 1);
        }
        unset($step);

        return [
            'days' => $days,
            'from' => $from,
            'to' => $to,
            'country_filter' => $country,
            'country_filter_name' => $country !== '' ? Helper::countryNameFromCode($country) : '',
            'country_filter_flag' => $country !== '' ? Helper::countryFlagEmoji($country) : '',
            'available_countries' => $availableCountries,
            'total_visits' => $totalVisits,
            'unique_visitors' => $uniqueVisitors,
            'pageviews' => $pageviews,
            'bounce_rate' => $bounceRate,
            'avg_pages_per_visit' => $avgPagesPerVisit,
            'avg_duration_seconds' => $avgDurationSeconds,
            'completed_orders' => $completedOrders,
            'revenue_total' => $revenueTotal,
            'conversion_rate' => $conversionRate,
            'buy_clicks' => $buyClicks,
            'checkout_starts' => $checkoutStarts,
            'contact_submissions' => $contactSubmissions,
            'contact_sent' => $contactSent,
            'daily_chart' => $dailyChart,
            'funnel' => $funnelSteps,
            'scroll_depth' => $scrollDepthCounts,
            'countries' => $country !== '' ? $countriesEnriched->where('code', $country)->values() : $countriesEnriched,
            'live_visitors' => $liveStats['total'],
            'live_countries' => $liveStats['countries'],
            'live_window_minutes' => $liveStats['window_minutes'],
            'utm_sources' => $utmSources,
            'utm_campaigns' => $utmCampaigns,
            'referrers' => $referrers,
            'devices' => $devices,
            'events' => $events,
            'payment_methods' => $paymentMethods,
            'recent_orders' => $recentOrders,
            'event_labels' => $this->landingAnalyticsEventLabels(),
        ];
    }

    /**
     * Attach country names and flag emojis to visit aggregates.
     */
    protected function enrichCountryStats($countries)
    {
        return $countries->map(function ($row) {
            $code = strtoupper(trim((string) ($row->lpv_country_code ?? '')));

            return (object) [
                'code' => $code !== '' ? $code : null,
                'name' => Helper::countryNameFromCode($code),
                'flag' => Helper::countryFlagEmoji($code),
                'visits' => (int) $row->visits,
                'unique_visitors' => (int) $row->unique_visitors,
                'pageviews' => (int) $row->pageviews,
            ];
        })->values();
    }

    /**
     * Count visitors active on a landing page within the live window.
     */
    protected function getLiveVisitorStats(int $lp_id, string $country = ''): array
    {
        $windowMinutes = 5;
        $since = now()->subMinutes($windowMinutes);

        $totalQuery = DB::table('landing_page_visits')
            ->where('lp_id', $lp_id)
            ->where('lpv_last_seen_at', '>=', $since);
        $this->applyAnalyticsCountryToVisits($totalQuery, $country);
        $total = (int) $totalQuery->distinct('lpv_visitor_id')->count('lpv_visitor_id');

        $countriesQuery = DB::table('landing_page_visits')
            ->select(
                'lpv_country_code',
                DB::raw('COUNT(DISTINCT lpv_visitor_id) as live_visitors')
            )
            ->where('lp_id', $lp_id)
            ->where('lpv_last_seen_at', '>=', $since);
        $this->applyAnalyticsCountryToVisits($countriesQuery, $country);
        $countries = $countriesQuery
            ->groupBy('lpv_country_code')
            ->orderByDesc('live_visitors')
            ->get();

        $countriesEnriched = $countries->map(function ($row) {
            $code = strtoupper(trim((string) ($row->lpv_country_code ?? '')));

            return [
                'code' => $code !== '' ? $code : null,
                'name' => Helper::countryNameFromCode($code),
                'flag' => Helper::countryFlagEmoji($code),
                'live_visitors' => (int) $row->live_visitors,
            ];
        })->values();

        return [
            'total' => $total,
            'countries' => $countriesEnriched,
            'window_minutes' => $windowMinutes,
            'updated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Detect device category from a user agent string.
     */
    protected function detectDeviceType(string $userAgent): string
    {
        $ua = strtolower($userAgent);

        if (strpos($ua, 'ipad') !== false || (strpos($ua, 'tablet') !== false && strpos($ua, 'mobile') === false)) {
            return 'tablet';
        }

        if (strpos($ua, 'mobile') !== false || strpos($ua, 'iphone') !== false || strpos($ua, 'android') !== false) {
            return 'mobile';
        }

        if (strpos($ua, 'windows') !== false || strpos($ua, 'macintosh') !== false || strpos($ua, 'linux') !== false) {
            return 'desktop';
        }

        return 'other';
    }

    /**
     * Human-readable labels for tracked landing page events.
     */
    protected function landingAnalyticsEventLabels(): array
    {
        return [
            'page_view' => 'Page View',
            'scroll_depth' => 'Scroll Depth',
            'buy_now_click' => 'Buy Now Click',
            'payment_modal_opened' => 'Payment Modal Opened',
            'payment_method_selected' => 'Payment Method Selected',
            'initiate_checkout' => 'Checkout Started',
            'chat_message_submitted' => 'Chat Message Submitted',
            'chat_message_sent' => 'Chat Message Sent',
            'purchase_completed' => 'Purchase Completed',
            'page_exit' => 'Page Exit',
        ];
    }

    /**
     * Generate landing page copy via OpenAI from title & About This Product.
     * Image generation is a separate request to avoid shared-host proxy timeouts.
     */
    public function generateWithAI(Request $request)
    {
        @set_time_limit(120);

        $languageCodes = array_keys(\Fickrr\Services\OpenAIService::contentLanguages());

        // Prefer About This Product; fall back to legacy lp_description for older clients.
        $aboutSource = $request->input('lp_about_description') ?: $request->input('lp_description');
        $aboutPlain = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $aboutSource)));
        $request->merge(['lp_about_description' => $aboutPlain]);

        $validator = Validator::make($request->all(), [
            'lp_title' => 'required|string|max:255',
            'lp_about_description' => 'required|string|min:10',
            'language' => 'nullable|string|in:' . implode(',', $languageCodes),
            'product_kind' => 'nullable|in:script,sheet,product',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        $service = new \Fickrr\Services\OpenAIService();

        if (!$service->isEnabled()) {
            return response()->json([
                'success' => false,
                'error' => 'OpenAI is not enabled. Configure it under Admin → AI API Connection.',
            ], 400);
        }

        // Always skip image here — frontend requests the banner in a second call.
        $result = $service->generateLandingPage(
            $request->input('lp_title'),
            $aboutPlain,
            false,
            (string) $request->input('language', 'en'),
            $request->input('product_kind') === 'sheet' ? 'sheet' : ($request->input('product_kind') === 'product' ? 'product' : 'script')
        );

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'],
        ]);
    }

    /**
     * Generate a landing-page banner image from an AI image prompt.
     */
    public function generateAiImage(Request $request)
    {
        @set_time_limit(180);

        $validator = Validator::make($request->all(), [
            'lp_title' => 'required|string|max:255',
            'image_prompt' => 'required|string|min:10|max:4000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        $service = new \Fickrr\Services\OpenAIService();

        if (!$service->isEnabled()) {
            return response()->json([
                'success' => false,
                'error' => 'OpenAI is not enabled. Configure it under Admin → AI API Connection.',
            ], 400);
        }

        $filename = $service->generateAndSaveImage(
            $request->input('image_prompt'),
            $request->input('lp_title')
        );

        if (!$filename) {
            return response()->json([
                'success' => false,
                'error' => 'Banner image could not be generated. Copy was still applied — you can upload a banner manually.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'banner_image' => $filename,
                'banner_url' => asset('storage/landing-pages/' . $filename),
            ],
        ]);
    }

    /**
     * @return string|null|\Illuminate\Http\RedirectResponse
     */
    protected function storeProductVideo(Request $request)
    {
        if (!$request->hasFile('lp_product_video')) {
            return null;
        }

        $file = $request->file('lp_product_video');
        $error = \Fickrr\Helpers\SecureUpload::validateVideo($file, 51200);
        if ($error !== null) {
            return redirect()->back()->with('error', $error)->withInput();
        }

        $filename = \Fickrr\Helpers\SecureUpload::safeFilename(\Fickrr\Helpers\SecureUpload::extension($file));
        $dir = public_path('storage/landing-pages');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $file->move($dir, $filename);

        return $filename;
    }

    protected function deleteProductVideoFile($filename): void
    {
        $filename = basename((string) $filename);
        if ($filename === '') {
            return;
        }
        $path = public_path('storage/landing-pages/' . $filename);
        if (file_exists($path)) {
            File::delete($path);
        }
    }

    protected function normalizeProductKind($kind): string
    {
        if (\Fickrr\Support\Store::isPhysical()) {
            return 'physical';
        }

        $kind = strtolower(trim((string) $kind));
        if (in_array($kind, ['sheet', 'product'], true)) {
            return $kind;
        }

        return 'script';
    }

    protected function normalizeSourceUrl($url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        return $url;
    }

    protected function normalizeContentLanguage($language): string
    {
        return \Fickrr\Helpers\LandingPageI18n::normalizeLocale((string) $language);
    }

    protected function resolveContentLanguage(Request $request): string
    {
        $lang = $this->normalizeContentLanguage(
            $request->input('lp_content_language') ?: $request->input('ai_content_language')
        );

        if ($lang !== 'en') {
            return $lang;
        }

        $probe = (object) [
            'lp_title' => $request->input('lp_title'),
            'lp_tagline' => $request->input('lp_tagline'),
            'lp_cta_text' => $request->input('lp_cta_text'),
            'lp_description' => $request->input('lp_description'),
            'lp_about_description' => $request->input('lp_about_description'),
        ];

        $inferred = \Fickrr\Helpers\LandingPageI18n::inferLocaleFromCopy($probe);
        if ($inferred !== null) {
            return $inferred;
        }

        return $lang;
    }

    protected function validateLandingSlug(?string $raw): ?string
    {
        $slug = LandingPage::normalizeSlug($raw);

        if ($slug === '') {
            return __('Please enter a URL slug using English letters and hyphens.');
        }

        if (!preg_match('/[a-z]/', $slug)) {
            return __('The URL slug must contain at least one English letter (not numbers only).');
        }

        $reserved = ['track', 'success', 'download', 'checkout-embed', 'add-to-cart', 'buy-now'];
        if (in_array($slug, $reserved, true)) {
            return __('This URL slug is reserved. Please choose another.');
        }

        return null;
    }

    protected function encodeReviewsInput($raw): ?string
    {
        if (is_array($raw)) {
            return json_encode($raw);
        }

        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return null;
        }

        return json_encode($decoded);
    }

    protected function catalogProducts()
    {
        if (!\Fickrr\Support\Store::isPhysical()) {
            return collect();
        }

        return \Fickrr\Services\Physical\PhysicalCatalog::publishedProducts();
    }

    protected function publishedCatalogId(Request $request): ?string
    {
        $id = (int) $request->input('lp_product_id');
        if ($id < 1 || !\Fickrr\Support\Store::isPhysical()) {
            return null;
        }

        $match = $this->catalogProducts()->first(function ($item) use ($id) {
            return (int) $item->item_id === $id;
        });

        return $match ? (string) $match->item_id : null;
    }
}

