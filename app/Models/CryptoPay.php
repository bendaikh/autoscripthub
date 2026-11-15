<?php

namespace Fickrr\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Manual crypto payments for landing pages. The buyer pays with Binance Pay
 * or KAST (QR code, ID/tag or pay link) or sends USDT to a wallet address, submits the transaction ID, and the
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
    public const METHODS = ['binance', 'kast', 'usdt'];

    public const NAMES = ['binance' => 'Binance Pay', 'kast' => 'KAST', 'usdt' => 'USDT'];

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
        $id = $method === 'binance' ? ($s->binance_pay_id ?? '') : ($s->kast_tag ?? '');
        $link = trim((string) ($method === 'binance' ? ($s->binance_link ?? '') : ($s->kast_link ?? '')));
        if ($link !== '' && !preg_match('#^https://#i', $link)) {
            $link = '';
        }
        $m = [
            'key' => $method,
            'name' => self::NAMES[$method],
            'qr' => self::fileUrl($method === 'binance' ? ($s->binance_qr ?? '') : ($s->kast_qr ?? '')),
            'id' => trim((string) $id),
            'link' => $link,
            'note' => trim((string) ($s->note ?? '')),
        ];
        $on = (int) ($method === 'binance' ? $s->binance_enabled : $s->kast_enabled) === 1;
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
            return (int) DB::table('landing_page_orders')
                ->whereIn('payment_method', self::METHODS)
                ->where('payment_status', 'awaiting')
                ->count();
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
