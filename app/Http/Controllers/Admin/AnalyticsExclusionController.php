<?php

namespace Fickrr\Http\Controllers\Admin;

use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\Settings;
use Fickrr\Support\Visits\AnalyticsExclusion;
use Fickrr\Support\Visits\ClientAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin > Analytics Exclusions: keep the owner's own visits out of the stats.
 */
class AnalyticsExclusionController extends Controller
{
    public function index(Request $request)
    {
        $setting['setting'] = Settings::editGeneral(1);
        $rows = AnalyticsExclusion::ready() ? DB::table('analytics_exclusions')->orderByDesc('id')->get() : collect();
        return view('admin.analytics-exclusions', [
            'setting' => $setting,
            'ready' => AnalyticsExclusion::ready(),
            'ips' => $rows->where('type', 'ip')->values(),
            'deviceCount' => $rows->where('type', 'device')->count(),
            'myIp' => $this->clientIp($request),
        ]);
    }

    /** Called from every admin page (once per device): excludes the browser and removes its past visits. */
    public function registerDevice(Request $request)
    {
        $ids = array_slice(array_filter((array) $request->input('ids', []), 'is_string'), 0, 5);
        $label = substr((string) $request->userAgent(), 0, 100);
        $removed = AnalyticsExclusion::excludeDevice($ids, $label);
        return response()->json(['ok' => true, 'removed' => $removed]);
    }

    public function addIp(Request $request)
    {
        $ip = trim((string) ($request->input('mine') === '1' ? $this->clientIp($request) : $request->input('ip')));
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return redirect()->back()->with('error', 'Please enter a valid IP address.');
        }
        $label = trim(strip_tags((string) $request->input('label')));
        AnalyticsExclusion::addIp($ip, $label !== '' ? $label : ($request->input('mine') === '1' ? 'My IP' : ''));
        return redirect()->back()->with('success', 'IP ' . $ip . ' is now excluded and its past landing page visits were removed.');
    }

    public function remove($id)
    {
        AnalyticsExclusion::remove((int) $id);
        return redirect()->back()->with('success', 'Removed.');
    }

    public function clearDevices()
    {
        if (AnalyticsExclusion::ready()) {
            DB::table('analytics_exclusions')->where('type', 'device')->delete();
            AnalyticsExclusion::forget();
        }
        return redirect()->back()->with('success', 'Device list cleared. Devices you open the admin panel on are added again automatically.');
    }

    private function clientIp(Request $request): string
    {
        $flat = [];
        foreach (array_change_key_case($request->headers->all(), CASE_LOWER) as $name => $values) {
            $flat[$name] = is_array($values) ? (string) ($values[0] ?? '') : (string) $values;
        }
        return ClientAddress::ip($flat, (string) $request->ip());
    }
}
