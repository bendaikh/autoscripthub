<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="rtl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>{{ __('Download Serial Key') }}</title>
@if($current_locale == 'ar')
<style type="text/css">
body {
    font-family: DejaVu Sans;
	direction:rtl !important;
    text-align:right !important;
}
</style>
@else
<style type="text/css">
body {
    font-family: DejaVu Sans;
	}
</style>	
@endif
@if($addition_settings->site_invoice == 1)
@php $current_locale = $current_locale; @endphp
@else
@php $current_locale = 'en'; @endphp
@endif
</head>
<body>
  <table width="100%" border="0">
      <tr>
        <td colspan="3">
          @if($allsettings->site_logo != '')
          <a href="{{ URL::to('/') }}" target="_blank">
          <img class="lazy" width="200" height="56" src="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}"  alt="{{ $allsettings->site_title }}"/>
          </a>
          @endif
        </td>
      </tr>
      <tr>
       <td colspan="3">
         <table cellpadding="0" cellspacing="10">
          <tr>
            <td><strong>{{ __('Serial Key') }} : </strong></td>
            <td colspan="2"></td>
          </tr>
          <tr>
          <td colspan="3">{!! nl2br($serial_key) !!}</td>
          </tr>
        </table>
      </td>
    </tr>  
    <tr>
      <td colspan="3">
      <p>{{ __('For any query related to this document or license please contact support via') }} <a href="{{ URL::to('/') }}" target="_blank"><strong>{{ URL::to('/') }}</strong></a></p>
      </td>
    </tr>      
    </table>
</body>
</body>
</html>