{{-- ash_fix11: homepage Wholesale products tiles. Data-driven from parent ai-licenses subcategories, plus a few external brand links. --}}
@php
  $ashWh = ['parent' => null, 'tiles' => [], 'parent_href' => url('/shop/category/ai-licenses')];
  try {
    if (class_exists(\Fickrr\Support\HomeWholesaleAi::class)) {
      $ashWh = \Fickrr\Support\HomeWholesaleAi::tiles();
    }
  } catch (\Throwable $e) {
    $ashWh = ['parent' => null, 'tiles' => [], 'parent_href' => url('/shop/category/ai-licenses')];
  }
@endphp
@if(!empty($ashWh['tiles']))
<section class="ash-wholesale" aria-label="{{ __('Wholesale products') }}">
  <div class="container">
    <div class="hv-head">
      <div>
        <h2>{{ __('Wholesale products') }} <span class="ash-wh-badge">{{ __('Wholesale') }}</span></h2>
        <p>{{ __('Shop by brand. Click a category to see all available products.') }}</p>
      </div>
      <a class="hv-head-link" href="{{ $ashWh['parent_href'] }}">{{ __('See all') }}<i class="dwg-arrow-right"></i></a>
    </div>
    <style>
    .ash-wholesale{padding:8px 0 6px}
    .ash-wh-badge{display:inline-block;margin-left:.4rem;padding:.12rem .45rem;border-radius:999px;background:#111827;color:#fff;font-size:.68rem;font-weight:700;letter-spacing:.04em;vertical-align:middle;text-transform:uppercase;line-height:1.2}
    .ash-wh-track{display:flex;flex-wrap:wrap;gap:12px;padding:2px 2px 12px}
    .ash-wh-tile{flex:0 0 132px;max-width:160px;display:flex;flex-direction:column;align-items:center;gap:8px;text-decoration:none;color:#111827;text-align:center;padding:14px 10px 12px;border:1px solid #e5e7eb;border-radius:14px;background:#fff;box-shadow:0 1px 2px rgba(15,23,42,.04);transition:transform .15s ease,box-shadow .15s ease}
    .ash-wh-tile:hover{color:#0f172a;text-decoration:none;transform:translateY(-2px);box-shadow:0 6px 16px rgba(15,23,42,.08)}
    .ash-wh-ico{width:52px;height:52px;border-radius:16px;display:flex;align-items:center;justify-content:center}
    .ash-wh-ico i{width:28px;height:28px;display:block;background:currentColor;-webkit-mask:var(--ico) center/contain no-repeat;mask:var(--ico) center/contain no-repeat}
    .ash-wh-name{font-weight:700;font-size:.9rem;line-height:1.2}
    .ash-wh-count{font-size:.75rem;line-height:1.2;color:#6b7280;font-weight:600}
    .ash-wh-tile.is-soon{opacity:.92}
    .ash-wh-tile.is-soon .ash-wh-count{color:#b45309}
    @media (max-width: 575.98px){
      .ash-wholesale{padding:4px 0 0}
      .ash-wh-track{flex-wrap:nowrap;overflow-x:auto;-webkit-overflow-scrolling:touch;scroll-snap-type:x proximity;scrollbar-width:thin}
      .ash-wh-tile{flex:0 0 118px;min-width:118px;max-width:140px;padding:12px 8px 10px;scroll-snap-align:start}
      .ash-wh-ico{width:46px;height:46px;border-radius:14px}
      .ash-wh-name{font-size:.82rem}
      .ash-wh-badge{display:block;margin:.35rem 0 0;width:max-content}
    }
    </style>
    <div class="ash-wh-track">
      @foreach($ashWh['tiles'] as $tile)
      <a class="ash-wh-tile{{ !empty($tile['coming_soon']) ? ' is-soon' : '' }}" href="{{ $tile['href'] }}">
        <span class="ash-wh-ico" style="background:{{ $tile['bg'] }};color:{{ $tile['fg'] }};--ico:url('{{ asset($tile['icon']) }}')">
          <i aria-hidden="true"></i>
        </span>
        <span class="ash-wh-name">{{ __($tile['name']) }}</span>
        @if(!empty($tile['coming_soon']))
        <span class="ash-wh-count">{{ __('Coming soon') }}</span>
        @else
        <span class="ash-wh-count">{{ $tile['count'] === 1 ? __('1 product') : __(':count products', ['count' => $tile['count']]) }}</span>
        @endif
      </a>
      @endforeach
    </div>
  </div>
</section>
@endif
