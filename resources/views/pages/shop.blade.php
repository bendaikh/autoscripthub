<!DOCTYPE HTML>
<html lang="en">
<head>
@php
  $shopHeading = $shop_heading ?? __('Shop');
  $shopIsCategory = !empty($shop_is_category);
  $shopRobots = !empty($seo_noindex) ? 'noindex,follow' : 'index,follow';
  $shopCanonical = $canonical_url ?? \Fickrr\Support\SiteUrl::to('/shop');
  if (empty($seo_desc) || \Fickrr\Support\PageMeta::isGeneric($seo_desc, $allsettings->site_desc ?? '') || \Fickrr\Support\PageMeta::isThinListingDesc($seo_desc ?? '')) {
      $seo_desc = \Fickrr\Support\PageMeta::listingDesc($shopHeading, !empty($shopIsCategory));
  }
  $shopTitle = $shopIsCategory
    ? $shopHeading.' - '.__('Buy Genuine Keys').' | '.$allsettings->site_title
    : __('Shop digital keys, scripts and licenses').' | '.$allsettings->site_title;
  $shopCanonical = \Fickrr\Support\LocaleUrl::withLang($shopCanonical, \Fickrr\Support\LocaleUrl::current() === 'ru' ? 'ru' : 'en');
  // SEO2: keyword-targeted title / description / H1 / intro (storage/app/seo/copy.json)
  $shopPath = '/'.trim((string) request()->path(), '/');
  $shopCopyKey = null;
  if (preg_match('#^/shop/(category|subcategory)/([^/]+)$#', $shopPath, $shopM)) {
      $shopCopyKey = $shopM[1].':'.$shopM[2];
  } elseif ($shopPath === '/shop') {
      $shopCopyKey = 'shop';
  }
  $shopCopy = $shopCopyKey ? \Fickrr\Support\SeoCopy::get($shopCopyKey) : null;
  if ($shopCopy) {
      $shopTitle = $shopCopy['title'] ?? $shopTitle;
      $seo_desc = $shopCopy['desc'] ?? $seo_desc;
      $shopHeading = $shopCopy['h1'] ?? $shopHeading;
  } elseif (\Fickrr\Support\LocaleUrl::current() === 'ru') {
      $shopHeading = __($shopHeading);
  }
  if (\Fickrr\Support\PageMeta::isGeneric($seo_desc, $allsettings->site_desc ?? '') || \Fickrr\Support\PageMeta::isThinListingDesc($seo_desc ?? '')) {
      $seo_desc = \Fickrr\Support\PageMeta::listingDesc($shopHeading, !empty($shopIsCategory));
  }
  $shopItems = $itemData['item'] ?? null;
  $shopIsPaged = is_object($shopItems) && method_exists($shopItems, 'currentPage');
  $shopPage = $shopIsPaged ? (int) $shopItems->currentPage() : 1;
  if ($shopPage > 1) {
      $shopSuffix = \Fickrr\Support\SeoCopy::pageSuffix($shopPage);
      $shopTitle = strpos($shopTitle, ' | ') !== false ? preg_replace('/ \| /', $shopSuffix.' | ', $shopTitle, 1) : $shopTitle.$shopSuffix;
      $seo_desc = rtrim($seo_desc, '. ').'. '.(\Fickrr\Support\LocaleUrl::current() === 'ru' ? 'Страница '.$shopPage.'.' : 'Page '.$shopPage.'.');
  }
  $shopIntro = ($shopCopy && $shopPage === 1 && empty($seo_noindex)) ? \Fickrr\Support\SeoCopy::introHtml($shopCopy['intro'] ?? '') : '';
  $shopPrev = ($shopIsPaged && empty($seo_noindex) && $shopItems->previousPageUrl()) ? $shopItems->previousPageUrl() : null;
  $shopNext = ($shopIsPaged && empty($seo_noindex) && $shopItems->hasMorePages()) ? $shopItems->nextPageUrl() : null;
  if ($shopPrev && $shopPage === 2) {
      $shopPrev = preg_replace('/([?&])page=1(&|$)/', '$1', $shopPrev);
      $shopPrev = rtrim($shopPrev, '?&');
  }
@endphp
<title>{{ $shopTitle }}</title>
<meta name="description" content="{{ $seo_desc }}" />
<meta name="keywords" content="{{ $seo_keyword }}" />
<meta name="robots" content="{{ $shopRobots }}" />
<meta property="og:title" content="{{ $shopTitle }}" />
<meta property="og:type" content="website" /> 
<meta property="og:description" content="{{ $seo_desc }}" /> 
<meta property="og:url" content="{{ $shopCanonical }}" /> 
<meta property="og:image" content="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}" />
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="{{ $allsettings->site_title }}" />
<meta name="twitter:title" content="{{ $shopTitle }}" />
<meta name="twitter:description" content="{{ $seo_desc }}" />
<meta name="twitter:image" content="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}" />
<link rel="canonical" href="{{ $shopCanonical }}" />
@if($shopPrev)<link rel="prev" href="{{ $shopPrev }}" />@endif
@if($shopNext)<link rel="next" href="{{ $shopNext }}" />@endif
<meta name="twitter:image:src" content="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}">
@include('style')
@include('partials.listing-head')
</head>
<body class="home-vibrant listing-page">
@include('header')
<section class="bg-position-center-top" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="py-4">
        <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ $shopHeading }}</li>
            </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ $shopHeading }}</h1>
        </div>
      </div>
      </div>
    </section>
@if($shopIntro !== '')
<div class="container pt-4 shop-seo-intro" style="max-width:1140px;font-size:.95rem;line-height:1.6;color:#4b5563;">{!! $shopIntro !!}</div>
@endif
<div class="container py-5 mt-md-2 mb-2">
      @if($addition_settings->shop_search_type == 'normal')
      @include('shop-normal')
      @else
      @include('shop-ajax')
      @endif
      </div>
    </div>
@include('footer')
@include('script')
@include('partials.listing-script')
</body>
</html>