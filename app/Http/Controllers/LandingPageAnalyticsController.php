<?php

namespace Fickrr\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Fickrr\Helpers\Helper;

class LandingPageAnalyticsController extends Controller
{
    /**
     * Public endpoint to track landing page visits/events.
     *
     * Accepts JSON or form-data:
     * - lp_id (required)
     * - visitor_id (required)
     * - event (required)
     * - properties (optional object/array/json string)
     * - url, referrer, utm_* (optional)
     */
    public function track(Request $request)
    {
        $lpId = (int) $request->input('lp_id');
        $visitorId = (string) $request->input('visitor_id');
        $event = (string) $request->input('event');

        if ($lpId <= 0 || $visitorId === '' || $event === '') {
            return response()->json(['success' => false, 'message' => 'Invalid payload'], 422);
        }

        // Ensure landing page exists
        $landingPage = DB::table('landing_pages')->where('lp_id', $lpId)->first();
        if (!$landingPage) {
            return response()->json(['success' => false, 'message' => 'Landing page not found'], 404);
        }

        $ip = $request->ip();
        $countryCode = null;
        try {
            $countryCode = Helper::getVisitorCountry($ip);
        } catch (\Throwable $e) {
            $countryCode = null;
        }

        $appKey = (string) config('app.key', 'app-key');
        $ipHash = $ip ? hash('sha256', $ip . '|' . $appKey) : null;
        $sessionId = method_exists($request, 'session') ? $request->session()->getId() : null;

        $url = (string) $request->input('url', '');
        $referrer = (string) $request->input('referrer', $request->headers->get('referer', ''));
        $userAgent = (string) $request->header('User-Agent', '');

        $utmSource = $request->input('utm_source');
        $utmMedium = $request->input('utm_medium');
        $utmCampaign = $request->input('utm_campaign');
        $utmTerm = $request->input('utm_term');
        $utmContent = $request->input('utm_content');

        // Parse properties
        $properties = $request->input('properties', null);
        if (is_string($properties)) {
            $decoded = json_decode($properties, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $properties = $decoded;
            }
        }
        if (!is_array($properties)) {
            $properties = null;
        }

        $now = now();

        // Find an existing "active" visit for this visitor on this landing page (within last 30 minutes)
        $activeVisit = DB::table('landing_page_visits')
            ->where('lp_id', $lpId)
            ->where('lpv_visitor_id', $visitorId)
            ->where('lpv_last_seen_at', '>=', $now->copy()->subMinutes(30))
            ->orderByDesc('lpv_id')
            ->first();

        if ($activeVisit) {
            $visitId = (int) $activeVisit->lpv_id;
            $update = [
                'lpv_last_seen_at' => $now,
                'updated_at' => $now,
            ];

            if ($countryCode && !$activeVisit->lpv_country_code) {
                $update['lpv_country_code'] = $countryCode;
            }

            // Increment pageviews for explicit page_view events
            if ($event === 'page_view') {
                $update['lpv_pageviews'] = (int) $activeVisit->lpv_pageviews + 1;
            }

            DB::table('landing_page_visits')->where('lpv_id', $visitId)->update($update);
        } else {
            $visitId = (int) DB::table('landing_page_visits')->insertGetId([
                'lp_id' => $lpId,
                'lpv_visitor_id' => $visitorId,
                'lpv_session_id' => $sessionId,
                'lpv_ip_hash' => $ipHash,
                'lpv_country_code' => $countryCode,
                'lpv_user_agent' => $userAgent ?: null,
                'lpv_referrer' => $referrer ?: null,
                'lpv_landing_url' => $url ?: null,
                'lpv_utm_source' => $utmSource,
                'lpv_utm_medium' => $utmMedium,
                'lpv_utm_campaign' => $utmCampaign,
                'lpv_utm_term' => $utmTerm,
                'lpv_utm_content' => $utmContent,
                'lpv_pageviews' => ($event === 'page_view') ? 1 : 0,
                'lpv_started_at' => $now,
                'lpv_last_seen_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('landing_page_events')->insert([
            'lp_id' => $lpId,
            'lpv_id' => $visitId ?: null,
            'lpe_visitor_id' => $visitorId,
            'lpe_name' => substr($event, 0, 64),
            'lpe_properties' => $properties ? json_encode($properties) : null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json(['success' => true]);
    }
}


