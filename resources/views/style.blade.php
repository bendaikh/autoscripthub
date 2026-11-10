@include('version')

<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
window.addEventListener('load',function(){(window.requestIdleCallback||function(cb){setTimeout(cb,1);})(function(){s.parentNode.insertBefore(t,s)},{timeout:3000});})}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '4153934531541650');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=4153934531541650&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta name="author" content="{{ $allsettings->site_title }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="_token" content="{{csrf_token()}}" />
<meta http-equiv="Cache-control" content="public">
@if($allsettings->site_favicon != '')
<link rel="apple-touch-icon" href="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_favicon }}">
<link rel="icon" href="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_favicon }}" type="image/x-icon">
<link rel="shortcut icon" href="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_favicon }}" type="image/x-icon">
@endif
@if($view_name == 'index')
<link rel="stylesheet" media="screen" href="{{ asset('theme/css/vendor.min.css') }}">
<link rel="stylesheet" media="screen" href="{{ asset('theme/css/theme.min.css') }}">
@include('dynamic')
<link href="{{ asset('theme/cookie/cookiealert.css') }}" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="{{ asset('theme/animate/aos.css') }}" />
<link rel="stylesheet" href="{{ asset('theme/autosearch/jquery-ui.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/validate/themes/red/red.css') }}" />
@else
<link rel="stylesheet" media="screen" href="{{ asset('theme/css/vendor.min.css') }}">
<link rel="stylesheet" media="screen" href="{{ asset('theme/css/theme.min.css') }}">
@include('dynamic')
<link href="{{ asset('theme/cookie/cookiealert.css') }}" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="{{ asset('theme/animate/aos.css') }}" />
<link rel="stylesheet" href="{{ asset('theme/autosearch/jquery-ui.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/validate/themes/red/red.css') }}" />
<link rel="stylesheet" href="{{ asset('theme/pagination/pagination.css') }}">
<link type="text/css" href="{{ asset('theme/countdown/jquery.countdown.css?v=1.0.0.0') }}" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('theme/video/video.css') }}">
<link rel="stylesheet" href="{{ asset('admin/datepicker/picker.css') }}">
<link href="{{ asset('admin/dragdrop/css/jquery.filer.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('theme/mp3/css/stylised.css') }}" />
@if($view_name == 'messages')
<link rel="stylesheet" href="{{ asset('theme/chatbox/chat.css') }}">
@endif
<link href="{{ asset('theme/emojione/emojione.picker.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="{{ asset('admin/dropzone/min/dropzone.min.css')}}">
@endif
@if($current_locale == 'ar')
<link rel="stylesheet" href="{{ asset('theme/css/rtl.css') }}" />
@endif
<link rel="stylesheet" href="{{ asset('assets/style.css') }}">
@php $asbBrandFile = base_path('public/assets/brand.css'); @endphp
<link rel="stylesheet" href="{{ asset('assets/brand.css') }}?v={{ file_exists($asbBrandFile) ? filemtime($asbBrandFile) : 1 }}">
@if($view_name == 'index')
@php $hvThemeFile = base_path('public/assets/home-theme.css'); @endphp
<link rel="stylesheet" href="{{ asset('assets/home-theme.css') }}?v={{ file_exists($hvThemeFile) ? filemtime($hvThemeFile) : 1 }}">
@endif
<link rel="stylesheet" href="{{ asset('theme/stripe/stripe.css') }}">
<!-- Sales Popup Notification System -->
<link rel="stylesheet" href="{{ asset('css/sales-popup.css') }}">
@if($addition_settings->google_ads == 1)
{{-- Auto Ads: load client script only. Do not push({}) here — that requires an empty <ins class="adsbygoogle"> and breaks checkout/pages without ad units. --}}
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9233828084974503"
     crossorigin="anonymous"></script>
@endif
@if($addition_settings->shop_search_type == 'ajax')
<link rel="stylesheet" href="{{ asset('theme/filter/jplist.core.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/filter/jplist.jquery-ui-bundle.min.css') }}">
<link rel="stylesheet" href="{{ asset('theme/filter/jquery-ui.css') }}" />
@endif
@if($addition_settings->header_layout == 'layout_two')
<link rel="stylesheet" href="{{ asset('theme/menu/layout_two.css') }}" />
@if($current_locale == 'ar')
<link rel="stylesheet" href="{{ asset('theme/menu/rtl.css') }}" />
@endif
@endif
@if($view_name == 'index')
@if($custom_settings->promotion_popup == 1)
<link rel="stylesheet" href="{{ asset('theme/popup/popup.css') }}" />
@endif
@endif
@laravelPWA