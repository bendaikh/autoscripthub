<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order {{ $order->order_number }}</title>
</head>
<body style="font-family: Georgia, serif; color: #1f2933; line-height: 1.5;">
    <p>{{ ($adminCopy ?? false) ? 'New order for ' . $storeName . '.' : 'Hello ' . $order->customer_name . ',' }}</p>
    <p>
        We received order <strong>{{ $order->order_number }}</strong> at {{ $storeName }}.
        This is a physical product. There is no file to download.
    </p>
    @if(($order->payment_status ?? '') === 'paid')
    <p>Payment by {{ $order->payment_method === 'paypal' ? 'PayPal' : 'card' }} is complete. We will ship the order to the address below.</p>
    @else
    <p>Payment by card or PayPal is still required. Nothing has been charged, and the order will not ship until that payment succeeds. Total due: {{ number_format((float) $order->total, 2) }} {{ $order->currency }}.</p>
    @endif
    <p>
        Ship to:<br>
        {{ $order->customer_name }}<br>
        {{ $order->address }}<br>
        {{ $order->city }}, {{ $order->country }}<br>
        {{ $order->customer_phone }}
    </p>
    <p>Items:</p>
    <ul>
        @foreach($items as $item)
        <li>{{ $item->quantity }} × {{ $item->name }}@if($item->variant_name) ({{ $item->variant_name }})@endif — {{ number_format((float) $item->unit_price, 2) }} {{ $order->currency }}</li>
        @endforeach
    </ul>
    <p>Subtotal {{ number_format((float) $order->subtotal, 2) }} {{ $order->currency }}. Shipping {{ number_format((float) $order->shipping_fee, 2) }} {{ $order->currency }}. Total {{ number_format((float) $order->total, 2) }} {{ $order->currency }}.</p>
    <p>Current status: {{ \Fickrr\Services\Physical\PhysicalOrderService::statusLabel($order->fulfillment_status) }}.</p>
    <p>We will email you again when the order ships. Questions: <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>.</p>
    <p><a href="{{ $storeUrl }}">{{ $storeUrl }}</a></p>
</body>
</html>
