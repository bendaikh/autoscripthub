<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $addition_settings->site_home_title }} - {{ $allsettings->site_title }}</title>
@include('meta')
@include('style')
</head>
<body>
@include('header')
<section class="bg-position-center-top" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="mb-lg-3 pb-4 pt-5">
        <div class="container">
          <div class="row mb-4 mb-sm-5">
            <div class="col-lg-7 col-md-9 text-center mx-auto">
              <h1 class="text-white line-height-base">{{ $allsettings->site_banner_heading }}</h1>
              <h2 class="h4 text-white font-weight-light">{{ $allsettings->site_banner_subheading }}</h2>
            </div>
          </div>
          
          <form action="{{ route('shop') }}" id="search_form" method="post" class="form-noborder searchbox" enctype="multipart/form-data">
          {{ csrf_field() }}
          <div class="row mb-4 mb-sm-5">
            <div class="col-lg-7 col-md-7 mx-auto text-center">
              <div class="input-group input-group-overlay input-group-lg">
                <div class="input-group-prepend-overlay"><span class="input-group-text"><i class="dwg-search"></i></span></div>
                <input class="form-control form-control-lg prepended-form-control rounded-right-0" type="text" id="product_item" name="product_item" placeholder="{{ __('Search your products...') }}">
                @if(count($category['view']) != 0)
                <select name="category_names[]" id="product_cat" class="custom-select home-category-select">
                    <option value="">{{ __('All categories') }}</option>
                    @foreach($category['view'] as $cat)
                    <option value="{{ 'category_'.$cat->cat_id }}">{{ $cat->category_name }}</option>
                    @foreach($cat->subcategory as $sub_category)
                    <option value="{{ 'subcategory_'.$sub_category->subcat_id }}"> - {{ $sub_category->subcategory_name }}</option>
                    @endforeach
                    @endforeach 
                </select>
                @endif
                <div class="input-group-append">
                  <button class="btn btn-primary btn-lg font-size-base" type="submit">{{ __('Search Now') }}</button>
                </div>
              </div>
            </div>
          </div>
          </form>
        </div>
      </div>
    </section>
    @if(in_array('home',$top_ads))
    <section class="container mb-lg-1" data-aos="fade-up" data-aos-delay="200">
      <div class="row">
          <div class="col-lg-12 mb-1" align="center">
             @php echo html_entity_decode($addition_settings->top_ads); @endphp
          </div>
       </div>   
    </section>   
    @endif
