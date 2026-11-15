<?php

namespace Fickrr\Http\Controllers\Admin;

use Fickrr\Http\Controllers\Controller;
use Fickrr\Models\CryptoPay;
use Fickrr\Models\LandingCustomer;
use Fickrr\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Admin > Crypto Payments: Binance Pay / wallet settings and approving
 * landing page orders paid with crypto.
 */
class CryptoPaymentController extends Controller
{
    public function index(Request $request)
    {
        $setting['setting'] = Settings::editGeneral(1);
        $filter = in_array($request->input('show'), ['awaiting', 'pending', 'completed', 'rejected'], true)
            ? $request->input('show') : 'awaiting';

        $orders = collect();
        $counts = [];
        try {
            $orders = DB::table('landing_page_orders as o')
                ->leftJoin('landing_pages as lp', 'lp.lp_id', '=', 'o.lp_id')
                ->whereIn('o.payment_method', CryptoPay::METHODS)
                ->where('o.payment_status', $filter)
                ->orderByDesc('o.id')
                ->limit(200)
                ->get(['o.*', 'lp.lp_title', 'lp.lp_slug']);
            $counts = DB::table('landing_page_orders')
                ->whereIn('payment_method', CryptoPay::METHODS)
                ->selectRaw('payment_status, COUNT(*) as c')
                ->groupBy('payment_status')
                ->pluck('c', 'payment_status')
                ->all();
        } catch (\Throwable $e) {
            // landing_page_orders missing: show settings only
        }

        $s = CryptoPay::settings();

        return view('admin.crypto-payments', [
            'setting' => $setting,
            'ready' => CryptoPay::ready(),
            'crypto' => $s,
            'binance' => $s ? CryptoPay::method('binance', $s) : null,
            'kast' => $s ? CryptoPay::method('kast', $s) : null,
            'usdt' => $s ? CryptoPay::method('usdt', $s) : null,
            'orders' => $orders,
            'filter' => $filter,
            'counts' => $counts,
        ]);
    }

    public function updateSettings(Request $request)
    {
        if (!CryptoPay::ready()) {
            return redirect()->back()->with('error', 'Please run the database migration first (php artisan migrate).');
        }
        $request->validate([
            'binance_enabled' => 'required|in:0,1',
            'binance_qr' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'binance_pay_id' => 'nullable|string|max:64',
            'binance_link' => 'nullable|url|starts_with:https://|max:255',
            'kast_enabled' => 'required|in:0,1',
            'kast_qr' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'kast_tag' => 'nullable|string|max:64',
            'kast_link' => 'nullable|url|starts_with:https://|max:255',
            'wallet_enabled' => 'required|in:0,1',
            'wallet_asset' => 'nullable|string|max:20',
            'wallet_network' => 'nullable|string|max:40',
            'wallet_address' => 'nullable|string|max:191|regex:/^[A-Za-z0-9:_-]+$/',
            'note' => 'nullable|string|max:500',
        ], ['wallet_address.regex' => 'The wallet address can only contain letters and numbers.']);

        $clean = fn ($k) => trim((string) $request->input($k)) ?: null;
        $data = [
            'binance_enabled' => (int) $request->input('binance_enabled'),
            'binance_pay_id' => $clean('binance_pay_id'),
            'binance_link' => $clean('binance_link'),
            'kast_enabled' => (int) $request->input('kast_enabled'),
            'kast_tag' => $clean('kast_tag'),
            'kast_link' => $clean('kast_link'),
            'wallet_enabled' => (int) $request->input('wallet_enabled'),
            'wallet_asset' => $clean('wallet_asset'),
            'wallet_network' => $clean('wallet_network'),
            'wallet_address' => $clean('wallet_address'),
            'note' => $clean('note'),
        ];

        $old = CryptoPay::settings();
        foreach (['binance_qr', 'kast_qr'] as $field) {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                $dir = public_path('storage/crypto');
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                $ext = strtolower($request->file($field)->guessExtension() ?: 'png');
                $ext = in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) ? $ext : 'png';
                $name = str_replace('_', '-', $field) . '-' . Str::random(16) . '.' . $ext;
                $request->file($field)->move($dir, $name);
                $data[$field] = $name;
                $this->deleteQr($old->$field ?? null);
            } elseif ($request->input('remove_' . $field) === '1') {
                $data[$field] = null;
                $this->deleteQr($old->$field ?? null);
            }
        }

        CryptoPay::updateSettings($data);
        return redirect()->back()->with('success', 'Crypto payment settings saved.');
    }

    public function approve($id)
    {
        $order = $this->order($id);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }
        if ($order->payment_status === 'completed') {
            return redirect()->back()->with('success', 'This order was already approved.');
        }

        $details = CryptoPay::details($order);
        $details['reviewed_at'] = now()->toDateTimeString();
        DB::table('landing_page_orders')->where('id', $order->id)->update([
            'payment_status' => 'completed',
            'completed_at' => now(),
            'payment_details' => json_encode($details),
            'updated_at' => now(),
        ]);
        LandingCustomer::createOrUpdateCustomer($order->customer_email, $order->amount);

        $mailed = $this->mailBuyer($order);

        return redirect()->back()->with('success', 'Payment approved. The buyer can now download the product'
            . ($mailed ? ' and was sent the download link by email.' : '. (The email could not be sent; you can send them this link: ' . url('/landing/success/' . $order->order_token) . ')'));
    }

    public function reject($id)
    {
        $order = $this->order($id);
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }
        if ($order->payment_status === 'completed') {
            return redirect()->back()->with('error', 'This order is already approved.');
        }
        $details = CryptoPay::details($order);
        $details['reviewed_at'] = now()->toDateTimeString();
        DB::table('landing_page_orders')->where('id', $order->id)->update([
            'payment_status' => 'rejected',
            'payment_details' => json_encode($details),
            'updated_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Order marked as payment not received.');
    }

    /** Shows the receipt the buyer uploaded (images inline, PDFs in the browser viewer). */
    public function receipt($id)
    {
        $order = $this->order($id);
        $file = $order ? basename((string) (CryptoPay::details($order)['receipt'] ?? '')) : '';
        $path = CryptoPay::receiptDir() . '/' . $file;
        if ($file === '' || !is_file($path)) {
            abort(404);
        }
        return response()->file($path, ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    private function order($id)
    {
        return DB::table('landing_page_orders')
            ->where('id', (int) $id)
            ->whereIn('payment_method', CryptoPay::METHODS)
            ->first();
    }

    private function deleteQr(?string $file): void
    {
        if ($file && preg_match('/^[A-Za-z0-9._-]+$/', $file)) {
            @unlink(public_path('storage/crypto/' . $file));
        }
    }

    private function mailBuyer($order): bool
    {
        try {
            $setting = Settings::editGeneral(1);
            $fromEmail = $setting->sender_email ?? '';
            $fromName = $setting->sender_name ?? ($setting->site_title ?? '');
            if ($fromEmail === '') {
                return false;
            }
            $title = DB::table('landing_pages')->where('lp_id', $order->lp_id)->value('lp_title') ?: 'your order';
            $data = [
                'title' => $title,
                'link' => url('/landing/success/' . $order->order_token),
                'site' => $setting->site_title ?? '',
            ];
            Mail::send('landing-pages.crypto-approved-mail', $data, function ($m) use ($order, $fromEmail, $fromName, $title) {
                $m->to($order->customer_email)->subject('Payment confirmed: ' . $title);
                $m->from($fromEmail, $fromName);
            });
            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }
}
