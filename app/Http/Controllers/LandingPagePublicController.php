<?php

namespace Fickrr\Http\Controllers;

use Illuminate\Http\Request;
use Fickrr\Models\LandingPage;
use Fickrr\Models\LandingCustomer;
use Fickrr\Models\LandingPageMessage;
use Fickrr\Models\Settings;
use Fickrr\Models\Items;
use Fickrr\Helpers\Helper;
use Auth;
use Session;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

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
        
        // Convert price to visitor's local currency
        // Assume lp_price is stored in USD
        $usdPrice = $data['landing_page']->lp_price;
        
        // Get visitor's IP and country for debugging
        $visitorIp = request()->ip();
        $detectedCountry = Helper::getVisitorCountry($visitorIp);
        
        // Convert price
        $data['price_data'] = Helper::convertPriceToLocalCurrency($usdPrice, $detectedCountry);
        
        // Also convert extended price if exists
        if ($data['landing_page']->lp_extended_price && $data['landing_page']->lp_extended_price > 0) {
            $data['extended_price_data'] = Helper::convertPriceToLocalCurrency($data['landing_page']->lp_extended_price, $detectedCountry);
        } else {
            $data['extended_price_data'] = null;
        }
        
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

        // Validate email
        $request->validate([
            'customer_email' => 'required|email',
            'payment_method' => 'required|in:paypal,dodopayments',
        ]);

        $payment_method = $request->payment_method;
        $customer_email = $request->customer_email;
        
        // Generate unique order token
        $order_token = 'LP-' . time() . '-' . rand(1000, 9999);

        // Create order record
        $order_data = [
            'lp_id' => $landing_page->lp_id,
            'customer_email' => $customer_email,
            'order_token' => $order_token,
            'amount' => $landing_page->lp_price,
            'currency' => $landing_page->lp_currency,
            'payment_method' => $payment_method,
            'payment_status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        \DB::table('landing_page_orders')->insert($order_data);

        // Save customer to database immediately (don't wait for payment completion)
        LandingCustomer::firstOrCreate(
            ['email' => $customer_email],
            [
                'total_purchases' => 0,
                'total_spent' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Store email in session for later use
        Session::put('landing_order_email', $customer_email);
        Session::put('landing_order_token', $order_token);

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
        
        $success_url = url('/landing/success/' . $order_token);
        $cancel_url = url('/landing/' . $landing_page->lp_slug);
        
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
                    'email' => Session::get('landing_order_email'),
                    'name' => Session::get('landing_order_email'),
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

    /**
     * Show success page after payment
     */
    public function success($order_token)
    {
        // Get order details
        $order = \DB::table('landing_page_orders')
            ->where('order_token', $order_token)
            ->first();

        if (!$order) {
            Session::flash('error', 'Order not found');
            return redirect('/');
        }

        // Update order status to completed if still pending
        if ($order->payment_status == 'pending') {
            \DB::table('landing_page_orders')
                ->where('order_token', $order_token)
                ->update([
                    'payment_status' => 'completed',
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);
                
            // Refresh order data
            $order = \DB::table('landing_page_orders')
                ->where('order_token', $order_token)
                ->first();
        }
        
        // Update customer purchase stats (works whether order was just completed or already completed)
        if ($order->payment_status == 'completed') {
            LandingCustomer::createOrUpdateCustomer($order->customer_email, $order->amount);
        }

        // Get landing page details
        $landing_page = LandingPage::getById($order->lp_id);
        
        if (!$landing_page) {
            Session::flash('error', 'Product not found');
            return redirect('/');
        }

        // Get settings
        $sid = 1;
        $data['setting'] = Settings::editGeneral($sid);
        $data['order'] = $order;
        $data['landing_page'] = $landing_page;

        return view('landing-pages.success', $data);
    }

    /**
     * Download product file
     */
    public function download($order_token)
    {
        // Get order details
        $order = \DB::table('landing_page_orders')
            ->where('order_token', $order_token)
            ->where('payment_status', 'completed')
            ->first();

        if (!$order) {
            Session::flash('error', 'Order not found or not completed');
            return redirect('/');
        }

        // Get landing page details
        $landing_page = LandingPage::getById($order->lp_id);
        
        if (!$landing_page) {
            Session::flash('error', 'Product not found');
            return redirect()->back();
        }

        // Handle link delivery method
        if ($landing_page->lp_delivery_method == 'link') {
            if (!$landing_page->lp_product_link) {
                Session::flash('error', 'Product link not found');
                return redirect()->back();
            }
            
            // Redirect to the download link
            return redirect($landing_page->lp_product_link);
        }

        // Handle file/folder upload delivery method
        if (!$landing_page->lp_product_file) {
            Session::flash('error', 'Product file not found');
            return redirect()->back();
        }

        if ($landing_page->lp_product_file_type == 'folder') {
            // Create ZIP of folder
            $folderPath = public_path('storage/landing-products/' . $landing_page->lp_product_file);
            
            if (!file_exists($folderPath)) {
                Session::flash('error', 'Product files not found');
                return redirect()->back();
            }

            $zipFileName = $landing_page->lp_product_file . '.zip';
            $zipFilePath = public_path('storage/landing-products/' . $zipFileName);

            // Create zip archive
            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($folderPath),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($folderPath) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }

                $zip->close();

                // Download and then delete the zip
                return response()->download($zipFilePath)->deleteFileAfterSend(true);
            } else {
                Session::flash('error', 'Could not create download archive');
                return redirect()->back();
            }
        } else {
            // Single file download
            $filePath = public_path('storage/landing-products/' . $landing_page->lp_product_file);
            
            if (!file_exists($filePath)) {
                Session::flash('error', 'Product file not found');
                return redirect()->back();
            }

            return response()->download($filePath);
        }
    }

    /**
     * Send message from landing page
     */
    public function sendMessage(Request $request, $slug)
    {
        $landing_page = LandingPage::getBySlug($slug);
        
        if (!$landing_page) {
            return response()->json([
                'success' => false,
                'message' => 'Landing page not found'
            ], 404);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'lp_id' => 'required|exists:landing_pages,lp_id',
            'lpm_name' => 'required|string|max:255',
            'lpm_email' => 'required|email|max:255',
            'lpm_phone' => 'nullable|string|max:50',
            'lpm_subject' => 'nullable|string|max:255',
            'lpm_message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Save message
        $message_data = [
            'lp_id' => $request->lp_id,
            'lpm_name' => $request->lpm_name,
            'lpm_email' => $request->lpm_email,
            'lpm_phone' => $request->lpm_phone,
            'lpm_subject' => $request->lpm_subject,
            'lpm_message' => $request->lpm_message,
            'lpm_status' => 0, // Unread
            'created_at' => now(),
            'updated_at' => now(),
        ];

        LandingPageMessage::saveMessage($message_data);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully! We will get back to you soon.'
        ]);
    }
}

