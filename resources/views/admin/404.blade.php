<div id="right-panel" class="right-panel">
@include('admin.header')
@if($view_name == 'admin-index')
@else
<h3 align="center" class="mt-3 pt-3">{{ __('404 Not Found') }}</h3>
@endif
</div>