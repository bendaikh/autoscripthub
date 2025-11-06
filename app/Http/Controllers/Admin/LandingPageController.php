<?php

namespace Fickrr\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\LandingPage;
use Fickrr\Models\Settings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Storage;
use Session;

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
            $banner_image = time() . '_' . $image->getClientOriginalName();
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
                
                if (is_array($files) && count($files) > 1) {
                    // Multiple files - folder upload
                    $product_file_type = 'folder';
                    $folderName = 'landing_product_' . time();
                    $folderPath = public_path('storage/landing-products/' . $folderName);
                    
                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0755, true);
                    }
                    
                    foreach ($files as $file) {
                        $fileName = $file->getClientOriginalName();
                        $file->move($folderPath, $fileName);
                    }
                    
                    $product_file = $folderName;
                } else {
                    // Single file upload
                    $product_file_type = 'file';
                    $file = is_array($files) ? $files[0] : $files;
                    $product_file = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('storage/landing-products'), $product_file);
                }
            }
        }

        $data = [
            'lp_title' => $request->lp_title,
            'lp_slug' => $slug,
            'lp_description' => $request->lp_description,
            'lp_banner_image' => $banner_image,
            'lp_features' => json_encode($features),
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

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            $order = 0;
            foreach ($request->file('gallery_images') as $gallery_image) {
                $gallery_filename = time() . '_' . rand(1000, 9999) . '_' . $gallery_image->getClientOriginalName();
                $gallery_image->move(public_path('storage/landing-pages'), $gallery_filename);
                
                LandingPage::saveGalleryImage([
                    'lp_id' => $landing_page_id,
                    'lpg_image' => $gallery_filename,
                    'lpg_order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
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
            $banner_image = time() . '_' . $image->getClientOriginalName();
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
                
                if (is_array($files) && count($files) > 1) {
                    // Multiple files - folder upload
                    $product_file_type = 'folder';
                    $folderName = 'landing_product_' . time();
                    $folderPath = public_path('storage/landing-products/' . $folderName);
                    
                    if (!file_exists($folderPath)) {
                        mkdir($folderPath, 0755, true);
                    }
                    
                    foreach ($files as $file) {
                        $fileName = $file->getClientOriginalName();
                        $file->move($folderPath, $fileName);
                    }
                    
                    $product_file = $folderName;
                } else {
                    // Single file upload
                    $product_file_type = 'file';
                    $file = is_array($files) ? $files[0] : $files;
                    $product_file = time() . '_' . $file->getClientOriginalName();
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
            'lp_banner_image' => $banner_image,
            'lp_features' => json_encode($features),
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

        // Handle new gallery images
        if ($request->hasFile('gallery_images')) {
            $existing_images = LandingPage::getGalleryImages($request->lp_id);
            $order = count($existing_images);
            
            foreach ($request->file('gallery_images') as $gallery_image) {
                $gallery_filename = time() . '_' . rand(1000, 9999) . '_' . $gallery_image->getClientOriginalName();
                $gallery_image->move(public_path('storage/landing-pages'), $gallery_filename);
                
                LandingPage::saveGalleryImage([
                    'lp_id' => $request->lp_id,
                    'lpg_image' => $gallery_filename,
                    'lpg_order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Session::flash('success', 'Landing page updated successfully!');
        return redirect()->route('admin.edit-landing-page', $request->lp_id);
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
}

