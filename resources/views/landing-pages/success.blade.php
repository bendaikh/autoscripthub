<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>Purchase Successful - {{ $landing_page->lp_title }}</title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .success-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 700px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }
        
        .success-icon i {
            color: white;
            font-size: 3rem;
        }
        
        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .success-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 15px;
        }
        
        .success-message {
            font-size: 1.2rem;
            color: #718096;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .order-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            text-align: left;
        }
        
        .order-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .order-detail-row:last-child {
            border-bottom: none;
        }
        
        .order-detail-label {
            font-weight: 600;
            color: #4a5568;
        }
        
        .order-detail-value {
            color: #2d3748;
            font-weight: 500;
        }
        
        .download-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 30px;
            color: white;
            margin: 30px 0;
        }
        
        .download-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .download-description {
            margin-bottom: 20px;
            opacity: 0.95;
        }
        
        .btn-download {
            background: white;
            color: #667eea;
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }
        
        .btn-download:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            color: #667eea;
            text-decoration: none;
        }
        
        .btn-home {
            background: #e2e8f0;
            color: #4a5568;
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        
        .btn-home:hover {
            background: #cbd5e0;
            color: #4a5568;
            text-decoration: none;
        }
        
        .email-info {
            background: #e6f7ff;
            border: 1px solid #91d5ff;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            color: #0050b3;
        }
        
        @media (max-width: 768px) {
            .success-container {
                padding: 30px 20px;
            }
            
            .success-title {
                font-size: 2rem;
            }
            
            .order-detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <!-- Success Icon -->
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <!-- Success Title -->
        <h1 class="success-title">Payment Successful!</h1>
        <p class="success-message">
            Thank you for your purchase! Your order has been completed successfully.
        </p>
        
        <!-- Email Info -->
        <div class="email-info">
            <i class="fas fa-envelope mr-2"></i>
            <strong>Order confirmation sent to:</strong> {{ $order->customer_email }}
        </div>
        
        <!-- Order Details -->
        <div class="order-details">
            <div class="order-detail-row">
                <span class="order-detail-label">Order Token:</span>
                <span class="order-detail-value">{{ $order->order_token }}</span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Product:</span>
                <span class="order-detail-value">{{ $landing_page->lp_title }}</span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Amount Paid:</span>
                <span class="order-detail-value">{{ $order->currency }} {{ number_format($order->amount, 2) }}</span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Payment Method:</span>
                <span class="order-detail-value">{{ ucfirst($order->payment_method) }}</span>
            </div>
            <div class="order-detail-row">
                <span class="order-detail-label">Date:</span>
                <span class="order-detail-value">{{ date('F d, Y - h:i A', strtotime($order->completed_at)) }}</span>
            </div>
        </div>
        
        <!-- Download Section -->
        @if($landing_page->lp_delivery_method == 'link' && $landing_page->lp_product_link)
        <div class="download-section">
            <div class="download-title">
                <i class="fas fa-download mr-2"></i>Download Your Product
            </div>
            <p class="download-description">
                Your purchase is ready! Click the button below to access your download link.
            </p>
            <a href="{{ url('/landing/download/' . $order->order_token) }}" class="btn-download" target="_blank">
                <i class="fas fa-external-link-alt mr-2"></i>Access Download Link
            </a>
        </div>
        @elseif($landing_page->lp_delivery_method == 'upload' && $landing_page->lp_product_file)
        <div class="download-section">
            <div class="download-title">
                <i class="fas fa-download mr-2"></i>Download Your Product
            </div>
            <p class="download-description">
                Your purchase is ready! Click the button below to download your 
                @if($landing_page->lp_product_file_type == 'folder')
                    files (folder will be downloaded as ZIP).
                @else
                    file.
                @endif
            </p>
            <a href="{{ url('/landing/download/' . $order->order_token) }}" class="btn-download">
                <i class="fas fa-download mr-2"></i>Download Now
            </a>
        </div>
        @endif
        
        <!-- Additional Info -->
        <p style="color: #718096; font-size: 0.9rem; margin-top: 20px;">
            <i class="fas fa-info-circle mr-1"></i>
            Keep this page bookmarked or save your order token to download your files again later.
        </p>
        
        <!-- Home Button -->
        @if(isset($setting->site_title))
        <a href="{{ url('/') }}" class="btn-home">
            <i class="fas fa-home mr-2"></i>Back to Home
        </a>
        @endif
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

