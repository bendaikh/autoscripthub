<?php

namespace Fickrr\Http\Controllers;

use Illuminate\Http\Request;
use Fickrr\Models\LandingPage;
use Fickrr\Models\Settings;
use Fickrr\Models\Items;
use Auth;
use Session;
use GuzzleHttp\Client;

class LandingPagePublicController extends Controller
{
    /**
     * Display landing page by slug
     */
    public function show($slug)
    {
        $data['landing_page'] = LandingPage::getBySlug($slug);
        
        if (!$data['landing_page']) {
            abort(404);
        }

        // Decode features
        $data['features'] = json_decode($data['landing_page']->lp_features, true) ?? [];
        
        // Get gallery images
        $data['gallery_images'] = LandingPage::getGalleryImages($data['landing_page']->lp_id);
        
        // Get settings
        $sid = 1;
        $data['setting'] = Settings::editGeneral($sid);
        $data['additional'] = Settings::editAdditional();
        
        return view('landing-pages.show', $data);
    }

    /**
     * Add landing page product to cart
     */
    public function addToCart($slug)
    {
        $landing_page = LandingPage::getBySlug($slug);
        
        if (!$landing_page) {
            Session::flash('error', 'Product not found');
            return redirect()->back();
        }

        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', 'Please login to purchase');
            return redirect('/login');
        }

        $session_id = Session::getId();
        $user_id = Auth::user()->id;

        // Generate unique order token
        $order_token = 'LP-' . time() . '-' . rand(1000, 9999);

        // Check if already in cart
        $existing_order = \DB::table('item_order')
            ->where('session_id', $session_id)
            ->where('order_status', 'pending')
            ->where('item_token', 'lp-' . $landing_page->lp_slug)
            ->first();

        if ($existing_order) {
            Session::flash('info', 'This product is already in your cart');
            return redirect('/cart');
        }

        // Add to cart
        $order_data = [
            'session_id' => $session_id,
            'item_id' => 0,
            'item_name' => $landing_page->lp_title,
            'item_user_id' => 1,
            'item_token' => 'lp-' . $landing_page->lp_slug,
            'license' => 'regular',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'item_price' => $landing_page->lp_price,
            'vendor_amount' => 0,
            'admin_amount' => $landing_page->lp_price,
            'total_price' => $landing_page->lp_price,
            'order_status' => 'pending',
            'item_serial_stock' => 1,
            'currency_type' => $landing_page->lp_currency,
            'currency_type_code' => $landing_page->lp_currency,
            'item_single_price' => $landing_page->lp_price,
        ];

        \DB::table('item_order')->insert($order_data);

