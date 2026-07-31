<?php

namespace Fickrr\Helpers;

use Fickrr\Models\Settings;

class CoinbaseVerify
{
    /**
     * Confirm a Coinbase Commerce charge for a purchase/deposit token via API.
     */
    public static function chargeConfirmed($purchaseToken, $apiKey = null)
    {
        if ($purchaseToken === null || $purchaseToken === '') {
            return false;
        }

        if ($apiKey === null || $apiKey === '') {
            $additional = Settings::editAdditional();
            $apiKey = $additional->coinbase_api_key ?? '';
        }

        if ($apiKey === '') {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.commerce.coinbase.com/charges');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-CC-Api-Key: ' . $apiKey,
            'X-CC-Version: 2018-03-22',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $result = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300 || $result === false) {
            return false;
        }

        $decoded = json_decode($result);
        if (empty($decoded->data) || !is_array($decoded->data)) {
            return false;
        }

        foreach ($decoded->data as $charge) {
            $trx = $charge->metadata->trx ?? null;
            if ((string) $trx !== (string) $purchaseToken) {
                continue;
            }

            if (!empty($charge->timeline) && is_array($charge->timeline)) {
                foreach ($charge->timeline as $event) {
                    $status = strtoupper((string) ($event->status ?? ''));
                    if (in_array($status, ['COMPLETED', 'RESOLVED'], true)) {
                        return true;
                    }
                }
            }

            if (!empty($charge->payments) && is_array($charge->payments)) {
                foreach ($charge->payments as $payment) {
                    if (strtoupper((string) ($payment->status ?? '')) === 'CONFIRMED') {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