@if(count($featured['items']) != 0)
<section class="container mb-lg-1" data-aos="fade-up" data-aos-delay="200">
      <!-- Heading-->
      <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2" data-aos="fade-down" data-aos-delay="100">{{ __('Featured Files') }}</h2>
        <div class="pt-3" data-aos="fade-down" data-aos-delay="100">
          <a class="btn btn-outline-accent" href="{{ URL::to('/') }}/featured-items">{{ __('Browse All Items') }}<i class="dwg-arrow-right font-size-ms ml-1"></i></a>
        </div>
      </div>
      <!-- Grid-->
      <div class="row pt-2 mx-n2 flash-sale">
        <!-- Product-->
        @php $no = 1; @endphp
        @foreach($featured['items'] as $featured)
        @php
        $price = Helper::price_info($featured->item_flash,$featured->regular_price);
        $count_rating = Helper::count_rating($featured->ratings);
        @endphp
        <div class="col-lg-3 col-md-4 col-sm-6 px-2 mb-grid-gutter">
          <!-- Product-->
          <div class="card product-card-alt">
            @if($custom_settings->author_key == Helper::Key_Owner())
            @if($featured->item_flash == 1)
            <div class="ribbon ribbon-top-left"><span>{{ $addition_settings->flash_sale_value }}% {{ __('OFF') }}</span></div>
            @endif
            @endif
            <div class="product-thumb">
              @if(Auth::guest()) 
              <a class="btn-wishlist btn-sm" href="{{ url('/') }}/login"><i class="dwg-heart"></i></a>
              @endif
              @if (Auth::check())
              @if($featured->user_id != Auth::user()->id)
              <a class="btn-wishlist btn-sm" href="{{ url('/item') }}/{{ base64_encode($featured->item_id) }}/favorite/{{ base64_encode($featured->item_liked) }}"><i class="dwg-heart"></i></a>
              @endif
              @endif
              <div class="product-card-actions"><a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-eye"></i></a>
              @php
              $checkif_purchased = Helper::if_purchased($featured->item_token);
              @endphp
              
              @if($addition_settings->guest_checkout == 1)
              <!--- Guest Checkout ON  -->
              @if($checkif_purchased == 0)
              @if($featured->free_download == 0)
              @if (Auth::check())
              @if(Auth::user()->id != 1 && $featured->user_id != Auth::user()->id)
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/add-to-cart') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/add-to-cart') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
              @else
              @if(Auth::guest())
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/login') }}"><i class="dwg-download"></i></a>
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/download/{{ base64_encode($featured->item_token) }}"><i class="dwg-download"></i></a>
              @endif
              @endif 
              @endif
              <!--- Guest Checkout ON  -->
              @else
              <!--- Guest Checkout OFF -->
              @if($checkif_purchased == 0)
              @if($featured->free_download == 0)
              @if (Auth::check())
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
              @else
              @if(Auth::guest())
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/login') }}"><i class="dwg-download"></i></a>
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/download/{{ base64_encode($featured->item_token) }}"><i class="dwg-download"></i></a>
              @endif
              @endif
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
               <!--- Guest Checkout OFF -->
              @endif  
              
              </div><a class="product-thumb-overlay" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"></a>
                            @if($featured->item_preview!='')
                            <img class="lazy" src="{{ Helper::Image_Path($featured->item_preview,'no-image.png') }}" alt="{{ $featured->item_name }}" width="300" height="200">
                            @else
                            <img class="lazy" src="{{ url('/') }}/public/img/no-image.png" alt="{{ $featured->item_name }}" width="300" height="200">
                            @endif
                          </div>
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-start pb-2">
                <div class="text-muted font-size-xs mr-1"><a class="product-meta font-weight-medium" href="{{ URL::to('/shop') }}/item-type/{{ $featured->item_type }}">{{ Helper::ItemTypeIdGetData($featured->item_type_id) }}</a></div>
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
              <h3 class="product-title font-size-sm mb-2"><a href="{{ URL::to('/item') }}/{{ $featured->item_slug }}">@if($addition_settings->item_name_limit != 0){{ mb_substr($featured->item_name,0,$addition_settings->item_name_limit,'utf-8').'...' }}
		      @else {{ $featured->item_name }} @endif</a></h3>
              <div class="card-footer d-flex align-items-center font-size-xs">
              <a class="blog-entry-meta-link" href="{{ URL::to('/user') }}/{{ $featured->username }}">
                    <div class="blog-entry-author-ava">
                    @if($featured->user_photo!='')
                    <img class="lazy" src="{{ url('/') }}/public/storage/users/{{ $featured->user_photo }}" alt="{{ $featured->username }}" width="26" height="26">
                    @else
                    <img class="lazy" src="{{ url('/') }}/public/img/no-user.png" alt="{{ $featured->username }}" width="26" height="26">
                    @endif
                    </div>
					@if($addition_settings->author_name_limit != 0){{ mb_substr($featured->username,0,$addition_settings->author_name_limit,'utf-8') }} @else {{ $featured->username }} @endif 
					@if($addition_settings->subscription_mode == 1) @if($featured->user_document_verified == 1) <span class="badges-success"><i class="dwg-check-circle danger"></i> {{ __('verified') }}</span>@endif @endif</a>
                  <div class="ml-auto text-nowrap"><i class="dwg-time"></i> {{ date('d M Y',strtotime($featured->updated_item)) }}</div>
                </div>
              <div class="d-flex flex-wrap justify-content-between align-items-center">
                @if($featured->file_type == 'serial') 
                @php
                if($featured->item_delimiter == 'comma')
                {
                $result_count = substr_count($featured->item_serials_list, ",");
                }
                else
                {
                $result_count = substr_count($featured->item_serials_list, "\n");
                }
                @endphp
                <div class="font-size-sm mr-2"><i class="dwg-cart text-muted mr-1"></i>{{ $result_count }}<span class="font-size-xs ml-1">{{ __('Stock') }}</span>
                </div>
                @else
                <div class="font-size-sm mr-2">
                @if($addition_settings->item_sale_count == 1)
                <i class="dwg-download text-muted mr-1"></i>{{ $featured->item_sold }}<span class="font-size-xs ml-1">{{ __('Sales') }}</span>
                @endif
                </div>
                @endif
                <div>
                @if($featured->free_download == 0)
                @if($featured->item_flash == 1)<del class="price-old">{{ Helper::price_format($allsettings->site_currency_position,$featured->regular_price,$currency_symbol,$multicurrency) }}</del>@endif <span class="bg-faded-accent text-accent rounded-sm py-1 px-2">{{ Helper::price_format($allsettings->site_currency_position,$price,$currency_symbol,$multicurrency) }}</span>
                @else
                <span class="price-badge rounded-sm py-1 px-2">{{ __('Free') }}</span> 
                @endif
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Product-->
        @php $no++; @endphp
	    @endforeach
       </div>
    </section>
    @endif
    @if(count($popular['items']) != 0)
