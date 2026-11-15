{{-- "We accept" row of payment method badges.
     Pass $pbMethods (enabled method keys) or leave it out to use the site's payment settings.
     Optional: $pbLabel (text before the badges), $pbAlign ('center' | 'start').
     Styles: public/assets/payment-badges.css (link it in the page head). --}}
@php
    if (!isset($pbMethods)) {
        try {
            $pbSetting = \Fickrr\Models\Settings::editGeneral(1);
            $pbMethods = array_merge(
                array_map('trim', explode(',', (string) ($pbSetting->payment_option ?? ''))),
                \Fickrr\Models\CryptoPay::enabledMethods()
            );
        } catch (\Throwable $e) {
            $pbMethods = [];
        }
    }
    $pbShow = [];
    if (in_array('paypal', $pbMethods, true)) { $pbShow[] = 'paypal'; }
    if (array_intersect(['stripe', 'dodopayments', 'whop', 'twocheckout'], $pbMethods)) { $pbShow[] = 'visa'; $pbShow[] = 'mastercard'; }
    foreach (['binance', 'kast', 'usdt'] as $pbCrypto) {
        if (in_array($pbCrypto, $pbMethods, true)) { $pbShow[] = $pbCrypto; }
    }
@endphp
@if(count($pbShow))
<div class="pay-badges pay-badges-{{ $pbAlign ?? 'center' }}">
    @if(!empty($pbLabel))<span class="pay-badges-label">{{ $pbLabel }}</span>@endif
    <span class="pay-badges-list">
    @foreach($pbShow as $pb)
        @if($pb === 'paypal')
        <span class="pay-badge" title="PayPal"><b class="pb-paypal">Pay<i>Pal</i></b></span>
        @elseif($pb === 'visa')
        <span class="pay-badge" title="Visa"><b class="pb-visa">VISA</b></span>
        @elseif($pb === 'mastercard')
        <span class="pay-badge" title="Mastercard"><span class="pb-mc" aria-hidden="true"><i></i><i></i></span><b class="pb-mc-text">Mastercard</b></span>
        @elseif($pb === 'binance')
        <span class="pay-badge" title="Binance Pay"><span class="pb-binance" aria-hidden="true"></span><b>Binance Pay</b></span>
        @elseif($pb === 'kast')
        <span class="pay-badge" title="KAST"><span class="pb-dot" style="background:#111827" aria-hidden="true">K</span><b>KAST</b></span>
        @elseif($pb === 'usdt')
        <span class="pay-badge" title="USDT"><span class="pb-dot" style="background:#26a17b" aria-hidden="true">₮</span><b>USDT</b></span>
        @endif
    @endforeach
    </span>
</div>
@endif
