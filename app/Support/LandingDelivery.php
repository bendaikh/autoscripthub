<?php

namespace Fickrr\Support;

use Fickrr\Models\LandingBonus;
use Fickrr\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

/**
 * What a paid landing page order receives: the main product plus the active
 * bonuses, on the thank-you page and in the purchase email.
 */
class LandingDelivery
{
    /** Call once when an order becomes paid: records the bonuses and emails the buyer. */
    public static function completed($order): bool
    {
        $order = self::fresh($order);
        if (!$order || $order->payment_status !== 'completed') {
            return false;
        }
        self::recordBonuses($order);
        return self::sendEmail($order);
    }

    /** Titles of the bonuses the order received (kept for Admin > Landing Customers). */
    public static function recordBonuses($order): array
    {
        $titles = array_map(fn ($d) => $d['title'], LandingBonus::downloadsFor($order));
        try {
            if (Schema::hasColumn('landing_page_orders', 'bonuses_delivered')) {
                DB::table('landing_page_orders')->where('id', $order->id)
                    ->update(['bonuses_delivered' => $titles ? json_encode($titles, JSON_UNESCAPED_UNICODE) : null]);
            }
        } catch (\Throwable $e) {
            report($e);
        }
        return $titles;
    }

    public static function deliveredTitles($order): array
    {
        $list = json_decode((string) ($order->bonuses_delivered ?? ''), true);
        return is_array($list) ? $list : [];
    }

    /** Purchase email: download page link plus one link per bonus (links valid 7 days). */
    public static function sendEmail($order): bool
    {
        try {
            $order = self::fresh($order);
            if (!$order || $order->payment_status !== 'completed' || !filter_var($order->customer_email, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
            $setting = Settings::editGeneral(1);
            $from = $setting->sender_email ?? '';
            if ($from === '') {
                return false;
            }
            $page = DB::table('landing_pages')->where('lp_id', $order->lp_id)->first();
            $title = $page->lp_title ?? 'your order';
            $data = [
                'title' => $title,
                'link' => url('/landing/success/' . $order->order_token),
                'orderToken' => $order->order_token,
                'bonuses' => LandingBonus::downloadsFor($order, 7),
                'site' => $setting->site_title ?? '',
            ];
            Mail::send('landing-pages.order-email', $data, function ($m) use ($order, $from, $setting, $title) {
                $m->to($order->customer_email)->subject('Your download is ready: ' . $title);
                $m->from($from, $setting->sender_name ?? ($setting->site_title ?? ''));
            });
            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    private static function fresh($order)
    {
        if (!$order) {
            return null;
        }
        return DB::table('landing_page_orders')->where('id', $order->id)->first();
    }
}
