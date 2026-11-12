<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Secure Checkout') }}</title>
@include('meta')
@include('style')
<style>
  #dodo-inline-checkout { min-height: 720px; width: 100%; }
  @include('partials.hv-card-loader-styles')
</style>
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
          <p class="text-muted font-size-sm">{{ __('Enter your card details below. Your account email is used automatically.') }}</p>
          <div id="dodo-card-stage">
          <div id="dodo-inline-checkout"></div>
          </div>
          <div id="dodo-checkout-error" class="alert alert-danger mt-3" style="display:none;" role="alert"></div>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card box-shadow-lg">
        <div class="card-header">
          <h2 class="h5 mb-0">{{ __('Order Summary') }}</h2>
        </div>
        <div class="card-body">
          @if(!empty($dodo_item_name))
          <p class="mb-2"><strong>{{ $dodo_item_name }}</strong></p>
          @endif
          @if(!empty($dodo_amount_label))
          <p class="mb-0">{{ __('You will be charged') }} <strong>{{ $dodo_amount_label }}</strong></p>
          @endif
          <div id="dodo-breakdown" class="mt-3 font-size-sm" style="display:none;">
            <div class="d-flex justify-content-between"><span>{{ __('Subtotal') }}</span><span id="dodo-subtotal"></span></div>
            <div class="d-flex justify-content-between" id="dodo-tax-row" style="display:none;"><span>{{ __('Tax') }}</span><span id="dodo-tax"></span></div>
            <hr>
            <div class="d-flex justify-content-between font-weight-bold"><span>{{ __('Total') }}</span><span id="dodo-total"></span></div>
          </div>
        </div>
        <div class="card-footer">
          <a href="{{ $dodo_cancel_url }}" class="btn btn-outline-secondary btn-sm">{{ __('Cancel') }}</a>
        </div>
      </div>
    </div>
  </div>
</div>
@include('footer')
@include('script')
@php $card_loader_provider = 'dodopayments'; @endphp
@include('landing-pages.partials.card-form-loader')
<script src="https://cdn.jsdelivr.net/npm/dodopayments-checkout@latest/dist/index.js"></script>
<script>
(function () {
  var checkoutUrl = @json($dodo_checkout_url);
  var mode = @json($dodo_mode);
  var errorBox = document.getElementById('dodo-checkout-error');

  function showError(message) {
    if (!errorBox) return;
    errorBox.textContent = message || @json(__('Checkout could not be loaded. Please try again.'));
    errorBox.style.display = 'block';
  }

  function formatAmount(amt, curr) {
    if (amt == null || !curr) return '';
    return curr + ' ' + (Number(amt) / 100).toFixed(2);
  }

  if (typeof DodoPaymentsCheckout === 'undefined' || !DodoPaymentsCheckout.DodoPayments) {
    showError();
    return;
  }

  var DodoPayments = DodoPaymentsCheckout.DodoPayments;

  DodoPayments.Initialize({
    mode: mode,
    displayType: 'inline',
    onEvent: function (event) {
      switch (event.event_type) {
        case 'checkout.breakdown':
          var breakdown = event.data && event.data.message ? event.data.message : null;
          if (!breakdown) break;
          var currency = breakdown.currency || breakdown.finalTotalCurrency || '';
          var box = document.getElementById('dodo-breakdown');
          if (box) box.style.display = 'block';
          if (breakdown.subTotal != null) {
            document.getElementById('dodo-subtotal').textContent = formatAmount(breakdown.subTotal, currency);
          }
          if (breakdown.tax != null) {
            document.getElementById('dodo-tax-row').style.display = 'flex';
            document.getElementById('dodo-tax').textContent = formatAmount(breakdown.tax, currency);
          }
          var total = breakdown.finalTotal != null ? breakdown.finalTotal : breakdown.total;
          var totalCurrency = breakdown.finalTotalCurrency || currency;
          if (total != null) {
            document.getElementById('dodo-total').textContent = formatAmount(total, totalCurrency);
          }
          break;
        case 'checkout.error':
          showError(event.data && event.data.message ? event.data.message : '');
          break;
        case 'checkout.link_expired':
          showError(@json(__('This checkout session has expired. Please return and start checkout again.')));
          break;
      }
    }
  });

  DodoPayments.Checkout.open({
    checkoutUrl: checkoutUrl,
    elementId: 'dodo-inline-checkout'
  });
})();
</script>
</body>
</html>
