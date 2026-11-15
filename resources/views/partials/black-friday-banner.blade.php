{{-- Promotion banner (Black Friday etc.) on the home page and landing pages. Edited in Admin > Black Friday Banner.
     On landing pages ($bfLanding) the shop link is hidden so visitors stay on the offer. --}}
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
<section class="bf-strip" id="bf-banner" @if($bfEnds) data-ends="{{ $bfEnds }}" @endif aria-label="{{ $promo->heading }}">
  <div class="container">
    <div class="bf-row">
      <div class="bf-title">
        @if(!empty($promo->badge))
        <span class="bf-badge"><i class="fa fa-bolt"></i> {{ $promo->badge }}</span>
        @endif
        <span class="bf-heading">{{ $promo->heading }}</span>
        @if(!empty($promo->discount_text))
        <span class="bf-discount">{{ $promo->discount_text }}</span>
        @endif
        @if(!empty($promo->subheading))
        <span class="bf-sub">{{ $promo->subheading }}</span>
        @endif
      </div>
      <div class="bf-side">
        @if($bfEnds)
        <div class="bf-timer" role="timer" aria-label="{{ __('Sale ends in') }}">
          <span class="bf-unit"><b data-bf="d">00</b><small>{{ __('d') }}</small></span>
          <span class="bf-unit"><b data-bf="h">00</b><small>{{ __('h') }}</small></span>
          <span class="bf-unit"><b data-bf="m">00</b><small>{{ __('m') }}</small></span>
          <span class="bf-unit"><b data-bf="s">00</b><small>{{ __('s') }}</small></span>
        </div>
        @endif
        @if(!empty($promo->coupon_code))
        <button type="button" class="bf-coupon" data-code="{{ $promo->coupon_code }}" title="{{ __('Click to copy') }}">
          <span class="bf-coupon-code">{{ $promo->coupon_code }}</span><i class="fa fa-copy"></i>
        </button>
        @endif
        @if(empty($bfLanding) && $bfUrl !== '' && !empty($promo->button_text))
        <a class="bf-btn" href="{{ $bfUrl }}">{{ $promo->button_text }} <i class="fa fa-arrow-right"></i></a>
        @endif
      </div>
    </div>
  </div>
</section>
<style>
.bf-strip{position:relative;overflow:hidden;color:#fff;padding:10px 0;background:linear-gradient(100deg,#0b0b0f 0%,#1a1208 45%,#0b0b0f 100%);border-bottom:2px solid #facc15}
.bf-strip::after{content:"";position:absolute;inset:0;pointer-events:none;background:linear-gradient(110deg,transparent 30%,rgba(250,204,21,.12) 45%,transparent 60%);background-size:250% 100%;animation:bf-shine 6s linear infinite}
@keyframes bf-shine{from{background-position:150% 0}to{background-position:-100% 0}}
.bf-row{position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;gap:10px 18px;flex-wrap:wrap}
.bf-title{display:flex;align-items:center;gap:6px 10px;flex-wrap:nowrap;min-width:0;flex:1 1 0}
.bf-badge{display:inline-flex;align-items:center;gap:4px;background:#facc15;color:#0b0b0f;font-weight:800;font-size:11px;letter-spacing:.06em;text-transform:uppercase;padding:3px 9px;border-radius:999px;white-space:nowrap}
.bf-heading{font-weight:800;font-size:16px;color:#fff;white-space:nowrap}
.bf-discount{font-weight:800;font-size:16px;white-space:nowrap;background:linear-gradient(90deg,#facc15,#fb923c);-webkit-background-clip:text;background-clip:text;color:transparent}
.bf-sub{font-size:13px;color:rgba(255,255,255,.65);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0;flex:0 1 auto}
.bf-side{display:flex;align-items:center;gap:10px;flex-wrap:nowrap;flex:0 0 auto}
.bf-timer{display:flex;gap:4px}
.bf-unit{display:inline-flex;align-items:baseline;gap:1px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);border-radius:6px;padding:3px 6px;line-height:1.2}
.bf-unit b{font-size:14px;font-weight:800;font-variant-numeric:tabular-nums}
.bf-unit small{font-size:10px;color:rgba(255,255,255,.6);text-transform:uppercase}
.bf-coupon{display:inline-flex;align-items:center;gap:6px;background:transparent;color:#facc15;border:1px dashed rgba(250,204,21,.8);border-radius:6px;padding:4px 10px;font:inherit;font-size:13px;font-weight:800;letter-spacing:.05em;cursor:pointer}
.bf-coupon:hover{background:rgba(250,204,21,.1)}
.bf-coupon i{font-size:12px;color:rgba(255,255,255,.7)}
.bf-btn{display:inline-flex;align-items:center;gap:6px;background:#facc15;color:#0b0b0f!important;font-weight:700;font-size:13px;padding:6px 14px;border-radius:6px;text-decoration:none!important;white-space:nowrap;transition:background .15s ease,transform .15s ease}
.bf-btn:hover{background:#fde047;transform:translateY(-1px)}
@media (max-width:991.98px){.bf-sub{display:none}}
@media (max-width:767.98px){
  .bf-strip{padding:8px 0}
  .bf-row{justify-content:center;gap:6px}
  .bf-title,.bf-side{justify-content:center;flex:1 1 100%;gap:6px}
  .bf-heading{display:none}
  .bf-discount{font-size:14px}
  .bf-unit{padding:2px 4px}
  .bf-unit b{font-size:13px}
  .bf-coupon{padding:3px 7px;font-size:12px}
  .bf-btn{padding:5px 10px;font-size:12px}
}
@media (prefers-reduced-motion:reduce){.bf-strip::after{animation:none}}
</style>
<script>
(function () {
  var root = document.getElementById('bf-banner');
  if (!root) return;

  var coupon = root.querySelector('.bf-coupon');
  if (coupon) {
    coupon.addEventListener('click', function () {
      var code = coupon.getAttribute('data-code');
      var label = coupon.querySelector('.bf-coupon-code');
      var done = function () {
        label.textContent = @json(__('Copied!'));
        setTimeout(function () { label.textContent = code; }, 1500);
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
