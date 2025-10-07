<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="rtl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>{{ $product_name }}</title>
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
        <h3>{{ __('License Certificate') }}</h3>
        <p>{{ __('This document cerifies the purchase of the following license :') }}<strong>{{ $license }}</strong><br/>
        {{ __('Details of the license can be accessed from your purchase page') }}
        </p>
        </td>
      </tr> 
      <tr>
       <td colspan="3">
         <table cellpadding="0" cellspacing="10">
          <tr>
            <td><strong>{{ __('Item Name') }}</strong></td>
            <td>:</td>
            <td>{{ $product_name }}</td>
          </tr>
          <tr>
            <td><strong>{{ __('Item Url') }}</strong></td>
            <td>:</td>
            <td><a href="{{ URL::to('/') }}/item/{{ $product_slug }}" target="_blank">{{ URL::to('/') }}/item/{{ $product_slug }}</a></td>
          </tr>
          <tr>
            <td><strong>{{ __('Price') }}</strong></td>
            <td>:</td>
            <td>{{ Helper::plan_format($allsettings->site_currency_position,$product_price,$currency_type) }}</td>
          </tr>
          <tr>
            <td><strong>{{ __('Licensee') }}</strong></td>
            <td>:</td>
            <td>{{ $username }}</td>
          </tr>
          <tr>
            <td><strong>{{ __('Order ID') }}</strong></td>
            <td>:</td>
            <td>{{ $order_id }}</td>
          </tr>
          <tr>
            <td><strong>{{ __('Product Id') }}</strong></td>
            <td>:</td>
            <td>{{ $product_id }}</td>
          </tr>
          <tr>
            <td><strong>{{ __('Purchase Id') }}</strong></td>
            <td>:</td>
            <td>{{ $purchase_id }}</td>
          </tr>
          <tr>
            <td><strong>{{ __('Purchase Date') }}</strong></td>
            <td>:</td>
            <td>{{ date("d M Y", strtotime($purchase_date)) }}</td>
          </tr>
          <tr>
            <td><strong>{{ __('Expiry Date') }}</strong></td>
            <td>:</td>
            <td>{{ date("d M Y", strtotime($expiry_date)) }}</td>
          </tr>
          <tr>
            <td><strong>{{ __('Payment Type') }}</strong></td>
            <td>:</td>
            <td>{{ $payment_type }}</td>
          </tr>
          <tr>
            <td><strong>{{ __('Payment Token') }}</strong></td>
            <td>:</td>
            <td>{{ $payment_token }}</td>
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