{{-- Home page promotion banner (Black Friday etc.). Edited in Admin > Black Friday Banner. --}}
@if(!empty($promo))
@php
  $bfUrl = trim((string) ($promo->button_url ?? ''));
  if ($bfUrl !== '' && !preg_match('#^https?://#i', $bfUrl)) {
      $bfUrl = URL::to('/') . '/' . ltrim($bfUrl, '/');
  }
  $bfEnds = !empty($promo->ends_at)
      ? \Carbon\Carbon::parse($promo->ends_at, 'UTC')->toIso8601String()
      : null;
@endphp
<section class="bf-banner" id="bf-banner" @if($bfEnds) data-ends="{{ $bfEnds }}" @endif aria-label="{{ $promo->heading }}">
  <div class="container">
    <div class="bf-inner">
      <div class="bf-text">
        @if(!empty($promo->badge))
        <span class="bf-badge"><i class="fa fa-bolt"></i>{{ $promo->badge }}</span>
        @endif
        <h2 class="bf-heading">{{ $promo->heading }}</h2>
        @if(!empty($promo->discount_text))
        <div class="bf-discount">{{ $promo->discount_text }}</div>
        @endif
        @if(!empty($promo->subheading))
        <p class="bf-sub">{{ $promo->subheading }}</p>
        @endif
        <div class="bf-actions">
          @if($bfUrl !== '' && !empty($promo->button_text))
          <a class="bf-btn" href="{{ $bfUrl }}">{{ $promo->button_text }} <i class="dwg-arrow-right"></i></a>
          @endif
          @if(!empty($promo->coupon_code))
          <button type="button" class="bf-coupon" data-code="{{ $promo->coupon_code }}" title="{{ __('Click to copy') }}">
            <span class="bf-coupon-label">{{ __('Use code') }}</span>
            <span class="bf-coupon-code">{{ $promo->coupon_code }}</span>
            <i class="fa fa-copy"></i>
          </button>
          @endif
        </div>
      </div>
      @if($bfEnds)
      <div class="bf-timer-wrap">
        <div class="bf-timer-title">{{ __('Sale ends in') }}</div>
        <div class="bf-timer" role="timer">
          <div class="bf-unit"><span data-bf="d">00</span><small>{{ __('Days') }}</small></div>
          <div class="bf-unit"><span data-bf="h">00</span><small>{{ __('Hours') }}</small></div>
          <div class="bf-unit"><span data-bf="m">00</span><small>{{ __('Minutes') }}</small></div>
          <div class="bf-unit"><span data-bf="s">00</span><small>{{ __('Seconds') }}</small></div>
        </div>
      </div>
      @endif
    </div>
  </div>
</section>
<style>
.bf-banner{background:radial-gradient(1200px 400px at 85% -20%,rgba(250,204,21,.18),transparent 60%),radial-gradient(900px 400px at 0% 120%,rgba(239,68,68,.22),transparent 60%),#0b0b0f;color:#fff;padding:28px 0;position:relative;overflow:hidden}
.bf-inner{display:flex;align-items:center;justify-content:space-between;gap:28px;flex-wrap:wrap}
.bf-text{flex:1 1 420px;min-width:0}
.bf-badge{display:inline-flex;align-items:center;gap:6px;background:#facc15;color:#0b0b0f;font-weight:800;font-size:12px;letter-spacing:.08em;text-transform:uppercase;padding:5px 12px;border-radius:999px}
.bf-heading{color:#fff;font-weight:800;font-size:clamp(26px,4vw,40px);line-height:1.1;margin:12px 0 4px}
.bf-discount{font-weight:800;font-size:clamp(22px,3.2vw,32px);line-height:1.15;background:linear-gradient(90deg,#facc15,#f97316,#ef4444);-webkit-background-clip:text;background-clip:text;color:transparent}
.bf-sub{color:rgba(255,255,255,.78);margin:10px 0 0;max-width:560px}
.bf-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:18px}
.bf-btn{display:inline-flex;align-items:center;gap:8px;background:#facc15;color:#0b0b0f!important;font-weight:700;padding:12px 22px;border-radius:10px;text-decoration:none!important;transition:transform .15s ease,background .15s ease}
.bf-btn:hover{background:#fde047;transform:translateY(-1px)}
.bf-coupon{display:inline-flex;align-items:center;gap:10px;background:transparent;color:#fff;border:2px dashed rgba(250,204,21,.7);border-radius:10px;padding:9px 16px;cursor:pointer;font:inherit}
.bf-coupon:hover{border-color:#facc15}
.bf-coupon-label{font-size:13px;color:rgba(255,255,255,.7)}
.bf-coupon-code{font-weight:800;letter-spacing:.06em;color:#facc15}
.bf-timer-wrap{flex:0 0 auto;text-align:center}
.bf-timer-title{font-size:13px;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,.7);margin-bottom:10px}
.bf-timer{display:flex;gap:10px}
.bf-unit{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);border-radius:12px;min-width:74px;padding:12px 8px}
.bf-unit span{display:block;font-size:30px;font-weight:800;line-height:1;font-variant-numeric:tabular-nums}
.bf-unit small{display:block;margin-top:6px;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:rgba(255,255,255,.65)}
@media (max-width:575.98px){
  .bf-banner{padding:22px 0}
  .bf-timer-wrap{width:100%}
  .bf-timer{justify-content:center;gap:6px}
  .bf-unit{min-width:0;flex:1;padding:10px 4px}
  .bf-unit span{font-size:22px}
  .bf-btn,.bf-coupon{flex:1 1 100%;justify-content:center}
}
</style>
<script>
(function () {
  var root = document.getElementById('bf-banner');
  if (!root) return;

  var coupon = root.querySelector('.bf-coupon');
  if (coupon) {
    coupon.addEventListener('click', function () {
      var code = coupon.getAttribute('data-code');
      var label = coupon.querySelector('.bf-coupon-label');
      var done = function () {
        var old = label.textContent;
        label.textContent = @json(__('Copied!'));
        setTimeout(function () { label.textContent = old; }, 1500);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(done, function () {});
      } else {
        var t = document.createElement('textarea');
        t.value = code; document.body.appendChild(t); t.select();
        try { document.execCommand('copy'); done(); } catch (e) {}
        document.body.removeChild(t);
      }
    });
  }

  var endsAttr = root.getAttribute('data-ends');
  if (!endsAttr) return;
  var ends = new Date(endsAttr).getTime();
  if (isNaN(ends)) return;
  var el = {
    d: root.querySelector('[data-bf="d"]'),
    h: root.querySelector('[data-bf="h"]'),
    m: root.querySelector('[data-bf="m"]'),
    s: root.querySelector('[data-bf="s"]')
  };
  var pad = function (n) { return (n < 10 ? '0' : '') + n; };
  var timer;
  var tick = function () {
    var left = Math.floor((ends - Date.now()) / 1000);
    if (left <= 0) { clearInterval(timer); root.style.display = 'none'; return; }
    el.d.textContent = pad(Math.floor(left / 86400));
    el.h.textContent = pad(Math.floor(left % 86400 / 3600));
    el.m.textContent = pad(Math.floor(left % 3600 / 60));
    el.s.textContent = pad(left % 60);
  };
  tick();
  timer = setInterval(tick, 1000);
})();
</script>
@endif
