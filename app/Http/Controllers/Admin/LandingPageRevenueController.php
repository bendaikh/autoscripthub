<?php

namespace Fickrr\Http\Controllers\Admin;

use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\LandingPage;
use Fickrr\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Session;

class LandingPageRevenueController extends Controller
{
    public function index()
    {
        $data['landing_pages'] = LandingPage::getAllLandingPages();
        $data['countries'] = Helper::countryList();
        $data['entries'] = DB::table('landing_page_revenues as r')
            ->join('landing_pages as p', 'p.lp_id', '=', 'r.lp_id')
            ->select('r.*', 'p.lp_title')
            ->orderByDesc('r.lpr_date')
            ->orderByDesc('r.lpr_id')
            ->get();

        return view('admin.landing-pages.revenue.index', $data);
    }

    public function store(Request $request)
    {
        $countries = array_keys(Helper::countryList());

        $validator = Validator::make($request->all(), [
            'lp_id' => 'required|exists:landing_pages,lp_id',
            'lpr_country_code' => 'required|string|size:2|in:' . implode(',', $countries),
            'lpr_date' => 'required|date',
            'lpr_amount' => 'required|numeric|min:0',
            'lpr_quantity' => 'required|integer|min:1',
            'lpr_currency' => 'nullable|string|size:3',
            'lpr_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::table('landing_page_revenues')->insert([
            'lp_id' => (int) $request->lp_id,
            'lpr_country_code' => strtoupper($request->lpr_country_code),
            'lpr_date' => $request->lpr_date,
            'lpr_amount' => $request->lpr_amount,
            'lpr_quantity' => (int) $request->lpr_quantity,
            'lpr_currency' => strtoupper($request->input('lpr_currency', 'USD')),
            'lpr_notes' => $request->lpr_notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Session::flash('success', __('Revenue entry added successfully.'));
        return redirect()->route('admin.landing-pages.revenue');
    }

    public function delete($id)
    {
        DB::table('landing_page_revenues')->where('lpr_id', (int) $id)->delete();
        Session::flash('success', __('Revenue entry deleted.'));
        return redirect()->route('admin.landing-pages.revenue');
    }
}
