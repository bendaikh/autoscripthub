<!DOCTYPE HTML>
<html lang="en">
<head>
@php
  $shopHeading = $shop_heading ?? __('Shop');
  $shopIsCategory = !empty($shop_is_category);
  $shopRobots = !empty($seo_noindex) ? 'noindex,follow' : 'index,follow';
  $shopCanonical = $canonical_url ?? \Fickrr\Support\SiteUrl::to('/shop');
  $shopTitle = $shopIsCategory
    ? $shopHeading.' - '.__('Buy Genuine Keys').' | '.$allsettings->site_title
    : $allsettings->site_title.' - '.__('Shop');
@endphp
<title>{{ $shopTitle }}</title>
<meta name="description" content="{{ $seo_desc }}" />
<meta name="keywords" content="{{ $seo_keyword }}" />
<meta name="robots" content="{{ $shopRobots }}" />
<meta property="og:title" content="{{ $shopHeading }}" />
<meta property="og:type" content="website" /> 
<meta property="og:description" content="{{ $seo_desc }}" /> 
<meta property="og:url" content="{{ $shopCanonical }}" /> 
<meta property="og:image" content="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}" />
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="{{ $allsettings->site_title }}" />
<meta name="twitter:title" content="{{ $shopHeading }}" />
<meta name="twitter:description" content="{{ $seo_desc }}" />
<meta name="twitter:image" content="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}" />
<link rel="canonical" href="{{ $shopCanonical }}" />
<meta name="twitter:image:src" content="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}">
@include('style')
</head>
<body>
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
</body>
</html>