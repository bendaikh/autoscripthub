@php
  $publicBrand = \Fickrr\Support\Store::publicName($allsettings->site_title ?? null);
  $publicDesc = \Fickrr\Support\Store::metaDescription($allsettings->site_desc ?? null);
@endphp
<meta name="description" content="{{ $publicDesc }}" />
<meta name="keywords" content="{{ $allsettings->site_keywords }}" />
<meta name="robots" content="index,follow" />
<meta property="og:title" content="{{ $publicBrand }}" />
<meta property="og:type" content="og:product" /> 
<meta property="og:description" content="{{ $publicDesc }}" /> 
<meta property="og:url" content="{{ url()->current() }}" /> 
<meta property="og:image" content="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}" />
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="{{ $publicBrand }}" />
<meta name="twitter:title" content="{{ $publicBrand }}" />
<meta name="twitter:description" content="{{ $publicDesc }}" />
<meta name="twitter:image" content="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}" />
<link rel="canonical" href="{{ url()->current() }}" />
<meta name="twitter:image:src" content="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}">