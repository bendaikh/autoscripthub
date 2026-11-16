{{-- Shared SEO tags. Pages may set: $seo_title, $seo_desc, $seo_image, $canonical_url, $seo_noindex, $skip_default_description. --}}
@php
  $mPath = '/' . ltrim((string) request()->path(), '/');
  $mPage = (int) request()->query('page', 0);
  // one canonical address per page: https://auto-script-hub.com + path (keeps ?page=N on paginated lists)
  $mCanonical = $canonical_url ?? (\Fickrr\Support\SiteUrl::to($mPath) . ($mPage > 1 ? '?page=' . $mPage : ''));
  // account, cart and search result pages should not appear in Google
  $mPrivate = request()->is('cart', 'cart/*', 'checkout', 'checkout/*', 'login', 'register', 'forgot', 'password/*', 'reset/*',
      'profile-settings', 'purchases', 'purchases/*', 'messages', 'messages/*', 'favourites', 'withdrawal', 'deposit', 'search', 'search/*',
      'manage-item', 'upload-item', 'upload-item/*', 'edit-item', 'edit-item/*', 'my-*', 'success/*', 'cancel', 'notifications', 'coupon', 'edit-coupon/*', 'add-coupon')
      || request()->query('search') !== null || request()->query('sort') !== null;
  $mRobots = (!empty($seo_noindex) || $mPrivate) ? 'noindex,follow' : 'index,follow,max-image-preview:large';
  $mTitle = !empty($seo_title) ? $seo_title : $allsettings->site_title;
  $mDesc = !empty($seo_desc) ? $seo_desc : $allsettings->site_desc;
  $mImage = !empty($seo_image) ? $seo_image : url('/') . '/public/storage/settings/' . $allsettings->site_logo;
@endphp
@if(empty($skip_default_description))
<meta name="description" content="{{ $mDesc }}" />
@endif
@if(!empty($allsettings->site_keywords))<meta name="keywords" content="{{ $allsettings->site_keywords }}" />@endif
<meta name="robots" content="{{ $mRobots }}" />
<link rel="canonical" href="{{ $mCanonical }}" />
<meta property="og:site_name" content="{{ $allsettings->site_title }}" />
<meta property="og:title" content="{{ $mTitle }}" />
<meta property="og:type" content="website" />
<meta property="og:description" content="{{ $mDesc }}" />
<meta property="og:url" content="{{ $mCanonical }}" />
<meta property="og:image" content="{{ $mImage }}" />
<meta name="twitter:card" content="{{ !empty($seo_image) ? 'summary_large_image' : 'summary' }}" />
<meta name="twitter:title" content="{{ $mTitle }}" />
<meta name="twitter:description" content="{{ $mDesc }}" />
<meta name="twitter:image" content="{{ $mImage }}" />
