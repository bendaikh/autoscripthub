<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ Helper::Email_Subject(21) }}</title>
</head>
<body class="preload dashboard-upload">
@if(\Fickrr\Support\Store::isPhysical())
<p>Hello {{ $buyer_name ?? '' }},</p>
<p>We received order {{ $order_id ?? '' }}. We will email you again when it ships.</p>
<p>Amount: {{ $amount ?? '' }} {{ $currency ?? '' }}. Payment: {{ $payment_type ?? '' }}. Status: {{ $payment_status ?? '' }}.</p>
@else
@php echo html_entity_decode(Helper::Email_Content(21,["{{buyer_name}}","{{buyer_email}}","{{amount}}","{{currency}}","{{order_id}}","{{payment_type}}","{{payment_date}}","{{payment_status}}"],["$buyer_name","$buyer_email","$amount","$currency","$order_id","$payment_type","$payment_date","$payment_status"])) @endphp
@endif
</body>
</html>