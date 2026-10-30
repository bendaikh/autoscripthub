<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Secure Checkout') }}</title>
    <style>
        html, body { margin: 0; padding: 0; background: #fff; font-family: system-ui, -apple-system, sans-serif; }
        .lp-embed-wrap { padding: 8px 4px 16px; }
        .lp-embed-summary { font-size: 14px; color: #334; margin: 0 0 12px; }
        #dodo-inline-checkout { min-height: 640px; width: 100%; }
        .whop-checkout-wrapper iframe { width: 100%; min-height: 640px; border: 0; }
        #checkout-error { display: none; color: #b42318; background: #fef3f2; border: 1px solid #fecdca; padding: 10px 12px; border-radius: 8px; }
    </style>
    @if(($embed['provider'] ?? '') === 'whop')
    <script async defer src="https://js.whop.com/static/checkout/loader.js"></script>
    @endif
</head>
<body>
<div class="lp-embed-wrap">
    @if(!empty($embed['item_name']) || !empty($embed['amount_label']))
    <p class="lp-embed-summary">
        @if(!empty($embed['item_name']))<strong>{{ $embed['item_name'] }}</strong><br>@endif
        @if(!empty($embed['amount_label'])){{ __('You will be charged') }} {{ $embed['amount_label'] }}@endif
    </p>
    @endif
    <div id="checkout-error"></div>

    @if(($embed['provider'] ?? '') === 'dodopayments')
        <div id="dodo-inline-checkout"></div>
        <script src="https://cdn.jsdelivr.net/npm/dodopayments-checkout@latest/dist/index.js"></script>
        <script>
        (function () {
            var checkoutUrl = @json($embed['dodo_checkout_url'] ?? '');
            var mode = @json($embed['dodo_mode'] ?? 'test');
            var errorBox = document.getElementById('checkout-error');
            function showError(message) {
                errorBox.textContent = message || @json(__('Checkout could not be loaded. Please try again.'));
                errorBox.style.display = 'block';
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
                    if (event.event_type === 'checkout.error') {
                        showError(event.data && event.data.message ? event.data.message : '');
                    }
                    if (event.event_type === 'checkout.link_expired') {
                        showError(@json(__('This checkout session has expired. Please return and start checkout again.')));
                    }
                }
            });
            DodoPayments.Checkout.open({
                checkoutUrl: checkoutUrl,
                elementId: 'dodo-inline-checkout'
            });
        })();
        </script>
    @elseif(($embed['provider'] ?? '') === 'whop')
        <script>
            window.onWhopCheckoutComplete = function (planId, receiptId) {
                var returnUrl = @json($embed['whop_return_url'] ?? '');
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
        @include('partials.whop-digital-checkout-setup', [
            'whop_prefill_email' => $embed['whop_prefill_email'] ?? '',
        ])
        <div class="whop-checkout-wrapper">
            <div
                id="whop-digital-checkout"
                data-whop-checkout-plan-id="{{ $embed['whop_plan_id'] }}"
                data-whop-checkout-session="{{ $embed['whop_session_id'] }}"
                data-whop-checkout-return-url="{{ $embed['whop_return_url'] }}"
                data-whop-checkout-theme="light"
                data-whop-checkout-skip-redirect="true"
                data-whop-checkout-hide-address="true"
                data-whop-checkout-hide-email="true"
                data-whop-checkout-on-complete="onWhopCheckoutComplete"
                data-whop-checkout-on-state-change="onWhopCheckoutStateChange"
                data-whop-checkout-on-address-validation-error="onWhopAddressValidationError"
                @if(!empty($embed['whop_prefill_email'])) data-whop-checkout-prefill-email="{{ $embed['whop_prefill_email'] }}" @endif
            ></div>
        </div>
    @endif
</div>
</body>
</html>
