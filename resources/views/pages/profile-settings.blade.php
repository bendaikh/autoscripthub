<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - @if(Auth::user()->id != 1) {{ __('Profile Settings') }} @else {{ __('404 Not Found') }} @endif</title>
@include('meta')
@include('style')
</head>
<body>
@include('header')
@if($addition_settings->subscription_mode == 0)
	@include('profile')
@else
	@if(Auth::user()->user_type == 'vendor')
        @include('profile')
   @elseif(Auth::user()->user_type == 'customer')
        @include('profile')
   @else
        @include('not-found')
   @endif
@endif
@include('footer')
@include('script')
</body>
</html>