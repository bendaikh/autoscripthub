{{--
  Bottom-right "Trending now" card on storefront pages.
  Shows the product pinned as "#1 Trending" (the GTA 6 listing; see TrendingPopup) and stays open until the visitor clicks the close button;
  once closed it stays hidden for the rest of the visit (sessionStorage).
  The same product is kept while the visitor moves between pages.
--}}
@php
  $tpItems = request()->is('checkout*', 'cart', 'cart/*')
      ? []
      : \Fickrr\Support\TrendingPopup::items((int) ($addition_settings->subscription_mode ?? 0) === 1);
@endphp
@if(count($tpItems) > 0)
<style>
.tp-pop{box-sizing:border-box;position:fixed;right:20px;bottom:92px;z-index:1035;width:320px;max-width:calc(100vw - 32px);background:#fff;color:#2b3445;border-radius:14px;box-shadow:0 12px 40px rgba(0,0,0,.18);padding:12px;opacity:0;transform:translateY(16px);transition:opacity .35s ease,transform .35s ease;pointer-events:none}
.tp-pop.is-shown{opacity:1;transform:none;pointer-events:auto}
.is-rtl .tp-pop{right:auto;left:20px}
.tp-pop[hidden]{display:none}
.tp-head{display:flex;align-items:center;justify-content:space-between;margin:0 0 8px}
.tp-label{display:inline-block;font-size:12px;font-weight:700;letter-spacing:.02em;color:#c2410c;background:#fff1e6;border-radius:999px;padding:3px 10px}
.tp-close{border:0;background:transparent;color:#7d879c;font-size:22px;line-height:1;width:28px;height:28px;border-radius:50%;cursor:pointer;padding:0}
.tp-close:hover,.tp-close:focus{background:#f3f5f9;color:#2b3445;outline:none}
.tp-item{display:flex;gap:12px;align-items:center;color:inherit;text-decoration:none}
.tp-item[hidden]{display:none}
.tp-item:hover{color:inherit;text-decoration:none}
.tp-img{flex:0 0 72px;width:72px;height:72px;object-fit:cover;border-radius:10px;background:#f3f5f9}
.tp-body{min-width:0;display:flex;flex-direction:column;gap:3px}
.tp-name{font-size:14px;font-weight:600;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.tp-price{font-size:14px;font-weight:700;color:#1e7d32}
.tp-price del{color:#9aa1ad;font-weight:400;margin-right:6px}
.tp-cta{font-size:13px;font-weight:600;color:#4f46e5}
.tp-item:hover .tp-cta{text-decoration:underline}
@media (max-width:575.98px){.tp-pop{right:16px;bottom:86px;width:calc(100vw - 32px)}.is-rtl .tp-pop{left:16px}.tp-img{flex-basis:56px;width:56px;height:56px}}
@media (prefers-reduced-motion:reduce){.tp-pop{transition:none}}
</style>
<div id="tp-pop" class="tp-pop" role="complementary" aria-label="{{ __('Trending now') }}" hidden>
  <div class="tp-head">
    <span class="tp-label">&#128293; {{ __('Trending now') }}</span>
    <button type="button" class="tp-close" aria-label="{{ __('Close') }}">&times;</button>
  </div>
  @foreach($tpItems as $tpItem)
  @php
    $tpExact = Helper::is_single_price_item($tpItem);
    $tpPrice = Helper::catalog_price($tpItem);
    $tpCompare = $tpExact ? $tpItem->regular_price : $tpItem->extended_price;
    $tpImage = Helper::card_image($tpItem);
  @endphp
  <a class="tp-item" data-tp-id="{{ (int) $tpItem->item_id }}" href="{{ URL::to('/item') }}/{{ $tpItem->item_slug }}" hidden>
    <img class="tp-img" loading="lazy" decoding="async" width="72" height="72" src="{{ $tpImage ? Helper::Image_Path($tpImage,'no-image.png') : url('/').'/public/img/no-image.png' }}" alt="">
    <span class="tp-body">
      <span class="tp-name">{{ $tpItem->item_name }}</span>
      <span class="tp-price">
        @if($tpItem->free_download == 0)
          @if($tpItem->item_flash == 1)<del>{{ Helper::price_format($allsettings->site_currency_position,$tpCompare,$currency_symbol,$multicurrency,$tpExact) }}</del>@endif{{ Helper::price_format($allsettings->site_currency_position,$tpPrice,$currency_symbol,$multicurrency,$tpExact) }}
        @else
          {{ __('Free') }}
        @endif
      </span>
      <span class="tp-cta">{{ __('View product') }} &rarr;</span>
    </span>
  </a>
  @endforeach
</div>
<script>
(function () {
  var pop = document.getElementById('tp-pop');
  if (!pop) return;
  function get(k) { try { return window.sessionStorage.getItem(k); } catch (e) { return null; } }
  function set(k, v) { try { window.sessionStorage.setItem(k, v); } catch (e) {} }
  if (get('tpClosed') === '1') return;

  var items = pop.querySelectorAll('.tp-item');
  var chosen = null, saved = get('tpItem');
  for (var i = 0; i < items.length; i++) {
    if (items[i].getAttribute('data-tp-id') === saved) { chosen = items[i]; }
  }
  if (!chosen) {
    chosen = items[Math.floor(Math.random() * items.length)];
    set('tpItem', chosen.getAttribute('data-tp-id'));
  }
  chosen.hidden = false;

  pop.querySelector('.tp-close').addEventListener('click', function () {
    set('tpClosed', '1');
    pop.classList.remove('is-shown');
    setTimeout(function () { pop.hidden = true; }, 350);
  });

  // Appears right away on later pages of the visit; with a short delay on the first one.
  var delay = get('tpSeen') === '1' ? 300 : 4000;
  setTimeout(function () {
    pop.hidden = false;
    set('tpSeen', '1');
    requestAnimationFrame(function () { requestAnimationFrame(function () { pop.classList.add('is-shown'); }); });
  }, delay);
})();
</script>
@endif
