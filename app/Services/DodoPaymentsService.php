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

