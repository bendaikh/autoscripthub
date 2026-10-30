<?php

namespace Fickrr\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class DodoPaymentsService
{
    protected $apiKey;
    protected $baseUrl;
    
    public function __construct($apiKey, $mode = 'test')
    {
        $this->apiKey = $apiKey;
        
        // Set base URL based on mode
        // Handle both string ('test'/'live') and numeric (0/1) mode values
        if ($mode === 'live' || $mode === 1 || $mode === '1') {
            $this->baseUrl = 'https://live.dodopayments.com';
        } else {
            $this->baseUrl = 'https://test.dodopayments.com';
        }
    }

    /**
     * Normalize Dodo Checkout SDK mode ('test' or 'live').
     */
    public static function sdkMode($mode)
    {
        if ($mode === 'live' || $mode === 1 || $mode === '1') {
            return 'live';
        }

        return 'test';
    }

    /**
     * Extract checkout session URL from Dodo API response.
     */
    public static function sessionCheckoutUrl($response)
    {
        if (!$response || !is_array($response)) {
            return null;
        }

        return $response['checkout_url'] ?? $response['url'] ?? $response['payment_url'] ?? null;
    }

    /**
     * Derive a display name from the customer's email (digital goods checkout).
     */
    public static function customerNameFromEmail($email)
    {
        $local = strstr((string) $email, '@', true);
        if ($local === false) {
            $local = (string) $email;
        }
        $local = trim(preg_replace('/[^\w\s.\-]/', ' ', $local));

        return $local !== '' ? $local : 'Customer';
    }

    /**
     * Generic US billing address for digital products (same approach as Whop).
     */
    public static function digitalBillingAddress()
    {
        return [
            'country' => 'US',
            'street' => '3500 S Dupont Hwy',
            'city' => 'Dover',
            'state' => 'DE',
            'zipcode' => '19901',
        ];
    }

    /**
     * Pre-fill customer details and skip contact step for card-only checkout.
     */
    public static function applyCardOnlyCheckoutOptions(array $sessionData, $customerEmail, $customerName = null)
    {
        $email = trim((string) $customerEmail);
        $name = trim((string) ($customerName ?? ''));
        if ($name === '' && $email !== '') {
            $name = self::customerNameFromEmail($email);
        }

        $customer = $sessionData['customer'] ?? [];
        if ($email !== '') {
            $customer['email'] = $email;
        }
        if ($name !== '') {
            $customer['name'] = $name;
        }
        $sessionData['customer'] = $customer;
        $sessionData['billing_address'] = self::digitalBillingAddress();
        $sessionData['confirm'] = true;
        $sessionData['allowed_payment_method_types'] = ['credit', 'debit', 'apple_pay', 'google_pay'];
        $sessionData['feature_flags'] = array_merge([
            'allow_phone_number_collection' => false,
            'allow_tax_id' => false,
            'allow_discount_code' => false,
            'allow_customer_editing_email' => false,
            'allow_customer_editing_name' => false,
            'allow_customer_editing_country' => false,
            'allow_customer_editing_street' => false,
            'allow_customer_editing_city' => false,
            'allow_customer_editing_state' => false,
            'allow_customer_editing_zipcode' => false,
        ], $sessionData['feature_flags'] ?? []);

        return $sessionData;
    }

