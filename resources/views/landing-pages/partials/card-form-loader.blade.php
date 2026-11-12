@include('partials.hv-card-loader')
@php
    $hvCardProvider = '';
    if (isset($embed) && is_array($embed)) {
        $hvCardProvider = $embed['provider'] ?? '';
    }
    if ($hvCardProvider === '' && !empty($card_loader_provider)) {
        $hvCardProvider = $card_loader_provider;
    }
@endphp
<script>
(function () {
    var provider = @json($hvCardProvider);
    var host = '';
    if (provider === 'whop') {
        host = document.querySelector('.whop-checkout-wrapper') ? '.whop-checkout-wrapper' : '#whop-digital-checkout';
    } else if (provider === 'dodopayments') {
        host = document.querySelector('#dodo-card-stage') ? '#dodo-card-stage' : '#dodo-inline-checkout';
    }
    if (!host || !document.querySelector(host) || !window.hvInitCardFormLoader) return;
    var hosts = {};
    hosts[provider] = host;
    window.hvInitCardFormLoader({
        root: document.getElementById('lp-embed-root') || document.body,
        hosts: hosts,
        errorSelector: '#checkout-error, #whop-setup-error, #dodo-checkout-error, #dodo-inline-error',
        getMethod: function () { return provider; },
        immediate: [provider],
        revealImmediately: window.parent !== window,
        onStatus: function (status) {
            if (window.parent === window) return;
            try {
                window.parent.postMessage({ type: 'hv-card-form', status: status }, window.location.origin);
            } catch (e) {}
        },
        texts: {
            label: @json(__('Credit or debit card')),
            title: @json(__('Loading secure payment form…')),
            sub: @json(__('This only takes a few seconds')),
            wait: @json(__('Please wait…')),
            pci: @json(__('Secure • PCI DSS Compliant')),
            fail: @json(__('Card form is taking longer than usual — please refresh or choose another payment method')),
            retry: @json(__('Refresh'))
        }
    });
    if (provider === 'whop') {
        var previous = window.onWhopCheckoutStateChange;
        window.onWhopCheckoutStateChange = function (state) {
            if (typeof previous === 'function') previous.apply(this, arguments);
            if (state === 'ready' && window.hvCardFormReady) window.hvCardFormReady('whop');
        };
    }
})();
</script>
