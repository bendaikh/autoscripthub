<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ __('Order') }} {{ $order->order_number }} - {{ \Fickrr\Support\Store::publicName($allsettings->site_title ?? null) }}</title>
@include('meta')
@include('style')
</head>
<body>
@include('header')
<div class="container py-5">
  <h1 class="h3">{{ __('Order') }} {{ $order->order_number }}</h1>
  <p class="my-3">@include('partials.physical-status', ['order' => $order])</p>
  <p>{{ __('This order is for physical products. There is nothing to download.') }}</p>
  @if(($order->payment_status ?? '') === 'paid')
  <p>{{ __('Paid online by') }} {{ $order->payment_method === 'paypal' ? 'PayPal' : __('card') }}. {{ __('The order can now be packed and shipped.') }}</p>
  @else
  <p>{{ __('Payment is still required. This order is not confirmed and will not ship until card or PayPal payment succeeds. No charge has been created in this environment.') }}</p>
  @endif
  <h2 class="h6 mt-4">{{ __('Ship to') }}</h2>
  <p>
    {{ $order->customer_name }}<br>
    {{ $order->address }}<br>
    {{ $order->city }}, {{ $order->country }}<br>
    {{ $order->customer_phone }}<br>
    {{ $order->customer_email }}
  </p>
  @if(!empty($order->tracking_number))
  <p>{{ __('Tracking number') }}: {{ $order->tracking_number }}</p>
  @endif
  <h2 class="h6">{{ __('Items') }}</h2>
  <ul>
    @foreach($items as $item)
    <li>{{ $item->quantity }} × {{ $item->name }}@if($item->variant_name) — {{ $item->variant_name }}@endif @if($item->sku) ({{ $item->sku }}) @endif</li>
    @endforeach
  </ul>
  <p>{{ __('Subtotal') }} {{ number_format((float) $order->subtotal, 2) }} {{ $order->currency }}</p>
  <p>{{ __('Shipping') }} {{ number_format((float) $order->shipping_fee, 2) }} {{ $order->currency }}</p>
  <p><strong>{{ __('Total') }} {{ number_format((float) $order->total, 2) }} {{ $order->currency }}</strong></p>
</div>
@include('footer')
@include('script')
</body>
</html>
