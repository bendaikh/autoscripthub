<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Subscription') }}</title>
@include('meta')
@include('style')
<link rel="stylesheet" href="{{ asset('theme/css/tailwind.min.css') }}" />
</head>
<body>
@include('header')
@if($addition_settings->subscription_mode == 1)
<section class="bg-position-center-top" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="py-4">
        <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Subscription') }}</li>
            </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Subscription') }}</h1>
        </div>
      </div>
      </div>
    </section>
<div class="faq-section section-padding">
		<div class="container py-5 mt-md-2 mb-2">
            @if(in_array('subscription',$top_ads))
              <div class="row">
                  <div class="col-lg-12 mt-4" align="center">
                     @php echo html_entity_decode($addition_settings->top_ads); @endphp
                  </div>
               </div>   
               @endif
            <div class="row">
                <div class="col-lg-12">
                  @if($addition_settings->subscription_title != "")
                  <h1>{{ $addition_settings->subscription_title }}</h1>
                  @endif
                  @if($addition_settings->subscription_desc != "")
                  <div class="font-size-md">@php echo html_entity_decode($addition_settings->subscription_desc); @endphp</div>
                  @endif
                 </div>
              </div>
			<div class="row">
                <div class="py-20 radius-for-skewed">
            <div class="container mx-auto px-4">
                <div class="flex flex-wrap -mx-4">
                    @foreach($subscription['view'] as $subscription)
                    <div class="w-full md:w-1/2 lg:w-1/3 px-4 mb-8 lg:mb-10">
                        <div @if($subscription->highlight_pack == 0) class="p-8 bg-white shadow rounded border-0 hover:border-2 cursor-pointer" @else class="p-8 shadow rounded border-0 hover:border-2 cursor-pointer" style="background: {{ $subscription->highlight_bg_color }}; color: {{ $subscription->highlight_text_color }};" @endif>
                            <h4 class="mb-2 text-2xl font-bold font-heading" data-config-id="03_title" @if($subscription->highlight_pack == 1) style="color: {{ $subscription->highlight_text_color }};" @endif>{{ $subscription->subscr_name }}</h4> <span class="text-6xl font-bold" data-config-id="03_price" @if($subscription->highlight_pack == 0) style="color:#000000;" @endif>{{ Helper::price_format($allsettings->site_currency_position,$subscription->subscr_price,$currency_symbol,$multicurrency) }}</span> <span @if($subscription->highlight_pack == 0) class="text-gray-400 text-xs" @else class="text-xs" @endif data-config-id="03_note">/{{ $subscription->subscr_duration }}</span>
                            @if($subscription->extra_info != "")
                            <p @if($subscription->highlight_pack == 0) class="mt-3 mb-6 text-gray-500 leading-loose" @else class="mt-3 mb-6 leading-loose" @endif>{{ $subscription->extra_info }}</p>
                            @endif
                            
                            <div class="price-list">
 							<ul>
                                @if($subscription->subscr_item_level == 'limited')
 								<li><i class="fa fa-check-circle" aria-hidden="true" style="color: {{ $subscription->icon_color }};"></i> {{ $subscription->subscr_item }} {{ __('Upload') }} {{ __('Items') }}</li>
                                @else
                                <li><i class="fa fa-check-circle" aria-hidden="true" style="color: {{ $subscription->icon_color }};"></i> {{ __('Unlimited Upload Items') }}</li>
                                @endif
                                @if($subscription->subscr_download_item != 0)
                                <li><i class="fa fa-check-circle" aria-hidden="true" style="color: {{ $subscription->icon_color }};"></i> {{ $subscription->subscr_download_item }} {{ __('Download items per day') }}</li>
                                @endif
                                @if($subscription->subscr_space_level == 'limited')
 								<li><i class="fa fa-check-circle" aria-hidden="true" style="color: {{ $subscription->icon_color }};"></i> {{ $subscription->subscr_space }}{{ $subscription->subscr_space_type }} {{ __('Space Available') }}</li>
                                @else
                                <li><i class="fa fa-check-circle" aria-hidden="true" style="color: {{ $subscription->icon_color }};"></i> {{ __('Unlimited Space Available') }}</li>
                                @endif
								<li>@if($subscription->subscr_email_support == 1)<i class="fa fa-check-circle" aria-hidden="true" style="color: {{ $subscription->icon_color }};"></i>@else<i class="fa fa-times-circle" aria-hidden="true" style="color: {{ $subscription->icon_color }};"></i>@endif {{ __('Email Support') }}</li>										
								<li>@if($subscription->subscr_payment_mode == 1)<i class="fa fa-check-circle" aria-hidden="true" style="color: {{ $subscription->icon_color }};"></i>@else<i class="fa fa-times-circle" aria-hidden="true" style="color: {{ $subscription->icon_color }};"></i>@endif {{ __('Direct Transfer Payment') }}</li>
								<li>@if($subscription->subscr_payment_mode == 1)<i class="fa fa-check-circle" aria-hidden="true" style="color: {{ $subscription->icon_color }};"></i> {{ __('Without Commission Payment') }}@else<i class="fa fa-times-circle" aria-hidden="true" style="color: {{ $subscription->icon_color }};"></i> {{ __('Without Commission Payment') }}@endif</li>
								<li><i class="fa fa-check-circle" aria-hidden="true" style="color: {{ $subscription->icon_color }};"></i> {{ __('Support 24 x 7') }}</li>
 							</ul>
 						</div>
                        @if(Auth::guest())
                      <a class="inline-block text-center py-2 px-4 w-full rounded-l-xl rounded-t-xl font-bold leading-loose transition duration-200" href="{{ URL::to('/login') }}" style="background: {{ $subscription->button_bg_color }}; color:{{ $subscription->button_text_color }};">{{ __('Upgrade') }}</a>
                        @else
                        @if(Auth::user()->id != 1)
                        
                        @if(Auth::user()->user_subscr_type == $subscription->subscr_name)
                        <a class="inline-block text-center py-2 px-4 w-full rounded-l-xl rounded-t-xl font-bold leading-loose transition duration-200" href="javascript:void(0)" style="background: {{ $subscription->button_bg_color }}; color:{{ $subscription->button_text_color }};">{{ __('Upgrade') }}</a>
                        @else
                        <a class="inline-block text-center py-2 px-4 w-full rounded-l-xl rounded-t-xl font-bold leading-loose transition duration-200" href="{{ URL::to('/confirm-subscription') }}/{{ base64_encode($subscription->subscr_id) }}" style="background: {{ $subscription->button_bg_color }}; color:{{ $subscription->button_text_color }};">{{ __('Upgrade') }}</a>
                        @endif
                        
                        @endif
                        @endif
                        </div>
                    </div>
                    @endforeach	
                </div>
            </div>
        </div>
        </div>
		</div>
        @if(in_array('subscription',$bottom_ads))
        <div class="row">
          <div class="col-lg-12 mt-2 mb-2" align="center">
             @php echo html_entity_decode($addition_settings->bottom_ads); @endphp
          </div>
       </div>   
       @endif
	</div>
    @else
    @include('not-found')
    @endif
@include('footer')
@include('script')
</body>
</html>