@php
    $lpPixels = \Fickrr\Models\LandingPixel::activeByPlatform();
    $fbPixels = $lpPixels['facebook'];
    $ttPixels = $lpPixels['tiktok'];
@endphp
@if(count($fbPixels) || count($ttPixels))
<script>
window.lpPixels = {
    facebook: @json($fbPixels),
    tiktok: @json($ttPixels)
};
window.lpTrackAds = function (eventName, params) {
    params = params || {};
    try {
        if (typeof fbq === 'function' && window.lpPixels.facebook.length) {
            var fbMap = {
                initiate_checkout: 'InitiateCheckout',
                buy_now_click: 'InitiateCheckout',
                payment_method_selected: 'AddPaymentInfo',
                chat_message_submitted: 'Contact',
                chat_message_sent: 'Lead',
                purchase_completed: 'Purchase'
            };
            var fbEvent = fbMap[eventName];
            if (fbEvent) {
                var fbParams = {};
                if (params.content_name) fbParams.content_name = params.content_name;
                if (params.value != null) fbParams.value = params.value;
                if (params.currency) fbParams.currency = params.currency;
                if (params.order_id) fbParams.content_ids = [String(params.order_id)];
                if (params.num_items != null) fbParams.num_items = params.num_items;
                if (params.method) fbParams.payment_method = params.method;
                fbq('track', fbEvent, fbParams);
            }
        }
        if (typeof ttq !== 'undefined' && window.lpPixels.tiktok.length) {
            var tiktokMap = {
                PageView: 'PageView',
                Lead: 'SubmitForm',
                InitiateCheckout: 'InitiateCheckout',
                Purchase: 'CompletePayment'
            };
            var ttEvent = tiktokMap[eventName] || eventName;
            var ttParams = {
                content_name: params.content_name || '',
                content_type: 'product',
                value: params.value,
                currency: params.currency,
                quantity: params.num_items || 1
            };
            if (params.order_id) {
                ttParams.order_id = params.order_id;
            }
            window.lpPixels.tiktok.forEach(function (pixelId) {
                var inst = (typeof ttq.instance === 'function') ? ttq.instance(pixelId) : ttq;
                if (inst && typeof inst.track === 'function') {
                    inst.track(ttEvent, ttParams);
                }
            });
        }
    } catch (e) {}
};
</script>
@endif
@if(count($fbPixels))
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
@foreach($fbPixels as $pixelId)
fbq('init', @json($pixelId));
fbq('set', 'autoConfig', false, @json($pixelId));
@endforeach
fbq('track', 'PageView');
</script>
<noscript>
@foreach($fbPixels as $pixelId)
<img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1" alt="">
@endforeach
</noscript>
@endif
@if(count($ttPixels))
<script>
!function (w, d, t) {
  w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];
  ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"];
  ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};
  for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);
  ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};
  ttq.load=function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js",o=n&&n.partner;ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=r,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};n=document.createElement("script");n.type="text/javascript",n.async=!0,n.src=r+"?sdkid="+e+"&lib="+t;e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(n,e)};
  @foreach($ttPixels as $pixelId)
  ttq.load(@json($pixelId));
  @endforeach
  ttq.page();
}(window, document, 'ttq');
</script>
@endif
