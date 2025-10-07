<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Checkout') }}</title>
@include('meta')
@include('style')
</head>
<body>
@include('header')
<div class="page-title-overlap pt-4" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Checkout') }}</li>
              </li>
             </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Checkout') }}</h1>
        </div>
      </div>
    </div>
<div class="container mb-5 pb-3">
      <div class="bg-light box-shadow-lg rounded-lg overflow-hidden">
        <div class="row">
         
          <!-- Content-->
          @if($cart_count != 0)
          <section class="col-lg-8 pt-2 pt-lg-4 pb-4 mb-3">
          <form action="{{ route('checkout') }}" class="needs-validation" id="checkout_form" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="pt-2 px-4 pr-lg-0 pl-xl-5">
             @if(Auth::guest())
              <h2 class="h6 border-bottom pb-3 mb-3">{{ __('Not a customer yet?') }}</h2>
              <!-- Billing detail-->
              <div class="row pb-4">
                <div class="col-6 form-group">
                  <label for="mc-email">{{ __('Email Address') }}  <span class='text-danger'>*</span></label>
                  <input type="text" id="email" class="form-control" name="email" data-bvalidator="required,email">
                </div>
                <div class="col-sm-6 form-group">
                  <label for="mc-company">{{ __('Password') }} <span class='text-danger'>*</span></label>
                  <input type="password" id="password" class="form-control" name="password" data-bvalidator="required,minlen[6]">
                </div>
              </div>
              @endif
              @if (Auth::check())
              <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
              @endif
             <div class="widget mb-3 d-lg-none">
                <h2 class="widget-title">{{ __('Order Summary') }}</h2>
                @php 
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
                @endphp
                @foreach($cart['item'] as $cart)
                @php 
                $itemprice = $cart->total_price;
                $itemsingler = $cart->item_single_price * $cart->item_serial_stock;
                @endphp
                <div class="media align-items-center pb-2 border-bottom">
                <a class="d-block mr-2" href="{{ url('/item') }}/{{ $cart->item_slug }}">
                @if($cart->item_thumbnail!='')
                <img class="lazy rounded-sm" width="64" height="49" src="{{ Helper::Image_Path($cart->item_thumbnail,'no-image.png') }}"  alt="{{ $cart->item_name }}"/>
                @else
                <img class="lazy rounded-sm" width="64" height="49" src="{{ url('/') }}/public/img/no-image.png"  alt="{{ $cart->item_name }}"/>
                @endif
                </a>
                  <div class="media-body pl-1">
                    <h6 class="widget-product-title"><a href="{{ url('/item') }}/{{ $cart->item_slug }}">{{ $cart->item_name }}</a></h6>
                    <div class="widget-product-meta"><span class="text-accent border-right pr-2 mr-2">{{ Helper::price_format($allsettings->site_currency_position,$itemsingler,$currency_symbol,$multicurrency) }}</span><span class="font-size-xs text-muted">{{ $cart->license }}@if($cart->license == 'regular') ({{ $addition_settings->regular_license }}) @elseif($cart->license == 'extended') ({{ $addition_settings->extended_license }}) @endif</span></div>
                    @if($cart->file_type == 'serial')
                    <span class="font-size-xs">{{ __('Stock') }} : {{ $cart->item_serial_stock }}</span>
                    @endif
                  </div>
                </div>
                @php
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
                @endphp
                @endforeach
                @php
                if($addition_settings->site_extra_fee_type == 'fixed')
                {
                   $extra_fee = $allsettings->site_extra_fee;
                }
                else
                {
                   $extra_fee = ($allsettings->site_extra_fee * $processfee) / 100;
                }
                @endphp
                <ul class="list-unstyled font-size-sm py-3">
                @if($extra_fee != 0)
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2">{{ __('Processing Fee') }}</span><span class="text-right">{{ Helper::price_format($allsettings->site_currency_position,$extra_fee,$currency_symbol,$multicurrency) }}</span></li>
                  @endif
                  @if($coupon_code != "")
                  @php 
                  $coupon_discount = $subtotal - $new_price;
                  $final = $new_price + $extra_fee;
                  $last_price =  $new_price;
                  $priceamount = $new_price;
                  $finalvalue = $single_price + Helper::price_value($extra_fee,$multicurrency);
                  $pin = $finalvalue - $discount_value;
                  $discountprices = $discount_value;
                  $default_discountprices = $default_discount_value;
                  @endphp
                  <li class="d-flex justify-content-between align-items-center font-size-base"><span class="mr-2">{{ __('Discount Price') }}</span><span class="text-right"> - {{ Helper::plan_format($allsettings->site_currency_position,$discount_value,$currency_symbol) }}</span></li>
                  @else
                  @php 
                  $final = $subtotal+$extra_fee; 
                  $last_price =  $subtotal;
                  $priceamount = $subtotal;
                  $finalvalue = $single_price + Helper::price_value($extra_fee,$multicurrency);
                  $pin = $finalvalue;
                  $discountprices = 0;
                  $default_discountprices = 0;
                  @endphp
                  @endif
                  @if($country_percent != 0)
                  @php
                  $vat_price = ($single_price * $country_percent) / 100;
                  $default_vat_price = ($default_single_price * $country_percent) / 100;
                  @endphp
                  <li class="d-flex justify-content-between align-items-center font-size-base"><span class="mr-2">{{ __('VAT') }} (%)</span><span class="text-right">{{ Helper::plan_format($allsettings->site_currency_position,$vat_price,$currency_symbol) }}</span></li>
                  @else
                  @php
                  $vat_price = 0;
                  $default_vat_price = 0;
                  @endphp
                  @endif 
                  @php
                  
                  $finbb = $pin + $vat_price;
                  @endphp 
                  <li class="d-flex justify-content-between align-items-center font-size-base"><span class="mr-2">{{ __('Total') }}</span><span class="text-right">{{ Helper::plan_format($allsettings->site_currency_position,$finbb,$currency_symbol) }}</span></li>
                </ul>
                @php
                $vendor_amount = $single_price - $commission;
                @endphp
                <input type="hidden" name="order_id" value="{{ rtrim($order_id,',') }}">
                <input type="hidden" name="currency_rate" value="{{ $encrypter->encrypt($currency_rate) }}">
                <input type="hidden" name="currency_type" value="{{ $encrypter->encrypt($currency_symbol) }}">
                <input type="hidden" name="currency_type_code" value="{{ $encrypter->encrypt($multicurrency) }}">
                <input type="hidden" name="item_single_prices" value="{{ $encrypter->encrypt(rtrim($item_single_prices,',')) }}">
                <input type="hidden" name="item_prices" value="{{ $encrypter->encrypt(rtrim($item_price,',')) }}">
                <input type="hidden" name="item_user_id" value="{{ rtrim($item_userid,',') }}">
                <input type="hidden" name="vat_price" value="{{ $encrypter->encrypt($vat_price) }}">
                <input type="hidden" name="amount" value="{{ $encrypter->encrypt($single_convert) }}">
                <input type="hidden" name="processing_fee" value="{{ $encrypter->encrypt(Helper::price_value($extra_fee,$multicurrency)) }}">
                <input type="hidden" name="default_processing_fee" value="{{ $encrypter->encrypt($extra_fee) }}">
                <input type="hidden" name="default_amount" value="{{ $encrypter->encrypt($default_single_convert) }}">
                <input type="hidden" name="default_vat_price" value="{{ $encrypter->encrypt($default_vat_price) }}">
                <input type="hidden" name="default_discount" value="{{ $encrypter->encrypt($default_discountprices) }}">
                <input type="hidden" name="website_url" value="{{ url('/') }}">
                <input type="hidden" name="admin_amount" value="{{ $encrypter->encrypt($commission) }}">
                <input type="hidden" name="vendor_amount" value="{{ $encrypter->encrypt($vendor_amount) }}">
                <input type="hidden" name="discount" value="{{ $encrypter->encrypt($discountprices) }}">
                <input type="hidden" name="token" class="token">
                <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
                
               </div>
              <div class="accordion mb-2" id="payment-method" role="tablist">
                @php $no = 1; @endphp
                @foreach($get_payment as $payment)
                @php 
                if($payment == '2checkout')
                {
                $payment = 'twocheckout';
                }
                else
                {
                $payment = $payment;
                }
                @endphp
                <div class="card">
                  <div class="card-header" role="tab">
                    @if (Auth::check())
                    <h3 class="accordion-heading"><a href="#{{ $payment }}" id="{{ $payment }}" data-toggle="collapse">{{ __('Pay with') }} @if($payment == 'twocheckout') {{ __('2Checkout') }} @else {{ $payment }} @endif<span class="accordion-indicator"><i data-feather="chevron-up"></i></span></a></h3>
                    @else
                    @if($payment != 'wallet')
                    <h3 class="accordion-heading"><a href="#{{ $payment }}" id="{{ $payment }}" data-toggle="collapse">{{ __('Pay with') }} @if($payment == 'twocheckout') {{ __('2Checkout') }} @else {{ $payment }} @endif<span class="accordion-indicator"><i data-feather="chevron-up"></i></span></a></h3>
                    @endif
                    @endif
                  </div>
                  <div class="collapse @if($no == 1) show @endif" id="{{ $payment }}" data-parent="#payment-method" role="tabpanel">
                  @if($payment == 'paypal')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required"> {{ __('PayPal') }}</span> - {{ __('the safer, easier way to pay') }}</p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with PayPal') }}</button>
                    </div>
                    @endif
                  @if($payment == 'stripe')
                    <div class="card-body font-size-sm custom-radio custom-control">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio"  value="{{ $payment }}" data-bvalidator="required"> {{ __('Stripe') }}</span> - {{ __('Credit or debit card') }}</p>
                      @if($stripe_type == 'charges')
                      <div class="stripebox mb-3" id="ifYes" style="display:none;">
                        <label for="card-element">{{ __('Credit or debit card') }}</label>
                        <div id="card-element"></div>
                        <div id="card-errors" role="alert"></div>
                      </div>
                      @endif
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Stripe') }}</button>
                    </div> 
                    @endif
                    @if (Auth::check())
                    @if($payment == 'wallet')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Wallet') }}</span> - ({{ Helper::price_format($allsettings->site_currency_position,Auth::user()->earnings,$currency_symbol,$multicurrency) }})</p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Wallet') }}</button>
                    </div>
                    @endif
                    @endif
                    @if($payment == 'twocheckout')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('2Checkout') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with 2Checkout') }}</button>
                    </div>
                    @endif
                    @if($payment == 'paystack')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('PayStack') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with PayStack') }}</button>
                    </div>
                    @endif
                    @if($payment == 'localbank')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Local Bank') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Local Bank') }}</button>
                    </div>
                    @endif
                    @if($payment == 'razorpay')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Razorpay') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Razorpay') }}</button>
                    </div>
                    @endif
                    @if($payment == 'payhere')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Payhere') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Payhere') }}</button>
                    </div>
                    @endif
                    @if($payment == 'payumoney')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Payumoney') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Payumoney') }}</button>
                    </div>
                    @endif
                    @if($payment == 'iyzico')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Iyzico') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Iyzico') }}</button>
                    </div>
                    @endif
                    @if($payment == 'flutterwave')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Flutterwave') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Flutterwave') }}</button>
                    </div>
                    @endif
                    @if($payment == 'coingate')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Coingate') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Coingate') }}</button>
                    </div>
                    @endif
                    @if($payment == 'ipay')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('iPay') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with iPay') }}</button>
                    </div>
                    @endif
                    @if($payment == 'payfast')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('PayFast') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with PayFast') }}</button>
                    </div>
                    @endif
                    @if($payment == 'coinpayments')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('CoinPayments') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with CoinPayments') }}</button>
                    </div>
                    @endif
                    @if($payment == 'mercadopago')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Mercadopago') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Mercadopago') }}</button>
                    </div>
                    @endif
                    @if($payment == 'sslcommerz')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('SSLCommerz') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with SSLCommerz') }}</button>
                    </div>
                    @endif
                    @if($payment == 'instamojo')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Instamojo') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Instamojo') }}</button>
                    </div>
                    @endif
                    @if($payment == 'aamarpay')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Aamarpay') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Aamarpay') }}</button>
                    </div>
                    @endif
                    @if($payment == 'mollie')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Mollie') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Mollie') }}</button>
                    </div>
                    @endif
                    @if($payment == 'robokassa')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Robokassa') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Robokassa') }}</button>
                    </div>
                    @endif
                    @if($payment == 'midtrans')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Midtrans') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Midtrans') }}</button>
                    </div>
                    @endif
                    @if($payment == 'coinbase')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Coinbase') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Coinbase') }}</button>
                    </div>
                    @endif
                    @if($payment == 'cashfree')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('Cashfree') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with Cashfree') }}</button>
                    </div>
                    @endif
                    @if($payment == 'nowpayments')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <p><span class='font-weight-medium'><input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio" value="{{ $payment }}" data-bvalidator="required"> {{ __('NowPayments') }}</span></p>
                      <button class="btn btn-primary" type="submit">{{ __('Checkout with NowPayments') }}</button>
                    </div>
                    @endif
                  </div>
                </div>
                @php $no++; @endphp
                @endforeach
              </div>
            </div>
            </form>
          </section>
          <aside class="col-lg-4 d-none d-lg-block">
            <hr class="d-lg-none">
            <div class="cz-sidebar-static h-100 ml-auto border-left">
              <div class="widget mb-3">
                <h2 class="widget-title text-center">{{ __('Order Summary') }}</h2>
                @php 
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
                @endphp
                @foreach($mobile['item'] as $cart)
                @php 
                $itemprice = $cart->total_price;
                $itemsingler = $cart->item_single_price * $cart->item_serial_stock;
                @endphp
                <div class="media align-items-center pb-3 mb-3 border-bottom">
                <a class="d-block mr-2" href="{{ url('/item') }}/{{ $cart->item_slug }}">
                @if($cart->item_thumbnail!='')
                <img class="lazy rounded-sm" width="64" height="49" src="{{ Helper::Image_Path($cart->item_thumbnail,'no-image.png') }}"  alt="{{ $cart->item_name }}"/>
                @else
                <img class="lazy rounded-sm" width="64" height="49" src="{{ url('/') }}/public/img/no-image.png"  alt="{{ $cart->item_name }}"/>
                @endif
                </a>
                  <div class="media-body pl-1">
                    <h6 class="widget-product-title"><a href="{{ url('/item') }}/{{ $cart->item_slug }}">{{ $cart->item_name }}</a></h6>
                    <div class="widget-product-meta"><span class="text-accent border-right pr-2 mr-2">{{ Helper::price_format($allsettings->site_currency_position,$itemsingler,$currency_symbol,$multicurrency) }}</span><span class="font-size-xs text-muted">{{ $cart->license }}@if($cart->license == 'regular') - {{ $addition_settings->regular_license }} @if($cart->item_support == 1) {{ __('Support') }} @else {{ __('not support') }} @endif @elseif($cart->license == 'extended') - {{ $addition_settings->extended_license }} @if($cart->item_support == 1) {{ __('Support') }} @else {{ __('not support') }} @endif @endif</span></div>
                    @if($cart->file_type == 'serial')
                    <span class="font-size-xs">{{ __('Stock') }} : {{ $cart->item_serial_stock }}</span>
                    @endif
                  </div>
                </div>
                @php 
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
                @endphp
                @endforeach
                @php
                if($addition_settings->site_extra_fee_type == 'fixed')
                {
                   $extra_fee = $allsettings->site_extra_fee;
                }
                else
                {
                   $extra_fee = ($allsettings->site_extra_fee * $processfee) / 100;
                }
                @endphp
                <ul class="list-unstyled font-size-sm pt-3 pb-2 border-bottom">
                @if($extra_fee != 0)
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2">{{ __('Processing Fee') }}</span><span class="text-right">{{ Helper::price_format($allsettings->site_currency_position,$extra_fee,$currency_symbol,$multicurrency) }}</span></li>
                  @endif
                  @if($coupon_code != "")
                  @php 
                  $coupon_discount = $subtotal - $new_price;
                  $final = $new_price + $extra_fee;
                  $last_price =  $new_price;
                  $priceamount = $new_price;
                  $finalvalue = $single_price + Helper::price_value($extra_fee,$multicurrency);
                  $pin = $finalvalue - $discount_value;
                  $discountprices = $discount_value;
                  @endphp
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2">{{ __('Discount Price') }}</span><span class="text-right"> - {{ Helper::plan_format($allsettings->site_currency_position,$discount_value,$currency_symbol) }}</span></li>
                  @else
                  @php 
                  $final = $subtotal+$extra_fee; 
                  $last_price =  $subtotal;
                  $priceamount = $subtotal;
                  $finalvalue = $single_price + Helper::price_value($extra_fee,$multicurrency);
                  $pin = $finalvalue;
                  $discountprices = 0;
                  @endphp
                  @endif
                  @if($country_percent != 0)
                  @php
                  $vat_price = ($single_price * $country_percent) / 100;
                  @endphp
                  <li class="d-flex justify-content-between align-items-center"><span class="mr-2">{{ __('VAT') }} (%)</span><span class="text-right">{{ Helper::plan_format($allsettings->site_currency_position,$vat_price,$currency_symbol) }}</span></li>
                  @else
                  @php
                  $vat_price = 0;
                  @endphp
                  @endif 
                  @php
                  
                  $finbb = $pin + $vat_price;
                  @endphp
                  <h3 class="font-weight-normal text-center my-4">{{ Helper::plan_format($allsettings->site_currency_position,$finbb,$currency_symbol) }}</h3>
                  </ul>
               </div>
            </div>
          </aside>
          @else
          <section class="col-lg-12 pt-2 pt-lg-4 pb-4 mb-3">
          <div class="pt-2 px-4 pr-lg-0 pl-xl-5">
          <div class="col-lg-12" data-aos="fade-up" data-aos-delay="200">
          <div class="font-size-md">{{ __('Your cart is empty!') }}</div>
          </div>
          </div>
          </section>
          @endif
        </div>
      </div>
    </div>
@include('footer')
@include('script')
<!-- stripe code -->
@if(!empty($stripe_publish))
<script src="https://js.stripe.com/v3/"></script>
@if($stripe_type == 'intents')
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
@else
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
				
				var stripe = Stripe('{{ $stripe_publish }}');
   
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
@endif
@endif
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
});		
</script>
<!-- stripe code -->
</body>
</html>