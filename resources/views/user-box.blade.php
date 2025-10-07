<div class="page-title-overlap pt-4" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="container d-flex flex-wrap flex-sm-nowrap justify-content-center justify-content-sm-between align-items-center pt-2">
        <div class="media media-ie-fix align-items-center pb-3">
          <div class="img-thumbnail rounded-circle position-relative">
          @if($user['user']->user_photo != '')
          <img class="lazy rounded-circle" width="90" height="90" src="{{ url('/') }}/public/storage/users/{{ $user['user']->user_photo }}"  alt="{{ $user['user']->username }}">
          @else
          <img class="lazy rounded-circle" width="90" height="90" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ $user['user']->username }}">
          @endif
          </div>
          <div class="media-body pl-3">
            <h3 class="text-light font-size-lg mb-0">
			@if($addition_settings->author_name_limit != 0)
			{{ mb_substr($user['user']->username,0,$addition_settings->author_name_limit,'utf-8') }}
			@else
			{{ $user['user']->username }}	  
			@endif			
			@if($addition_settings->subscription_mode == 1) @if($user['user']->user_document_verified == 1) <span class="badges-success"><i class="dwg-check-circle danger"></i> {{ __('verified') }}</span> @endif @endif</h3>
            <span class="d-block text-light font-size-ms opacity-60 py-1">
            @if($user['user']->user_type == 'vendor')
            @if($user['user']->country_badge == 1){{ $country['view']->country_name }},@endif @endif @if($user['user']->user_type == 'customer') @if($user['user']->country != '') {{ $country['view']->country_name }}, @endif @endif {{ __('Member since') }} {{ $since }}</span>
            @if($user['user']->user_freelance == 1)
            <span class="badge badge-success"><i class="dwg-check mr-1"></i>{{ __('Available for Freelance work') }}</span>
            @endif
            @if(Auth::guest())
            <div class="mt-2">
            <a class="btn btn-primary btn-sm btn-shadow" href="javascript:void(0);" onClick="alert('{{ __('Login user only') }}');">{{ __('Follow') }}</a>
            </div>
            @endif
            @if (Auth::check())
            @if($user['user']->username != Auth::user()->username)
            @if($followcheck == 0)
            <div class="mt-2">
            <a href="{{ url('/user') }}/{{ Auth::user()->id }}/{{ $user['user']->id }}" class="btn btn-primary btn-shadow btn-sm">{{ __('Follow') }}</a>
            </div>
            @else
            <div class="mt-2">
            <a href="{{ url('/user') }}/unfollow/{{ Auth::user()->id }}/{{ $user['user']->id }}" class="btn btn-primary btn-shadow btn-sm">{{ __('UnFollow') }}</a>
            </div>
            @endif
            @endif
            @endif
          </div>
        </div>
        @if($user['user']->user_type == 'vendor')
        <div class="d-flex">
          @if($addition_settings->item_sale_count == 1)
          <div class="text-sm-right mr-5">
            <div class="text-light font-size-base">{{ __('total sales') }}</div>
            <h3 class="text-light">{{ $getsalecount }}</h3>
          </div>
          @endif
          <div class="text-sm-right">
            <div class="text-light font-size-base">{{ __('Total Ratings') }}</div>
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
            <div class="text-light opacity-60 font-size-xs">({{ $getreview }})</div>
          </div>
        </div>
        @endif
      </div>
    </div>