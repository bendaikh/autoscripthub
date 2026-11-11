<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Checkout') }}</title>
@include('meta')
@include('style')
@if(in_array('whop', $get_payment ?? []))
<script async defer src="https://js.whop.com/static/checkout/loader.js"></script>
<style>
  .whop-checkout-wrapper { width: 100%; min-height: 420px; }
  .whop-checkout-wrapper iframe { width: 100% !important; min-height: 420px; border: 0; }
  #whopInlineBox label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
</style>
@endif
@if(in_array('dodopayments', $get_payment ?? []))
<script src="https://cdn.jsdelivr.net/npm/dodopayments-checkout@latest/dist/index.js"></script>
<style>
  .dodo-checkout-wrapper { width: 100%; min-height: 420px; }
  #dodo-inline-checkout { min-height: 420px; width: 100%; }
  #dodoInlineBox label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
</style>
@endif
<style>
.hv-checkout { background:#fff; border:1px solid #e6e9f0; border-radius:18px; padding:1.35rem 1.35rem 1.15rem; box-shadow:0 10px 30px rgba(15,23,42,.06); }
.hv-co-title { font-size:1.35rem; font-weight:800; margin:0 0 1.1rem; color:#0f172a; }
.hv-co-step { display:flex; gap:.85rem; margin-bottom:1.15rem; }
.hv-co-num { flex:0 0 28px; width:28px; height:28px; border-radius:50%; background:#7c3aed; color:#fff; font-size:.82rem; font-weight:800; display:inline-flex; align-items:center; justify-content:center; }
.hv-co-step-body { flex:1; min-width:0; }
.hv-co-step-body h3 { font-size:.98rem; font-weight:700; margin:0 0 .7rem; color:#0f172a; }
.hv-co-signed { color:#475569; }
.hv-co-tiles { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:.75rem; }
.hv-co-tile { position:relative; display:flex; flex-direction:column; justify-content:center; gap:.35rem; min-height:92px; margin:0; padding:.9rem 1rem; border:2px solid #e6e9f0; border-radius:14px; background:#fff; cursor:pointer; }
.hv-co-tile strong { font-size:.95rem; color:#0f172a; }
.hv-co-tile small { color:#64748b; font-size:.75rem; }
.hv-co-tile.is-selected { border-color:#7c3aed; background:#f5f3ff; }
.hv-co-check { position:absolute; top:10px; right:10px; width:22px; height:22px; border-radius:50%; background:#e9e4f8; color:transparent; font-size:.75rem; display:flex; align-items:center; justify-content:center; }
.hv-co-tile.is-selected .hv-co-check { background:#7c3aed; color:#fff; }
.hv-co-radio { position:absolute; opacity:0; width:1px; height:1px; }
.hv-co-paypal { font-weight:800; font-size:1.2rem; letter-spacing:-.03em; color:#003087; }
.hv-co-paypal span { color:#009cde; }
.hv-co-brands { display:flex; gap:.3rem; }
.hv-co-brands i { font-style:normal; font-size:.62rem; font-weight:800; letter-spacing:.03em; padding:.15rem .35rem; border-radius:4px; background:#fff; border:1px solid #e6e9f0; color:#334155; }
.hv-co-panels { margin-top:.85rem; }
.hv-co-panel[hidden] { display:none !important; }
.hv-co-native-btn { position:absolute !important; width:1px !important; height:1px !important; padding:0 !important; margin:-1px !important; overflow:hidden !important; clip:rect(0,0,0,0) !important; border:0 !important; }
.hv-co-pay { display:block; width:100%; border:0; border-radius:12px; padding:.9rem 1rem; font-size:1rem; font-weight:800; background:#7c3aed; color:#fff; }
.hv-co-pay.is-paypal { background:#ffc439; color:#003087; }
.hv-co-pay:hover { filter:brightness(1.05); color:#fff; }
.hv-co-pay.is-paypal:hover { color:#003087; }
.hv-co-trust { margin:.7rem 0 0; text-align:center; color:#64748b; font-size:.82rem; }
@media (max-width: 640px) {
  .hv-checkout { padding:1rem .85rem; }
  .hv-co-tiles { grid-template-columns:1fr; }
}
</style>
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
              <div id="guest-checkout-holder" class="d-none" aria-hidden="true"></div>
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
                $checkoutExact = false;
                @endphp
                @foreach($cart['item'] as $cart)
                @php 
                $itemprice = $cart->total_price;
                $itemsingler = $cart->item_single_price * $cart->item_serial_stock;
                $lineExact = Helper::is_single_price_item($cart);
                if ($lineExact) { $checkoutExact = true; }
                $lineAmount = Helper::price_value($itemsingler,$multicurrency,$lineExact);
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
                    <div class="widget-product-meta"><span class="text-accent border-right pr-2 mr-2">{{ Helper::price_format($allsettings->site_currency_position,$itemsingler,$currency_symbol,$multicurrency,$lineExact) }}</span>@if(!$lineExact)<span class="font-size-xs text-muted">{{ $cart->license }}@if($cart->license == 'regular') ({{ $addition_settings->regular_license }}) @elseif($cart->license == 'extended') ({{ $addition_settings->extended_license }}) @endif</span>@endif</div>
                    @if($cart->file_type == 'serial')
                    <span class="font-size-xs">{{ __('Stock') }} : {{ $cart->item_serial_stock }}</span>
                    @endif
                  </div>
                </div>
                @php
                $processfee += $cart->total_price; 
                $subtotal += $itemsingler;
                $order_id .= $cart->ord_id.',';
                $item_price .= $lineAmount.','; 
                $item_single_prices .= $itemsingler.',';
                $item_userid .= $cart->item_user_id.','; 
                $item_user_type .= $cart->exclusive_author; 
                $amount_price = $subtotal;
                $single_price += $lineAmount;
                $default_single_price += $itemsingler;
                $discount_value = 0;
                $default_discount_value = 0;
                $single_convert += $lineAmount;
                $default_single_convert += $itemsingler;
                $nprice = $lineAmount;
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
                  <li class="d-flex justify-content-between align-items-center font-size-base"><span class="mr-2">{{ __('Total') }}</span><span class="text-right">{{ Helper::plan_format($allsettings->site_currency_position,$finbb,$currency_symbol,$checkoutExact) }}</span></li>
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
              <div class="hv-checkout" id="hv-checkout" data-amount="{{ Helper::plan_format($allsettings->site_currency_position,$finbb,$currency_symbol,$checkoutExact) }}">
                <h2 class="hv-co-title">{{ __('Checkout') }}</h2>
                <div class="hv-co-step">
                  <span class="hv-co-num">1</span>
                  <div class="hv-co-step-body">
                    <h3>{{ __('Your account') }}</h3>
                    @if(Auth::guest())
                    <div id="guest-checkout-fields" class="guest-checkout-fields">
                      <div class="row">
                        <div class="col-sm-6 form-group mb-2">
                          <label for="email">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                          <input type="text" id="email" class="form-control" name="email" data-bvalidator="required,email" autocomplete="email" required>
                        </div>
                        <div class="col-sm-6 form-group mb-2">
                          <label for="password">{{ __('Password') }} <span class="text-danger">*</span></label>
                          <input type="password" id="password" class="form-control" name="password" data-bvalidator="required,minlen[6]" autocomplete="new-password" required>
                        </div>
                      </div>
                      <p class="font-size-sm text-muted mb-0">{{ __('Create an account to complete your purchase and download your files.') }}</p>
                    </div>
                    @else
                    <p class="hv-co-signed mb-0">{{ __('Signed in as') }} <strong>{{ Auth::user()->email }}</strong></p>
                    @endif
                  </div>
                </div>
                <div class="hv-co-step">
                  <span class="hv-co-num">2</span>
                  <div class="hv-co-step-body">
                    <h3>{{ __('Choose how you want to pay') }}</h3>
                    <div class="hv-co-tiles" id="payment-method">
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
                @if(Auth::guest() && $payment == 'wallet')
                @else
                @php
                  $hvPayLabel = $payment;
                  if ($payment == 'twocheckout') { $hvPayLabel = __('2Checkout'); }
                  elseif ($payment == 'dodopayments' || $payment == 'whop' || $payment == 'stripe') { $hvPayLabel = __('Credit/Debit card'); }
                  elseif ($payment == 'paypal') { $hvPayLabel = 'PayPal'; }
                  else { $hvPayLabel = ucfirst($payment); }
                  $hvPayKind = ($payment == 'paypal') ? 'paypal' : (in_array($payment, ['stripe','whop','dodopayments','twocheckout'], true) ? 'card' : 'other');
                @endphp
                <label class="hv-co-tile{{ $no == 1 ? ' is-selected' : '' }}" id="{{ $payment }}" data-method="{{ $payment }}" data-kind="{{ $hvPayKind }}" data-label="{{ $hvPayLabel }}">
                  <input id="opt1-{{ $payment }}" name="payment_method" type="radio" class="custom_radio hv-co-radio" value="{{ $payment }}" @if($no == 1) checked @endif data-bvalidator="required">
                  <span class="hv-co-check" aria-hidden="true">&#10003;</span>
                  @if($hvPayKind == 'paypal')
                  <span class="hv-co-paypal">Pay<span>Pal</span></span>
                  <small>{{ __('the safer, easier way to pay') }}</small>
                  @elseif($hvPayKind == 'card')
                  <strong>{{ $hvPayLabel }}</strong>
                  <span class="hv-co-brands"><i>Visa</i><i>MC</i><i>Amex</i></span>
                  @else
                  <strong>{{ $hvPayLabel }}</strong>
                  <small>{{ __('Secure checkout') }}</small>
                  @endif
                </label>
                @php $no++; @endphp
                @endif
                @endforeach
                    </div>
                    <div class="hv-co-panels">
                @php $no = 1; @endphp
                @foreach($get_payment as $payment)
                @php
                if($payment == '2checkout') { $payment = 'twocheckout'; }
                @endphp
                @if(Auth::guest() && $payment == 'wallet')
                @else
                <div class="hv-co-panel" data-method="{{ $payment }}" @if($no != 1) hidden @endif>
                  @if($payment == 'paypal')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <div class="guest-checkout-slot" data-guest-slot="paypal"></div>
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with PayPal') }}</button>
                    </div>
                    @endif
                  @if($payment == 'stripe')
                    <div class="card-body font-size-sm custom-radio custom-control">
                      @if($stripe_type == 'charges')
                      <div class="stripebox mb-3" id="ifYes" style="display:none;">
                        <label for="card-element">{{ __('Credit or debit card') }}</label>
                        <div id="card-element"></div>
                        <div id="card-errors" role="alert"></div>
                      </div>
                      @endif
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Stripe') }}</button>
                    </div> 
                    @endif
                    @if (Auth::check())
                    @if($payment == 'wallet')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Wallet') }}</button>
                    </div>
                    @endif
                    @endif
                    @if($payment == 'twocheckout')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with 2Checkout') }}</button>
                    </div>
                    @endif
                    @if($payment == 'paystack')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with PayStack') }}</button>
                    </div>
                    @endif
                    @if($payment == 'localbank')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Local Bank') }}</button>
                    </div>
                    @endif
                    @if($payment == 'razorpay')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Razorpay') }}</button>
                    </div>
                    @endif
                    @if($payment == 'payhere')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Payhere') }}</button>
                    </div>
                    @endif
                    @if($payment == 'payumoney')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Payumoney') }}</button>
                    </div>
                    @endif
                    @if($payment == 'iyzico')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Iyzico') }}</button>
                    </div>
                    @endif
                    @if($payment == 'flutterwave')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Flutterwave') }}</button>
                    </div>
                    @endif
                    @if($payment == 'coingate')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Coingate') }}</button>
                    </div>
                    @endif
                    @if($payment == 'ipay')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with iPay') }}</button>
                    </div>
                    @endif
                    @if($payment == 'payfast')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with PayFast') }}</button>
                    </div>
                    @endif
                    @if($payment == 'coinpayments')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with CoinPayments') }}</button>
                    </div>
                    @endif
                    @if($payment == 'mercadopago')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Mercadopago') }}</button>
                    </div>
                    @endif
                    @if($payment == 'sslcommerz')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with SSLCommerz') }}</button>
                    </div>
                    @endif
                    @if($payment == 'instamojo')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Instamojo') }}</button>
                    </div>
                    @endif
                    @if($payment == 'aamarpay')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Aamarpay') }}</button>
                    </div>
                    @endif
                    @if($payment == 'mollie')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Mollie') }}</button>
                    </div>
                    @endif
                    @if($payment == 'robokassa')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Robokassa') }}</button>
                    </div>
                    @endif
                    @if($payment == 'midtrans')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Midtrans') }}</button>
                    </div>
                    @endif
                    @if($payment == 'coinbase')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Coinbase') }}</button>
                    </div>
                    @endif
                    @if($payment == 'dodopayments')
                    <div class="card-body font-size-sm custom-radio custom-control">
                      <div class="guest-checkout-slot" data-guest-slot="dodopayments"></div>
                      <div class="dodobox mb-3" id="dodoInlineBox" style="display:none;">
                        <label for="dodo-inline-checkout">{{ __('Credit or debit card') }}</label>
                        <div id="dodo-inline-loading" class="text-muted font-size-sm mb-2" style="display:none;">{{ __('Loading secure card form...') }}</div>
                        <div id="dodo-inline-checkout" class="dodo-checkout-wrapper"></div>
                        <div id="dodo-inline-error" class="text-danger mt-2" role="alert" style="display:none;"></div>
                      </div>
                      <button class="btn btn-primary hv-co-native-btn" type="button" id="dodoCheckoutBtn">{{ __('Pay with Card') }}</button>
                    </div>
                    @endif
                    @if($payment == 'whop')
                    <div class="card-body font-size-sm custom-radio custom-control">
                      <div class="guest-checkout-slot" data-guest-slot="whop"></div>
                      <div class="whopbox mb-3" id="whopInlineBox" style="display:none;">
                        <label for="whop-inline-checkout">{{ __('Credit or debit card') }}</label>
                        <div id="whop-inline-loading" class="text-muted font-size-sm mb-2" style="display:none;">{{ __('Loading secure card form...') }}</div>
                        <div id="whop-inline-checkout" class="whop-checkout-wrapper"></div>
                        <div id="whop-inline-error" class="text-danger mt-2" role="alert" style="display:none;"></div>
                      </div>
                      <button class="btn btn-primary hv-co-native-btn" type="button" id="whopCheckoutBtn">{{ __('Checkout with Card') }}</button>
                    </div>
                    @endif
                    @if($payment == 'cashfree')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with Cashfree') }}</button>
                    </div>
                    @endif
                    @if($payment == 'nowpayments')
                    <div class="card-body font-size-sm custom-control custom-radio">
                      <button class="btn btn-primary hv-co-native-btn" type="submit">{{ __('Checkout with NowPayments') }}</button>
                    </div>
                    @endif
                  </div>
                @php $no++; @endphp
                @endif
                @endforeach
                    </div>
                  </div>
                </div>
                <div class="hv-co-step">
                  <span class="hv-co-num">3</span>
                  <div class="hv-co-step-body">
                    <h3>{{ __('Pay securely') }}</h3>
                    <button class="hv-co-pay" id="hv-co-pay" type="submit">{{ __('Pay securely') }}</button>
                    <p class="hv-co-trust"><i class="dwg-lock"></i> {{ __('Secure payment') }} · {{ __('Instant delivery') }}</p>
                  </div>
                </div>
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
                $checkoutExact = false;
                @endphp
                @foreach($mobile['item'] as $cart)
                @php 
                $itemprice = $cart->total_price;
                $itemsingler = $cart->item_single_price * $cart->item_serial_stock;
                $lineExact = Helper::is_single_price_item($cart);
                if ($lineExact) { $checkoutExact = true; }
                $lineAmount = Helper::price_value($itemsingler,$multicurrency,$lineExact);
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
                    <div class="widget-product-meta"><span class="text-accent border-right pr-2 mr-2">{{ Helper::price_format($allsettings->site_currency_position,$itemsingler,$currency_symbol,$multicurrency,$lineExact) }}</span>@if(!$lineExact)<span class="font-size-xs text-muted">{{ $cart->license }}@if($cart->license == 'regular') - {{ $addition_settings->regular_license }} @if($cart->item_support == 1) {{ __('Support') }} @else {{ __('not support') }} @endif @elseif($cart->license == 'extended') - {{ $addition_settings->extended_license }} @if($cart->item_support == 1) {{ __('Support') }} @else {{ __('not support') }} @endif @endif</span>@endif</div>
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
                $single_price += $lineAmount;
                $discount_value = 0;
                $single_convert += $lineAmount;
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
                  <h3 class="font-weight-normal text-center my-4">{{ Helper::plan_format($allsettings->site_currency_position,$finbb,$currency_symbol,$checkoutExact) }}</h3>
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

        @if(Auth::guest())
        (function () {
            var $fields = $('#guest-checkout-fields');
            var $holder = $('#guest-checkout-holder');

            window.placeGuestCheckoutFields = function () {
                if ($fields.length) {
                    $fields.removeClass('d-none').show();
                }
            };

            var initialMethod = $('input[name=payment_method]:checked').val();
            if (initialMethod === 'paypal' || initialMethod === 'whop' || initialMethod === 'dodopayments') {
                window.placeGuestCheckoutFields(initialMethod);
            } else if ($('.guest-checkout-slot[data-guest-slot="paypal"]').length) {
                window.placeGuestCheckoutFields('paypal');
            } else if ($('.guest-checkout-slot[data-guest-slot="whop"]').length) {
                window.placeGuestCheckoutFields('whop');
            } else if ($('.guest-checkout-slot[data-guest-slot="dodopayments"]').length) {
                window.placeGuestCheckoutFields('dodopayments');
            }
        })();
        @endif

		$('#paypal').click(function(){
            var value = "paypal";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
            if (typeof window.placeGuestCheckoutFields === 'function') {
                window.placeGuestCheckoutFields('paypal');
            }
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
            if (typeof window.placeGuestCheckoutFields === 'function') {
                window.placeGuestCheckoutFields('dodopayments');
            }
        });
		$('#whop').click(function(){
            var value = "whop";
            $("input[name=payment_method][value=" + value + "]").prop('checked', true);
            if (typeof window.placeGuestCheckoutFields === 'function') {
                window.placeGuestCheckoutFields('whop');
            }
        });

        @if(in_array('whop', $get_payment ?? []))
        (function () {
            var whopMounted = false;
            var whopLoading = false;
            var $form = $('#checkout_form');
            var $btn = $('#whopCheckoutBtn');
            var $box = $('#whopInlineBox');
            var $error = $('#whop-inline-error');
            var $mount = $('#whop-inline-checkout');
            var $loading = $('#whop-inline-loading');
            var defaultBtnText = $btn.text();
            var isGuest = {{ Auth::guest() ? 'true' : 'false' }};

            function showWhopError(message) {
                $loading.hide();
                $error.text(message || @json(__('Payment initialization failed. Please try again.'))).show();
                $box.show();
            }

            function showGuestRequiredError(message) {
                $loading.hide();
                $mount.empty();
                $box.hide();
                $error.hide().text('');
                if (typeof window.placeGuestCheckoutFields === 'function') {
                    window.placeGuestCheckoutFields('whop');
                }
                var $email = $('#email');
                var $password = $('#password');
                var bVal = $form.data('bValidator');
                if (bVal && typeof bVal.showMsg === 'function') {
                    if (!$email.val() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($.trim($email.val() || ''))) {
                        bVal.showMsg($email, message || @json(__('This field is required.')));
                        $email.focus();
                    }
                    if (!$password.val() || ($password.val() || '').length < 6) {
                        bVal.showMsg($password, ($password.val() || '').length > 0
                            ? @json(__('The length must be at least 6 characters.'))
                            : @json(__('This field is required.')));
                        if (!$email.val()) {
                            $email.focus();
                        } else {
                            $password.focus();
                        }
                    }
                    return;
                }
                alert(message || @json(__('Please enter your email address and password before continuing.')));
                $email.focus();
            }

            function validateGuestFields() {
                if (!isGuest) {
                    return true;
                }
                if (typeof window.placeGuestCheckoutFields === 'function') {
                    window.placeGuestCheckoutFields('whop');
                }

                var $email = $('#email');
                var $password = $('#password');
                if (!$email.length || !$password.length) {
                    showGuestRequiredError(@json(__('Please enter your email address and password before continuing.')));
                    return false;
                }

                // Same plugin PayPal uses — do not open the card frame until these pass
                var bVal = $form.data('bValidator');
                if (bVal && typeof bVal.validate === 'function') {
                    var valid = bVal.validate($email.add($password));
                    if (!valid) {
                        $mount.empty();
                        $box.hide();
                        $loading.hide();
                        $btn.prop('disabled', false).text(defaultBtnText);
                        if (!$email.val()) {
                            $email.focus();
                        } else if (!$password.val() || ($password.val() || '').length < 6) {
                            $password.focus();
                        }
                        return false;
                    }
                    return true;
                }

                var email = $.trim($email.val() || '');
                var password = $password.val() || '';
                if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) || !password || password.length < 6) {
                    showGuestRequiredError(@json(__('Please enter your email address and password before continuing.')));
                    $btn.prop('disabled', false).text(defaultBtnText);
                    return false;
                }
                return true;
            }

            function mountWhopEmbed(data) {
                var frameUrl = data.whop_frame_url;
                if (!frameUrl) {
                    showWhopError(@json(__('Checkout could not be loaded. Please try again.')));
                    $btn.prop('disabled', false).text(defaultBtnText);
                    whopLoading = false;
                    return;
                }

                // Always pass the customer's email (account or guest form) into the Whop frame
                var customerEmail = String(data.whop_prefill_email || $('#email').val() || '').trim();
                if (customerEmail) {
                    try {
                        var u = new URL(frameUrl, window.location.origin);
                        u.searchParams.set('email', customerEmail);
                        frameUrl = u.toString();
                    } catch (e) {
                        frameUrl += (frameUrl.indexOf('?') === -1 ? '?' : '&') + 'email=' + encodeURIComponent(customerEmail);
                    }
                } else {
                    showWhopError(@json(__('Please enter your email address before paying with card.')));
                    $btn.prop('disabled', false).text(defaultBtnText);
                    whopLoading = false;
                    return;
                }

                $error.hide().text('');
                $loading.hide();
                $mount.html(
                    '<iframe src="' + frameUrl + '" title="Whop Checkout" style="width:100%;min-height:560px;border:0;background:#fff;" allow="payment *"></iframe>'
                );
                $box.show();
                $btn.hide();
                whopMounted = true;
                whopLoading = false;
            }

            function startWhopCheckout() {
                if (whopMounted || whopLoading) {
                    return;
                }
                $("#opt1-whop").prop('checked', true);

                // Never show loading/frame until guest account fields are valid
                $mount.empty();
                $box.hide();
                $loading.hide();
                $error.hide().text('');

                if (!validateGuestFields()) {
                    whopLoading = false;
                    $btn.prop('disabled', false).text(defaultBtnText);
                    return;
                }

                $error.hide().text('');
                $box.show();
                $loading.show();
                $mount.empty();
                whopLoading = true;
                $btn.prop('disabled', true).text(@json(__('Loading...')));

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize() + '&payment_method=whop&inline_embed=1',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                }).done(function (res) {
                    if (res && res.success && res.whop_frame_url) {
                        mountWhopEmbed(res);
                        return;
                    }
                    whopLoading = false;
                    showWhopError(res && res.message ? res.message : '');
                    $btn.prop('disabled', false).text(defaultBtnText);
                }).fail(function (xhr) {
                    whopLoading = false;
                    var message = '';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            var first = Object.values(xhr.responseJSON.errors)[0];
                            message = Array.isArray(first) ? first[0] : first;
                        }
                    }
                    showWhopError(message);
                    $btn.prop('disabled', false).text(defaultBtnText);
                });
            }

            $btn.on('click', function (e) {
                e.preventDefault();
                startWhopCheckout();
            });

            $form.on('submit', function (e) {
                if (!$('#opt1-whop').is(':checked')) {
                    return;
                }
                e.preventDefault();
                startWhopCheckout();
            });
        })();
        @endif

        @if(in_array('dodopayments', $get_payment ?? []))
        (function () {
            var dodoMounted = false;
            var dodoLoading = false;
            var $form = $('#checkout_form');
            var $btn = $('#dodoCheckoutBtn');
            var $box = $('#dodoInlineBox');
            var $error = $('#dodo-inline-error');
            var $mount = $('#dodo-inline-checkout');
            var $loading = $('#dodo-inline-loading');
            var defaultBtnText = $btn.text();
            var isGuest = {{ Auth::guest() ? 'true' : 'false' }};

            function showDodoError(message) {
                $loading.hide();
                $error.text(message || @json(__('Payment initialization failed. Please try again.'))).show();
                $box.show();
            }

            function showGuestRequiredError(message) {
                $loading.hide();
                $mount.empty();
                $box.hide();
                $error.hide().text('');
                if (typeof window.placeGuestCheckoutFields === 'function') {
                    window.placeGuestCheckoutFields('dodopayments');
                }
                var $email = $('#email');
                var $password = $('#password');
                var bVal = $form.data('bValidator');
                if (bVal && typeof bVal.showMsg === 'function') {
                    if (!$email.val() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($.trim($email.val() || ''))) {
                        bVal.showMsg($email, message || @json(__('This field is required.')));
                        $email.focus();
                    }
                    if (!$password.val() || ($password.val() || '').length < 6) {
                        bVal.showMsg($password, ($password.val() || '').length > 0
                            ? @json(__('The length must be at least 6 characters.'))
                            : @json(__('This field is required.')));
                        if (!$email.val()) {
                            $email.focus();
                        } else {
                            $password.focus();
                        }
                    }
                    return;
                }
                alert(message || @json(__('Please enter your email address and password before continuing.')));
                $email.focus();
            }

            function validateGuestFields() {
                if (!isGuest) {
                    return true;
                }
                if (typeof window.placeGuestCheckoutFields === 'function') {
                    window.placeGuestCheckoutFields('dodopayments');
                }

                var $email = $('#email');
                var $password = $('#password');
                if (!$email.length || !$password.length) {
                    showGuestRequiredError(@json(__('Please enter your email address and password before continuing.')));
                    return false;
                }

                var bVal = $form.data('bValidator');
                if (bVal && typeof bVal.validate === 'function') {
                    var valid = bVal.validate($email.add($password));
                    if (!valid) {
                        $mount.empty();
                        $box.hide();
                        $loading.hide();
                        $btn.prop('disabled', false).text(defaultBtnText);
                        if (!$email.val()) {
                            $email.focus();
                        } else if (!$password.val() || ($password.val() || '').length < 6) {
                            $password.focus();
                        }
                        return false;
                    }
                    return true;
                }

                var email = $.trim($email.val() || '');
                var password = $password.val() || '';
                if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) || !password || password.length < 6) {
                    showGuestRequiredError(@json(__('Please enter your email address and password before continuing.')));
                    $btn.prop('disabled', false).text(defaultBtnText);
                    return false;
                }
                return true;
            }

            function mountDodoEmbed(data) {
                var checkoutUrl = data.dodo_checkout_url;
                var mode = data.dodo_mode || 'test';
                if (!checkoutUrl) {
                    showDodoError(@json(__('Checkout could not be loaded. Please try again.')));
                    $btn.prop('disabled', false).text(defaultBtnText);
                    dodoLoading = false;
                    return;
                }
                if (typeof DodoPaymentsCheckout === 'undefined' || !DodoPaymentsCheckout.DodoPayments) {
                    showDodoError(@json(__('Checkout could not be loaded. Please try again.')));
                    $btn.prop('disabled', false).text(defaultBtnText);
                    dodoLoading = false;
                    return;
                }

                $error.hide().text('');
                $loading.hide();
                $mount.empty();

                var DodoPayments = DodoPaymentsCheckout.DodoPayments;
                DodoPayments.Initialize({
                    mode: mode,
                    displayType: 'inline',
                    onEvent: function (event) {
                        switch (event.event_type) {
                            case 'checkout.error':
                                showDodoError(event.data && event.data.message ? event.data.message : '');
                                break;
                            case 'checkout.link_expired':
                                showDodoError(@json(__('This checkout session has expired. Please return and start checkout again.')));
                                break;
                        }
                    }
                });

                DodoPayments.Checkout.open({
                    checkoutUrl: checkoutUrl,
                    elementId: 'dodo-inline-checkout'
                });

                $box.show();
                $btn.hide();
                dodoMounted = true;
                dodoLoading = false;
            }

            function startDodoCheckout() {
                if (dodoMounted || dodoLoading) {
                    return;
                }
                $("#opt1-dodopayments").prop('checked', true);

                $mount.empty();
                $box.hide();
                $loading.hide();
                $error.hide().text('');

                if (!validateGuestFields()) {
                    dodoLoading = false;
                    $btn.prop('disabled', false).text(defaultBtnText);
                    return;
                }

                $error.hide().text('');
                $box.show();
                $loading.show();
                $mount.empty();
                dodoLoading = true;
                $btn.prop('disabled', true).text(@json(__('Loading...')));

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize() + '&payment_method=dodopayments&inline_embed=1',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                }).done(function (res) {
                    if (res && res.success && res.dodo_checkout_url) {
                        mountDodoEmbed(res);
                        return;
                    }
                    dodoLoading = false;
                    showDodoError(res && res.message ? res.message : '');
                    $btn.prop('disabled', false).text(defaultBtnText);
                }).fail(function (xhr) {
                    dodoLoading = false;
                    var message = '';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            var first = Object.values(xhr.responseJSON.errors)[0];
                            message = Array.isArray(first) ? first[0] : first;
                        }
                    }
                    showDodoError(message);
                    $btn.prop('disabled', false).text(defaultBtnText);
                });
            }

            $btn.on('click', function (e) {
                e.preventDefault();
                startDodoCheckout();
            });

            $form.on('submit', function (e) {
                if (!$('#opt1-dodopayments').is(':checked')) {
                    return;
                }
                e.preventDefault();
                startDodoCheckout();
            });
        })();
        @endif
});		
</script>
<!-- stripe code -->
<script>
$(function () {
  var $root = $('#hv-checkout');
  if (!$root.length) return;
  var amount = $root.attr('data-amount') || '';
  var cardMethods = { stripe: 1, whop: 1, dodopayments: 1, twocheckout: 1 };
  function syncCheckout() {
    var method = $('input[name=payment_method]:checked').val() || '';
    $('.hv-co-tile').removeClass('is-selected');
    $('.hv-co-tile[data-method="' + method + '"]').addClass('is-selected');
    $('.hv-co-panel').attr('hidden', true);
    $('.hv-co-panel[data-method="' + method + '"]').removeAttr('hidden');
    var $btn = $('#hv-co-pay');
    $btn.removeClass('is-paypal is-card');
    if (method === 'paypal') {
      $btn.addClass('is-paypal').text(@json(__('Continue to PayPal')) + ' — ' + amount);
    } else if (cardMethods[method]) {
      $btn.addClass('is-card').text(@json(__('Pay')) + ' ' + amount + ' ' + @json(__('securely')));
    } else {
      var name = $('.hv-co-tile[data-method="' + method + '"]').attr('data-label') || method;
      $btn.addClass('is-card').text(@json(__('Continue with')) + ' ' + name + ' — ' + amount);
    }
  }
  $(document).on('change', 'input[name=payment_method]', syncCheckout);
  $('.hv-co-tile').on('click', function () {
    $('#opt1-' + $(this).data('method')).prop('checked', true);
    syncCheckout();
  });
  syncCheckout();
});
</script>
</body>
</html>