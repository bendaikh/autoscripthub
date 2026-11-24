{{-- ash_fix7: compact homepage icon row. Targets verified live (200 + products). --}}
@php
  $ashSc = [
    [
      'en' => 'Windows',
      'ru' => 'Windows',
      'href' => url('/shop/category/operating-system-licenses'),
      'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M3 5.2l8.2-1.2V11H3V5.2zm9.2-1.3L21 2.5V11h-8.8V3.9zM3 12.2h8.2V20L3 18.7V12.2zm9.2 0H21V21l-8.8-1.3V12.2z"/></svg>',
      'bg' => '#e8f1ff', 'fg' => '#135bad',
    ],
    [
      'en' => 'Office',
      'ru' => 'Office',
      'href' => url('/shop/category/office-licenses'),
      'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M4 4.5h11.5A1.5 1.5 0 0117 6v12a1.5 1.5 0 01-1.5 1.5H4A1.5 1.5 0 012.5 18V6A1.5 1.5 0 014 4.5zm13.2 3.2L21.5 9v9.2c0 .7-.8 1.2-1.5.9l-2.8-1.3V7.7zM6.2 8.2v7.6h1.7V14h2.2c1.7 0 2.7-1 2.7-2.9S11.8 8.2 10 8.2H6.2zm1.7 1.5h1.9c.8 0 1.2.5 1.2 1.4s-.4 1.4-1.2 1.4H7.9v-2.8z"/></svg>',
      'bg' => '#f3e8ff', 'fg' => '#c43e1c',
    ],
    [
      'en' => 'Steam',
      'ru' => 'Steam',
      'href' => url('/shop/subcategory/steam'),
      'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 00-9.9 11.3l5.3-2.2a2.8 2.8 0 015.2-1.4l3.3 2.4A3.3 3.3 0 1114.3 16l-3.7-2.7a2.8 2.8 0 01-3.8.4L3 15.4A10 10 0 1012 2zm-5.1 12.3a1.6 1.6 0 11-1.2 3.1 1.6 1.6 0 011.2-3.1zm8.4-.2a1.9 1.9 0 11.1 3.8 1.9 1.9 0 01-.1-3.8z"/></svg>',
      'bg' => '#e8eef5', 'fg' => '#1b2838',
    ],
    [
      'en' => 'Xbox',
      'ru' => 'Xbox',
      'href' => url('/shop/subcategory/xbox-console'),
      'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 2.2c1.7 0 3.9 2.6 4.8 6.6-1.4-1.7-3-2.9-4.8-2.9S8.6 9.1 7.2 10.8C8.1 6.8 10.3 4.2 12 4.2zm-7.3 9.5c.8 3.8 3.8 6.6 7.3 6.6s6.5-2.8 7.3-6.6C17.6 16 14.9 18 12 18s-5.6-2-7.3-4.3z"/></svg>',
      'bg' => '#e7f8ea', 'fg' => '#107c10',
    ],
    [
      'en' => 'PlayStation',
      'ru' => 'PlayStation',
      'href' => url('/shop/subcategory/playstation'),
      'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M8.6 14.8c.3.1.6.2 1.2.2 2 0 2.7-1.2 2.7-2.7V8.1h-1.8v4.1c0 .6-.2 1.1-.9 1.1-.2 0-.5 0-.8-.1l-.4 1.6zm4.2-1.4c1.1.5 2.2.8 3.1.8 1.3 0 1.7-.6 1.7-1.2 0-.7-.4-1.1-1.8-1.5-1.8-.5-3-.9-3-2.4 0-1.5 1.1-2.5 3.3-2.5.9 0 1.9.2 2.7.5l-.5 1.6c-.6-.3-1.4-.5-2.2-.5-.9 0-1.4.4-1.4 1 0 .6.4 1 1.9 1.4 1.9.6 2.9 1.2 2.9 2.6 0 1.6-1.2 2.6-3.6 2.6-1.1 0-2.3-.3-3.4-.8l.3-1.6zM4.4 16.2c-.7 0-1.3-.2-1.8-.6l.5-1.5c.3.3.8.5 1.3.5.8 0 1.3-.4 1.3-1.1V7.8h1.9v6c0 1.6-1.1 2.4-3.2 2.4z"/></svg>',
      'bg' => '#e8eefc', 'fg' => '#003791',
    ],
    [
      'en' => 'Gift cards',
      'ru' => 'Подарочные карты',
      'href' => url('/shop/category/gift-cards'),
      'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M3.5 7.5A1.5 1.5 0 015 6h14a1.5 1.5 0 011.5 1.5v9A1.5 1.5 0 0119 18H5a1.5 1.5 0 01-1.5-1.5v-9zM12 8.3L10.6 7A1.3 1.3 0 008.7 7a1.3 1.3 0 000 1.8L12 12l3.3-3.2A1.3 1.3 0 0015.3 7a1.3 1.3 0 00-1.9 0L12 8.3zM4.8 13.5h14.4v3H4.8v-3z"/></svg>',
      'bg' => '#fff3e8', 'fg' => '#c2410c',
    ],
    [
      'en' => 'Scripts',
      'ru' => 'Скрипты',
      'href' => url('/shop/category/scripts'),
      'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M8.2 7.2L3.8 12l4.4 4.8 1.5-1.4L6.7 12l3-3.4-1.5-1.4zm7.6 0l-1.5 1.4 3 3.4-3 3.4 1.5 1.4 4.4-4.8-4.4-4.8z"/></svg>',
      'bg' => '#f3e8ff', 'fg' => '#6d28d9',
    ],
  ];
  $ashScRu = \Fickrr\Support\LocaleUrl::current() === 'ru';
@endphp
<style>
.ash-shortcuts{padding:12px 0 4px}
.ash-shortcuts-track{display:flex;gap:10px;overflow-x:auto;-webkit-overflow-scrolling:touch;scroll-snap-type:x proximity;padding:2px 2px 10px;scrollbar-width:thin}
.ash-shortcuts a{flex:1 0 auto;min-width:92px;max-width:140px;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;gap:8px;text-decoration:none;color:#111827;font-weight:600;font-size:.86rem;line-height:1.2;text-align:center;scroll-snap-align:start;padding:4px}
.ash-shortcuts a:hover{color:#0f172a;text-decoration:none}
.ash-shortcuts a:hover .ash-sc-ico{transform:translateY(-1px)}
.ash-sc-ico{width:52px;height:52px;border-radius:16px;display:flex;align-items:center;justify-content:center;transition:transform .15s ease}
.ash-sc-ico svg{width:26px;height:26px}
@media (max-width: 575.98px){
  .ash-shortcuts{padding:8px 0 0}
  .ash-shortcuts a{min-width:78px;font-size:.78rem}
  .ash-sc-ico{width:46px;height:46px;border-radius:14px}
}
</style>
<nav class="ash-shortcuts" aria-label="{{ __('Shop by category') }}">
  <div class="container">
    <div class="ash-shortcuts-track">
      @foreach($ashSc as $sc)
      <a href="{{ $sc['href'] }}">
        <span class="ash-sc-ico" style="background:{{ $sc['bg'] }};color:{{ $sc['fg'] }};">{!! $sc['icon'] !!}</span>
        <span>{{ $ashScRu ? $sc['ru'] : $sc['en'] }}</span>
      </a>
      @endforeach
    </div>
  </div>
</nav>