<section class="container mb-lg-1 flash-sale" data-aos="fade-up" data-aos-delay="200">
      <!-- Heading-->
      <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2" data-aos="fade-down" data-aos-delay="100">{{ __('Popular Items') }}</h2>
        <div class="pt-3" data-aos="fade-down" data-aos-delay="100">
          <a class="btn btn-outline-accent" href="{{ URL::to('/') }}/popular-items">{{ __('Browse All Items') }}<i class="dwg-arrow-right font-size-ms ml-1"></i></a>
        </div>
      </div>
      <!-- Grid-->
      <div class="row pt-2 mx-n2">
        <!-- Product-->
        @php $no = 1; @endphp
        @foreach($popular['items'] as $featured)
        @php
        $price = Helper::price_info($featured->item_flash,$featured->regular_price);
        $count_rating = Helper::count_rating($featured->ratings);
        @endphp
        <div class="col-lg-3 col-md-4 col-sm-6 px-2 mb-grid-gutter">
          <!-- Product-->
          <div class="card product-card-alt">
            @if($custom_settings->author_key == Helper::Key_Owner())
            @if($featured->item_flash == 1)
            <div class="ribbon ribbon-top-left"><span>{{ $addition_settings->flash_sale_value }}% {{ __('OFF') }}</span></div>
            @endif
            @endif
            <div class="product-thumb">
              @if(Auth::guest()) 
              <a class="btn-wishlist btn-sm" href="{{ url('/') }}/login"><i class="dwg-heart"></i></a>
              @endif
              @if (Auth::check())
              @if($featured->user_id != Auth::user()->id)
              <a class="btn-wishlist btn-sm" href="{{ url('/item') }}/{{ base64_encode($featured->item_id) }}/favorite/{{ base64_encode($featured->item_liked) }}"><i class="dwg-heart"></i></a>
              @endif
              @endif
              <div class="product-card-actions"><a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-eye"></i></a>
              @php
              $checkif_purchased = Helper::if_purchased($featured->item_token);
              @endphp
              
              @if($addition_settings->guest_checkout == 1)
              <!--- Guest Checkout ON  -->
              @if($checkif_purchased == 0)
              @if($featured->free_download == 0)
              @if (Auth::check())
              @if(Auth::user()->id != 1 && $featured->user_id != Auth::user()->id)
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/add-to-cart') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/add-to-cart') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
              @else
              @if(Auth::guest())
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/login') }}"><i class="dwg-download"></i></a>
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/download/{{ base64_encode($featured->item_token) }}"><i class="dwg-download"></i></a>
              @endif
              @endif 
              @endif
              <!--- Guest Checkout ON  -->
              @else
              <!--- Guest Checkout OFF -->
              @if($checkif_purchased == 0)
              @if($featured->free_download == 0)
              @if (Auth::check())
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
              @else
              @if(Auth::guest())
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/login') }}"><i class="dwg-download"></i></a>
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/download/{{ base64_encode($featured->item_token) }}"><i class="dwg-download"></i></a>
              @endif
              @endif
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
               <!--- Guest Checkout OFF -->
              @endif
                 
              </div><a class="product-thumb-overlay" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"></a>
                            @if($featured->item_preview!='')
                            <img class="lazy" src="{{ Helper::Image_Path($featured->item_preview,'no-image.png') }}" alt="{{ $featured->item_name }}" width="300" height="200">
                            @else
                            <img class="lazy" src="{{ url('/') }}/public/img/no-image.png" alt="{{ $featured->item_name }}" width="300" height="200">
                            @endif
                          </div>
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-start pb-2">
                <div class="text-muted font-size-xs mr-1"><a class="product-meta font-weight-medium" href="{{ URL::to('/shop') }}/item-type/{{ $featured->item_type }}">{{ Helper::ItemTypeIdGetData($featured->item_type_id) }}</a></div>
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
              <h3 class="product-title font-size-sm mb-2"><a href="{{ URL::to('/item') }}/{{ $featured->item_slug }}">
			  @if($addition_settings->item_name_limit != 0)
			  {{ mb_substr($featured->item_name,0,$addition_settings->item_name_limit,'utf-8').'...' }}
		      @else
			  {{ $featured->item_name }}	  
			  @endif
			  </a></h3>
              <div class="card-footer d-flex align-items-center font-size-xs">
              <a class="blog-entry-meta-link" href="{{ URL::to('/user') }}/{{ $featured->username }}">
                    <div class="blog-entry-author-ava">
                    @if($featured->user_photo!='')
                    <img class="lazy" src="{{ url('/') }}/public/storage/users/{{ $featured->user_photo }}"  alt="{{ $featured->username }}" width="26" height="26">
                    @else
                    <img class="lazy" src="{{ url('/') }}/public/img/no-user.png" alt="{{ $featured->username }}" width="26" height="26">
                    @endif
                    </div>
					@if($addition_settings->author_name_limit != 0){{ mb_substr($featured->username,0,$addition_settings->author_name_limit,'utf-8') }} @else {{ $featured->username }} @endif
					@if($addition_settings->subscription_mode == 1) @if($featured->user_document_verified == 1) <span class="badges-success"><i class="dwg-check-circle danger"></i> {{ __('verified') }}</span>@endif @endif</a>
                  <div class="ml-auto text-nowrap"><i class="dwg-time"></i> {{ date('d M Y',strtotime($featured->updated_item)) }}</div>
                </div>
              <div class="d-flex flex-wrap justify-content-between align-items-center">
                @if($featured->file_type == 'serial') 
                @php
                if($featured->item_delimiter == 'comma')
                {
                $result_count = substr_count($featured->item_serials_list, ",");
                }
                else
                {
                $result_count = substr_count($featured->item_serials_list, "\n");
                }
                @endphp
                <div class="font-size-sm mr-2"><i class="dwg-cart text-muted mr-1"></i>{{ $result_count }}<span class="font-size-xs ml-1">{{ __('Stock') }}</span>
                </div>
                @else
                <div class="font-size-sm mr-2">
                @if($addition_settings->item_sale_count == 1)
                <i class="dwg-download text-muted mr-1"></i>{{ $featured->item_sold }}<span class="font-size-xs ml-1">{{ __('Sales') }}</span>
                @endif
                </div>
                @endif
                <div>
                @if($featured->free_download == 0)
                @if($featured->item_flash == 1)<del class="price-old">{{ Helper::price_format($allsettings->site_currency_position,$featured->regular_price,$currency_symbol,$multicurrency) }}</del>@endif <span class="bg-faded-accent text-accent rounded-sm py-1 px-2">{{ Helper::price_format($allsettings->site_currency_position,$price,$currency_symbol,$multicurrency) }}</span>
                @else
                <span class="price-badge rounded-sm py-1 px-2">{{ __('Free') }}</span> 
                @endif
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Product-->
        @php $no++; @endphp
	    @endforeach
       </div>
    </section>
    @endif
    @if(count($flash['items']) != 0)
