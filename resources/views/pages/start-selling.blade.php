<!DOCTYPE HTML>
<html lang="en">
<head>
<title>
@if($allsettings->site_selling_display == 1) 
@if(Auth::guest())
{{ __('Start Selling') }}
@else
@if(Auth::user()->user_type == 'vendor')
{{ __('Start Selling') }}
@else
{{ __('404 Not Found') }} 
@endif
@endif 
@else 
{{ __('404 Not Found') }} 
@endif - {{ $allsettings->site_title }}
</title>
@include('meta')
@include('style')
</head>
<body>
@include('header')
@if($allsettings->site_selling_display == 1)
@if(Auth::guest())
@include('selling')
@else
@if(Auth::user()->user_type == 'vendor')
@include('selling')
@else
@include('not-found')
@endif
@endif
@else
@include('not-found')
@endif
@include('footer')
@include('script')
</body>
</html>