    /**
     * Append query params so Dodo checkout shows card fields only.
     */
    public static function cardOnlyCheckoutUrl($checkoutUrl, $customerEmail, $customerName = null)
    {
        if (!$checkoutUrl) {
            return null;
        }

        $email = trim((string) $customerEmail);
        $name = trim((string) ($customerName ?? ''));
        if ($name === '' && $email !== '') {
            $name = self::customerNameFromEmail($email);
        }
        $billing = self::digitalBillingAddress();

        $query = [
            'email' => $email,
            'fullName' => $name,
            'country' => $billing['country'],
            'addressLine' => $billing['street'],
            'city' => $billing['city'],
            'state' => $billing['state'],
            'zipCode' => $billing['zipcode'],
            'disableFullName' => 'true',
            'disableFirstName' => 'true',
            'disableLastName' => 'true',
            'disableEmail' => 'true',
            'disableCountry' => 'true',
            'disableAddressLine' => 'true',
            'disableCity' => 'true',
            'disableState' => 'true',
            'disableZipCode' => 'true',
            'showDiscounts' => 'false',
        ];

        $parts = parse_url($checkoutUrl);
        $existing = [];
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $existing);
        }
        $merged = array_merge($existing, $query);
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = $parts['path'] ?? '';

        return $scheme . '://' . $host . $port . $path . '?' . http_build_query($merged);
    }
    
    /**
     * Create a product in Dodo Payments (one-time price)
     *
     * @param float $price Price in dollars (e.g., 14.30)
     * @param string $currency
     * @param string $name
     * @param string $taxCategory
     * @return array|null
     */
    public function createProduct($price, $currency, $name = null, $taxCategory = 'digital_products')
    {
        try {
            $client = new Client();

            // DodoPayments expects price in smallest currency unit (cents for USD)
            // Convert price from dollars to cents and round to integer
            $priceInCents = (int)round($price * 100);

            $payload = [
                'price' => [
                    'currency' => strtoupper($currency),
                    'discount' => 0,  // Integer, no discount
                    'price' => $priceInCents,  // Integer in smallest currency unit
                    'purchasing_power_parity' => true,
                    'type' => 'one_time_price',
                ],
                'tax_category' => $taxCategory,
            ];

            if (!empty($name)) {
                $payload['name'] = $name;
            }

            Log::info('Dodo Payments Create Product Request', ['payload' => $payload, 'original_price' => $price]);

            $response = $client->post($this->baseUrl . '/products', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload
            ]);

            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);
            Log::info('Dodo Payments Create Product Response', ['response' => $result]);
            return $result;
        } catch (GuzzleException $e) {
            Log::error('Dodo Payments Create Product Error: ' . $e->getMessage());
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                Log::error('Dodo Payments Error Response: ' . $responseBody);
            }
            return null;
        }
    }

    /**
     * Create a one-time payment
     * 
     * @param array $paymentData
     * @return array|null
     */
    public function createPayment($paymentData)
    {
        try {
            $client = new Client();
            
            $response = $client->post($this->baseUrl . '/payments', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $paymentData
            ]);
            
            $body = $response->getBody()->getContents();
            return json_decode($body, true);
            
        } catch (GuzzleException $e) {
            Log::error('Dodo Payments API Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a checkout session
     * 
     * @param array $sessionData
     * @return array|null
     */
    public function createCheckoutSession($sessionData)
    {
        try {
            $client = new Client();
            
            Log::info('Dodo Payments Create Checkout Session Request', ['sessionData' => $sessionData]);
            
            $response = $client->post($this->baseUrl . '/checkouts', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $sessionData
            ]);
            
            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);
            Log::info('Dodo Payments Create Checkout Session Response', ['response' => $result]);
            return $result;
            
        } catch (GuzzleException $e) {
            Log::error('Dodo Payments Checkout Session Error: ' . $e->getMessage());
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                Log::error('Dodo Payments Checkout Session Error Response: ' . $responseBody);
            }
            return null;
        }
    }
    
    /**
     * Get payment details
     * 
     * @param string $paymentId
     * @return array|null
     */
    public function getPayment($paymentId)
    {
        try {
            $client = new Client();
            
            $response = $client->get($this->baseUrl . '/payments/' . $paymentId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ]
            ]);
            
            $body = $response->getBody()->getContents();
            return json_decode($body, true);
            
        } catch (GuzzleException $e) {
            Log::error('Dodo Payments Get Payment Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a subscription
     * 
     * @param array $subscriptionData
     * @return array|null
     */
    public function createSubscription($subscriptionData)
    {
        try {
            $client = new Client();
            
            $response = $client->post($this->baseUrl . '/subscriptions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $subscriptionData
            ]);
            
            $body = $response->getBody()->getContents();
            return json_decode($body, true);
            
        } catch (GuzzleException $e) {
            Log::error('Dodo Payments Subscription Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Verify webhook signature
     * 
     * @param string $payload
     * @param string $signature
     * @param string $secret
     * @return bool
     */
    public function verifyWebhookSignature($payload, $signature, $secret)
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }
}

