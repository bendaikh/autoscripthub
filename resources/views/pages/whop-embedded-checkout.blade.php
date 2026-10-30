<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Secure Checkout') }}</title>
@include('meta')
@include('style')
<style>
  .whop-checkout-wrapper iframe { width: 100%; min-height: 720px; border: 0; }
</style>
<script async defer src="https://js.whop.com/static/checkout/loader.js"></script>
</head>
<body>
@include('header')
<section class="bg-position-center-top" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
  <div class="py-4">
    <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
      <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
            <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
            <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Secure Checkout') }}</li>
          </ol>
        </nav>
      </div>
      <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
        <h1 class="h3 mb-0 text-white">{{ __('Secure Checkout') }}</h1>
      </div>
    </div>
  </div>
</section>
<div class="container py-5 mt-md-2 mb-2">
  <div class="row">
    <div class="col-lg-7 mb-4">
      <div class="card box-shadow-lg">
        <div class="card-header">
          <h2 class="h5 mb-0">{{ __('Payment details') }}</h2>
        </div>
        <div class="card-body">
          <p class="text-muted font-size-sm">{{ __('Enter your payment information below. You will not leave this website.') }}</p>
          <script>
            window.onWhopCheckoutComplete = function (planId, receiptId) {
              var returnUrl = @json($whop_return_url);
              var url = returnUrl + (returnUrl.indexOf('?') === -1 ? '?' : '&') + 'status=success';
              if (receiptId) {
                url += '&payment_id=' + encodeURIComponent(receiptId);
              }
              window.location.href = url;
            };
          </script>
          @include('partials.whop-digital-checkout-setup')
          <div
            id="whop-digital-checkout"
            data-whop-checkout-plan-id="{{ $whop_plan_id }}"
            data-whop-checkout-session="{{ $whop_session_id }}"
            data-whop-checkout-return-url="{{ $whop_return_url }}"
            data-whop-checkout-theme="light"
            data-whop-checkout-skip-redirect="true"
            data-whop-checkout-hide-address="true"
            data-whop-checkout-hide-email="true"
            data-whop-checkout-on-complete="onWhopCheckoutComplete"
            data-whop-checkout-on-state-change="onWhopCheckoutStateChange"
            data-whop-checkout-on-address-validation-error="onWhopAddressValidationError"
            @if(!empty($whop_prefill_email)) data-whop-checkout-prefill-email="{{ $whop_prefill_email }}" @endif
          ></div>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card box-shadow-lg">
        <div class="card-header">
          <h2 class="h5 mb-0">{{ __('Order Summary') }}</h2>
        </div>
        <div class="card-body">
          @if(!empty($whop_item_name))
          <p class="mb-2"><strong>{{ $whop_item_name }}</strong></p>
          @endif
          @if(!empty($whop_amount_label))
          <p class="mb-0">{{ __('You will be charged') }} <strong>{{ $whop_amount_label }}</strong></p>
          @endif
        </div>
        <div class="card-footer">
          <a href="{{ $whop_cancel_url }}" class="btn btn-outline-secondary btn-sm">{{ __('Cancel') }}</a>
        </div>
      </div>
    </div>
  </div>
</div>
@include('footer')
@include('script')
</body>
</html>
