<?php

namespace Fickrr\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\LandingPixel;
use Fickrr\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LandingPixelController extends Controller
{
    public function index()
    {
        $sid = 1;
        $data['setting'] = Settings::editGeneral($sid);
        $data['pixels'] = LandingPixel::allPixels();
        $data['facebook_count'] = $data['pixels']->where('platform', 'facebook')->count();
        $data['tiktok_count'] = $data['pixels']->where('platform', 'tiktok')->count();

        return view('admin.landing-pages.pixels', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|in:facebook,tiktok',
            'pixel_id' => 'required|max:80',
            'label' => 'nullable|max:120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $pixelId = preg_replace('/\s+/', '', (string) $request->pixel_id);

        if ($request->platform === 'facebook' && !preg_match('/^\d{5,20}$/', $pixelId)) {
            return redirect()->back()
                ->with('error', __('Enter a valid Facebook Pixel ID (numbers only).'))
                ->withInput();
        }

        if ($request->platform === 'tiktok' && !preg_match('/^[A-Za-z0-9]{6,40}$/', $pixelId)) {
            return redirect()->back()
                ->with('error', __('Enter a valid TikTok Pixel ID.'))
                ->withInput();
        }

        $exists = DB::table('landing_pixels')
            ->where('platform', $request->platform)
            ->where('pixel_id', $pixelId)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', __('This pixel is already connected.'))
                ->withInput();
        }

        DB::table('landing_pixels')->insert([
            'platform' => $request->platform,
            'pixel_id' => $pixelId,
            'label' => $request->label ? trim($request->label) : null,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.landing-pages.pixels')
            ->with('success', __('Pixel connected. Landing pages will now track with this pixel.'));
    }

    public function toggle($id)
    {
        $pixel = DB::table('landing_pixels')->where('id', $id)->first();
        if (!$pixel) {
            return redirect()->route('admin.landing-pages.pixels')->with('error', __('Pixel not found.'));
        }

        DB::table('landing_pixels')->where('id', $id)->update([
            'is_active' => $pixel->is_active ? 0 : 1,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.landing-pages.pixels')
            ->with('success', $pixel->is_active ? __('Pixel paused.') : __('Pixel activated.'));
    }

    public function delete($id)
    {
        DB::table('landing_pixels')->where('id', $id)->delete();

        return redirect()->route('admin.landing-pages.pixels')
            ->with('success', __('Pixel disconnected.'));
    }
}
