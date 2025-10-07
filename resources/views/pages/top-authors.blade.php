<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Top Authors') }}</title>
@include('meta')
@include('style')
</head>
<body>
@include('header')
<section class="bg-position-center-top" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="py-4">
        <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Top Authors') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Top Authors') }}</li>
            </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Top Authors') }}</h1>
        </div>
      </div>
      </div>
    </section>
<div class="container py-5 mt-md-2 mb-2">
      <div class="row">
      <section class="col-lg-8">
          @if(in_array('top-authors',$top_ads))
          <div class="mt-2 mb-4" align="center">
          @php echo html_entity_decode($addition_settings->top_ads); @endphp
          </div>
          @endif
		  @foreach($itemData['item'] as $user)
          @if($count_sale->has($user->id) != 0)
          @php
          $membership = date('m/d/Y',strtotime($user->created_at));
          $membership_date = explode("/", $membership);
          $year = (date("md", date("U", mktime(0, 0, 0, $membership_date[0], $membership_date[1], $membership_date[2]))) > date("md")
                                        ? ((date("Y") - $membership_date[2]) - 1)
                                        : (date("Y") - $membership_date[2]));
          $referral_count = $user->referral_count;  
          @endphp
          <div class="d-sm-flex justify-content-between mt-lg-4 mb-4 pb-3 pb-sm-2 border-bottom prod-item" data-aos="fade-up" data-aos-delay="200">
            <div class="media media-ie-fix d-block d-sm-flex text-sm-left">
            <a href="{{ url('/user') }}/{{ $user->username }}" class="d-inline-block mx-auto mr-sm-4" style="width: 7rem;">
             @if($user->user_photo != '')
             <img class="lazy img-rounded" width="112" height="112" src="{{ url('/') }}/public/storage/users/{{ $user->user_photo }}"  alt="{{ $user->name }}">
             @else
             <img class="lazy img-rounded" width="112" height="112" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ $user->name }}">
             @endif
             </a>
              <div class="media-body pt-2">
                <h3 class="product-title font-size-base mb-2"><a href="{{ url('/user') }}/{{ $user->username }}">
				@if($addition_settings->author_name_limit != 0)
				{{ mb_substr($user->name,0,$addition_settings->author_name_limit,'utf-8') }}
				@else
				{{ $user->name }}	  
				@endif				
				@if($addition_settings->subscription_mode == 1) @if($user->user_document_verified == 1) <span class="badges-success"><i class="dwg-check-circle danger"></i> {{ __('verified') }}</span>@endif @endif</a></h3>
                @if($user->country_badge == 1)
                <div class="badges-icon">
                <ul>
                 @if($user->country_badges != "")
                   <li>
                     <img class="lazy icon-badges" width="32" height="32" src="{{ url('/') }}/public/storage/flag/{{ $user->country_badges }}"  border="0"  title="{{ __('Located in') }} {{ $user->country_name }}">  
                   </li>
                    @endif
                     @if($user->exclusive_author == 1)
                      <li>
                       <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->exclusive_author_icon }}"  border="0" title="{{ __('Exclusive Author: Sells items exclusively on') }} {{ $allsettings->site_title }}">
                       </li>
                       @endif
                       @if($year == 1)
                       <li>
                       <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->one_year_icon }}"  border="0" title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                       </li>
                       @endif
                       @if($year == 2)
                        <li>
                        <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->two_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                        </li>
                        @endif
                        @if($year == 3)
                        <li>
                        <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->three_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                         </li>
                         @endif
                        @if($year == 4)
                        <li>
                        <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->four_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                        </li>
                        @endif
                        @if($year == 5)
                        <li>
                        <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->five_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                        </li>
                        @endif 
                        @if($year == 6)
                        <li>
                        <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->six_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                        </li>
                        @endif
                        @if($year == 7)
                        <li>
                        <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->seven_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                       </li>
                       @endif
                       @if($year == 8)
                       <li>
                       <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->eight_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                       </li>
                       @endif
                       @if($year == 9)
                       <li>
                         <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->nine_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                         </li>
                       @endif
                       @if($year >= 10)
                       <li>
                        <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->ten_year_icon }}"  border="0"  title="@if($year >= 10) 10+ @else {{ $year }} @endif {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} @if($year >= 10) 10+ @else {{ $year }} @endif {{ __('years') }}">
                         </li>
                         @endif
                        @if($referral_count >= $badges['setting']->author_referral_level_one && $badges['setting']->author_referral_level_two > $referral_count) 
                        <li>
                         <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_one_icon }}"  border="0"  title="{{ __('Affiliate Level') }} 1: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_one }}+ {{ __('Members') }}">
                         </li>
                         @endif
                         @if($referral_count >= $badges['setting']->author_referral_level_two && $badges['setting']->author_referral_level_three > $referral_count) 
                         <li>
                          <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_two_icon }}"  border="0"  title="{{ __('Affiliate Level') }} 2: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_two }}+ {{ __('Members') }}">
                           </li>
                          @endif
                          @if($referral_count >= $badges['setting']->author_referral_level_three && $badges['setting']->author_referral_level_four > $referral_count) 
                           <li>
                           <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_three_icon }}" border="0"   title="{{ __('Affiliate Level') }} 3: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_three }}+ {{ __('Members') }}">
                           </li>
                         @endif
                         @if($referral_count >= $badges['setting']->author_referral_level_four && $badges['setting']->author_referral_level_five > $referral_count) 
                          <li>
                            <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_four_icon }}"  border="0"  title="{{ __('Affiliate Level') }} 4: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_four }}+ {{ __('Members') }}">
                             </li>
                          @endif
                          @if($referral_count >= $badges['setting']->author_referral_level_five && $badges['setting']->author_referral_level_six > $referral_count) 
                           <li>
                            <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_five_icon }}"  border="0"  title="{{ __('Affiliate Level') }} 5: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_five }}+ {{ __('Members') }}">
                            </li>
                         @endif
                         @if($referral_count >= $badges['setting']->author_referral_level_six) 
                           <li>
                            <img class="lazy other-badges" width="35" height="35" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_six_icon }}" border="0"  title="{{ __('Affiliate Level') }} 6: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_six }}+ {{ __('Members') }}">
                            </li>
                         @endif
                         </ul>
                         </div>
                         @endif
                <div class="font-size-sm">{{ $count_items->has($user->id) ? count($count_items[$user->id]) : 0 }} {{ __('Items') }}</div>
                <div class="font-size-sm">{{ __('Member since') }} {{ date("F Y", strtotime($user->created_at)) }}</div>
                <div class="font-size-sm">@if($user->country_badge == 1){{ $user->country_name }}@endif</div>
              </div>
            </div>
            <div class="pt-2 pl-sm-3 mx-auto mx-sm-0 text-center">
             <p><span class="sale-count">{{ $count_sale->has($user->id) ? count($count_sale[$user->id]) : 0 }}</span><br/>{{ __('Sales') }}</p>
             @php
             $count_rating = Helper::count_rating($user->ratings);
             @endphp
             <div class="star-rating">
                    @if($count_rating == 0)
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    @endif
                    @if($count_rating == 1)
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    @endif
                    @if($count_rating == 2)
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    @endif
                    @if($count_rating == 3)
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    @endif
                    @if($count_rating == 4)
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    @endif
                    @if($count_rating == 5)
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    @endif
                </div> 
                                                  
            </div>
          </div>
          @endif
          @endforeach
          <div class="pagination-area">
          {{ $itemData['item']->links('pagination::bootstrap-4') }}
          </div>
          @if(in_array('top-authors',$bottom_ads))
          <div class="mt-3 mb-2" align="center">
          @php echo html_entity_decode($addition_settings->bottom_ads); @endphp
          </div>
          @endif
        </section>
        <aside class="col-lg-4">
          <!-- Sidebar-->
          <div class="cz-sidebar border-left ml-lg-auto" id="blog-sidebar">
            <div class="cz-sidebar-header box-shadow-sm">
              <button class="close ml-auto" type="button" data-dismiss="sidebar" aria-label="Close"><span class="d-inline-block font-size-xs font-weight-normal align-middle">{{ __('Close sidebar') }}</span><span class="d-inline-block align-middle ml-2" aria-hidden="true">×</span></button>
            </div>
            <div class="cz-sidebar-body py-lg-1" data-simplebar="init" data-simplebar-auto-hide="true"><div class="simplebar-wrapper" style="margin: -4px -16px -4px -30px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" style="height: auto; overflow: hidden;"><div class="simplebar-content" style="padding: 4px 16px 4px 30px;">
              <!-- Categories-->
              <div class="widget widget-links mb-grid-gutter pb-grid-gutter border-bottom">
                <h3 class="widget-title">{{ __('Categories') }}</h3>
                @if(count($category['view']) != 0)
                <ul class="widget-list">
                @foreach($category['view'] as $cat)
                  <li class="widget-list-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="{{ URL::to('/shop/category/') }}/{{$cat->category_slug}}"><span>{{ $cat->category_name }}</span></a></li>
                 @endforeach 
                </ul>
                @endif
              </div>
              <!-- Trending posts-->
              <div class="widget mb-grid-gutter pb-grid-gutter border-bottom">
                <h3 class="widget-title">{{ __('Popular Items') }}</h3>
                @if(count($popular['items']) != 0)
                @php $no = 1; @endphp
                @foreach($popular['items'] as $featured)
                <div class="media align-items-center mb-3"><a href="{{ URL::to('/item') }}/{{ $featured->item_slug }}">
                @if($featured->item_preview!='')
                <img class="lazy rounded" width="64" height="49" src="{{ Helper::Image_Path($featured->item_preview,'no-image.png') }}"  alt="{{ $featured->item_name }}">
                @else
                <img class="lazy rounded" width="64" height="49" src="{{ url('/') }}/public/img/no-image.png" alt="{{ $featured->item_name }}">
                @endif
                </a>
                  <div class="media-body pl-3">
                    <h6 class="blog-entry-title font-size-sm mb-0"><a href="{{ URL::to('/item') }}/{{ $featured->item_slug }}">
					@if($addition_settings->item_name_limit != 0)
			        {{ mb_substr($featured->item_name,0,$addition_settings->item_name_limit,'utf-8').'...' }}
					@else
					{{ $featured->item_name }}	  
					@endif
					</a></h6><span class="font-size-ms text-muted">{{ __('by') }} <a href="{{ URL::to('/user') }}/{{ $featured->username }}" class="blog-entry-meta-link">{{ $featured->username }}</a></span>
                  </div>
                </div>
                @endforeach
                @endif
              </div>
              </div></div></div></div><div class="simplebar-placeholder" style="width: auto; height: 1070px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="transform: translate3d(0px, 0px, 0px); display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: hidden;"><div class="simplebar-scrollbar" style="transform: translate3d(0px, 0px, 0px); display: none;"></div></div></div>
          </div>
          @if(in_array('top-authors',$sidebar_ads))
          	<div class="mt-3 mb-2" align="center">
            @php echo html_entity_decode($addition_settings->sidebar_ads); @endphp
          	</div>
         	@endif
        </aside>
       </div>
    </div>
@include('footer')
@include('script')
</body>
</html>