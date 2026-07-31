<?php

namespace Fickrr\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\LandingPage;
use Fickrr\Models\LandingPageMessage;
use Fickrr\Models\Settings;
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
        $sid = 1;
        $data['setting'] = Settings::editGeneral($sid);
        
        return view('admin.landing-pages.index', $data);
    }

    /**
     * Show form to create new landing page
     */
    public function create()
    {
        $sid = 1;
        $data['setting'] = Settings::editGeneral($sid);
        
        return view('admin.landing-pages.create', $data);
    }

    /**
     * Store new landing page
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lp_title' => 'required|max:255',
            'lp_description' => 'required',
            'lp_price' => 'required|numeric|min:0',
            'lp_banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'lp_product_file' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate unique slug
        $slug = LandingPage::generateSlug($request->lp_title);

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
        
        if ($delivery_method == 'link') {
            // Save product link
            $product_link = $request->lp_product_link;
        } else {
            // Handle product file/folder upload
            if ($request->hasFile('lp_product_file')) {
                $files = $request->file('lp_product_file');
                $productAllowed = ['zip', 'rar', '7z', 'tar', 'gz', 'pdf', 'jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'mp3'];
                
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

        $data = [
            'lp_title' => $request->lp_title,
            'lp_slug' => $slug,
            'lp_description' => $request->lp_description,
            'lp_what_you_get' => $request->lp_what_you_get,
            'lp_banner_image' => $banner_image,
            'lp_features' => json_encode($features),
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
            'created_at' => now(),
            'updated_at' => now(),
        ];

        LandingPage::saveLandingPage($data);

        // Get the last inserted ID
        $landing_page_id = \DB::getPdo()->lastInsertId();

        // Handle gallery images with descriptions
        if ($request->hasFile('gallery_images')) {
            $order = 0;
            $descriptions = $request->input('gallery_descriptions', []);
            $gallery_images = $request->file('gallery_images');
            
            foreach ($gallery_images as $index => $gallery_image) {
                if ($gallery_image && $gallery_image->isValid()) {
                    $gallery_filename = time() . '_' . rand(1000, 9999) . '_' . $gallery_image->getClientOriginalName();
                    $gallery_image->move(public_path('storage/landing-pages'), $gallery_filename);
                    
                    LandingPage::saveGalleryImage([
                        'lp_id' => $landing_page_id,
                        'lpg_image' => $gallery_filename,
                        'lpg_description' => $descriptions[$index] ?? null,
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
        
        return view('admin.landing-pages.edit', $data);
    }

    /**
     * Update landing page
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lp_id' => 'required|exists:landing_pages,lp_id',
            'lp_title' => 'required|max:255',
            'lp_description' => 'required',
            'lp_price' => 'required|numeric|min:0',
            'lp_banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'lp_product_file' => 'nullable',
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
        
        if ($delivery_method == 'link') {
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
                $productAllowed = ['zip', 'rar', '7z', 'tar', 'gz', 'pdf', 'jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'mp3'];
                
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

        // Generate slug if title changed
        $slug = $landing_page->lp_slug;
        if ($request->lp_title !== $landing_page->lp_title) {
            $slug = Str::slug($request->lp_title);
            if (LandingPage::slugExists($slug, $request->lp_id)) {
                $slug = LandingPage::generateSlug($request->lp_title);
            }
        }

        $data = [
            'lp_title' => $request->lp_title,
            'lp_slug' => $slug,
            'lp_description' => $request->lp_description,
            'lp_what_you_get' => $request->lp_what_you_get,
            'lp_banner_image' => $banner_image,
            'lp_features' => json_encode($features),
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
            'updated_at' => now(),
        ];

        LandingPage::updateLandingPage($request->lp_id, $data);

        // Update existing gallery image descriptions
        if ($request->has('existing_gallery_descriptions')) {
            foreach ($request->input('existing_gallery_descriptions') as $lpg_id => $description) {
                \DB::table('landing_page_gallery')
                    ->where('lpg_id', $lpg_id)
                    ->update([
                        'lpg_description' => $description,
                        'updated_at' => now()
                    ]);
            }
        }

        // Handle new gallery images with descriptions
        if ($request->hasFile('gallery_images')) {
            $existing_images = LandingPage::getGalleryImages($request->lp_id);
            $order = count($existing_images);
            $descriptions = $request->input('gallery_descriptions', []);
            $gallery_images = $request->file('gallery_images');
            
            foreach ($gallery_images as $index => $gallery_image) {
                if ($gallery_image && $gallery_image->isValid()) {
                    $gallery_filename = time() . '_' . rand(1000, 9999) . '_' . $gallery_image->getClientOriginalName();
                    $gallery_image->move(public_path('storage/landing-pages'), $gallery_filename);
                    
                    LandingPage::saveGalleryImage([
                        'lp_id' => $request->lp_id,
                        'lpg_image' => $gallery_filename,
                        'lpg_description' => $descriptions[$index] ?? null,
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
        $new_slug = LandingPage::generateSlug($new_title);

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
            'lp_description' => $landing_page->lp_description,
            'lp_what_you_get' => $landing_page->lp_what_you_get,
            'lp_banner_image' => $new_banner_image,
            'lp_features' => $landing_page->lp_features,
            'lp_youtube_url' => $landing_page->lp_youtube_url,
            'lp_price' => $landing_page->lp_price,
            'lp_extended_price' => $landing_page->lp_extended_price,
            'lp_currency' => $landing_page->lp_currency ?? 'USD',
            'lp_meta_title' => $landing_page->lp_meta_title,
            'lp_meta_description' => $landing_page->lp_meta_description,
            'lp_meta_keywords' => $landing_page->lp_meta_keywords,
            'lp_status' => 0, // Set to inactive by default
            'lp_product_file' => $new_product_file,
            'lp_product_file_type' => $product_file_type,
            'lp_product_link' => $landing_page->lp_product_link,
            'lp_delivery_method' => $landing_page->lp_delivery_method ?? 'upload',
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
        $landing_page = LandingPage::getById($id);
        
        if (!$landing_page) {
            Session::flash('error', 'Landing page not found');
            return redirect()->route('admin.landing-pages');
        }

        // Delete banner image
        if ($landing_page->lp_banner_image && file_exists(public_path('storage/landing-pages/' . $landing_page->lp_banner_image))) {
            File::delete(public_path('storage/landing-pages/' . $landing_page->lp_banner_image));
        }

        // Delete gallery images
        $gallery_images = LandingPage::getGalleryImages($id);
        foreach ($gallery_images as $image) {
            if (file_exists(public_path('storage/landing-pages/' . $image->lpg_image))) {
                File::delete(public_path('storage/landing-pages/' . $image->lpg_image));
            }
        }

        LandingPage::deleteLandingPage($id);

        Session::flash('success', 'Landing page deleted successfully!');
        return redirect()->route('admin.landing-pages');
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
     * Landing page analytics (countries + events summary)
     */
    public function analytics(Request $request, $lp_id)
    {
        $lp_id = (int) $lp_id;
        $landingPage = LandingPage::getById($lp_id);
        if (!$landingPage) {
            Session::flash('error', 'Landing page not found');
            return redirect()->route('admin.landing-pages');
        }

        $days = (int) $request->get('days', 30);
        if ($days <= 0) {
            $days = 30;
        }
        if ($days > 365) {
            $days = 365;
        }

        $from = now()->subDays($days);

        $visitsBase = DB::table('landing_page_visits')
            ->where('lp_id', $lp_id)
            ->where('created_at', '>=', $from);

        $data['landing_page'] = $landingPage;
        $data['days'] = $days;
        $data['from'] = $from;

        $data['total_visits'] = (clone $visitsBase)->count();
        $data['unique_visitors'] = (clone $visitsBase)->distinct('lpv_visitor_id')->count('lpv_visitor_id');
        $data['pageviews'] = (int) ((clone $visitsBase)->sum('lpv_pageviews') ?? 0);

        $data['countries'] = DB::table('landing_page_visits')
            ->select(
                'lpv_country_code',
                DB::raw('COUNT(*) as visits'),
                DB::raw('COUNT(DISTINCT lpv_visitor_id) as unique_visitors'),
                DB::raw('COALESCE(SUM(lpv_pageviews),0) as pageviews')
            )
            ->where('lp_id', $lp_id)
            ->where('created_at', '>=', $from)
            ->groupBy('lpv_country_code')
            ->orderByDesc('visits')
            ->get();

        $data['events'] = DB::table('landing_page_events')
            ->select('lpe_name', DB::raw('COUNT(*) as total'))
            ->where('lp_id', $lp_id)
            ->where('created_at', '>=', $from)
            ->groupBy('lpe_name')
            ->orderByDesc('total')
            ->get();

        $sid = 1;
        $data['setting'] = Settings::editGeneral($sid);

        return view('admin.landing-pages.analytics', $data);
    }
}

