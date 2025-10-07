<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ Helper::Email_Subject(21) }}</title>
</head>
<body class="preload dashboard-upload">
@php echo html_entity_decode(Helper::Email_Content(21,["{{buyer_name}}","{{buyer_email}}","{{amount}}","{{currency}}","{{order_id}}","{{payment_type}}","{{payment_date}}","{{payment_status}}"],["$buyer_name","$buyer_email","$amount","$currency","$order_id","$payment_type","$payment_date","$payment_status"])) @endphp
</body>
</html>