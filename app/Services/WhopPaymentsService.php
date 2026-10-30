<?php

namespace Fickrr\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class WhopPaymentsService
{
    protected $apiKey;
    protected $companyId;
    protected $baseUrl;

    public function __construct($apiKey, $companyId, $mode = 'live')
    {
        $this->apiKey = $apiKey;
        $this->companyId = $companyId;

        if ($mode === 'sandbox' || $mode === 'test' || $mode === 0 || $mode === '0') {
            $this->baseUrl = 'https://sandbox-api.whop.com/api/v1';
        } else {
            $this->baseUrl = 'https://api.whop.com/api/v1';
        }
    }

    public static function fromConfig()
    {
        return new self(
            config('services.whop.api_key'),
            config('services.whop.company_id'),
            config('services.whop.mode', 'live')
        );
    }

    public function isConfigured()
    {
        return !empty($this->apiKey) && !empty($this->companyId);
    }

    /**
     * Create a checkout configuration with an inline one-time plan.
     * Official: POST /checkout_configurations
     *
     * @return array|null
     */
    public function createCheckoutConfiguration($amount, $currency, $title, $returnUrl, array $metadata = [])
    {
        try {
            $client = new Client();
            $payload = [
                'company_id' => $this->companyId,
                'mode' => 'payment',
                'redirect_url' => $returnUrl,
                'metadata' => $metadata,
                'plan' => [
                    'company_id' => $this->companyId,
                    'initial_price' => (float) $amount,
                    'plan_type' => 'one_time',
                    'currency' => strtolower($currency),
                    'force_create_new_plan' => true,
                    // Digital products: card only (hide bank wire, ACH, crypto, etc.)
                    'payment_method_configuration' => [
                        'enabled' => ['card', 'apple_pay', 'google_pay', 'link'],
                        'disabled' => [],
                        'include_platform_defaults' => false,
                    ],
                    'product' => [
                        // Whop API max title length is 80 characters
                        'title' => mb_substr(trim((string) $title), 0, 80) ?: 'Digital product',
                        'external_identifier' => 'auto-script-hub-marketplace',
                    ],
                ],
            ];

            Log::info('Whop Create Checkout Configuration Request', ['payload' => $payload]);

            $response = $client->post($this->baseUrl . '/checkout_configurations', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            Log::info('Whop Create Checkout Configuration Response', ['response' => $result]);

            return $result;
        } catch (GuzzleException $e) {
            Log::error('Whop Checkout Configuration Error: ' . $e->getMessage());
            if ($e->hasResponse()) {
                Log::error('Whop Checkout Configuration Error Response: ' . $e->getResponse()->getBody()->getContents());
            }
            return null;
        }
    }

    /**
     * GET /payments/{id}
     */
    public function getPayment($paymentId)
    {
        try {
            $client = new Client();
            $response = $client->get($this->baseUrl . '/payments/' . $paymentId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Whop Get Payment Error: ' . $e->getMessage());
            return null;
        }
    }

    public static function extractSessionId($response)
    {
        if (!$response || !is_array($response)) {
            return null;
        }
        if (isset($response['data']) && is_array($response['data'])) {
            $response = $response['data'];
        }

        return $response['id'] ?? $response['session_id'] ?? null;
    }

    public static function extractPlanId($response)
    {
        if (!$response || !is_array($response)) {
            return null;
        }
        if (isset($response['data']) && is_array($response['data'])) {
            $response = $response['data'];
        }

        if (!empty($response['plan']['id'])) {
            return $response['plan']['id'];
        }

        return $response['plan_id'] ?? null;
    }

    public static function isSuccessfulStatus($status)
    {
        $status = strtolower((string) $status);

        return in_array($status, ['succeeded', 'successful', 'paid', 'complete', 'completed'], true);
    }

    /**
     * Whop webhooks use the Standard Webhooks spec.
     * @see https://docs.whop.com/developer/guides/webhooks
     */
    public static function verifyWebhookSignature($payload, $headers, $secret)
    {
        if (empty($secret) || $payload === '' || $payload === null) {
            return false;
        }

        $normalized = [];
        foreach ($headers as $key => $value) {
            $normalized[strtolower($key)] = is_array($value) ? $value[0] : $value;
        }

        $msgId = $normalized['webhook-id'] ?? '';
        $timestamp = $normalized['webhook-timestamp'] ?? '';
        $signatureHeader = $normalized['webhook-signature'] ?? '';

        if ($msgId === '' || $timestamp === '' || $signatureHeader === '') {
            return false;
        }

        if (abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        $secretBytes = self::webhookSecretBytes($secret);
        $toSign = $msgId . '.' . $timestamp . '.' . $payload;
        $expected = base64_encode(hash_hmac('sha256', $toSign, $secretBytes, true));

        foreach (preg_split('/\s+/', $signatureHeader) as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            $sig = $part;
            if (strpos($part, ',') !== false) {
                $sig = explode(',', $part, 2)[1];
            } elseif (strpos($part, 'v1=') === 0) {
                $sig = substr($part, 3);
            }
            if (hash_equals($expected, $sig)) {
                return true;
            }
        }

        return false;
    }

    protected static function webhookSecretBytes($secret)
    {
        if (strpos($secret, 'whsec_') === 0) {
            $decoded = base64_decode(substr($secret, 6), true);
            if ($decoded !== false) {
                return $decoded;
            }
        }

        return $secret;
    }
}
