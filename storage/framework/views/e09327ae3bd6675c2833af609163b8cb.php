<!DOCTYPE HTML>
<html lang="en">
<head>
<title><?php echo e($allsettings->site_title); ?> - <?php echo e(__('Checkout')); ?></title>
<?php echo $__env->make('meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>
<body>
<?php echo $__env->make('header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="page-title-overlap pt-4" style="background-image: url('<?php echo e(url('/')); ?>/public/storage/settings/<?php echo e($allsettings->site_banner); ?>');">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="<?php echo e(URL::to('/')); ?>"><i class="dwg-home"></i><?php echo e(__('Home')); ?></a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page"><?php echo e(__('Checkout')); ?></li>
              </li>
             </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white"><?php echo e(__('Checkout')); ?></h1>
        </div>
      </div>
    </div>
<div class="container mb-5 pb-3">
      <div class="bg-light box-shadow-lg rounded-lg overflow-hidden">
        <div class="row">
         
          <!-- Content-->
          <?php if($cart_count != 0): ?>
          <section class="col-lg-8 pt-2 pt-lg-4 pb-4 mb-3">
          <form action="<?php echo e(route('checkout')); ?>" class="needs-validation" id="checkout_form" method="post" enctype="multipart/form-data">
            <?php echo e(csrf_field()); ?>

            <div class="pt-2 px-4 pr-lg-0 pl-xl-5">
             <?php if(Auth::guest()): ?>
              <h2 class="h6 border-bottom pb-3 mb-3"><?php echo e(__('Not a customer yet?')); ?></h2>
              <!-- Billing detail-->
              <div class="row pb-4">
                <div class="col-6 form-group">
                  <label for="mc-email"><?php echo e(__('Email Address')); ?>  <span class='text-danger'>*</span></label>
                  <input type="text" id="email" class="form-control" name="email" data-bvalidator="required,email">
                </div>
                <div class="col-sm-6 form-group">
                  <label for="mc-company"><?php echo e(__('Password')); ?> <span class='text-danger'>*</span></label>
                  <input type="password" id="password" class="form-control" name="password" data-bvalidator="required,minlen[6]">
                </div>
              </div>
              <?php endif; ?>
              <?php if(Auth::check()): ?>
              <input type="hidden" name="user_id" value="<?php echo e(Auth::user()->id); ?>">
              <?php endif; ?>
             <div class="widget mb-3 d-lg-none">
                <h2 class="widget-title"><?php echo e(__('Order Summary')); ?></h2>
                <?php 
                $subtotal = 0;
                $order_id = '';
                $item_price = '';
                $item_single_prices = '';
                $item_userid = '';
                $item_user_type = '';
                $commission = 0;
                $vendor_amount = 0;
                $single_price = 0;
                $default_single_price = 0;
                $coupon_code = ""; 
                $new_price = 0;
                $processfee = 0;
                $single_convert = 0;
                $default_single_convert = 0;
                ?>
                <?php $__currentLoopData = $cart['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php 
                $itemprice = $cart->total_price;
                $itemsingler = $cart->item_single_price * $cart->item_serial_stock;
                ?>
                <div class="media align-items-center pb-2 border-bottom">
                <a class="d-block mr-2" href="<?php echo e(url('/item')); ?>/<?php echo e($cart->item_slug); ?>">
                <?php if($cart->item_thumbnail!=''): ?>
                <img class="lazy rounded-sm" width="64" height="49" src="<?php echo e(Helper::Image_Path($cart->item_thumbnail,'no-image.png')); ?>"  alt="<?php echo e($cart->item_name); ?>"/>
                <?php else: ?>
                <img class="lazy rounded-sm" width="64" height="49" src="<?php echo e(url('/')); ?>/public/img/no-image.png"  alt="<?php echo e($cart->item_name); ?>"/>
                <?php endif; ?>
                </a>
                  <div class="media-body pl-1">
                    <h6 class="widget-product-title"><a href="<?php echo e(url('/item')); ?>/<?php echo e($cart->item_slug); ?>"><?php echo e($cart->item_name); ?></a></h6>
                    <div class="widget-product-meta"><span class="text-accent border-right pr-2 mr-2"><?php echo e(Helper::price_format($allsettings->site_currency_position,$itemsingler,$currency_symbol,$multicurrency)); ?></span><span class="font-size-xs text-muted"><?php echo e($cart->license); ?><?php if($cart->license == 'regular'): ?> (<?php echo e($addition_settings->regular_license); ?>) <?php elseif($cart->license == 'extended'): ?> (<?php echo e($addition_settings->extended_license); ?>) <?php endif; ?></span></div>
                    <?php if($cart->file_type == 'serial'): ?>
                    <span class="font-size-xs"><?php echo e(__('Stock')); ?> : <?php echo e($cart->item_serial_stock); ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                <?php
                $processfee += $cart->total_price; 
                $subtotal += $itemsingler;
                $order_id .= $cart->ord_id.',';
                $item_price .= Helper::price_value($itemsingler,$multicurrency).','; 
                $item_single_prices .= $itemsingler.',';
                $item_userid .= $cart->item_user_id.','; 
                $item_user_type .= $cart->exclusive_author; 
                $amount_price = $subtotal;
                $single_price += Helper::price_value($itemsingler,$multicurrency);
                $default_single_price += $itemsingler;
                $discount_value = 0;
                $default_discount_value = 0;
                $single_convert += Helper::price_value($itemsingler,$multicurrency);
                $default_single_convert += $itemsingler;
                $nprice = Helper::price_value($itemsingler,$multicurrency);
                if($cart->discount_price != 0)
                {
                    $price = $cart->discount_price;
                    $new_price += $cart->discount_price;
                    $coupon_code = $cart->coupon_code;
                    if($cart->coupon_type == 'percentage')
                    {
                    $discount_value += ($single_convert * $cart->coupon_value) / 100;
                    $default_discount_value += ($default_single_convert * $cart->coupon_value) / 100;
                    }
                    else
                    {
                      $discount_value += $cart->coupon_value;
                      $default_discount_value += $cart->coupon_value;
                    }
                }
                else
                {
                   $price = $itemprice;
                   $new_price += $itemprice;
                   $coupon_code = "";
                   $discount_value += 0;
                   $default_discount_value += 0;
                }
				if($cart->exclusive_author == 1)
                {
                   $commission +=($nprice * $allsettings->site_exclusive_commission) / 100;
                }
                else
                {
                  $commission +=($nprice * $allsettings->site_non_exclusive_commission) / 100;
                }
                ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php
                if($addition_settings->site_extra_fee_type == 'fixed')
                {
                   $extra_fee = $allsettings->site_extra_fee;
                }
                else
                {
                   $extra_fee = ($allsettings->site_extra_fee * $processfee) / 100;
                }
                ?>
                <ul class="list-unstyled font-size-sm py-3">
                <?php if($extra_fee != 0): ?>
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2"><?php echo e(__('Processing Fee')); ?></span><span class="text-right"><?php echo e(Helper::price_format($allsettings->site_currency_position,$extra_fee,$currency_symbol,$multicurrency)); ?></span></li>
                  <?php endif; ?>
                  <?php if($coupon_code != ""): ?>
                  <?php 
                  $coupon_discount = $subtotal - $new_price;
                  $final = $new_price + $extra_fee;
                  $last_price =  $new_price;
                  $priceamount = $new_price;
                  $finalvalue = $single_price + Helper::price_value($extra_fee,$multicurrency);
                  $pin = $finalvalue - $discount_value;
                  $discountprices = $discount_value;
                  $default_discountprices = $default_discount_value;
                  ?>
                  <li class="d-flex justify-content-between align-items-center font-size-base"><span class="mr-2"><?php echo e(__('Discount Price')); ?></span><span class="text-right"> - <?php echo e(Helper::plan_format($allsettings->site_currency_position,$discount_value,$currency_symbol)); ?></span></li>
                  <?php else: ?>
                  <?php 
                  $final = $subtotal+$extra_fee; 
                  $last_price =  $subtotal;
                  $priceamount = $subtotal;
                  $finalvalue = $single_price + Helper::price_value($extra_fee,$multicurrency);
                  $pin = $finalvalue;
                  $discountprices = 0;
                  $default_discountprices = 0;
                  ?>
                  <?php endif; ?>
                  <?php if($country_percent != 0): ?>
                  <?php
                  $vat_price = ($single_price * $country_percent) / 100;
                  $default_vat_price = ($default_single_price * $country_percent) / 100;
                  ?>
                  <li class="d-flex justify-content-between align-items-center font-size-base"><span class="mr-2"><?php echo e(__('VAT')); ?> (%)</span><span class="text-right"><?php echo e(Helper::plan_format($allsettings->site_currency_position,$vat_price,$currency_symbol)); ?></span></li>
                  <?php else: ?>
                  <?php
                  $vat_price = 0;
                  $default_vat_price = 0;
                  ?>
                  <?php endif; ?> 
                  <?php
                  
                  $finbb = $pin + $vat_price;
                  ?> 
                  <li class="d-flex justify-content-between align-items-center font-size-base"><span class="mr-2"><?php echo e(__('Total')); ?></span><span class="text-right"><?php echo e(Helper::plan_format($allsettings->site_currency_position,$finbb,$currency_symbol)); ?></span></li>
                </ul>
                <?php
                $vendor_amount = $single_price - $commission;
                ?>
                <input type="hidden" name="order_id" value="<?php echo e(rtrim($order_id,',')); ?>">
                <input type="hidden" name="currency_rate" value="<?php echo e($encrypter->encrypt($currency_rate)); ?>">
                <input type="hidden" name="currency_type" value="<?php echo e($encrypter->encrypt($currency_symbol)); ?>">
                <input type="hidden" name="currency_type_code" value="<?php echo e($encrypter->encrypt($multicurrency)); ?>">
                <input type="hidden" name="item_single_prices" value="<?php echo e($encrypter->encrypt(rtrim($item_single_prices,','))); ?>">
                <input type="hidden" name="item_prices" value="<?php echo e($encrypter->encrypt(rtrim($item_price,','))); ?>">
                <input type="hidden" name="item_user_id" value="<?php echo e(rtrim($item_userid,',')); ?>">
                <input type="hidden" name="vat_price" value="<?php echo e($encrypter->encrypt($vat_price)); ?>">
                <input type="hidden" name="amount" value="<?php echo e($encrypter->encrypt($single_convert)); ?>">
                <input type="hidden" name="processing_fee" value="<?php echo e($encrypter->encrypt(Helper::price_value($extra_fee,$multicurrency))); ?>">
                <input type="hidden" name="default_processing_fee" value="<?php echo e($encrypter->encrypt($extra_fee)); ?>">
                <input type="hidden" name="default_amount" value="<?php echo e($encrypter->encrypt($default_single_convert)); ?>">
                <input type="hidden" name="default_vat_price" value="<?php echo e($encrypter->encrypt($default_vat_price)); ?>">
                <input type="hidden" name="default_discount" value="<?php echo e($encrypter->encrypt($default_discountprices)); ?>">
                <input type="hidden" name="website_url" value="<?php echo e(url('/')); ?>">
                <input type="hidden" name="admin_amount" value="<?php echo e($encrypter->encrypt($commission)); ?>">
                <input type="hidden" name="vendor_amount" value="<?php echo e($encrypter->encrypt($vendor_amount)); ?>">
                <input type="hidden" name="discount" value="<?php echo e($encrypter->encrypt($discountprices)); ?>">
                <input type="hidden" name="token" class="token">
                <input type="hidden" name="reference" value="<?php echo e(Paystack::genTranxRef()); ?>">
                
               </div>
              <div class="accordion mb-2" id="payment-method" role="tablist">
                <?php $no = 1; ?>
                <?php $__currentLoopData = $get_payment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php 
                if($payment == '2checkout')
                {
                $payment = 'twocheckout';
                }
                else
                {
                $payment = $payment;
                }
                ?>
                <div class="card">
                  <div class="card-header" role="tab">
                    <?php if(Auth::check()): ?>
                    <h3 class="accordion-heading"><a href="#<?php echo e($payment); ?>" id="<?php echo e($payment); ?>" data-toggle="collapse"><?php echo e(__('Pay with')); ?> <?php if($payment == 'twocheckout'): ?> <?php echo e(__('2Checkout')); ?> <?php elseif($payment == 'dodopayments'): ?> <?php echo e(__('Card')); ?> <?php else: ?> <?php echo e($payment); ?> <?php endif; ?><span class="accordion-indicator"><i data-feather="chevron-up"></i></span></a></h3>
                    <?php else: ?>
                    <?php if($payment != 'wallet'): ?>
                    <h3 class="accordion-heading"><a href="#<?php echo e($payment); ?>" id="<?php echo e($payment); ?>" data-toggle="collapse"><?php echo e(__('Pay with')); ?> <?php if($payment == 'twocheckout'): ?> <?php echo e(__('2Checkout')); ?> <?php elseif($payment == 'dodopayments'): ?> <?php echo e(__('Card')); ?> <?php else: ?> <?php echo e($payment); ?> <?php endif; ?><span class="accordion-indicator"><i data-feather="chevron-up"></i></span></a></h3>
                    <?php endif; ?>
                    <?php endif; ?>
                  </div>
                  <div class="collapse <?php if($no == 1): ?> show <?php endif; ?>" id="<?php echo e($payment); ?>" data-parent="#payment-method" role="tabpanel">
                  <?php if($payment == 'paypal'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" <?php if($no == 1): ?> checked <?php endif; ?> data-bvalidator="required"> <?php echo e(__('PayPal')); ?></span> - <?php echo e(__('the safer, easier way to pay')); ?></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with PayPal')); ?></button>
                    </div>
                    <?php endif; ?>
                  <?php if($payment == 'stripe'): ?>
                    <div class="card-body font-size-sm custom-radio custom-control">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio"  value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Stripe')); ?></span> - <?php echo e(__('Credit or debit card')); ?></p>
                      <?php if($stripe_type == 'charges'): ?>
                      <div class="stripebox mb-3" id="ifYes" style="display:none;">
                        <label for="card-element"><?php echo e(__('Credit or debit card')); ?></label>
                        <div id="card-element"></div>
                        <div id="card-errors" role="alert"></div>
                      </div>
                      <?php endif; ?>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Stripe')); ?></button>
                    </div> 
                    <?php endif; ?>
                    <?php if(Auth::check()): ?>
                    <?php if($payment == 'wallet'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Wallet')); ?></span> - (<?php echo e(Helper::price_format($allsettings->site_currency_position,Auth::user()->earnings,$currency_symbol,$multicurrency)); ?>)</p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Wallet')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if($payment == 'twocheckout'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('2Checkout')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with 2Checkout')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'paystack'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('PayStack')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with PayStack')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'localbank'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Local Bank')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Local Bank')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'razorpay'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Razorpay')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Razorpay')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'payhere'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Payhere')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Payhere')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'payumoney'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Payumoney')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Payumoney')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'iyzico'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Iyzico')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Iyzico')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'flutterwave'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Flutterwave')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Flutterwave')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'coingate'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Coingate')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Coingate')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'ipay'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('iPay')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with iPay')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'payfast'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('PayFast')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with PayFast')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'coinpayments'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('CoinPayments')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with CoinPayments')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'mercadopago'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Mercadopago')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Mercadopago')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'sslcommerz'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('SSLCommerz')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with SSLCommerz')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'instamojo'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Instamojo')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Instamojo')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'aamarpay'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Aamarpay')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Aamarpay')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'mollie'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Mollie')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Mollie')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'robokassa'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Robokassa')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Robokassa')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'midtrans'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Midtrans')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Midtrans')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'coinbase'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Coinbase')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Coinbase')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'dodopayments'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Credit/Debit Card')); ?></span> - <?php echo e(__('Pay securely with your card')); ?></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Pay with Card')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'cashfree'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('Cashfree')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with Cashfree')); ?></button>
                    </div>
                    <?php endif; ?>
                    <?php if($payment == 'nowpayments'): ?>
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-<?php echo e($payment); ?>" name="payment_method" type="radio" class="custom_radio" value="<?php echo e($payment); ?>" data-bvalidator="required"> <?php echo e(__('NowPayments')); ?></span></p>
                      <button class="btn btn-primary" type="submit"><?php echo e(__('Checkout with NowPayments')); ?></button>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
                <?php $no++; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
            </div>
            </form>
          </section>
          <aside class="col-lg-4 d-none d-lg-block">
            <hr class="d-lg-none">
            <div class="cz-sidebar-static h-100 ml-auto border-left">
              <div class="widget mb-3">
                <h2 class="widget-title text-center"><?php echo e(__('Order Summary')); ?></h2>
                <?php 
                $subtotal = 0;
                $order_id = '';
                $item_price = '';
                $item_userid = '';
                $item_user_type = '';
                $commission = 0;
                $vendor_amount = 0;
                $single_price = 0;
                $coupon_code = ""; 
                $new_price = 0;
                $processfee = 0;
                $single_convert = 0;
                ?>
                <?php $__currentLoopData = $mobile['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php 
                $itemprice = $cart->total_price;
                $itemsingler = $cart->item_single_price * $cart->item_serial_stock;
                ?>
                <div class="media align-items-center pb-3 mb-3 border-bottom">
                <a class="d-block mr-2" href="<?php echo e(url('/item')); ?>/<?php echo e($cart->item_slug); ?>">
                <?php if($cart->item_thumbnail!=''): ?>
                <img class="lazy rounded-sm" width="64" height="49" src="<?php echo e(Helper::Image_Path($cart->item_thumbnail,'no-image.png')); ?>"  alt="<?php echo e($cart->item_name); ?>"/>
                <?php else: ?>
                <img class="lazy rounded-sm" width="64" height="49" src="<?php echo e(url('/')); ?>/public/img/no-image.png"  alt="<?php echo e($cart->item_name); ?>"/>
                <?php endif; ?>
                </a>
                  <div class="media-body pl-1">
                    <h6 class="widget-product-title"><a href="<?php echo e(url('/item')); ?>/<?php echo e($cart->item_slug); ?>"><?php echo e($cart->item_name); ?></a></h6>
                    <div class="widget-product-meta"><span class="text-accent border-right pr-2 mr-2"><?php echo e(Helper::price_format($allsettings->site_currency_position,$itemsingler,$currency_symbol,$multicurrency)); ?></span><span class="font-size-xs text-muted"><?php echo e($cart->license); ?><?php if($cart->license == 'regular'): ?> - <?php echo e($addition_settings->regular_license); ?> <?php if($cart->item_support == 1): ?> <?php echo e(__('Support')); ?> <?php else: ?> <?php echo e(__('not support')); ?> <?php endif; ?> <?php elseif($cart->license == 'extended'): ?> - <?php echo e($addition_settings->extended_license); ?> <?php if($cart->item_support == 1): ?> <?php echo e(__('Support')); ?> <?php else: ?> <?php echo e(__('not support')); ?> <?php endif; ?> <?php endif; ?></span></div>
                    <?php if($cart->file_type == 'serial'): ?>
                    <span class="font-size-xs"><?php echo e(__('Stock')); ?> : <?php echo e($cart->item_serial_stock); ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                <?php 
                $processfee += $cart->total_price;
                $subtotal += $itemsingler;
                $order_id .= $cart->ord_id.',';
                $item_price .= $itemprice.','; 
                $item_userid .= $cart->item_user_id.','; 
                $item_user_type .= $cart->exclusive_author; 
                $amount_price = $subtotal;
                $single_price += Helper::price_value($itemsingler,$multicurrency);
                $discount_value = 0;
                $single_convert += Helper::price_value($itemsingler,$multicurrency);
                if($cart->discount_price != 0)
                {
                    $price = $cart->discount_price;
                    $new_price += $cart->discount_price;
                    $coupon_code = $cart->coupon_code;
                    if($cart->coupon_type == 'percentage')
                    {
                    $discount_value += ($single_convert * $cart->coupon_value) / 100;
                    }
                    else
                    {
                      $discount_value += $cart->coupon_value;
                    }
                }
                else
                {
                   $price = $itemprice;
                   $new_price += $itemprice;
                   $coupon_code = "";
                   $discount_value += 0;
                }
				if($cart->exclusive_author == 1)
                {
                   $commission +=($price * $allsettings->site_exclusive_commission) / 100;
                }
                else
                {
                   $commission +=($price * $allsettings->site_non_exclusive_commission) / 100;
                }
                ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php
                if($addition_settings->site_extra_fee_type == 'fixed')
                {
                   $extra_fee = $allsettings->site_extra_fee;
                }
                else
                {
                   $extra_fee = ($allsettings->site_extra_fee * $processfee) / 100;
                }
                ?>
                <ul class="list-unstyled font-size-sm pt-3 pb-2 border-bottom">
                <?php if($extra_fee != 0): ?>
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2"><?php echo e(__('Processing Fee')); ?></span><span class="text-right"><?php echo e(Helper::price_format($allsettings->site_currency_position,$extra_fee,$currency_symbol,$multicurrency)); ?></span></li>
                  <?php endif; ?>
                  <?php if($coupon_code != ""): ?>
                  <?php 
                  $coupon_discount = $subtotal - $new_price;
                  $final = $new_price + $extra_fee;
                  $last_price =  $new_price;
                  $priceamount = $new_price;
                  $finalvalue = $single_price + Helper::price_value($extra_fee,$multicurrency);
                  $pin = $finalvalue - $discount_value;
                  $discountprices = $discount_value;
                  ?>
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2"><?php echo e(__('Discount Price')); ?></span><span class="text-right"> - <?php echo e(Helper::plan_format($allsettings->site_currency_position,$discount_value,$currency_symbol)); ?></span></li>
                  <?php else: ?>
                  <?php 
                  $final = $subtotal+$extra_fee; 
                  $last_price =  $subtotal;
                  $priceamount = $subtotal;
                  $finalvalue = $single_price + Helper::price_value($extra_fee,$multicurrency);
                  $pin = $finalvalue;
                  $discountprices = 0;
                  ?>
                  <?php endif; ?>
                  <?php if($country_percent != 0): ?>
                  <?php
                  $vat_price = ($single_price * $country_percent) / 100;
                  ?>
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2"><?php echo e(__('VAT')); ?> (%)</span><span class="text-right"><?php echo e(Helper::plan_format($allsettings->site_currency_position,$vat_price,$currency_symbol)); ?></span></li>
                  <?php else: ?>
                  <?php
                  $vat_price = 0;
                  ?>
                  <?php endif; ?> 
                  <?php
                  
                  $finbb = $pin + $vat_price;
                  ?>
                  <h3 class="font-weight-normal text-center my-4"><?php echo e(Helper::plan_format($allsettings->site_currency_position,$finbb,$currency_symbol)); ?></h3>
                  </ul>
               </div>
            </div>
          </aside>
          <?php else: ?>
          <section class="col-lg-12 pt-2 pt-lg-4 pb-4 mb-3">
          <div class="pt-2 px-4 pr-lg-0 pl-xl-5">
          <div class="col-lg-12" data-aos="fade-up" data-aos-delay="200">
          <div class="font-size-md"><?php echo e(__('Your cart is empty!')); ?></div>
          </div>
          </div>
          </section>
          <?php endif; ?>
        </div>
      </div>
    </div>
<?php echo $__env->make('footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- stripe code -->
<?php if(!empty($stripe_publish)): ?>
<script src="https://js.stripe.com/v3/"></script>
<?php if($stripe_type == 'intents'): ?>
<script type="text/javascript">
$(function () {
'use strict';
		$("#ifYes").hide();
        $('#stripe').click(function(){
            var value = "stripe";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
		
            if ($("#opt1-stripe").is(":checked")) {
                $("#ifYes").show();

} else {
                $("#ifYes").hide();
            }
        });
    });
</script>
<?php else: ?>
<script type="text/javascript">

	$(document).ready(function(){
        'use strict';
		$("#ifYes").hide();
        
		$('#stripe').click(function(){
            var value = "stripe";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
			if ($("#opt1-stripe").is(":checked")) {
                $("#ifYes").show();
				
				/* stripe code */
				
				var stripe = Stripe('<?php echo e($stripe_publish); ?>');
   
				var elements = stripe.elements();
					
				var style = {
				base: {
					color: '#32325d',
					lineHeight: '18px',
					fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
					fontSmoothing: 'antialiased',
					fontSize: '14px',
					'::placeholder': {
					color: '#aab7c4'
					}
				},
				invalid: {
					color: '#fa755a',
					iconColor: '#fa755a'
				}
				};
			 
				
				var card = elements.create('card', {style: style, hidePostalCode: true});
			 
				
				card.mount('#card-element');
			 
			   
				card.addEventListener('change', function(event) {
					var displayError = document.getElementById('card-errors');
					if (event.error) {
						displayError.textContent = event.error.message;
					} else {
						displayError.textContent = '';
					}
				});
			 
				
				var form = document.getElementById('checkout_form');
				form.addEventListener('submit', function(event) {
					/*event.preventDefault();*/
			        if ($("#opt1-stripe").is(":checked")) { event.preventDefault(); }
					stripe.createToken(card).then(function(result) {
					
						if (result.error) {
						
						var errorElement = document.getElementById('card-errors');
						errorElement.textContent = result.error.message;
						
						
						} else {
							
							document.querySelector('.token').value = result.token.id;
							 
							document.getElementById('checkout_form').submit();
						}
						/*document.querySelector('.token').value = result.token.id;
							 
							document.getElementById('checkout_form').submit();*/
						
					});
				});
							
						
			/* stripe code */	
				
				
				
            } else {
                $("#ifYes").hide();
            }
			
			
        });
	});
</script>
<?php endif; ?>
<?php endif; ?>
<script type="text/javascript">
$(document).ready(function(){
        'use strict';
		$('#paypal').click(function(){
            var value = "paypal";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
         $('#wallet').click(function(){
            var value = "wallet";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
  
        $('#twocheckout').click(function(){
            var value = "twocheckout";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		
		$('#paystack').click(function(){
            var value = "paystack";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		
		$('#localbank').click(function(){
            var value = "localbank";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		
		$('#razorpay').click(function(){
            var value = "razorpay";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		
		$('#payhere').click(function(){
            var value = "payhere";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		
		$('#payumoney').click(function(){
            var value = "payumoney";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		
		$('#iyzico').click(function(){
            var value = "iyzico";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		
		$('#flutterwave').click(function(){
            var value = "flutterwave";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		
		$('#coingate').click(function(){
            var value = "coingate";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		
		$('#ipay').click(function(){
            var value = "ipay";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		
		$('#payfast').click(function(){
            var value = "payfast";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		
		$('#coinpayments').click(function(){
            var value = "coinpayments";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
        $('#mercadopago').click(function(){
            var value = "mercadopago";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		$('#sslcommerz').click(function(){
            var value = "sslcommerz";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		$('#instamojo').click(function(){
            var value = "instamojo";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		$('#aamarpay').click(function(){
            var value = "aamarpay";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		$('#mollie').click(function(){
            var value = "mollie";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		$('#robokassa').click(function(){
            var value = "robokassa";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		$('#midtrans').click(function(){
            var value = "midtrans";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		$('#coinbase').click(function(){
            var value = "coinbase";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		$('#cashfree').click(function(){
            var value = "cashfree";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		$('#nowpayments').click(function(){
            var value = "nowpayments";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
		$('#dodopayments').click(function(){
            var value = "dodopayments";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
        });
});		
</script>
<!-- stripe code -->
</body>
</html><?php /**PATH C:\laragon\www\autoscripthub\resources\views/pages/checkout.blade.php ENDPATH**/ ?>