<?php

namespace Fickrr\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Manual crypto payments for landing pages. The buyer pays with Binance Pay
 * KAST or Wise (QR code, ID/tag or pay link) or sends USDT to a wallet address, submits the transaction ID, and the
 * admin approves the order in Admin > Crypto Payments.
 *
 * Order statuses used in landing_page_orders.payment_status:
 *   pending   - order created, buyer has not submitted a transaction ID yet
 *   awaiting  - transaction ID submitted, waiting for the admin to check it
 *   completed - approved by the admin (download unlocked)
 *   rejected  - admin could not find the payment
 */
class CryptoPay
{
    public const METHODS = ['binance', 'kast', 'usdt', 'wise'];

    public const NAMES = ['binance' => 'Binance Pay', 'kast' => 'KAST', 'usdt' => 'USDT', 'wise' => 'Wise'];

    /** Methods paid in normal money (the order currency) instead of USDT. */
    public const FIAT = ['wise'];

    /** Settings columns of the QR / ID / link methods: [id, link, qr, enabled]. */
    private const COLUMNS = [
        'binance' => ['binance_pay_id', 'binance_link', 'binance_qr', 'binance_enabled'],
        'kast' => ['kast_tag', 'kast_link', 'kast_qr', 'kast_enabled'],
        'wise' => ['wise_tag', 'wise_link', 'wise_qr', 'wise_enabled'],
    ];

    public static function ready(): bool
    {
        try {
            return Schema::hasTable('crypto_pay_settings');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function settings()
    {
        try {
            return DB::table('crypto_pay_settings')->where('id', 1)->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function updateSettings(array $data): void
    {
        $data['updated_at'] = now();
        if (DB::table('crypto_pay_settings')->where('id', 1)->exists()) {
            DB::table('crypto_pay_settings')->where('id', 1)->update($data);
        } else {
            DB::table('crypto_pay_settings')->insert($data + ['id' => 1, 'created_at' => now()]);
        }
    }

    /**
     * Buyer-facing details of one method: name, qr (url), id, link, enabled.
     */
    public static function method(string $method, $s = null): ?array
    {
        $s = $s ?: (self::ready() ? self::settings() : null);
        if (!$s || !in_array($method, self::METHODS, true)) {
            return null;
        }
        if ($method === 'usdt') {
            $asset = strtoupper(trim((string) ($s->wallet_asset ?? ''))) ?: 'USDT';
            $address = trim((string) ($s->wallet_address ?? ''));
            return [
                'key' => 'usdt',
                'name' => $asset,
                'asset' => $asset,
                'network' => trim((string) ($s->wallet_network ?? '')),
                'address' => $address,
                'qr' => null,
                'id' => '',
                'link' => '',
                'note' => trim((string) ($s->note ?? '')),
                'enabled' => (int) ($s->wallet_enabled ?? 0) === 1 && $address !== '',
            ];
        }
        [$idCol, $linkCol, $qrCol, $onCol] = self::COLUMNS[$method];
        $id = $s->$idCol ?? '';
        $link = trim((string) ($s->$linkCol ?? ''));
        if ($link !== '' && !preg_match('#^https://#i', $link)) {
            $link = '';
        }
        $m = [
            'key' => $method,
            'name' => self::NAMES[$method],
            'qr' => self::fileUrl($s->$qrCol ?? ''),
            'id' => trim((string) $id),
            'link' => $link,
            'note' => trim((string) ($s->note ?? '')),
        ];
        $on = (int) ($s->$onCol ?? 0) === 1;
        $m['enabled'] = $on && ($m['qr'] || $m['id'] !== '' || $m['link'] !== '');
        return $m;
    }

    /** Methods that are switched on and have enough details for a buyer to pay. */
    public static function enabledMethods(): array
    {
        $s = self::ready() ? self::settings() : null;
        if (!$s) {
            return [];
        }
        return array_values(array_filter(self::METHODS, fn ($k) => self::method($k, $s)['enabled']));
    }

    /** Adds the switched-on crypto methods to a checkout's list of payment options. */
    public static function withMethods(array $options): array
    {
        $options = array_values(array_filter(array_map('trim', $options), 'strlen'));
        foreach (self::enabledMethods() as $m) {
            if (!in_array($m, $options, true)) {
                $options[] = $m;
            }
        }
        return $options;
    }

    public static function shopReady(): bool
    {
        try {
            return Schema::hasTable('shop_crypto_payments');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Creates the crypto payment row for a site checkout and returns the buyer's payment page URL. */
    public static function startShopPayment($purchaseToken, string $method, $amount, $currency, $email): string
    {
        $key = \Illuminate\Support\Str::random(40);
        DB::table('shop_crypto_payments')->insert([
            'purchase_token' => (string) $purchaseToken,
            'token' => $key,
            'method' => $method,
            'amount' => (float) $amount,
            'currency' => $currency ? strtoupper((string) $currency) : null,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'email' => $email,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return url('/shop-crypto/' . $key);
    }

    public static function fileUrl(?string $file): ?string
    {
        $file = (string) $file;
        if ($file === '' || !preg_match('/^[A-Za-z0-9._-]+$/', $file) || !is_file(public_path('storage/crypto/' . $file))) {
            return null;
        }
        return url('/public/storage/crypto/' . $file);
    }

    /** Orders waiting for the admin to check the payment. */
    public static function awaitingCount(): int
    {
        try {
            $n = (int) DB::table('landing_page_orders')
                ->whereIn('payment_method', self::METHODS)
                ->where('payment_status', 'awaiting')
                ->count();
            if (self::shopReady()) {
                $n += (int) DB::table('shop_crypto_payments')->where('status', 'awaiting')->count();
            }
            return $n;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /** Private folder for payment receipts uploaded by buyers (not web accessible). */
    public static function receiptDir(): string
    {
        $dir = storage_path('app/crypto-receipts');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }

    public static function details($order): array
    {
        $d = json_decode((string) ($order->payment_details ?? ''), true);
        return is_array($d) ? $d : [];
    }
}
