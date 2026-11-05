<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order {{ $order->order_number }} shipped</title>
</head>
<body style="font-family: Georgia, serif; color: #1f2933; line-height: 1.5;">
    <p>Hello {{ $order->customer_name }},</p>
    <p>Order <strong>{{ $order->order_number }}</strong> from {{ $storeName }} has been marked shipped. This is a parcel, not a download.</p>
    @if(!empty($order->tracking_number))
    <p>Tracking number: {{ $order->tracking_number }}</p>
    @endif
    <p>
        Delivery address:<br>
        {{ $order->address }}<br>
        {{ $order->city }}, {{ $order->country }}
    </p>
    @if($order->payment_method === 'cod')
    <p>Please have {{ number_format((float) $order->total, 2) }} {{ $order->currency }} ready for cash on delivery.</p>
    @endif
    <p>Questions: <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
</body>
</html>
