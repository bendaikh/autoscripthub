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

        // Real purchase count, used only when there is enough of it to show
        $completedOrders = \DB::table('landing_page_orders')
            ->where('lp_id', $data['landing_page']->lp_id)
            ->where('payment_status', 'completed')
            ->count();

        // Set page language before building translated content
        \Fickrr\Helpers\LandingPageI18n::usePage($data['landing_page']);

        $data['content'] = \Fickrr\Helpers\LandingPageContent::build(
            $data['landing_page'],
            $data['features'],
            $data['gallery_images'],
            $completedOrders
        );

        $data['landing_payment_methods'] = $this->enabledLandingPaymentMethods($data['setting']);

        return view('landing-pages.show', $data);
    }

    /**
     * Add landing page product to cart
     */
    public function addToCart($slug)
    {
        $landing_page = LandingPage::getBySlug($slug);
        
        if (!$landing_page) {
            Session::flash('error', __('Product not found'));
            return redirect()->back();
        }

        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', __('Please login to purchase'));
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
            Session::flash('info', __('This product is already in your cart'));
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

        Session::flash('success', __('Product added to cart successfully!'));
        return redirect('/cart');
    }

    /**
     * Buy now - direct checkout
     */
    public function buyNow($slug)
    {
        $landing_page = LandingPage::getBySlug($slug);
        
        if (!$landing_page) {
            Session::flash('error', __('Product not found'));
            return redirect()->back();
        }

        // Check if user is logged in
        if (!Auth::check()) {
            Session::flash('error', __('Please login to purchase'));
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
            Session::flash('error', __('Product not found'));
            return redirect()->back();
        }

        // Validate email
        $request->validate([
            'customer_email' => 'required|email',
            'payment_method' => 'required|in:paypal,dodopayments,whop',
        ]);

        $payment_method = $request->payment_method;
        $customer_email = $request->customer_email;
        $embedInPopup = $request->ajax() || $request->wantsJson();
        $sid = 1;
        $setting = Settings::editGeneral($sid);
        $enabledMethods = $this->enabledLandingPaymentMethods($setting);

        if (!in_array($payment_method, $enabledMethods, true)) {
            if ($embedInPopup) {
                return response()->json(['success' => false, 'message' => __('Invalid payment method')], 422);
            }
            Session::flash('error', __('Invalid payment method'));
            return redirect()->back();
        }
        
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
            return $this->processDodoPayments($landing_page, $order_token, $embedInPopup);
        } elseif ($payment_method == 'whop') {
            return $this->processWhop($landing_page, $order_token, $embedInPopup);
        }

        Session::flash('error', __('Invalid payment method'));
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
    private function processDodoPayments($landing_page, $order_token, $embedInPopup = false)
    {
        // Get DodoPayments configuration
        $dodopayments_mode = config('services.dodopayments.mode', 'test');
        $dodopayments_api_key = config('services.dodopayments.api_key');
        
        if (empty($dodopayments_api_key)) {
            if ($embedInPopup) {
                return response()->json(['success' => false, 'message' => 'DodoPayments API key is not configured. Please check your settings.'], 422);
            }
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
        
        $success_url = url('/landing/success/' . $order_token);
        
        // Create checkout session
        try {
            $landingEmail = trim((string) Session::get('landing_order_email'));
            $checkoutData = [
                'product_cart' => [
                    [
                        'product_id' => $defaultProductId,
                        'quantity' => 1,
                        'price' => $landing_page->lp_price * 100, // Amount in cents
                    ]
                ],
                'success_url' => $success_url,
                'cancel_url' => url('/landing/' . $landing_page->lp_slug),
                'customer' => [
                    'email' => $landingEmail,
                    'name' => $landingEmail,
                ],
                'metadata' => [
                    'order_token' => $order_token,
                    'landing_page_slug' => $landing_page->lp_slug,
                ],
            ];
            $checkoutData = \Fickrr\Services\DodoPaymentsService::applyCardOnlyCheckoutOptions(
                $checkoutData,
                $landingEmail
            );
            
            $response = $dodoService->createCheckoutSession($checkoutData);
            $checkoutUrl = \Fickrr\Services\DodoPaymentsService::cardOnlyCheckoutUrl(
                \Fickrr\Services\DodoPaymentsService::sessionCheckoutUrl($response),
                $landingEmail
            );

            if ($checkoutUrl) {
                $embed = [
                    'provider' => 'dodopayments',
                    'order_token' => $order_token,
                    'dodo_checkout_url' => $checkoutUrl,
                    'dodo_mode' => \Fickrr\Services\DodoPaymentsService::sdkMode($dodopayments_mode),
                    'item_name' => $landing_page->lp_title,
                    'amount_label' => $landing_page->lp_currency . ' ' . number_format((float) $landing_page->lp_price, abs((float) $landing_page->lp_price - round((float) $landing_page->lp_price)) < 0.00001 ? 0 : 2),
                    'cancel_url' => url('/landing/' . $landing_page->lp_slug),
                ];
                Session::put('landing_checkout_embed_' . $order_token, $embed);

                if ($embedInPopup) {
                    return response()->json([
                        'success' => true,
                        'embed_url' => url('/landing/checkout-embed/' . $order_token),
                    ]);
                }

                $data = [
                    'dodo_checkout_url' => $checkoutUrl,
                    'dodo_mode' => $embed['dodo_mode'],
                    'dodo_item_name' => $embed['item_name'],
                    'dodo_amount_label' => $embed['amount_label'],
                    'dodo_cancel_url' => $embed['cancel_url'],
                ];
                return view('dodo-embedded-checkout')->with($data);
            }

            if ($embedInPopup) {
                return response()->json(['success' => false, 'message' => 'Failed to initialize DodoPayments checkout'], 422);
            }
            
            Session::flash('error', 'Failed to initialize DodoPayments checkout');
            return redirect()->back();
            
        } catch (\Exception $e) {
            if ($embedInPopup) {
                return response()->json(['success' => false, 'message' => 'Payment error: ' . $e->getMessage()], 422);
            }
            Session::flash('error', 'Payment error: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    private function processWhop($landing_page, $order_token, $embedInPopup = false)
    {
        $whopService = \Fickrr\Services\WhopPaymentsService::fromConfig();
        if (!$whopService->isConfigured()) {
            if ($embedInPopup) {
                return response()->json(['success' => false, 'message' => 'Whop is not configured. Please check your payment settings.'], 422);
            }
            Session::flash('error', 'Whop is not configured. Please check your payment settings.');
            return redirect()->back();
        }

        $success_url = url('/landing/success/' . $order_token);
        $response = $whopService->createCheckoutConfiguration(
            $landing_page->lp_price,
            $landing_page->lp_currency,
            $landing_page->lp_title,
            $success_url,
            [
                'order_token' => $order_token,
                'landing_page_slug' => $landing_page->lp_slug,
                'payment_type' => 'landing',
            ]
        );

        $sessionId = \Fickrr\Services\WhopPaymentsService::extractSessionId($response);
        $planId = \Fickrr\Services\WhopPaymentsService::extractPlanId($response);
        if ($sessionId && $planId) {
            $embed = [
                'provider' => 'whop',
                'order_token' => $order_token,
                'whop_session_id' => $sessionId,
                'whop_plan_id' => $planId,
                'whop_return_url' => $success_url,
                'item_name' => $landing_page->lp_title,
                'amount_label' => $landing_page->lp_currency . ' ' . number_format((float) $landing_page->lp_price, abs((float) $landing_page->lp_price - round((float) $landing_page->lp_price)) < 0.00001 ? 0 : 2),
                'cancel_url' => url('/landing/' . $landing_page->lp_slug),
                'whop_prefill_email' => Session::get('landing_order_email'),
            ];
            Session::put('landing_checkout_embed_' . $order_token, $embed);

            if ($embedInPopup) {
                return response()->json([
                    'success' => true,
                    'embed_url' => url('/landing/checkout-embed/' . $order_token),
                ]);
            }

            return view('whop-embedded-checkout')->with([
                'whop_session_id' => $sessionId,
                'whop_plan_id' => $planId,
                'whop_return_url' => $success_url,
                'whop_item_name' => $landing_page->lp_title,
                'whop_amount_label' => $embed['amount_label'],
                'whop_cancel_url' => $embed['cancel_url'],
                'whop_prefill_email' => $embed['whop_prefill_email'],
            ]);
        }

        if ($embedInPopup) {
            return response()->json(['success' => false, 'message' => 'Failed to initialize Whop checkout'], 422);
        }

        Session::flash('error', 'Failed to initialize Whop checkout');
        return redirect()->back();
    }

    /**
     * Slim checkout UI for the landing-page payment popup iframe.
     */
    public function embedCheckout($order_token)
    {
        $embed = Session::get('landing_checkout_embed_' . $order_token);
        if (!$embed || ($embed['order_token'] ?? '') !== $order_token) {
            abort(404);
        }

        return view('landing-pages.embed-checkout', ['embed' => $embed]);
    }

    /**
     * PayPal plus a single activated card processor (Dodo or Whop).
     */
    private function enabledLandingPaymentMethods($setting)
    {
        $enabled = array_values(array_filter(array_map('trim', explode(',', $setting->payment_option ?? ''))));
        $methods = [];

        if (in_array('paypal', $enabled, true)) {
            $methods[] = 'paypal';
        }

        foreach ($enabled as $option) {
            if ($option === 'dodopayments' || $option === 'whop') {
                $methods[] = $option;
                break;
            }
        }

        return $methods;
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
            Session::flash('error', __('Order not found'));
            return redirect('/');
        }

        if (($order->payment_method ?? '') === 'whop' && $order->payment_status == 'pending') {
            $payment_token = request()->input('payment_id', request()->input('receipt_id', ''));
            if (request()->input('status') === 'error') {
                Session::flash('error', __('Payment failed or was canceled.'));
                return redirect('/');
            }
            if (empty($payment_token)) {
                Session::flash('error', __('Payment is pending. Please wait for confirmation.'));
                return redirect('/');
            }
            $whopService = \Fickrr\Services\WhopPaymentsService::fromConfig();
            $paymentDetails = $whopService->getPayment($payment_token);
            if (!$paymentDetails || !\Fickrr\Services\WhopPaymentsService::isSuccessfulStatus($paymentDetails['status'] ?? '')) {
                Session::flash('error', __('Payment verification failed.'));
                return redirect('/');
            }
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
            Session::flash('error', __('Product not found'));
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
            Session::flash('error', __('Order not found or not completed'));
            return redirect('/');
        }

        // Get landing page details
        $landing_page = LandingPage::getById($order->lp_id);
        
        if (!$landing_page) {
            Session::flash('error', __('Product not found'));
            return redirect()->back();
        }

        // Handle link delivery method
        if ($landing_page->lp_delivery_method == 'link') {
            if (!$landing_page->lp_product_link) {
                Session::flash('error', __('Product link not found'));
                return redirect()->back();
            }
            
            // Redirect to the download link
            return redirect($landing_page->lp_product_link);
        }

        // Handle file/folder upload delivery method
        if (!$landing_page->lp_product_file) {
            Session::flash('error', __('Product file not found'));
            return redirect()->back();
        }

        if ($landing_page->lp_product_file_type == 'folder') {
            // Create ZIP of folder
            $folderPath = public_path('storage/landing-products/' . $landing_page->lp_product_file);
            
            if (!file_exists($folderPath)) {
                Session::flash('error', __('Product files not found'));
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
                Session::flash('error', __('Could not create download archive'));
                return redirect()->back();
            }
        } else {
            // Single file download
            $filePath = public_path('storage/landing-products/' . $landing_page->lp_product_file);
            
            if (!file_exists($filePath)) {
                Session::flash('error', __('Product file not found'));
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
                'message' => __('Landing page not found')
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
                'message' => __('Validation failed'),
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
            'message' => __('Your message has been sent successfully! We will get back to you soon.')
        ]);
    }
}