        Session::flash('success', 'Product added to cart successfully!');
        return redirect('/cart');
    }

    /**
     * Buy now - direct checkout
     */
    public function buyNow($slug)
    {
        $landing_page = LandingPage::getBySlug($slug);
        
        if (!$landing_page) {
            Session::flash('error', 'Product not found');
            return redirect()->back();
        }

        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', 'Please login to purchase');
            return redirect('/login');
        }

        $session_id = Session::getId();
        $user_id = Auth::user()->id;

        // Clear any pending orders for this session
        \DB::table('item_order')
            ->where('session_id', $session_id)
            ->where('order_status', 'pending')
            ->delete();

        // Generate unique order token
        $order_token = 'LP-' . time() . '-' . rand(1000, 9999);

        // Add to cart
        $order_data = [
            'session_id' => $session_id,
            'item_id' => 0,
            'item_name' => $landing_page->lp_title,
            'item_user_id' => 1,
            'item_token' => 'lp-' . $landing_page->lp_slug,
            'license' => 'regular',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'item_price' => $landing_page->lp_price,
            'vendor_amount' => 0,
            'admin_amount' => $landing_page->lp_price,
            'total_price' => $landing_page->lp_price,
            'order_status' => 'pending',
            'item_serial_stock' => 1,
            'currency_type' => $landing_page->lp_currency,
            'currency_type_code' => $landing_page->lp_currency,
            'item_single_price' => $landing_page->lp_price,
        ];

        \DB::table('item_order')->insert($order_data);

        // Redirect to checkout
        return redirect('/checkout');
    }

    /**
     * Process payment for landing page
     */
    public function processPayment(Request $request, $slug)
    {
        $landing_page = LandingPage::getBySlug($slug);
        
        if (!$landing_page) {
            Session::flash('error', 'Product not found');
            return redirect()->back();
        }

        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', 'Please login to purchase');
            return redirect('/login');
        }

        $payment_method = $request->payment_method;
        
        if (!$payment_method) {
            Session::flash('error', 'Please select a payment method');
            return redirect()->back();
        }

        $session_id = Session::getId();
        $user_id = Auth::user()->id;

        // Clear any existing pending orders
        \DB::table('item_order')
            ->where('session_id', $session_id)
            ->where('order_status', 'pending')
            ->delete();

        // Generate unique order token
        $order_token = 'LP-' . time() . '-' . rand(1000, 9999);

        // Add to cart
        $order_data = [
            'session_id' => $session_id,
            'item_id' => 0,
            'item_name' => $landing_page->lp_title,
            'item_user_id' => 1,
            'item_token' => 'lp-' . $landing_page->lp_slug,
            'license' => 'regular',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'item_price' => $landing_page->lp_price,
            'vendor_amount' => 0,
            'admin_amount' => $landing_page->lp_price,
            'total_price' => $landing_page->lp_price,
            'order_status' => 'pending',
            'item_serial_stock' => 1,
            'currency_type' => $landing_page->lp_currency,
            'currency_type_code' => $landing_page->lp_currency,
            'item_single_price' => $landing_page->lp_price,
        ];

        \DB::table('item_order')->insert($order_data);

        // Process based on payment method
        if ($payment_method == 'paypal') {
            return $this->processPayPal($landing_page, $order_token);
        } elseif ($payment_method == 'dodopayments') {
            return $this->processDodoPayments($landing_page, $order_token);
        }

        Session::flash('error', 'Invalid payment method');
        return redirect()->back();
    }

    /**
     * Process PayPal payment
     */
    private function processPayPal($landing_page, $order_token)
    {
        $sid = 1;
        $setting = Settings::editGeneral($sid);
        
        $paypal_email = $setting->paypal_email;
        $paypal_mode = $setting->paypal_mode;
        
        if ($paypal_mode == 1) {
            $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
        } else {
            $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        }
        
        $success_url = url('/success/' . $order_token);
        $cancel_url = url('/cancel');
        
        $paypal_params = [
            'cmd' => '_xclick',
            'business' => $paypal_email,
            'item_name' => $landing_page->lp_title,
            'item_number' => $order_token,
            'amount' => $landing_page->lp_price,
            'currency_code' => $landing_page->lp_currency,
            'return' => $success_url,
            'cancel_return' => $cancel_url,
            'notify_url' => url('/paypal-ipn'),
            'custom' => $order_token,
        ];
        
        $query_string = http_build_query($paypal_params);
        
        return redirect($paypal_url . '?' . $query_string);
    }

    /**
     * Process DodoPayments
     */
    private function processDodoPayments($landing_page, $order_token)
    {
        $encrypter = app('Illuminate\Contracts\Encryption\Encrypter');
        
        // Get DodoPayments configuration
        $dodopayments_mode = config('services.dodopayments.mode', 'test');
        $dodopayments_api_key = config('services.dodopayments.api_key');
        
        if (empty($dodopayments_api_key)) {
            Session::flash('error', 'DodoPayments API key is not configured. Please check your settings.');
            return redirect()->back();
        }
        
        // Initialize Dodo Payments Service
        $dodoService = new \Fickrr\Services\DodoPaymentsService(
            $dodopayments_api_key,
            $dodopayments_mode
        );
        
        // Get product ID from config
        $defaultProductId = config('services.dodopayments.product_id');
        
        if (empty($defaultProductId)) {
            // Create a temporary product for this landing page
            try {
                $productResponse = $dodoService->createProduct(
                    $landing_page->lp_price, // price in dollars
                    $landing_page->lp_currency, // currency
                    $landing_page->lp_title, // name
                    'digital_products' // tax category
                );
                
                if (isset($productResponse['product_id'])) {
                    $defaultProductId = $productResponse['product_id'];
                }
            } catch (\Exception $e) {
                // If product creation fails, continue with order
            }
        }
        
        // Build success URL with encrypted token
        $success_url = url('/checkout-dodopayments/' . $encrypter->encrypt($order_token));
        
        // Create checkout session
        try {
            $checkoutData = [
                'product_cart' => [
                    [
                        'product_id' => $defaultProductId,
                        'quantity' => 1,
                        'price' => $landing_page->lp_price * 100, // Amount in cents
                    ]
                ],
                'success_url' => $success_url,
                'cancel_url' => url('/cancel'),
                'customer' => [
                    'email' => Auth::user()->email,
                    'name' => Auth::user()->name ?? Auth::user()->username,
                ],
                'metadata' => [
                    'order_token' => $order_token,
                    'landing_page_slug' => $landing_page->lp_slug,
                ],
            ];
            
            $response = $dodoService->createCheckoutSession($checkoutData);
            
            if (isset($response['checkout_url'])) {
                return redirect($response['checkout_url']);
            } elseif (isset($response['url'])) {
                return redirect($response['url']);
            } elseif (isset($response['payment_url'])) {
                return redirect($response['payment_url']);
            }
            
            Session::flash('error', 'Failed to initialize DodoPayments checkout');
            return redirect()->back();
            
        } catch (\Exception $e) {
            Session::flash('error', 'Payment error: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}

