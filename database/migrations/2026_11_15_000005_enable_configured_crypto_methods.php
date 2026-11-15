<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One-time fix: the "Show to buyers" switches in Admin > Crypto Payments
 * started on "No", so methods whose details were filled in stayed hidden.
 * Switch on every method that already has payment details.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crypto_pay_settings')) {
            return;
        }
        $s = DB::table('crypto_pay_settings')->where('id', 1)->first();
        if (!$s) {
            return;
        }
        $filled = fn (...$values) => count(array_filter($values, fn ($v) => trim((string) $v) !== '')) > 0;
        $data = [];
        if ((int) $s->binance_enabled !== 1 && $filled($s->binance_qr, $s->binance_pay_id, $s->binance_link)) {
            $data['binance_enabled'] = 1;
        }
        if ((int) $s->kast_enabled !== 1 && $filled($s->kast_qr, $s->kast_tag, $s->kast_link)) {
            $data['kast_enabled'] = 1;
        }
        if ((int) ($s->wallet_enabled ?? 0) !== 1 && $filled($s->wallet_address ?? '')) {
            $data['wallet_enabled'] = 1;
        }
        if ($data) {
            $data['updated_at'] = now();
            DB::table('crypto_pay_settings')->where('id', 1)->update($data);
        }
    }

    public function down(): void
    {
    }
};
