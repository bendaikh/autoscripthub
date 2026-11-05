<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ \Fickrr\Support\Store::publicName($allsettings->site_title ?? null) }} - {{ __('Checkout') }}</title>
@include('meta')
@include('style')
</head>
<body>
@include('header')
<div class="container mb-5 pb-3 pt-4">
  <h1 class="h3 mb-4">{{ __('Checkout') }}</h1>
  @if(empty($cart['item']) || count($cart['item']) === 0)
    <p>{{ __('Your cart is empty.') }}</p>
    <a href="{{ url('/shop') }}">{{ __('Continue browsing') }}</a>
  @else
  @php
    $lines = [];
    foreach ($cart['item'] as $row) {
        $lines[] = [
            'quantity' => (int) ($row->item_serial_stock ?? 1),
            'unit_price' => (float) ($row->item_single_price ?? $row->item_price ?? 0),
            'shipping_fee' => (float) ($row->shipping_fee ?? 0),
            'weight_grams' => (int) ($row->weight_grams ?? 0),
            'requires_shipping' => true,
        ];
    }
    $quote = app(\Fickrr\Services\Physical\ShippingCalculator::class)->quote($lines);
    $currency = $cart['item'][0]->currency_type ?? ($allsettings->site_currency ?? 'USD');
  @endphp
  <form method="post" action="{{ route('physical.checkout') }}">
    @csrf
    <div class="row">
      <div class="col-lg-7">
        <h2 class="h6">{{ __('Delivery details') }}</h2>
        <div class="form-group">
          <label for="customer_name">{{ __('Full name') }}</label>
          <input class="form-control" id="customer_name" name="customer_name" value="{{ old('customer_name', Auth::check() ? Auth::user()->name : '') }}" required>
        </div>
        <div class="form-group">
          <label for="customer_email">{{ __('Email') }}</label>
          <input class="form-control" type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', Auth::check() ? Auth::user()->email : '') }}" required>
        </div>
        <div class="form-group">
          <label for="customer_phone">{{ __('Phone') }}</label>
          <input class="form-control" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
        </div>
        <div class="form-group">
          <label for="address">{{ __('Street address') }}</label>
          <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="city">{{ __('City') }}</label>
            <input class="form-control" id="city" name="city" value="{{ old('city') }}" required>
          </div>
          <div class="form-group col-md-6">
            <label for="country">{{ __('Country') }}</label>
            <input class="form-control" id="country" name="country" value="{{ old('country') }}" required>
          </div>
        </div>
        <h2 class="h6 mt-3">{{ __('Payment') }}</h2>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="cod" checked>
          <label class="form-check-label" for="pay_cod">{{ __('Cash on delivery') }}</label>
        </div>
        @if(in_array('stripe', $get_payment ?? []) && config('store.stripe_enabled'))
        <div class="form-check">
          <input class="form-check-input" type="radio" name="payment_method" id="pay_stripe" value="stripe">
          <label class="form-check-label" for="pay_stripe">{{ __('Card (Stripe)') }}</label>
          <small class="form-text text-muted">{{ __('Card payment is not charged in this environment unless the live store has turned charges on.') }}</small>
        </div>
        @endif
      </div>
      <div class="col-lg-5">
        <h2 class="h6">{{ __('Order') }}</h2>
        <ul class="list-unstyled">
          @foreach($cart['item'] as $index => $row)
          @php $qty = (int) ($row->item_serial_stock ?? 1); @endphp
          <li class="mb-3">
            <strong>{{ $row->item_name }}</strong>
            <div class="text-muted">{{ __('Qty') }} {{ $qty }}</div>
            <input type="hidden" name="lines[{{ $index }}][item_id]" value="{{ $row->item_id }}">
            <input type="hidden" name="lines[{{ $index }}][quantity]" value="{{ $qty }}">
          </li>
          @endforeach
        </ul>
        <p class="mb-1">{{ __('Subtotal') }}: {{ number_format($quote['subtotal'], 2) }} {{ $currency }}</p>
        <p class="mb-1">{{ __('Shipping') }}: {{ number_format($quote['shipping_fee'], 2) }} {{ $currency }}</p>
        <p><strong>{{ __('Total') }}: {{ number_format($quote['total'], 2) }} {{ $currency }}</strong></p>
        <p class="text-muted small">{{ __('The total is calculated again on the server when you place the order.') }}</p>
        <button class="btn btn-primary" type="submit">{{ __('Place order') }}</button>
      </div>
    </div>
  </form>
  @endif
</div>
@include('footer')
@include('script')
</body>
</html>