<section class="container mb-lg-1 flash-sale" data-aos="fade-up" data-aos-delay="200">
      <!-- Heading-->
      <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2" data-aos="fade-down" data-aos-delay="100">{{ __('Flash Sale') }}</h2>
        <div class="pt-3" data-aos="fade-down" data-aos-delay="100">
          <a class="btn btn-outline-accent" href="{{ URL::to('/') }}/flash-sale">{{ __('Browse All Items') }}<i class="dwg-arrow-right font-size-ms ml-1"></i></a>
        </div>
      </div>
      <!-- Grid-->
      <div class="row pt-2 mx-n2">
        <!-- Product-->
        @php $no = 1; @endphp
        @foreach($flash['items'] as $featured)
        @php
        $price = Helper::price_info($featured->item_flash,$featured->regular_price);
        $count_rating = Helper::count_rating($featured->ratings);
        @endphp
        <div class="col-lg-3 col-md-4 col-sm-6 px-2 mb-grid-gutter">
          <!-- Product-->
          <div class="card product-card-alt">
            @if($custom_settings->author_key == Helper::Key_Owner())
            @if($featured->item_flash == 1)
            <div class="ribbon ribbon-top-left"><span>{{ $addition_settings->flash_sale_value }}% {{ __('OFF') }}</span></div>
            @endif
            @endif
            <div class="product-thumb">
              @if(Auth::guest()) 
              <a class="btn-wishlist btn-sm" href="{{ url('/') }}/login"><i class="dwg-heart"></i></a>
              @endif
              @if (Auth::check())
              @if($featured->user_id != Auth::user()->id)
              <a class="btn-wishlist btn-sm" href="{{ url('/item') }}/{{ base64_encode($featured->item_id) }}/favorite/{{ base64_encode($featured->item_liked) }}"><i class="dwg-heart"></i></a>
              @endif
              @endif
              <div class="product-card-actions"><a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-eye"></i></a>
              @php
              $checkif_purchased = Helper::if_purchased($featured->item_token);
              @endphp
              
              @if($addition_settings->guest_checkout == 1)
              <!--- Guest Checkout ON  -->
              @if($checkif_purchased == 0)
              @if($featured->free_download == 0)
              @if (Auth::check())
              @if(Auth::user()->id != 1 && $featured->user_id != Auth::user()->id)
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/add-to-cart') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/add-to-cart') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
              @else
              @if(Auth::guest())
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/login') }}"><i class="dwg-download"></i></a>
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/download/{{ base64_encode($featured->item_token) }}"><i class="dwg-download"></i></a>
              @endif
              @endif 
              @endif
              <!--- Guest Checkout ON  -->
              @else
              <!--- Guest Checkout OFF -->
              @if($checkif_purchased == 0)
              @if($featured->free_download == 0)
              @if (Auth::check())
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
              @else
              @if(Auth::guest())
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/login') }}"><i class="dwg-download"></i></a>
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/download/{{ base64_encode($featured->item_token) }}"><i class="dwg-download"></i></a>
              @endif
              @endif
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
               <!--- Guest Checkout OFF -->
              @endif
                
              </div><a class="product-thumb-overlay" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"></a>
                            @if($featured->item_preview!='')
                            <img class="lazy" src="{{ Helper::Image_Path($featured->item_preview,'no-image.png') }}" alt="{{ $featured->item_name }}" width="300" height="200">
                            @else
                            <img class="lazy" src="{{ url('/') }}/public/img/no-image.png" alt="{{ $featured->item_name }}" width="300" height="200">
                            @endif
                          </div>
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-start pb-2">
                <div class="text-muted font-size-xs mr-1"><a class="product-meta font-weight-medium" href="{{ URL::to('/shop') }}/item-type/{{ $featured->item_type }}">{{ Helper::ItemTypeIdGetData($featured->item_type_id) }}</a></div>
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
              <h3 class="product-title font-size-sm mb-2"><a href="{{ URL::to('/item') }}/{{ $featured->item_slug }}">
			  @if($addition_settings->item_name_limit != 0)
			  {{ mb_substr($featured->item_name,0,$addition_settings->item_name_limit,'utf-8').'...' }}
		      @else
			  {{ $featured->item_name }}	  
			  @endif
			  </a></h3>
              <div class="card-footer d-flex align-items-center font-size-xs">
              <a class="blog-entry-meta-link" href="{{ URL::to('/user') }}/{{ $featured->username }}">
                    <div class="blog-entry-author-ava">
                    @if($featured->user_photo!='')
                    <img class="lazy" src="{{ url('/') }}/public/storage/users/{{ $featured->user_photo }}" alt="{{ $featured->username }}" width="26" height="26">
                    @else
                    <img class="lazy" src="{{ url('/') }}/public/img/no-user.png" alt="{{ $featured->username }}" width="26" height="26">
                    @endif
                    </div>
					@if($addition_settings->author_name_limit != 0)
					{{ mb_substr($featured->username,0,$addition_settings->author_name_limit,'utf-8') }}
				    @else
				    {{ $featured->username }}	  
				    @endif
					@if($addition_settings->subscription_mode == 1) @if($featured->user_document_verified == 1) <span class="badges-success"><i class="dwg-check-circle danger"></i> {{ __('verified') }}</span>@endif @endif</a>
                  <div class="ml-auto text-nowrap"><i class="dwg-time"></i> {{ date('d M Y',strtotime($featured->updated_item)) }}</div>
                </div>
              <div class="d-flex flex-wrap justify-content-between align-items-center">
                @if($featured->file_type == 'serial') 
                @php
                if($featured->item_delimiter == 'comma')
                {
                $result_count = substr_count($featured->item_serials_list, ",");
                }
                else
                {
                $result_count = substr_count($featured->item_serials_list, "\n");
                }
                @endphp
                <div class="font-size-sm mr-2"><i class="dwg-cart text-muted mr-1"></i>{{ $result_count }}<span class="font-size-xs ml-1">{{ __('Stock') }}</span>
                </div>
                @else
                <div class="font-size-sm mr-2">
                @if($addition_settings->item_sale_count == 1)
                <i class="dwg-download text-muted mr-1"></i>{{ $featured->item_sold }}<span class="font-size-xs ml-1">{{ __('Sales') }}</span>
                @endif
                </div>
                @endif
                <div>@if($featured->item_flash == 1)<del class="price-old">{{ Helper::price_format($allsettings->site_currency_position,$featured->regular_price,$currency_symbol,$multicurrency) }}</del>@endif <span class="price-badge rounded-sm py-1 px-2">{{ Helper::price_format($allsettings->site_currency_position,$price,$currency_symbol,$multicurrency) }}</span></div>
              </div>
            </div>
          </div>
        </div>
        <!-- Product-->
        @php $no++; @endphp
	    @endforeach
       </div>
    </section>
    @endif
    @if(count($free['items']) != 0)
