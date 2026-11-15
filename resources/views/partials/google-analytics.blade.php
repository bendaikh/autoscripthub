{{-- Google Analytics (GA4), ID from Admin > General Settings > Google Analytics.
     Used by the main site (script.blade.php) and the landing pages.
     $gaLanding = true: visits are grouped as "Landing pages" and landing page
     events (checkout, payment method, purchase, chat lead) are sent to GA. --}}
@php $gaId = trim((string) ($allsettings->google_analytics ?? '')); @endphp
@if($gaId !== '' && empty($GLOBALS['__gaLoaded']))
@php $GLOBALS['__gaLoaded'] = true; $gaConfig = !empty($gaLanding) ? ['content_group' => 'Landing pages'] : new \stdClass(); @endphp
<!-- Google Analytics -->
<script>
window.addEventListener('load',function(){
  (window.requestIdleCallback||function(cb){setTimeout(cb,1);})(function(){
    var gs=document.createElement('script'); gs.async=true;
    gs.src='https://www.googletagmanager.com/gtag/js?id={{ urlencode($gaId) }}';
    document.head.appendChild(gs);
  },{timeout:3000});
});
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', @json($gaId), @json($gaConfig));
</script>
@if(!empty($gaLanding))
<script>
(function () {
  var map = { payment_modal_opened: 'begin_checkout', initiate_checkout: 'begin_checkout', payment_method_selected: 'add_payment_info', purchase_completed: 'purchase', chat_message_sent: 'generate_lead', buy_now_click: 'select_promotion' };
  var prev = window.lpTrackAds;
  window.lpTrackAds = function (eventName, params) {
    if (typeof prev === 'function') { try { prev(eventName, params); } catch (e) {} }
    var name = map[eventName];
    if (!name || typeof gtag !== 'function') return;
    params = params || {};
    var ga = { content_group: 'Landing pages' };
    if (params.content_name) { ga.items = [{ item_name: params.content_name }]; }
    if (params.value != null) { ga.value = Number(params.value); }
    if (params.currency) { ga.currency = params.currency; }
    if (params.order_id) { ga.transaction_id = String(params.order_id); }
    if (params.payment_method || params.method) { ga.payment_type = params.payment_method || params.method; }
    gtag('event', name, ga);
  };
})();
</script>
@endif
<!-- End Google Analytics -->
@endif
