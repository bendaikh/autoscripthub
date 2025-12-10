<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- SEO Meta Tags -->
    <title>{{ $landing_page->lp_meta_title ?? $landing_page->lp_title }}</title>
    <meta name="description" content="{{ $landing_page->lp_meta_description ?? strip_tags(substr($landing_page->lp_description, 0, 160)) }}">
    <meta name="keywords" content="{{ $landing_page->lp_meta_keywords ?? $landing_page->lp_title }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $landing_page->lp_title }}">
    <meta property="og:description" content="{{ strip_tags(substr($landing_page->lp_description, 0, 200)) }}">
    @if($landing_page->lp_banner_image)
    <meta property="og:image" content="{{ asset('storage/landing-pages/' . $landing_page->lp_banner_image) }}">
    @endif
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            background-color: #fff;
            overflow-x: hidden;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.5;
        }
        
        .hero-content {
            position: relative;
            z-index: 10;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 25px;
            line-height: 1.2;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.95;
            font-weight: 400;
        }
        
        .hero-banner {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            margin-top: 30px;
        }
        
        .hero-banner img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        /* CTA Buttons */
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 18px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
            color: white;
            text-decoration: none;
        }
        
        .btn-secondary-custom {
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            padding: 18px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-left: 15px;
        }
        
        .btn-secondary-custom:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
            text-decoration: none;
        }
        
        /* Price Display */
        .price-display {
            display: flex;
            align-items: baseline;
            margin: 30px 0;
        }
        
        .price-currency {
            font-size: 1.8rem;
            font-weight: 600;
            margin-right: 5px;
        }
        
        .price-amount {
            font-size: 3.5rem;
            font-weight: 800;
        }
        
        .price-extended {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: line-through;
            margin-left: 15px;
        }
        
        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #2d3748;
            text-align: center;
        }
        
        .section-subtitle {
            font-size: 1.2rem;
            color: #718096;
            text-align: center;
            margin-bottom: 60px;
        }
        
        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            color: white;
            font-size: 1.5rem;
        }
        
        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2d3748;
        }
        
        .feature-description {
            color: #718096;
            line-height: 1.6;
        }
        
        /* Description Section */
        .description-section {
            padding: 80px 0;
            background: white;
        }
        
        .description-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #4a5568;
        }
        
        .description-content h1,
        .description-content h2,
        .description-content h3,
        .description-content h4 {
            margin-top: 30px;
            margin-bottom: 15px;
            color: #2d3748;
            font-weight: 600;
        }
        
        .description-content p {
            margin-bottom: 15px;
        }
        
        .description-content ul,
        .description-content ol {
            margin-bottom: 20px;
            padding-left: 30px;
        }
        
        /* Gallery Section */
        .gallery-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }
        
        .gallery-item {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background: white;
        }
        
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .gallery-item img {
            width: 100%;
            height: auto;
            min-height: 280px;
            max-height: 600px;
            object-fit: contain;
            object-position: center;
            display: block;
            background: #f8f9fa;
        }
        
        .gallery-item-content {
            padding: 20px;
        }
        
        .gallery-item-description {
            color: #4a5568;
            font-size: 1rem;
            line-height: 1.6;
            margin: 0;
        }
        
        /* Trust/Security Section */
        .trust-section {
            padding: 60px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-top: 1px solid #e2e8f0;
        }
        
        .trust-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .trust-item {
            text-align: center;
            padding: 30px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .trust-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .trust-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 1.8rem;
        }
        
        .trust-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .trust-description {
            color: #718096;
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 0;
        }
        
        .payment-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .payment-icons img {
            height: 30px;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        
        .payment-icons img:hover {
            opacity: 1;
        }
        
        .payment-icons i {
            font-size: 2rem;
            color: #718096;
            transition: color 0.3s;
        }
        
        .payment-icons i:hover {
            color: #667eea;
        }
        
        @media (max-width: 768px) {
            .gallery-grid {
                grid-template-columns: 1fr;
            }
            
            .trust-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .cta-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .cta-subtitle {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        
        .cta-section .btn-primary-custom {
            background: white;
            color: #667eea;
        }
        
        .cta-section .btn-primary-custom:hover {
            background: #f8f9fa;
        }
        
        /* Navbar */
        .landing-navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand-custom {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            text-decoration: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .btn-primary-custom,
            .btn-secondary-custom {
                padding: 15px 30px;
                font-size: 1rem;
                display: block;
                margin: 10px 0;
                text-align: center;
            }
            
            .price-amount {
                font-size: 2.5rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .cta-title {
                font-size: 2rem;
            }
        }
        
        /* Alert Messages */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        /* Chat Icon */
        .chat-icon {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
        }

        .chat-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.6);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            }
            50% {
                box-shadow: 0 5px 30px rgba(102, 126, 234, 0.7);
            }
            100% {
                box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            }
        }

        .chat-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }

        /* Chat Modal */
        .chat-modal-content {
            border-radius: 15px;
            border: none;
            overflow: hidden;
        }

        .chat-modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 20px 30px;
        }

        .chat-modal-body {
            padding: 30px;
        }

        .chat-form-group {
            margin-bottom: 20px;
        }

        .chat-form-group label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: block;
        }

        .chat-form-group input,
        .chat-form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .chat-form-group input:focus,
        .chat-form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .chat-submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
        }

        .chat-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .chat-submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .chat-icon {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="landing-navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ url('/') }}" class="navbar-brand-custom">
                    @if(isset($setting->site_logo) && $setting->site_logo)
                        <img src="{{ url('/') }}/public/storage/settings/{{ $setting->site_logo }}" alt="{{ $setting->site_title }}" style="height: 40px;">
                    @else
                        {{ $setting->site_title ?? 'Home' }}
                    @endif
                </a>
                <div>
                    <!-- No login/register buttons for landing pages -->
                </div>
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    @if(Session::has('success'))
    <div class="container mt-4">
        <div class="alert alert-success">
            <i class="fas fa-check-circle mr-2"></i> {{ Session::get('success') }}
        </div>
    </div>
    @endif

    @if(Session::has('error'))
    <div class="container mt-4">
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ Session::get('error') }}
        </div>
    </div>
    @endif

    @if(Session::has('info'))
    <div class="container mt-4">
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i> {{ Session::get('info') }}
        </div>
    </div>
    @endif

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center hero-content">
                <div class="col-lg-6">
                    <h1 class="hero-title">{{ $landing_page->lp_title }}</h1>
                    <p class="hero-subtitle">{{ strip_tags(substr($landing_page->lp_description, 0, 200)) }}...</p>
                    
                    <div class="price-display">
                        <span class="price-currency">{{ $price_data['symbol'] ?? '$' }}</span>
                        <span class="price-amount">{{ number_format($price_data['price'] ?? $landing_page->lp_price, 2) }}</span>
                        @if($extended_price_data && $extended_price_data['price'] > 0)
                        <span class="price-extended">{{ $extended_price_data['symbol'] }} {{ number_format($extended_price_data['price'], 2) }}</span>
                        @endif
                    </div>
                    
                    {{-- Debug info (remove this in production) --}}
                    @if(isset($debug_info) && config('app.debug'))
                    <div style="font-size: 0.8rem; color: #999; margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 5px;">
                        <strong>Debug Info:</strong><br>
                        IP: {{ $debug_info['ip'] }}<br>
                        Country: {{ $debug_info['country'] }}<br>
                        Currency: {{ $debug_info['currency_code'] }}<br>
                        USD Price: {{ $debug_info['original_usd'] }}<br>
                        Converted Price: {{ $debug_info['converted_price'] }}<br>
                        @if($debug_info['ip'] === '127.0.0.1' || $debug_info['ip'] === '::1')
                        <br><strong style="color: #e74c3c;">⚠️ Localhost Detected!</strong><br>
                        To test with Morocco, add <code>?force_country=MA</code> to the URL<br>
                        Example: <code>{{ url()->current() }}?force_country=MA</code>
                        @endif
                    </div>
                    @endif
                    
                    <div class="hero-actions">
                        <button type="button" class="btn-primary-custom" onclick="showPaymentModal()">
                            <i class="fas fa-shopping-cart mr-2"></i> Buy Now
                        </button>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    @if($landing_page->lp_banner_image)
                    <div class="hero-banner">
                        <img src="{{ asset('storage/landing-pages/' . $landing_page->lp_banner_image) }}" alt="{{ $landing_page->lp_title }}">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    @if(count($features) > 0)
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Key Features</h2>
            <p class="section-subtitle">Everything you need to succeed</p>
            
            <div class="row">
                @foreach($features as $index => $feature)
                @if(!empty($feature))
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            @php
                                $icons = ['fas fa-star', 'fas fa-rocket', 'fas fa-shield-alt', 'fas fa-cog', 'fas fa-check-circle', 'fas fa-bolt', 'fas fa-crown', 'fas fa-gem', 'fas fa-heart'];
                                $icon = $icons[$index % count($icons)];
                            @endphp
                            <i class="{{ $icon }}"></i>
                        </div>
                        <h3 class="feature-title">{{ $feature }}</h3>
                        <p class="feature-description">Experience the power of this amazing feature that helps you achieve your goals faster and more efficiently.</p>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Description Section -->
    <section class="description-section">
        <div class="container">
            <div class="description-content">
                {!! $landing_page->lp_description !!}
            </div>
        </div>
    </section>

    <!-- What You'll Get Section -->
    @if(!empty($landing_page->lp_what_you_get))
    <section class="what-you-get-section" style="padding: 80px 0; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
        <div class="container">
            <h2 class="section-title">What You'll Get After Payment</h2>
            <p class="section-subtitle">Here's exactly what you'll receive when you complete your purchase</p>
            <div class="what-you-get-content" style="max-width: 900px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);">
                <div class="description-content">
                    {!! $landing_page->lp_what_you_get !!}
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Gallery Section -->
    @if(count($gallery_images) > 0)
    <section class="gallery-section">
        <div class="container">
            <div class="gallery-grid">
                @foreach($gallery_images as $image)
                <div class="gallery-item">
                    <img src="{{ asset('storage/landing-pages/' . $image->lpg_image) }}" alt="{{ $image->lpg_description ?? 'Product Image' }}">
                    @if(!empty($image->lpg_description))
                    <div class="gallery-item-content">
                        <p class="gallery-item-description">{{ $image->lpg_description }}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Ready to Get Started?</h2>
            <p class="cta-subtitle">Join thousands of satisfied customers today!</p>
            <div class="price-display justify-content-center">
                <span class="price-currency">{{ $price_data['symbol'] }}</span>
                <span class="price-amount">{{ number_format($price_data['price'], 2) }}</span>
            </div>
            <button type="button" class="btn-primary-custom" onclick="showPaymentModal()">
                <i class="fas fa-shopping-cart mr-2"></i> Purchase Now
            </button>
        </div>
    </section>

    <!-- Trust & Security Section -->
    <section class="trust-section">
        <div class="container">
            <div class="trust-grid">
                <!-- Secure Payments -->
                <div class="trust-item">
                    <div class="trust-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="trust-title">Secure Payments</h3>
                    <p class="trust-description">Your payment information is protected with industry-standard SSL encryption. All transactions are 100% secure.</p>
                    <div class="payment-icons">
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fab fa-cc-amex"></i>
                        <i class="fab fa-paypal"></i>
                    </div>
                </div>
                
                <!-- PayPal Protection -->
                <div class="trust-item">
                    <div class="trust-icon">
                        <i class="fab fa-paypal"></i>
                    </div>
                    <h3 class="trust-title">PayPal Protected</h3>
                    <p class="trust-description">Pay safely with PayPal's Buyer Protection. Your purchase is protected and you can shop with confidence.</p>
                </div>
                
                <!-- Money Back Guarantee -->
                <div class="trust-item">
                    <div class="trust-icon">
                        <i class="fas fa-undo-alt"></i>
                    </div>
                    <h3 class="trust-title">Refund Policy</h3>
                    <p class="trust-description">Not satisfied? We offer hassle-free refunds. Your satisfaction is our priority and we stand behind our products.</p>
                </div>
                
                <!-- Customer Support -->
                <div class="trust-item">
                    <div class="trust-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="trust-title">Premium Support</h3>
                    <p class="trust-description">Get help when you need it. Our dedicated support team is here to assist you with any questions or concerns.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background: #2d3748; color: white; padding: 40px 0; text-align: center;">
        <div class="container">
            <p>&copy; {{ date('Y') }} {{ $setting->site_title ?? 'Your Site' }}. All rights reserved.</p>
            <p class="mt-2">
                <a href="{{ url('/') }}" style="color: white; margin: 0 15px;">Home</a>
                <a href="{{ url('/contact') }}" style="color: white; margin: 0 15px;">Contact</a>
                @if(isset($setting->site_email))
                <a href="mailto:{{ $setting->site_email }}" style="color: white; margin: 0 15px;">{{ $setting->site_email }}</a>
                @endif
            </p>
        </div>
    </footer>

    <!-- Chat Icon -->
    <div class="chat-icon" onclick="showChatModal()" title="Send us a message">
        <i class="fas fa-comments"></i>
    </div>

    <!-- Chat Modal -->
    <div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content chat-modal-content">
                <div class="modal-header chat-modal-header">
                    <h5 class="modal-title" id="chatModalLabel">
                        <i class="fas fa-comments mr-2"></i> Send us a Message
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body chat-modal-body">
                    <form id="chatForm" action="{{ url('/landing/' . $landing_page->lp_slug . '/send-message') }}" method="POST">
                        @csrf
                        <input type="hidden" name="lp_id" value="{{ $landing_page->lp_id }}">
                        
                        <div class="chat-form-group">
                            <label for="lpm_name">Your Name <span style="color: red;">*</span></label>
                            <input type="text" name="lpm_name" id="lpm_name" class="form-control" required placeholder="Enter your name">
                        </div>

                        <div class="chat-form-group">
                            <label for="lpm_email">Your Email <span style="color: red;">*</span></label>
                            <input type="email" name="lpm_email" id="lpm_email" class="form-control" required placeholder="Enter your email">
                        </div>

                        <div class="chat-form-group">
                            <label for="lpm_phone">Phone Number (Optional)</label>
                            <input type="text" name="lpm_phone" id="lpm_phone" class="form-control" placeholder="Enter your phone number">
                        </div>

                        <div class="chat-form-group">
                            <label for="lpm_subject">Subject (Optional)</label>
                            <input type="text" name="lpm_subject" id="lpm_subject" class="form-control" placeholder="Enter subject">
                        </div>

                        <div class="chat-form-group">
                            <label for="lpm_message">Message <span style="color: red;">*</span></label>
                            <textarea name="lpm_message" id="lpm_message" class="form-control" rows="5" required placeholder="Enter your message..."></textarea>
                        </div>

                        <button type="submit" class="chat-submit-btn" id="chatSubmitBtn">
                            <i class="fas fa-paper-plane mr-2"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="fas fa-lock mr-2"></i> Choose Payment Method
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 40px 30px;">
                    <div class="text-center mb-4">
                        <h3 style="font-weight: 600; color: #2d3748;">{{ $landing_page->lp_title }}</h3>
                        <div style="font-size: 2rem; font-weight: 700; color: #667eea; margin-top: 15px;">
                            {{ $price_data['symbol'] }} {{ number_format($price_data['price'], 2) }}
                        </div>
                    </div>

                    <form id="paymentForm" action="{{ url('/landing/' . $landing_page->lp_slug . '/process-payment') }}" method="POST">
                        @csrf
                        
                        <!-- Email Field (Mandatory) -->
                        <div class="form-group">
                            <label for="customer_email" style="font-weight: 600; color: #2d3748;">
                                <i class="fas fa-envelope mr-2"></i>Email Address <span style="color: red;">*</span>
                            </label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control" 
                                   placeholder="Enter your email address" required
                                   style="padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem;">
                            <small style="color: #718096;">We'll send your purchase details to this email</small>
                        </div>
                        
                        <!-- PayPal Option -->
                        <div class="payment-option" onclick="selectPayment('paypal')" style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 15px; cursor: pointer; transition: all 0.3s;">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" id="paypal_radio" value="paypal" style="width: 20px; height: 20px; margin-right: 15px;">
                                <div class="flex-grow-1">
                                    <h5 style="margin: 0; font-weight: 600; color: #2d3748;">
                                        <i class="fab fa-paypal" style="color: #0070ba; font-size: 1.5rem;"></i> PayPal
                                    </h5>
                                    <p style="margin: 5px 0 0 0; color: #718096; font-size: 0.9rem;">The safer, easier way to pay</p>
                                </div>
                            </div>
                        </div>

                        <!-- DodoPayments (Card) Option -->
                        <div class="payment-option" onclick="selectPayment('dodopayments')" style="border: 2px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; cursor: pointer; transition: all 0.3s;">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="payment_method" id="dodopayments_radio" value="dodopayments" style="width: 20px; height: 20px; margin-right: 15px;">
                                <div class="flex-grow-1">
                                    <h5 style="margin: 0; font-weight: 600; color: #2d3748;">
                                        <i class="fas fa-credit-card" style="color: #667eea; font-size: 1.5rem;"></i> Credit/Debit Card
                                    </h5>
                                    <p style="margin: 5px 0 0 0; color: #718096; font-size: 0.9rem;">Pay securely with your card</p>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="paymentBtn" class="btn btn-block" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; font-size: 1.1rem; font-weight: 600; border-radius: 10px; border: none; transition: all 0.3s;" disabled>
                            <i class="fas fa-lock mr-2"></i> Proceed to Payment
                        </button>
                    </form>

                    <div class="text-center mt-3" style="color: #718096; font-size: 0.85rem;">
                        <i class="fas fa-shield-alt mr-1"></i> Secure payment • <i class="fas fa-lock mr-1"></i> SSL encrypted
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Payment Modal Scripts -->
    <script>
        function showPaymentModal() {
            $('#paymentModal').modal('show');
        }

        function selectPayment(method) {
            // Remove active class from all options
            $('.payment-option').css({
                'border-color': '#e2e8f0',
                'background': 'white'
            });
            
            // Add active class to selected option
            $('input[value="' + method + '"]').closest('.payment-option').css({
                'border-color': '#667eea',
                'background': '#f7fafc'
            });
            
            // Check the radio button
            $('input[value="' + method + '"]').prop('checked', true);
            
            // Enable submit button if email is filled
            checkFormValidity();
        }
        
        // Check form validity
        function checkFormValidity() {
            const email = $('#customer_email').val();
            const paymentMethod = $('input[name="payment_method"]:checked').val();
            
            if (email && email.includes('@') && paymentMethod) {
                $('#paymentBtn').prop('disabled', false);
            } else {
                $('#paymentBtn').prop('disabled', true);
            }
        }

        // Hover effects
        $(document).ready(function() {
            // Validate email input
            $('#customer_email').on('input', function() {
                checkFormValidity();
            });
            
            $('.payment-option').hover(
                function() {
                    if (!$(this).find('input').is(':checked')) {
                        $(this).css('border-color', '#cbd5e0');
                    }
                },
                function() {
                    if (!$(this).find('input').is(':checked')) {
                        $(this).css('border-color', '#e2e8f0');
                    }
                }
            );
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Chat Modal Functions
        function showChatModal() {
            $('#chatModal').modal('show');
        }

        // Handle chat form submission
        $('#chatForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $('#chatSubmitBtn');
            const originalText = submitBtn.html();
            
            // Disable button and show loading
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Sending...');
            
            // Submit form via AJAX
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        $('#chatModal').modal('hide');
                        $('body').append('<div class="alert alert-success" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;"><i class="fas fa-check-circle mr-2"></i> ' + response.message + '</div>');
                        
                        // Reset form
                        $('#chatForm')[0].reset();
                        
                        // Hide alert after 5 seconds
                        setTimeout(function() {
                            $('.alert-success').fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 5000);
                    } else {
                        alert(response.message || 'An error occurred. Please try again.');
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalText);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join('\n');
                    }
                    alert(errorMessage);
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                }
            });
        });

        // Reset form when modal is closed
        $('#chatModal').on('hidden.bs.modal', function() {
            $('#chatForm')[0].reset();
            $('#chatSubmitBtn').prop('disabled', false);
            $('#chatSubmitBtn').html('<i class="fas fa-paper-plane mr-2"></i> Send Message');
        });
    </script>
</body>
</html>

