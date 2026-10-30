<?php

namespace Fickrr\Http\Controllers\Admin;

use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\LandingPage;
use Fickrr\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Session;

class LandingPageAdCostController extends Controller
{
    public function index()
    {
        $data['landing_pages'] = LandingPage::getAllLandingPages();
        $data['countries'] = Helper::countryList();
        $data['entries'] = DB::table('landing_page_ad_costs as c')
            ->join('landing_pages as p', 'p.lp_id', '=', 'c.lp_id')
            ->select('c.*', 'p.lp_title')
            ->orderByDesc('c.lpac_date')
            ->orderByDesc('c.lpac_id')
            ->get();

        return view('admin.landing-pages.ad-costs.index', $data);
    }

    public function store(Request $request)
    {
        $countries = array_keys(Helper::countryList());

        $validator = Validator::make($request->all(), [
            'lp_id' => 'required|exists:landing_pages,lp_id',
            'lpac_country_code' => 'required|string|size:2|in:' . implode(',', $countries),
            'lpac_date' => 'required|date',
            'lpac_amount' => 'required|numeric|min:0',
            'lpac_currency' => 'nullable|string|size:3',
            'lpac_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::table('landing_page_ad_costs')->insert([
            'lp_id' => (int) $request->lp_id,
            'lpac_country_code' => strtoupper($request->lpac_country_code),
            'lpac_date' => $request->lpac_date,
            'lpac_amount' => $request->lpac_amount,
            'lpac_currency' => strtoupper($request->input('lpac_currency', 'USD')),
            'lpac_notes' => $request->lpac_notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Session::flash('success', __('Ad cost entry added successfully.'));
        return redirect()->route('admin.landing-pages.ad-costs');
    }

    public function delete($id)
    {
        DB::table('landing_page_ad_costs')->where('lpac_id', (int) $id)->delete();
        Session::flash('success', __('Ad cost entry deleted.'));
        return redirect()->route('admin.landing-pages.ad-costs');
    }
}
