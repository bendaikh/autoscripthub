@if($allsettings->maintenance_mode == 1)
@if (Auth::check())
@if(Auth::user()->id == 1)
@include('pages.index')
@else
@include('503')
@endif
@else
@include('503')
@endif
@else
@include('pages.index')
@endif