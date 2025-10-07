@if($allsettings->maintenance_mode == 1)
@if (Auth::check())
@if(Auth::user()->id == 1)
@include('pages.contact')
@else
@include('503')
@endif
@else
@include('503')
@endif
@else
@include('pages.contact')
@endif