<section class="container mb-lg-1 flash-sale" data-aos="fade-up" data-aos-delay="200">
      <!-- Heading-->
      <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2" data-aos="fade-down" data-aos-delay="100">{{ __('Free Items') }}</h2>
        <div class="pt-3" data-aos="fade-down" data-aos-delay="100">
          <a class="btn btn-outline-accent" href="{{ URL::to('/') }}/free-items">{{ __('Browse All Items') }}<i class="dwg-arrow-right font-size-ms ml-1"></i></a>
        </div>
      </div>
      <!-- Grid-->
      <div class="row pt-2 mx-n2">
        <!-- Product-->
        @php $no = 1; @endphp
        @foreach($free['items'] as $featured)
        @php
        $price = Helper::price_info($featured->item_flash,$featured->regular_price);
        $count_rating = Helper::count_rating($featured->ratings);
        @endphp
        <div class="col-lg-3 col-md-4 col-sm-6 px-2 mb-grid-gutter">
          <!-- Product-->
          <div class="card product-card-alt">
            @if($custom_settings->author_key == Helper::Key_Owner())
            @if($featured->item_flash == 1)
            <div class="ribbon ribbon-top-left"><span>{{ $addition_settings->flash_sale_value }}% {{ __('OFF') }}</span></div>
            @endif
            @endif
            <div class="product-thumb">
              @if(Auth::guest()) 
              <a class="btn-wishlist btn-sm" href="{{ url('/') }}/login"><i class="dwg-heart"></i></a>
              @endif
              @if (Auth::check())
              @if($featured->user_id != Auth::user()->id)
              <a class="btn-wishlist btn-sm" href="{{ url('/item') }}/{{ base64_encode($featured->item_id) }}/favorite/{{ base64_encode($featured->item_liked) }}"><i class="dwg-heart"></i></a>
              @endif
              @endif
              <div class="product-card-actions"><a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-eye"></i></a>
              @php
              $checkif_purchased = Helper::if_purchased($featured->item_token);
              @endphp
              
              @if($addition_settings->guest_checkout == 1)
              <!--- Guest Checkout ON  -->
              @if($checkif_purchased == 0)
              @if($featured->free_download == 0)
              @if (Auth::check())
              @if(Auth::user()->id != 1 && $featured->user_id != Auth::user()->id)
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/add-to-cart') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/add-to-cart') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
              @else
              @if(Auth::guest())
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/login') }}"><i class="dwg-download"></i></a>
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/download/{{ base64_encode($featured->item_token) }}"><i class="dwg-download"></i></a>
              @endif
              @endif 
              @endif
              <!--- Guest Checkout ON  -->
              @else
              <!--- Guest Checkout OFF -->
              @if($checkif_purchased == 0)
              @if($featured->free_download == 0)
              @if (Auth::check())
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
              @else
              @if(Auth::guest())
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/login') }}"><i class="dwg-download"></i></a>
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/download/{{ base64_encode($featured->item_token) }}"><i class="dwg-download"></i></a>
              @endif
              @endif
              @else
              <a class="btn btn-light btn-icon btn-shadow font-size-base mx-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"><i class="dwg-cart"></i></a>
              @endif
               <!--- Guest Checkout OFF -->
              @endif
              
                
              </div><a class="product-thumb-overlay" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}"></a>
                            @if($featured->item_preview!='')
                            <img class="lazy" src="{{ Helper::Image_Path($featured->item_preview,'no-image.png') }}" alt="{{ $featured->item_name }}" width="300" height="200">
                            @else
                            <img class="lazy" src="{{ url('/') }}/public/img/no-image.png" alt="{{ $featured->item_name }}" width="300" height="200">
                            @endif
                          </div>
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-start pb-2">
                <div class="text-muted font-size-xs mr-1"><a class="product-meta font-weight-medium" href="{{ URL::to('/shop') }}/item-type/{{ $featured->item_type }}">{{ Helper::ItemTypeIdGetData($featured->item_type_id) }}</a></div>
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
              <h3 class="product-title font-size-sm mb-2"><a href="{{ URL::to('/item') }}/{{ $featured->item_slug }}">
			  @if($addition_settings->item_name_limit != 0)
			  {{ mb_substr($featured->item_name,0,$addition_settings->item_name_limit,'utf-8').'...' }}
		      @else
			  {{ $featured->item_name }}	  
			  @endif
			  </a></h3>
              <div class="card-footer d-flex align-items-center font-size-xs">
              <a class="blog-entry-meta-link" href="{{ URL::to('/user') }}/{{ $featured->username }}">
                    <div class="blog-entry-author-ava">
                    @if($featured->user_photo!='')
                    <img class="lazy" src="{{ url('/') }}/public/storage/users/{{ $featured->user_photo }}" alt="{{ $featured->username }}" width="26" height="26">
                    @else
                    <img class="lazy" src="{{ url('/') }}/public/img/no-user.png" alt="{{ $featured->username }}" width="26" height="26">
                    @endif
                    </div>
					@if($addition_settings->author_name_limit != 0)
					{{ mb_substr($featured->username,0,$addition_settings->author_name_limit,'utf-8') }}
				    @else
				    {{ $featured->username }}	  
				    @endif 
					@if($addition_settings->subscription_mode == 1) @if($featured->user_document_verified == 1) <span class="badges-success"><i class="dwg-check-circle danger"></i> {{ __('verified') }}</span>@endif @endif</a>
                  <div class="ml-auto text-nowrap"><i class="dwg-time"></i> {{ date('d M Y',strtotime($featured->updated_item)) }}</div>
                </div>
              <div class="d-flex flex-wrap justify-content-between align-items-center">
                @if($featured->file_type == 'serial') 
                @php
                if($featured->item_delimiter == 'comma')
                {
                $result_count = substr_count($featured->item_serials_list, ",");
                }
                else
                {
                $result_count = substr_count($featured->item_serials_list, "\n");
                }
                @endphp
                <div class="font-size-sm mr-2"><i class="dwg-cart text-muted mr-1"></i>{{ $result_count }}<span class="font-size-xs ml-1">{{ __('Stock') }}</span>
                </div>
                @else
                <div class="font-size-sm mr-2">
                @if($addition_settings->item_sale_count == 1)
                <i class="dwg-download text-muted mr-1"></i>{{ $featured->item_sold }}<span class="font-size-xs ml-1">{{ __('Sales') }}</span>
                @endif
                </div>
                @endif
                <div>
                @if($featured->free_download == 0)
                @if($featured->item_flash == 1)<del class="price-old">{{ Helper::price_format($allsettings->site_currency_position,$featured->regular_price,$currency_symbol,$multicurrency) }}</del>@endif <span class="bg-faded-accent text-accent rounded-sm py-1 px-2">{{ Helper::price_format($allsettings->site_currency_position,$price,$currency_symbol,$multicurrency) }}</span>
                @else
                <span class="price-badge rounded-sm py-1 px-2">{{ __('Free') }}</span> 
                @endif
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Product-->
        @php $no++; @endphp
	    @endforeach
       </div>
    </section>
    @endif
    @if(count($newest['items']) != 0)
    <section class="container pb-4 pb-md-5" data-aos="fade-up" data-aos-delay="200">
      <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2" data-aos="fade-down" data-aos-delay="100">{{ __('New Releases') }}</h2>
        <div class="pt-3" data-aos="fade-down" data-aos-delay="100">
          <a class="btn btn-outline-accent" href="{{ URL::to('/new-releases') }}">{{ __('Browse All Items') }}<i class="dwg-arrow-right font-size-ms ml-1"></i></a>
        </div>
      </div>
      <div class="row">
        <!-- Bestsellers-->
            @php $no = 1; @endphp
            @foreach($newest['items'] as $featured)
            @php
            $price = Helper::price_info($featured->item_flash,$featured->regular_price);
        $count_rating = Helper::count_rating($featured->ratings);
            @endphp
          <div class="col-lg-4 col-md-6 mb-2 py-3">
           <div class="widget">    
            <div class="media align-items-center pb-2 border-bottom">
            <a class="d-block mr-2" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}">
            @if($featured->item_preview!='')
            <img class="lazy" src="{{ Helper::Image_Path($featured->item_preview,'no-image.png') }}"  alt="{{ $featured->item_name }}" width="64" height="48">
           @else
           <img class="lazy" src="{{ url('/') }}/public/img/no-image.png" alt="{{ $featured->item_name }}" width="64" height="48">
           @endif
            </a>
              <div class="media-body">
                <h6 class="widget-product-title"><a href="{{ URL::to('/item') }}/{{ $featured->item_slug }}">{{ $featured->item_name }}</a></h6>
                <div class="widget-product-meta">
                @if($featured->free_download == 0)
                <span class="text-accent">{{ Helper::price_format($allsettings->site_currency_position,$price,$currency_symbol,$multicurrency) }}</span> @if($featured->item_flash == 1)<del class="price-old">{{ Helper::price_format($allsettings->site_currency_position,$featured->regular_price,$currency_symbol,$multicurrency) }}</del>@endif
                @else
                <span class="text-accent">{{ __('Free') }}</span>
                @endif
                </div>
              </div>
            </div>
           </div>
        </div>
            @php $no++; @endphp
            @endforeach
       </div>
    </section>
    @endif
    @if($allsettings->site_features_display == 1)
    <section class="bg-size-cover bg-position-center pt-5 pb-4 pb-lg-5 feature-panel" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="container pt-lg-3" data-aos="fade-up" data-aos-delay="200">
        <h2 class="h3 mb-3 pb-4 text-light text-center">{{ __('Why Choose') }} {{ $allsettings->site_title }}?</h2>
        <div class="row pt-lg-2 text-center">
          <div class="col-lg-3 col-md-3 col-sm-12 mb-grid-gutter" data-aos="fade-right" data-aos-delay="200">
            <div class="d-inline-block">
              <div class="media media-ie-fix align-items-center text-left"><span class="{{ $allsettings->site_icon1 }}"></span>
                <div class="media-body pl-3">
                  <h6 class="text-light font-size-base mb-1">{{ $allsettings->site_text1 }}</h6>
                  <p class="text-light font-size-ms opacity-70 mb-0">{{ $allsettings->site_sub_text1 }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 mb-grid-gutter" data-aos="fade-right" data-aos-delay="200">
            <div class="d-inline-block">
              <div class="media media-ie-fix align-items-center text-left"><span class="{{ $allsettings->site_icon2 }}"></span>
                <div class="media-body pl-3">
                  <h6 class="text-light font-size-base mb-1">{{ $allsettings->site_text2 }}</h6>
                  <p class="text-light font-size-ms opacity-70 mb-0">{{ $allsettings->site_sub_text2 }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 mb-grid-gutter" data-aos="fade-right" data-aos-delay="200">
            <div class="d-inline-block">
              <div class="media media-ie-fix align-items-center text-left"><span class="{{ $allsettings->site_icon3 }}"></span>
                <div class="media-body pl-3">
                  <h6 class="text-light font-size-base mb-1">{{ $allsettings->site_text3 }}</h6>
                  <p class="text-light font-size-ms opacity-70 mb-0">{{ $allsettings->site_sub_text3 }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-12 mb-grid-gutter" data-aos="fade-right" data-aos-delay="200">
            <div class="d-inline-block">
              <div class="media media-ie-fix align-items-center text-left"><span class="{{ $allsettings->site_icon4 }}"></span>
                <div class="media-body pl-3">
                  <h6 class="text-light font-size-base mb-1">{{ $allsettings->site_text4 }}</h6>
                  <p class="text-light font-size-ms opacity-70 mb-0">{{ $allsettings->site_sub_text4 }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    @endif
    @if($allsettings->site_blog_display == 1)
    @if($allsettings->home_blog_display == 1)
    @if(count($blog['data']) != 0)
    <section class="container pb-4 pb-md-5 homeblog" data-aos="fade-up" data-aos-delay="200">
      <div class="d-flex flex-wrap justify-content-between align-items-center pt-1 border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-3 mr-2" data-aos="fade-down" data-aos-delay="100">{{ __('Our Blog') }}</h2>
        <div class="pt-3" data-aos="fade-down" data-aos-delay="100">
          <a class="btn btn-outline-accent" href="{{ URL::to('/blog') }}">{{ __('Ream more posts') }}<i class="dwg-arrow-right font-size-ms ml-1"></i></a>
        </div>
      </div>
        <div class="row">
          @php $no = 1; @endphp
          @foreach($blog['data'] as $post)
            <div class="col-lg-4 col-md-6 mb-2 py-3">
              <div class="card">
              <a class="blog-entry-thumb" href="{{ URL::to('/single') }}/{{ $post->post_slug }}" title="{{ $post->post_title }}">
              @if($post->post_image!='')
              <img class="lazy card-img-top" src="{{ url('/') }}/public/storage/post/{{ $post->post_image }}" alt="{{ $post->post_title }}" width="388" height="240">
              @else
              <img class="lazy card-img-top" src="{{ url('/') }}/public/img/no-image.png" width="388" height="240">
              @endif
              </a>
                <div class="card-body">
                  <h2 class="h6 blog-entry-title"><a href="{{ URL::to('/single') }}/{{ $post->post_slug }}">{{ $post->post_title }}</a></h2>
                  <p class="font-size-sm">
				  @if($addition_settings->post_short_desc_limit != 0)
				  {{ mb_substr($post->post_short_desc,0,$addition_settings->post_short_desc_limit,'utf-8').'...' }}
                  @else
				  {{ $post->post_short_desc }}	  
                  @endif					  
				  </p>
                  <div class="font-size-xs text-nowrap"><span class="blog-entry-meta-link text-nowrap">{{ date('d M Y', strtotime($post->post_date)) }}</span><span class="blog-entry-meta-divider mx-2"></span><span class="blog-entry-meta-link text-nowrap"><i class="dwg-message"></i>{{ $comments->has($post->post_id) ? count($comments[$post->post_id]) : 0 }}</span></div>
                </div>
              </div>
            </div>
            @php $no++; @endphp
            @endforeach
         </div>
        <!-- More button-->
     </section>
     @endif 
     @endif
     @endif
     @if(in_array('home',$bottom_ads))
    <section class="container pt-2" data-aos="fade-up" data-aos-delay="200">
      <div class="row">
          <div class="col-lg-12 mb-3" align="center">
             @php echo html_entity_decode($addition_settings->bottom_ads); @endphp
          </div>
       </div>   
    </section>   
    @endif 
    @if($custom_settings->promotion_popup == 1)
    <?php /** promoation offer ***/ ?>
    <div id="my-welcome-message">
        <div class="header">
           <h3>{{ $custom_settings->pr_promo_heading }}</h3>
        </div>
        <div class="bodies">
          <div class="container-fluid p-0">
           <div class="row no-gutters">
              <div class="col-md-12 textboxrow" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $custom_settings->promotion_bg_image }}'); background-size:cover;">
              <h1>{{ $custom_settings->pr_promo_title_one }}</h1>
              <h5>{{ $custom_settings->pr_promo_title_two }}</h5>
              <p class="saledate">{{ __('Sales end') }} {{ date('M d, Y', strtotime($custom_settings->pr_promo_date)) }}</p>
              <a class="btn btn-outline-accent" href="{{ $custom_settings->pr_promo_button_link }}">{{ __('Download Now') }}</a>
              </div>
           </div>
          </div>    
         </div> 
        </div> 
        @endif 
@include('footer')
@include('script')
</body>
</html>