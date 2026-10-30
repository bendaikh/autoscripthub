<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Secure Checkout') }}</title>
    <style>
        html, body { margin: 0; padding: 0; background: #fff; }
        .whop-frame-wrap { width: 100%; min-height: 520px; }
        .whop-frame-wrap iframe { width: 100% !important; min-height: 520px; border: 0; }
        .whop-frame-error { color: #b42318; padding: 12px; font-family: system-ui, sans-serif; font-size: 14px; }
    </style>
    <script async defer src="https://js.whop.com/static/checkout/loader.js"></script>
</head>
<body>
@if(empty($whop_session_id) || empty($whop_plan_id))
    <div class="whop-frame-error">{{ __('Checkout could not be loaded. Please try again.') }}</div>
@else
    <script>
        window.onWhopCheckoutComplete = function (planId, receiptId) {
            var returnUrl = @json($whop_return_url ?? '');
            var url = returnUrl + (returnUrl.indexOf('?') === -1 ? '?' : '&') + 'status=success';
            if (receiptId) {
                url += '&payment_id=' + encodeURIComponent(receiptId);
            }
            if (window.top) {
                window.top.location.href = url;
            } else {
                window.location.href = url;
            }
        };
    </script>
    @include('partials.whop-digital-checkout-setup')
    <div class="whop-frame-wrap">
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
@endif
</body>
</html>
