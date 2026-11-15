<?php

namespace Fickrr\Http\Controllers;

use Fickrr\Models\CryptoPay;
use Fickrr\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Buyer side of crypto payments made at the main site checkout
 * (Binance Pay / KAST / USDT). The admin approves them in Admin > Crypto Payments.
 */
class ShopCryptoController extends Controller
{
    public function show($token)
    {
        $pay = $this->payment($token);
        if (!$pay) {
            abort(404);
        }
        $setting = Settings::editGeneral(1);
        \Fickrr\Helpers\LandingPageI18n::setLocale((string) app()->getLocale());

        return view('landing-pages.crypto-pay', [
            'method' => CryptoPay::method($pay->method),
            'cp' => [
                'method' => $pay->method,
                'status' => $pay->status,
                'details' => ['reference' => $pay->reference, 'payer' => $pay->payer],
                'amount' => $pay->amount,
                'currency' => $pay->currency,
                'title' => ($setting->site_title ?? '') !== '' ? $setting->site_title : __('Your order'),
                'backUrl' => url('/'),
                'email' => $pay->email,
                'orderLabel' => '#' . $pay->purchase_token,
                'confirmUrl' => url('/shop-crypto/' . $pay->token . '/confirm'),
                'statusUrl' => url('/shop-crypto/' . $pay->token . '/status'),
                'successUrl' => url('/purchases'),
                'accent' => '#7c3aed',
                'accentDark' => '#db2777',
                'accentRgb' => '124, 58, 237',
            ],
        ]);
    }

    public function confirm(Request $request, $token)
    {
        $pay = $this->payment($token);
        if (!$pay) {
            abort(404);
        }
        if ($pay->status === 'completed') {
            return redirect('/purchases');
        }
        $request->validate([
            'receipt' => 'required|file|mimes:jpg,jpeg,png,webp,gif,pdf|max:8192',
            'reference' => 'nullable|string|max:150',
            'payer' => 'nullable|string|max:100',
        ]);
        $file = $request->file('receipt');
        $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $ext = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf'], true) ? $ext : 'bin';
        $name = 'shop-' . $pay->id . '-' . Str::random(24) . '.' . $ext;
        $file->move(CryptoPay::receiptDir(), $name);
        if (!empty($pay->receipt)) {
            @unlink(CryptoPay::receiptDir() . '/' . basename($pay->receipt));
        }
        $reference = trim(strip_tags((string) $request->input('reference'))) ?: null;
        $payer = trim(strip_tags((string) $request->input('payer'))) ?: null;
        DB::table('shop_crypto_payments')->where('id', $pay->id)->update([
            'status' => 'awaiting',
            'receipt' => $name,
            'reference' => $reference,
            'payer' => $payer,
            'submitted_at' => now(),
            'updated_at' => now(),
        ]);
        $this->notifyAdmin($pay, $reference, $payer);

        return redirect('/shop-crypto/' . $pay->token);
    }

    public function status($token)
    {
        $pay = $this->payment($token);
        return response()->json(['status' => $pay->status ?? 'missing'])->header('Cache-Control', 'no-store');
    }

    private function payment($token)
    {
        if (!is_string($token) || !preg_match('/^[A-Za-z0-9]{40}$/', $token) || !CryptoPay::shopReady()) {
            return null;
        }
        return DB::table('shop_crypto_payments')->where('token', $token)->first();
    }

    private function notifyAdmin($pay, $reference, $payer): void
    {
        try {
            $setting = Settings::editGeneral(1);
            $to = $setting->sender_email ?? '';
            if ($to === '') {
                return;
            }
            $method = CryptoPay::NAMES[$pay->method] ?? $pay->method;
            $text = "New crypto payment to check (site checkout)\n\n"
                . "Order: #{$pay->purchase_token}\nAmount: {$pay->amount} {$pay->currency}\nMethod: {$method}\n"
                . "Buyer: {$pay->email}\nTransaction ID: " . ($reference ?: '-') . "\n"
                . ($payer ? "Paid from: {$payer}\n" : '')
                . "Receipt: uploaded (open it in the admin)\n\nApprove it here: " . url('/admin/crypto-payments?type=shop');
            Mail::raw($text, function ($m) use ($to, $setting, $pay) {
                $m->to($to)->subject('Crypto payment waiting: order #' . $pay->purchase_token);
                $m->from($to, $setting->sender_name ?? 'Website');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
