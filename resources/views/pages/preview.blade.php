<!DOCTYPE html>
<html lang="en">
<head>
<title>{{ $item['item']->item_name }} - {{ $allsettings->site_title }}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
@if($allsettings->site_favicon != '')
<link rel="apple-touch-icon" href="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_favicon }}">
<link rel="shortcut icon" href="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_favicon }}">
@endif
<link rel="stylesheet" href="{{ asset('theme/preview/css/bootstrap.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('theme/preview/css/app.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('theme/preview/css/style.css') }}" type="text/css" />
</head>

<body>
<div id="header-bar">
<header id="header" class="navbar navbar-fixed-top bg-white-only box-shadow"  data-spy="affix" data-offset-top="1">
<div class="navbar-header text-center">
         @if($allsettings->site_logo != '')
         <a href="{{ URL::to('/') }}" class="navbar-brand m-r-lg">
         <img class="lazy img-fluid" width="200" height="56" src="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}"  alt="{{ $allsettings->site_title }}">
         </a>
         @endif
</div>
<ul class="nav navbar-nav text-center deskonly">
<li>
     <div class="">
       <a href="{{ URL::to('/item') }}/{{ $item['item']->item_slug }}" class="btn btn-md w-sm btn-success m-r-sm m-t-xxs"><strong>{{ __('Buy Now') }}</strong></a>
       <a href="{{ $item['item']->demo_url }}" class="btn btn-link btn-md"><i class="fa fa-remove m-r-xs m-t-xxs"></i>{{ __('Remove frame') }}</a>
     </div>
</li>
</ul>  
</div>
</header>
<iframe id="preview-frame" class="w-full h-full" src="{{ $item['item']->demo_url }}" name="preview-frame" frameborder="0" noresize="noresize">Your browser does not support frames.
</iframe>
<!-- / footer -->
<script src="{{ asset('theme/preview/js/jquery.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('theme/preview/js/bootstrap.js') }}"></script>
</body>
</html>