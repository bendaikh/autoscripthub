<!DOCTYPE HTML>
<html lang="en">
<head>
@if($item_slug != '')
@php if($item['item']->item_flash == 1)
{ 
   $item_price = Helper::price_info($item['item']->item_flash,$item['item']->regular_price);
   $extend_item_price = Helper::price_info($item['item']->item_flash,$item['item']->extended_price);
} 
else 
{ 
   $item_price = $item['item']->regular_price;
   $extend_item_price = $item['item']->extended_price; 
} 
@endphp
@else
$item_price = 0;
$extend_item_price = 0;
@endif
<script type="application/ld+json">
{
	  "@context": "http://schema.org",
	  "@type": "WebSite",
	  "name": "{{ $allsettings->site_title }}",
	  "url": "{{ URL::to('/') }}"
}
</script>
<script type="application/ld+json">
	         {"@context":"http://schema.org","@type":"Product","category":"{{ $category_name }}","url":"{{ URL::to('/item') }}/{{ $item['item']->item_slug }}","description":"{{ $item['item']->item_seo_desc }}","name":"{{ $item['item']->item_seo_desc }}","image":"{{ Helper::Image_Path(($item['item']->item_preview ?? $item['item']->item_thumbnail),'no-image.png') }}","brand":{"@type":"Brand","name":"{{ $item['item']->item_name }}"},"sku":"{{ $item['item']->item_token }}","mpn":"I-{{ $item['item']->item_token }}","offers":{"@type":"Offer","Price":"{{ $item_price }}", "priceCurrency":"{{ $multicurrency }}", "priceValidUntil":"{{ date('Y-m-d H:i:s') }}", "availability":"http://schema.org/InStock", "itemCondition":"http://schema.org/NewCondition" ,"url":"{{ URL::to('/item') }}/{{ $item['item']->item_slug }}"}, "aggregateRating":{"@type":"AggregateRating","ratingValue":"{{ $getreview }}","reviewCount":"{{ $getreview }}"},  "review": [ { "@type": "Review", "reviewRating": { "@type": "Rating", "ratingValue": "{{ $getreview }}" , "bestRating":"{{ $getreview }}"}, "author": { "@type": "Person", "name": "{{ $item['item']->username }}" }}    ]}
</script>
@if($check_if_item != 0)
<title>{{ $allsettings->site_title }} - {{ $item['item']->item_name }}</title>
@if($item_slug != '')
@if($item['item']->item_allow_seo == 1)
@if($item['item']->free_download == 0)
<meta name="description" content="{{ $item['item']->item_seo_desc }}" />
<meta name="keywords" content="{{ $item['item']->item_seo_keyword }}" />
<meta name="robots" content="index,follow" />
<meta property="og:title" content="{{ $item['item']->item_name }}" />
<meta property="og:type" content="og:product" /> 
<meta property="og:description" content="{{ $item['item']->item_seo_desc }}" /> 
<meta property="og:url" content="{{ url()->current() }}" /> 
<meta property="og:image" content="{{ Helper::Image_Path(($item['item']->item_preview ?? $item['item']->item_thumbnail),'no-image.png') }}" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="{{ $allsettings->site_title }}" />
<meta name="twitter:title" content="{{ $item['item']->item_name }}" />
<meta name="twitter:description" content="{{ $item['item']->item_seo_desc }}" />
<meta name="twitter:image" content="{{ Helper::Image_Path(($item['item']->item_preview ?? $item['item']->item_thumbnail),'no-image.png') }}" />
<link rel="canonical" href="{{ url()->current() }}" />
<meta name="twitter:image:src" content="{{ Helper::Image_Path(($item['item']->item_preview ?? $item['item']->item_thumbnail),'no-image.png') }}">
<meta name="twitter:image:width" content= "280" />
<meta name="twitter:image:height" content= "480" />
@else
@include('meta')
@endif
@else
@include('meta')
@endif
@else
<title>{{ __('404 Not Found') }} - {{ $allsettings->site_title }}</title>
@endif
@endif
@include('style')
</head>
<body>
@include('header')
@if($check_if_item != 0)
<div class="page-title-overlap pt-4" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ $item['item']->item_name }}</li>
              </li>
             </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ $item['item']->item_name }}</h1>
        </div>
      </div>
    </div>
<section class="container mb-3 pb-3">
      <div class="bg-light box-shadow-lg rounded-lg overflow-hidden">
        <div class="row">
          <!-- Content-->
          <section class="col-lg-8 pt-2 pt-lg-4 pb-4 mb-lg-3">
            <div class="pt-2 px-4 pr-lg-0 pl-xl-5">
              @if(in_array('item-details',$top_ads))
          	  <div class="mt-2 mb-4" align="center">
              @php echo html_entity_decode($addition_settings->top_ads); @endphp
          	  </div>
         	  @endif
              <!-- Product gallery-->
              <div class="cz-gallery">
                      @if($item['item']->video_preview_type!='')
                      @if($item['item']->video_preview_type == 'youtube')
                      @if($item['item']->video_url != '')
                      @php
                      $url = $item['item']->video_url;
                      preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
                      $video_id = $match[1];
                      @endphp
                      <iframe width="100%" height="430" src="https://www.youtube.com/embed/{{ $video_id }}?rel=0&version=3&loop=1&playlist={{ $video_id }}" frameborder="3" class="frame-border" allow="autoplay" scrolling="no"></iframe> 
                      @else
                      <img class="lazy single-thumbnail" width="762" height="430" src="{{ url('/') }}/resources/views/assets/no-video.png"  alt="{{ $item['item']->item_name }}">
                      @endif
                      @endif
                      @if($item['item']->video_preview_type == 'mp4')
                      @if($item['item']->video_file != '')
					  <video width="100%" height="auto" src="{{ Helper::Image_Path($item['item']->video_file,'no-video.png') }}" controls>{{ __('Your browser does not support the video tag.') }}</video>	  
                      @else
                      <img class="lazy single-thumbnail" width="762" height="430" src="{{ url('/') }}/resources/views/assets/no-video.png"  alt="{{ $item['item']->item_name }}">
                      @endif
                      @endif
                      @if($item['item']->video_preview_type == 'mp3')
                      @if($item['item']->audio_file != '')
					  <audio controls="controls"><source src="{{ Helper::Image_Path($item['item']->audio_file,'no-audio.png') }}" type="audio/mpeg" /></audio>	  
                      @else
                      <img class="lazy single-thumbnail" width="762" height="430" src="{{ url('/') }}/resources/views/assets/no-audio.png"  alt="{{ $item['item']->item_name }}">
                      @endif
                      @endif
                      @else  
                      @if($item['item']->item_preview!='')
                      <a class="gallery-item rounded-lg mb-grid-gutter" href="{{ Helper::Image_Path($item['item']->item_preview,'no-image.png') }}" data-sub-html="{{ $item['item']->item_name }}">
                      <img class="lazy single-thumbnail" width="762" height="430" src="{{ Helper::Image_Path($item['item']->item_preview,'no-image.png') }}"  alt="{{ $item['item']->item_name }}">
                      <span class="gallery-item-caption">{{ $item['item']->item_name }}</span>
                      </a>
                      @else
                      <img class="lazy single-thumbnail" width="762" height="430" src="{{ url('/') }}/public/img/no-image.png"  alt="{{ $item['item']->item_name }}">
                      @endif
                      @endif
              @if($getcount != 0)
                <div class="row">
                  @foreach($item_allimage as $image)
                  <div class="col-sm-2"><a class="gallery-item rounded-lg mb-grid-gutter thumber" href="{{ Helper::Image_Path($image->item_image,'no-image.png') }}" data-sub-html="{{ $item['item']->item_name }}"><img class="lazy" width="762" height="430" src="{{ Helper::Image_Path($image->item_image,'no-image.png') }}"  alt="{{ $image->item_image }}"/><span class="gallery-item-caption">{{ $item['item']->item_name }}</span></a></div>
                  @endforeach 
                </div>
                @endif
              </div>
              <!-- Wishlist + Sharing-->
              <div class="d-flex flex-wrap justify-content-between align-items-center border-top pt-3">
                <div class="py-2 mr-2">
                  @if($item['item']->demo_url != '') 
                  <a class="btn btn-outline-accent btn-sm" href="{{ url('/preview') }}/{{ $item['item']->item_slug }}" target="_blank"><i class="dwg-eye font-size-sm mr-2"></i>{{ __('Live Preview') }}</a>
                  @endif
                  @if(Auth::guest())
                  <a class="btn btn-outline-accent btn-sm" href="{{ URL::to('/login') }}"><i class="dwg-heart font-size-lg mr-2"></i>{{ __('Add To Favorites') }}</a>
                  @endif
                  @if (Auth::check())
                  @if($item['item']->user_id != Auth::user()->id)
                  <a class="btn btn-outline-accent btn-sm" href="{{ url('/item') }}/{{ base64_encode($item['item']->item_id) }}/favorite/{{ base64_encode($item['item']->item_liked) }}"><i class="dwg-heart font-size-lg mr-2"></i>{{ __('Add To Favorites') }}</a>
                  @endif
                  @endif
                  </div>
                <div class="py-2"><i class="dwg-share-alt font-size-lg align-middle text-muted mr-2"></i>
                <a class="social-btn sb-outline sb-facebook sb-sm ml-2 share-button" data-share-url="{{ URL::to('/item') }}/{{ $item['item']->item_slug }}" data-share-network="facebook" data-share-text="{{ $item['item']->item_shortdesc }}" data-share-title="{{ $item['item']->item_name }}" data-share-via="{{ $allsettings->site_title }}" data-share-tags="" data-share-media="{{ Helper::Image_Path($item['item']->item_thumbnail,'no-image.png') }}" href="javascript:void(0)"><i class="dwg-facebook"></i></a>
                <a class="social-btn sb-outline sb-twitter sb-sm ml-2 share-button" data-share-url="{{ URL::to('/item') }}/{{ $item['item']->item_slug }}" data-share-network="twitter" data-share-text="{{ $item['item']->item_shortdesc }}" data-share-title="{{ $item['item']->item_name }}" data-share-via="{{ $allsettings->site_title }}" data-share-tags="" data-share-media="{{ Helper::Image_Path($item['item']->item_thumbnail,'no-image.png') }}" href="javascript:void(0)"><i class="dwg-twitter"></i></a>
                <a class="social-btn sb-outline sb-pinterest sb-sm ml-2 share-button" data-share-url="{{ URL::to('/item') }}/{{ $item['item']->item_slug }}" data-share-network="pinterest" data-share-text="{{ $item['item']->item_shortdesc }}" data-share-title="{{ $item['item']->item_name }}" data-share-via="{{ $allsettings->site_title }}" data-share-tags="" data-share-media="{{ Helper::Image_Path($item['item']->item_thumbnail,'no-image.png') }}" href="javascript:void(0)"><i class="dwg-pinterest"></i></a>
                <a class="social-btn sb-outline sb-linkedin sb-sm ml-2 share-button" data-share-url="{{ URL::to('/item') }}/{{ $item['item']->item_slug }}" data-share-network="linkedin" data-share-text="{{ $item['item']->item_shortdesc }}" data-share-title="{{ $item['item']->item_name }}" data-share-via="{{ $allsettings->site_title }}" data-share-tags="" data-share-media="{{ Helper::Image_Path($item['item']->item_thumbnail,'no-image.png') }}" href="javascript:void(0)"><i class="dwg-linkedin"></i></a>
                </div>
              </div>
              <div class="mt-4 mb-4 mb-lg-5">
      <!-- Nav tabs-->
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item"><a class="nav-link p-4 active" href="#details" data-toggle="tab" role="tab">{{ __('Item Details') }}</a></li>
        <li class="nav-item"><a class="nav-link p-4" href="#comments" data-toggle="tab" role="tab">{{ __('Comments') }} <span>({{ $comment_count }})</span></a></li>
        <li class="nav-item"><a class="nav-link p-4" href="#reviews" data-toggle="tab" role="tab">{{ __('Reviews') }}<span>({{ $getreview }})</span></a></li>
        @if(Auth::guest())
        <li class="nav-item"><a class="nav-link p-4" href="#suppport" data-toggle="tab" role="tab">{{ __('Support') }}</a></li>
        @endif
        @if (Auth::check())
        @if($item['item']->user_id != Auth::user()->id)
        <li class="nav-item"><a class="nav-link p-4" href="#suppport" data-toggle="tab" role="tab">{{ __('Support') }}</a></li>
        @endif
        @endif
        @if($addition_settings->show_refund_term == 1)
        <li class="nav-item"><a class="nav-link p-4" href="#terms" data-toggle="tab" role="tab">{{ __('Terms') }}</a></li>
        @endif
      </ul>
      <div class="tab-content pt-2">
        <div class="tab-pane fade" id="suppport" role="tabpanel">
           <div class="row">
            <div class="col-lg-12">
               <h4>{{ __('Contact the Author') }}</h4>
               @if(Auth::guest())
                    <p>{{ __('Please') }}
                    <a href="{{ URL::to('/login') }}" class="theme-color">{{ __('Sign In') }}</a> {{ __('to contact this author.') }}</p>
                    @endif
                    @if (Auth::check())
                    <form action="{{ route('support') }}" class="support_form media-body needs-validation" id="support_form" method="post" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="form-group">
                                                    <label for="subj">{{ __('Subject') }}</label>
                                                    <input type="text" id="support_subject" name="support_subject" class="form-control" placeholder="Enter your subject" data-bvalidator="required">                                            </div>
                                                <div class="form-group">
                                                    <label for="supmsg">{{ __('Message') }} </label>
                                                    <textarea class="form-control" id="support_msg" name="support_msg" rows="5" placeholder="Enter your message" data-bvalidator="required"></textarea>                                            </div>
                                                <input type="hidden" name="to_address" value="{{ $item['item']->email }}">
                                                <input type="hidden" name="to_id" value="{{ $item['item']->id }}">
                                                <input type="hidden" name="to_name" value="{{ $item['item']->username }}">
                                                <input type="hidden" name="from_address" value="{{ Auth::user()->email }}">
                                                <input type="hidden" name="from_name" value="{{ Auth::user()->username }}">
                                                <input type="hidden" name="item_url" value="{{ URL::to('/item') }}/{{ $item['item']->item_slug }}">
                              <button type="submit" class="btn btn-primary btn-sm">{{ __('Submit Now') }}</button>
                      </form>
                @endif
            </div>
           </div> 
        </div>
        <!-- Product details tab-->
        <div class="tab-pane fade" id="terms" role="tabpanel">
          <div class="row">
            <div class="col-lg-12 term_text">
              <p class="font-size-md mb-1">
              @if($addition_settings->show_moneyback == 1)
              @if($item['item']->seller_money_back == 1)
              @if(!empty($item['item']->seller_money_back_days))
              <h1>{{ $item['item']->seller_money_back_days }} {{ __('DAYS MONEY BACK GUARANTEE') }}</h1>
              @endif
              @else
              <h1>{{ __('THIS PRODUCT DO NOT OFFER MONEY BACK GUARANTEE') }}</h1>
              @endif
              <br/>
              @endif
              @if($addition_settings->show_refund_term == 1)
              @php echo $item['item']->seller_refund_term; @endphp
              @endif
              </p>
            </div>
          </div>
        </div>
        <div class="tab-pane fade show active" id="details" role="tabpanel">
          <div class="row">
            <div class="col-lg-12">
              <p class="font-size-md mb-1">@php echo html_entity_decode($item['item']->item_desc); @endphp</p>
            </div>
          </div>
        </div>
        <!-- Reviews tab-->
        <div class="tab-pane fade" id="reviews" role="tabpanel">
         @if($getreview != 0)
         <div class="row pb-4">
            <!-- Reviews list-->
            <div class="col-md-12">
              <!-- Review-->
              @foreach($getreviewdata['view'] as $rating)
              <div class="product-review pb-4 mb-4 border-bottom review-item">
                <div class="d-flex mb-3">
                  <div class="media media-ie-fix align-items-center mr-4 pr-2">
                  @if($rating->user_photo!='')
                  <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/storage/users/{{ $rating->user_photo }}"  alt="{{ $rating->username }}"/>
                  @else
                  <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ $rating->username }}"/>
                  @endif
                    <div class="media-body pl-3">
                      <h6 class="font-size-sm mb-0">{{ $rating->username }}</h6><span class="font-size-ms text-muted">{{ date('d F Y H:i:s', strtotime($rating->rating_date)) }}</span></div>
                  </div>
                  <div>
                    <div class="star-rating">
                    @if($rating->rating == 0)
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    @endif
                    @if($rating->rating == 1)
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    @endif
                    @if($rating->rating == 2)
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    @endif
                    @if($rating->rating == 3)
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    <i class="sr-star dwg-star"></i>
                    @endif
                    @if($rating->rating == 4)
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star"></i>
                    @endif
                    @if($rating->rating == 5)
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    <i class="sr-star dwg-star-filled active"></i>
                    @endif
                    </div>
                    <div class="review_tag">{{ $rating->rating_reason }}</div>
                  </div>
                </div>
                <p class="font-size-md mb-2">{{ $rating->rating_comment }}</p>
              </div>
              @endforeach
              <div class="float-right">
                 <div class="pagination-area">
                    <div class="turn-page" id="reviewpager"></div>
                    </div> 
              </div>
            </div>
            <!-- Leave review form-->
         </div>
         @endif
        </div>
        <!-- Comments tab-->
        <div class="tab-pane fade" id="comments" role="tabpanel">
          <div class="row thread">
            <div class="col-lg-12">
              <div class="media-list thread-list" id="listShow">
                                    @foreach ($comment['view'] as $parent)
                                        <div class="single-thread commli-item">
                                            <div class="media">
                                                <div class="media-left">
                                                    @if($parent->user_photo!='')
                                                    <img  class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/storage/users/{{ $parent->user_photo }}"  alt="{{ $parent->username }}">                                                    @else
                                                    <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ $parent->username }}">
                                                    @endif
                                                </div>
                                                <div class="media-body">
                                                    <div>
                                                        <div class="media-heading">
                                                            <h6 class="font-size-md mb-0">{{ $parent->username }}</h6>
                                                        </div>
                                                        @if($parent->id == $item['item']->user_id)
                                                        <span class="comment-tag buyer">{{ __('Author') }}</span>
                                                        @endif
                                                        @if (Auth::check())
                                                        @if($item['item']->user_id == Auth::user()->id)
                                                        <a href="javascript:void(0);" class="nav-link-style font-size-sm font-weight-medium reply-link"><i class="dwg-reply mr-2">
                                                        </i>{{ __('Reply') }}</a>
                                                        @endif
                                                        @endif
                                                    </div>
                                                    <p class="font-size-md mb-1">{{ $parent->comm_text }}</p>
                                                    <span class="font-size-ms text-muted"><i class="dwg-time align-middle mr-2"></i>{{ date('d F Y, H:i:s', strtotime($parent->comm_date)) }}</span>
                                                </div>
                                            </div>
                                            <div class="children">
                                            @foreach ($parent->replycomment as $child)
                                                <div class="single-thread depth-2">
                                                    <div class="media">
                                                        <div class="media-left">
                                                    @if($child->user_photo!='')
                                                    <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/storage/users/{{ $child->user_photo }}"  alt="{{ $child->username }}">                                                    @else
                                                    <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ $child->username }}">
                                                    @endif
                                                    </div>
                                                        <div class="media-body">
                                                            <div class="media-heading">
                                                                <h6 class="font-size-md mb-0">{{ $child->username }}</h6>
                                                             </div>
                                                            @if($child->id == $item['item']->user_id)
                                                            <span class="comment-tag buyer">{{ __('Author') }}</span>
                                                            @endif
                                                            <p class="font-size-md mb-1">{{ $child->comm_text }}</p>
                                                            <span class="font-size-ms text-muted"><i class="dwg-time align-middle mr-2"></i>{{ date('d F Y, H:i:s', strtotime($child->comm_date)) }}</span>                                                        </div>
                                                    </div>
                                                  </div>
                                                @endforeach
                                            </div>
                                            <!-- comment reply -->
                                            @if (Auth::check())
                                            <div class="media depth-2 reply-comment">
                                                <div class="media-left">
                                                    @if(Auth::user()->user_photo!='')
                                           <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/storage/users/{{ Auth::user()->user_photo }}"  alt="{{ Auth::user()->username }}">                                                @else
                                           <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ Auth::user()->username }}">
                                           @endif
                                            </div>
                                                <div class="media-body">
                                                    <form action="{{ route('reply-post-comment') }}" class="comment-reply-form media-body needs-validation" method="post" enctype="multipart/form-data">{{ csrf_field() }}
                                                    <textarea name="comm_text" class="form-control" placeholder="{{ __('Write your comment...') }}" required></textarea>
                                                    <input type="hidden" name="comm_user_id" value="{{ Auth::user()->id }}">
                                                    <input type="hidden" name="comm_item_user_id" value="{{ $item['item']->user_id }}">
                                                    <input type="hidden" name="comm_item_id" value="{{ $item['item']->item_id }}">
                                                    <input type="hidden" name="comm_id" value="{{ $parent->comm_id }}">
                                                    <input type="hidden" name="comm_item_url" value="{{ URL::to('/item') }}/{{ $item['item']->item_slug }}">
                                                   <button class="btn btn-primary btn-sm">{{ __('Post Comment') }}</button>
                                                </form>
                                                </div>
                                            </div>
                                            @endif
                                            <!-- comment reply -->
                                        </div>
                                       @endforeach
                                    </div>
                                   @if($comment_count != 0)
                                   <div class="float-right">
                                        <div class="pagination-area">
                                                <div class="turn-page" id="commpager"></div>
                                        </div> 
                                   </div>
                                   @endif
                  <div class="clearfix"></div>
                  @if (Auth::check())
                  @if($item['item']->user_id != Auth::user()->id)
                   <div class="card border-0 box-shadow my-2">
                   <h4 class="mt-4 ml-4">{{ __('Leave a comment') }}</h4>
                    <div class="card-body">
                      <div class="media">
                      @if(Auth::user()->user_photo != '')
                      <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/storage/users/{{ Auth::user()->user_photo }}"  alt="{{ Auth::user()->name }}"/>
                      @else
                      <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ Auth::user()->name }}"/>
                      @endif
                      <form action="{{ route('post-comment') }}" class="comment-reply-form media-body needs-validation ml-3" id="item_form" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                          <div class="form-group">
                            <textarea class="form-control" rows="4" name="comm_text" placeholder="{{ __('Write your comment...') }}" data-bvalidator="required"></textarea>
                            <input type="hidden" name="comm_user_id" value="{{ Auth::user()->id }}">
                            <input type="hidden" name="comm_item_user_id" value="{{ $item['item']->user_id }}">
                            <input type="hidden" name="comm_item_id" value="{{ $item['item']->item_id }}">
                            <input type="hidden" name="comm_item_url" value="{{ URL::to('/item') }}/{{ $item['item']->item_slug }}">
                            <div class="invalid-feedback">{{ __('Please write your comment') }}</div>
                          </div>
                          <button class="btn btn-primary btn-sm" type="submit">{{ __('Post Comment') }}</button>
                        </form>
                  </div>
                </div>
              </div>
              @endif
              @endif
            </div>
          </div>
        </div>
         </div>
        </div>
            @if(in_array('item-details',$bottom_ads))
             <div class="mt-3 mb-2" align="center">
             @php echo html_entity_decode($addition_settings->bottom_ads); @endphp
             </div>
             @endif
            </div>
          </section>
          
          <!-- Sidebar-->
          <aside class="col-lg-4">
            <hr class="d-lg-none">
            <form action="{{ route('cart') }}" class="setting_form" method="post" id="contact_form" enctype="multipart/form-data">
            {{ csrf_field() }} 
            <div class="cz-sidebar-static h-100 ml-auto border-left">
               @if($item['item']->free_download == 1)
               <div class="bg-secondary rounded p-3 mb-4">
               <p>{{ __('This item is one of the') }} <strong>{{ __('Free Files') }}</strong>. {{ __('You are able to download this item for free for a limited time. Updates and support are only available if you purchase this item.') }}</p>
               @php if($item['item']->download_count == "") { $dcount = 0; } else { $dcount = $item['item']->download_count; } @endphp
               <div align="center">
                   @if (Auth::check())
                   <?php /*?>@if($addition_settings->subscription_mode == 0)<?php */?>
                   <a href="{{ URL::to('/item') }}/download/{{ base64_encode($item['item']->item_token) }}" class="btn btn-primary btn-sm"> <i class="fa fa-download"></i> {{ __('Download this file for free') }} ({{ $dcount }})</a>
                   <?php /*?>@else
                   @if(Auth::user()->user_type == 'vendor')
                   @if(Auth::user()->user_subscr_date >= date('Y-m-d'))
                   <a href="{{ URL::to('/item') }}/download/{{ base64_encode($item['item']->item_token) }}" class="btn btn-primary btn-sm"> <i class="fa fa-download"></i> {{ __('Download this file for free') }} ({{ $dcount }})</a>
                   @else
                   <a href="javascript:void(0)" class="btn btn-primary btn-sm" onClick="alert('Your subscription has been expired. Please renewal your subscription')"> <i class="fa fa-download"></i> {{ __('Download this file for free') }} ({{ $dcount }})</a>
                   @endif
                   @else<?php */?>
                   <?php /*?><a href="javascript:void(0)" class="btn btn-primary btn-sm" onClick="alert('Subscription user only')"> <i class="fa fa-download"></i> {{ __('Download this file for free') }} ({{ $dcount }})</a>
                   @endif
                   @endif<?php */?>
                   @endif
                   @if(Auth::guest())
                   <a href="{{ URL::to('/login') }}" class="btn btn-primary btn-sm"> <i class="fa fa-download"></i> {{ __('Download this file for free') }} ({{ $dcount }})</a>
                   @endif
                </div>
               </div>
               @else
               @if($addition_settings->subscription_mode == 1)
               @if($item['item']->subscription_item == 1)
               <div class="bg-secondary rounded p-3 mb-4">
               @if(Auth::guest())
               <p>{{ __('This item is one of the') }} <strong>{{ __('Free Files') }}</strong>. {{ __('You are able to download this item for free for a limited time. Updates and support are only available if you purchase this item.') }}</p>
               
               @endif
               @php if($item['item']->download_count == "") { $dcount = 0; } else { $dcount = $item['item']->download_count; } @endphp
               <div>
                   @if (Auth::check())
                   @if(Auth::user()->user_subscr_date >= date('Y-m-d') && Auth::user()->user_subscr_payment_status == 'completed')
                   <p>{{ __('This item is one of the Subscribe Users Free Download Files. You are able to download this item for free for a limited time. Updates and support are only available if you purchase this item.') }}</p>
                   <div align="center">
                   <a href="{{ URL::to('/item') }}/download/{{ base64_encode($item['item']->item_token) }}" class="btn btn-primary btn-sm"> <i class="fa fa-download"></i> {{ __('Download this file for free') }} ({{ $dcount }})</a>
                   </div>
                   @else
                   <p>{{ __('Subscribe to unlock this item, plus millions of creative assets with unlimited downloads.') }}</p>
                   <div align="center">
                   <a href="{{ URL::to('/subscription') }}" class="btn btn-primary btn-sm"> <i class="fa fa-download"></i> {{ __('Subscribe to download') }} ({{ $dcount }})</a>
                   </div>
                   @endif
                   <?php /*?>@if($addition_settings->subscription_mode == 0)<?php */?>
                   
                   <?php /*?>@else
                   @if(Auth::user()->user_type == 'vendor')
                   @if(Auth::user()->user_subscr_date >= date('Y-m-d'))
                   <a href="{{ URL::to('/item') }}/download/{{ base64_encode($item['item']->item_token) }}" class="btn btn-primary btn-sm"> <i class="fa fa-download"></i> {{ __('Download this file for free') }} ({{ $dcount }})</a>
                   @else
                   <a href="javascript:void(0)" class="btn btn-primary btn-sm" onClick="alert('Your subscription has been expired. Please renewal your subscription')"> <i class="fa fa-download"></i> {{ __('Download this file for free') }} ({{ $dcount }})</a>
                   @endif
                   @else<?php */?>
                   <?php /*?><a href="javascript:void(0)" class="btn btn-primary btn-sm" onClick="alert('Subscription user only')"> <i class="fa fa-download"></i> {{ __('Download this file for free') }} ({{ $dcount }})</a>
                   @endif
                   @endif<?php */?>
                   @endif
                   @if(Auth::guest())
                   <div align="center">
                   <a href="{{ URL::to('/login') }}" class="btn btn-primary btn-sm"> <i class="fa fa-download"></i> {{ __('Download this file for free') }} ({{ $dcount }})</a>
                   </div>
                   @endif
                </div>
               </div>
               @endif
               @endif
               @endif 
               
               @if($item['item']->free_download == 1)
               @else
                @php if($item['item']->item_flash == 1)
                { 
                $item_price = Helper::price_info($item['item']->item_flash,$item['item']->regular_price);
                $extend_item_price = Helper::price_info($item['item']->item_flash,$item['item']->extended_price);
                } 
                else 
                { 
                $item_price = $item['item']->regular_price;
                $extend_item_price = $item['item']->extended_price; 
                } 
                @endphp
              <div class="accordion" id="licenses">
                <div class="card border-top-0 border-left-0 border-right-0">
                  <div class="card-header d-flex justify-content-between align-items-center py-3 border-0">
                    <div class="custom-control custom-radio">
                      <input class="custom-control-input" type="radio" name="item_price" value="{{ base64_encode($item_price) }}_regular" id="license-std" checked>
                      <label class="custom-control-label font-weight-medium text-dark" for="license-std" data-toggle="collapse" data-target="#standard-license">{{ __('Regular License') }}</label>
                    </div>
                    <h5 class="mb-0 text-accent font-weight-normal">
                    @if($item['item']->item_flash == 1)<del class="price-old fontsize17">{{ Helper::price_format($allsettings->site_currency_position,$item['item']->regular_price,$currency_symbol,$multicurrency) }}</del>@endif <span class="bg-faded-accent rounded-sm py-1 px-2 fontsize17">{{ Helper::price_format($allsettings->site_currency_position,$item_price,$currency_symbol,$multicurrency) }}</span>
                    </h5>
                  </div>
                  <div class="collapse show" id="standard-license" data-parent="#licenses">
                    <div class="card-body py-0 pb-2">
                      <ul class="list-unstyled font-size-sm">
                        <li class="d-flex align-items-center"><i class="dwg-check-circle text-success mr-1"></i><span class="font-size-ms">{{ __('Quality checked by') }} {{ $allsettings->site_title }}</span></li>
                        @if($addition_settings->show_feature_update == 1)
                        @if($item['item']->future_update == 1) 
                        <li class="d-flex align-items-center"><i class="dwg-check-circle text-success mr-1"></i><span class="font-size-ms">{{ __('Future updates') }}</span></li>
                        @else
                        <li class="d-flex align-items-center"><i class="dwg-close-circle text-danger mr-1"></i><span class="font-size-ms">{{ __('Future updates') }}</span></li>
                        @endif
                        @endif
                        @if($item['item']->item_support == 1)
                        <li class="d-flex align-items-center"><i class="dwg-check-circle text-success mr-1"></i><span class="font-size-ms">{{ $addition_settings->regular_license }} {{ __('support from') }} {{ $item['item']->username }}</span></li>
                        @else
                        <li class="d-flex align-items-center"><i class="dwg-close-circle text-danger mr-1"></i><span class="font-size-ms">{{ $addition_settings->regular_license }} {{ __('not support from') }} {{ $item['item']->username }}</span></li>
                        @endif
                      </ul>
                    </div>
                  </div>
                </div>
                @if($item['item']->extended_price != 0)
                <div class="card border-bottom-0 border-left-0 border-right-0">
                  <div class="card-header d-flex justify-content-between align-items-center py-3 border-0">
                    <div class="custom-control custom-radio">
                      <input class="custom-control-input" type="radio" name="item_price" id="license-ext" value="{{ base64_encode($extend_item_price) }}_extended">
                      <label class="custom-control-label font-weight-medium text-dark" for="license-ext" data-toggle="collapse" data-target="#extended-license">{{ __('Extended License') }}</label>
                    </div>
                    <h5 class="mb-0 text-accent font-weight-normal">
                    @if($item['item']->item_flash == 1)<del class="price-old fontsize17">{{ Helper::price_format($allsettings->site_currency_position,$item['item']->extended_price,$currency_symbol,$multicurrency) }}</del>@endif <span class="bg-faded-accent rounded-sm py-1 px-2 fontsize17">{{ Helper::price_format($allsettings->site_currency_position,$extend_item_price,$currency_symbol,$multicurrency) }}</span>
                    </h5>
                  </div>
                  <div class="collapse" id="extended-license" data-parent="#licenses">
                    <div class="card-body py-0 pb-2">
                      <ul class="list-unstyled font-size-sm">
                        <li class="d-flex align-items-center"><i class="dwg-check-circle text-success mr-1"></i><span class="font-size-ms">{{ __('Quality checked by') }} {{ $allsettings->site_title }}</span></li>
                        @if($item['item']->future_update == 1) 
                        <li class="d-flex align-items-center"><i class="dwg-check-circle text-success mr-1"></i><span class="font-size-ms">{{ __('Future updates') }}</span></li>
                        @else
                        <li class="d-flex align-items-center"><i class="dwg-close-circle text-danger mr-1"></i><span class="font-size-ms">{{ __('Future updates') }}</span></li>
                        @endif
                        @if($item['item']->item_support == 1)
                        <li class="d-flex align-items-center"><i class="dwg-check-circle text-success mr-1"></i><span class="font-size-ms">{{ $addition_settings->extended_license }} {{ __('support from') }} {{ $item['item']->username }}</span></li>
                        @else
                        <li class="d-flex align-items-center"><i class="dwg-close-circle text-danger mr-1"></i><span class="font-size-ms">{{ $addition_settings->extended_license }} {{ __('not support from') }} {{ $item['item']->username }}</span></li>
                        @endif
                      </ul>
                    </div>
                  </div>
                </div>
                @endif
              </div>
              @endif
              @if($item['item']->file_type == 'serial')
              @php
              if($item['item']->item_delimiter == 'comma')
              {
                $result_count = substr_count($item['item']->item_serials_list, ","); 
              }
              else
              {
                $result_count = substr_count($item['item']->item_serials_list, "\n");
              }
              @endphp
              <div class="d-flex justify-content-between align-items-center py-3 border-0 stockqty">
                    <div class="float-left">
                      <label class="font-weight-medium text-dark">{{ __('Stock') }}</label>
                    </div>
                    <h5 class="mb-0 text-accent font-weight-normal" style="max-width: 5rem;">
                    <input class="form-control qty" type="number"  name="qty" id="qty" value="1" min="1" data-bvalidator="required,digit,max[{{ $result_count }}]">
                    </h5>
                  </div>
              <hr> 
              @else
              <input class="qty" type="hidden"  name="qty" id="qty" value="1" min="1">
              @endif 
              @if($allsettings->item_support_link !='')
              @if($item['item']->free_download == 1)
              @else
              <p class="mt-2 mb-3"><a href="javascript:void(0)" data-toggle="modal" data-target="#myModal" class="font-size-xs">{{ $page['view']->page_title }}</a></p>
              @endif
                <div class="modal fade" id="myModal">
                    <div class="modal-dialog modal-xl">
                      <div class="modal-content">
                         <div class="modal-header">
                          <h4 class="modal-title">{{ $page['view']->page_title }}</h4>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                         @php echo html_entity_decode($page['view']->page_desc); @endphp
                        </div>
                       </div>
                    </div>
                  </div>
                @endif
                <?php /*?><input type="hidden" name="user_id" value="{{ Auth::user()->id }}"><?php */?>
                <input type="hidden" name="item_id" value="{{ $item['item']->item_id }}">
                <input type="hidden" name="item_name" value="{{ $item['item']->item_name }}">
                <input type="hidden" name="item_user_id" value="{{ $item['item']->user_id }}">
                <input type="hidden" name="item_token" value="{{ $item['item']->item_token }}">
                <input type="hidden" name="file_type" value="{{ $item['item']->file_type }}">
                <input type="hidden" name="item_delimiter" value="{{ $item['item']->item_delimiter }}">
                
                @if($addition_settings->guest_checkout == 1)
                <!--- Guest Checkout ON  -->
                @if($item['item']->free_download == 1)
                @else
                @if(Auth::guest())
                <button type="submit" class="btn btn-primary btn-shadow btn-block mt-4"><i class="dwg-cart font-size-lg mr-2"></i>{{ __('Buy Now') }}</button>
                @endif
                @if (Auth::check())
                @if($item['item']->user_id == Auth::user()->id)
                <a href="{{ URL::to('/edit-item') }}/{{ $item['item']->item_token }}" class="btn btn-primary btn-shadow btn-block mt-4"><i class="dwg-cart font-size-lg mr-2"></i>{{ __('Edit Item') }}</a>
                @else
                
                @if($checkif_purchased == 0)
                @if(Auth::user()->id != 1)
                <button type="submit" class="btn btn-primary btn-shadow btn-block mt-4"><i class="dwg-cart font-size-lg mr-2"></i>{{ __('Buy Now') }}</button>
                @endif
                @else
                <a class="btn btn-primary btn-shadow btn-block mt-4" href="{{ URL::to('/purchases') }}"><i class="dwg-cart font-size-lg mr-2"></i>{{ __('Purchased Item') }}</a> 
                @endif    
                @endif
                @endif 
                @endif
                <!--- Guest Checkout ON  -->
                @else
                <!--- Guest Checkout OFF -->
                @if($item['item']->free_download == 1)
                @else
                @if(Auth::guest())
                <a class="btn btn-primary btn-shadow btn-block mt-4" href="{{ URL::to('/login') }}"><i class="dwg-cart font-size-lg mr-2"></i>{{ __('Buy Now') }}</a> 
                @endif
                @if (Auth::check())
                @if($item['item']->user_id == Auth::user()->id)
                <a href="{{ URL::to('/edit-item') }}/{{ $item['item']->item_token }}" class="btn btn-primary btn-shadow btn-block mt-4"><i class="dwg-cart font-size-lg mr-2"></i>{{ __('Edit Item') }}</a>
                @else
                
                @if($checkif_purchased == 0)
                @if(Auth::user()->id != 1)
                <button type="submit" class="btn btn-primary btn-shadow btn-block mt-4"><i class="dwg-cart font-size-lg mr-2"></i>{{ __('Buy Now') }}</button>
                @endif
                @else
                <a class="btn btn-primary btn-shadow btn-block mt-4" href="{{ URL::to('/purchases') }}"><i class="dwg-cart font-size-lg mr-2"></i>{{ __('Purchased Item') }}</a> 
                @endif    
                @endif
                @endif 
                @endif
                <!--- Guest Checkout OFF -->
                @endif
                @if($custom_settings->author_key == Helper::Key_Owner())
                @if(count($other_market_link) != 0)
                <div class="mt-3" align="center">
                <h5 class="mb-3">{{ __('You can download this file our others shop') }} - {{ count($other_market_link) }}</h5>
                <ul class="list-unstyled font-size-sm">
                @foreach($other_market_link as $market)
                @if(!empty($market->iom_market_link))
                <li class="d-flex justify-content-between mb-3 pb-3 border-bottom"><span class="text-dark font-weight-medium"><img src="{{ url('/') }}/public/storage/settings/{{ Helper::id_get_image_market($market->im_id) }}" alt="" width="120" height="40"></span><span class="text-muted"><a href="{{ $market->iom_market_link }}" target="_blank" style="font-weight:bold;">DOWNLOAD</a></span></li>
                @endif
                @endforeach
                </ul>
                </div>
                @endif
                @endif
                @if($item['item']->file_type == 'serial')
                @php
                if($item['item']->item_delimiter == 'comma')
                {
                $result_count = substr_count($item['item']->item_serials_list, ","); 
                }
                else
                {
                $result_count = substr_count($item['item']->item_serials_list, "\n");
                }
                @endphp
                <div class="bg-secondary rounded p-3 mt-4"><i class="dwg-cart h5 text-muted align-middle mb-0 mt-n1 mr-2"></i><span class="d-inline-block h6 mb-0 mr-1">{{ $result_count }}</span><span class="font-size-sm">{{ __('Stock') }}</span></div>
                @endif
                @if($item['item']->item_featured == 'yes')
                <div class="bg-secondary rounded p-3 mt-4">
                <span class="d-inline-block font-size-sm mb-0 mr-1"><img class="lazy single-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->featured_item_icon }}"  border="0" title="{{ __('Featured Item') }}"> {{ __('This item was featured on') }} {{ $allsettings->site_title }}</span>
                </div>
                @endif
                @if($sold_amount >= $badges['setting']->author_sold_level_six)
                <div class="bg-secondary rounded p-3 mt-4">
                <span class="d-inline-block font-size-sm mb-0 mr-1"><img class="lazy single-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->power_elite_author_icon }}"  border="0" title="{{ $badges['setting']->author_sold_level_six_label }} : {{ __('Sold more than') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_six }}+ {{ __('ON') }} {{ $allsettings->site_title }}"> {{ $badges['setting']->author_sold_level_six_label }}</span>
                </div>
                @endif
                <div class="bg-secondary rounded p-3 mt-4 mb-2">
                <a class="media" href="{{ url('/user') }}/{{ $item['item']->username }}">
                @if($item['item']->user_photo != '')
                <img class="lazy rounded-circle vertical-img" width="80" height="80" src="{{ url('/') }}/public/storage/users/{{ $item['item']->user_photo }}"  alt="{{ $item['item']->name }}">
                @else
                <img class="lazy rounded-circle vertical-img" width="80" height="80" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ $item['item']->name }}">
                @endif
                <div class="media-body pl-2 item-details">
                    <h6 class="font-size-sm mb-0">{{ $item['item']->username }}</h6>
                    <span class="font-size-ms text-muted">{{ __('Member since') }} {{ date("F Y", strtotime($item['item']->created_at)) }}</span>
                    <div class="mb-3">@if($addition_settings->subscription_mode == 1) @if($item['item']->user_document_verified == 1) <span class="badges-success"><i class="dwg-check-circle danger"></i> {{ __('verified') }}</span> @endif @endif</div>
                    <ul>
                                        @if($item['item']->country_badge == 1)
                                        @if($country['view']->country_badges != "")
                                        <li>
                                          <img class="lazy icon-badges" width="30" height="30" src="{{ url('/') }}/public/storage/flag/{{ $country['view']->country_badges }}"  border="0" title="{{ __('Located in') }} {{ $country['view']->country_name }}">  
                                        </li>
                                        @endif
                                        @endif
                                        @if($item['item']->exclusive_author == 1)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->exclusive_author_icon }}"  border="0" title="{{ __('Exclusive Author: Sells items exclusively on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($trends != 0)
                                         <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->trends_icon }}"  border="0" title="{{ __('Trendsetter: Had an item that was trending') }}">
                                        </li>
                                        @endif
                                        @if($item['item']->item_featured == 'yes')
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->featured_item_icon }}" border="0"  title="{{ __('Featured Item: Had an item featured on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($item['item']->free_download == 1)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->free_item_icon }}"  border="0" title="{{ __('Free Item : Contributed a free file of this item') }}">
                                        </li>
                                        @endif
                                        @if($year == 1)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->one_year_icon }}"  border="0"  title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year == 2)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->two_year_icon }}"  border="0" title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year == 3)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->three_year_icon }}"  border="0" title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>

                                        @endif
                                        @if($year == 4)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->four_year_icon }}"  border="0" title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year == 5)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->five_year_icon }}"  border="0" title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif 
                                        @if($year == 6)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->six_year_icon }}"  border="0" title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year == 7)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->seven_year_icon }}"  border="0" title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year == 8)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->eight_year_icon }}"  border="0" title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year == 9)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->nine_year_icon }}"  border="0" title="{{ $year }} {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} {{ $year }} {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($year >= 10)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->ten_year_icon }}"  border="0" title="@if($year >= 10) 10+ @else {{ $year }} @endif {{ __('Years of Membership: Has been part of the') }} {{ $allsettings->site_title }} {{ __('Community for over') }} @if($year >= 10) 10+ @else {{ $year }} @endif {{ __('years') }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_one && $badges['setting']->author_sold_level_two > $sold_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_sold_level_one_icon }}"  border="0" title="{{ __('Author Level') }} 1: {{ __('Has sold') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_one }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_two &&  $badges['setting']->author_sold_level_three > $sold_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_sold_level_two_icon }}"  border="0" title="{{ __('Author Level') }} 2: {{ __('Has sold') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_two }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_three &&  $badges['setting']->author_sold_level_four > $sold_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->	author_sold_level_three_icon }}"  border="0"  title="{{ __('Author Level') }} 3: {{ __('Has sold') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_three }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_four &&  $badges['setting']->author_sold_level_five > $sold_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_sold_level_four_icon }}"  border="0" title="{{ __('Author Level') }} 4: {{ __('Has sold') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_four }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_five &&  $badges['setting']->author_sold_level_six > $sold_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_sold_level_five_icon }}"  border="0" title="{{ __('Author Level') }} 5: {{ __('Has sold') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_five }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_six) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_sold_level_six_icon }}"  border="0" title="{{ __('Author Level') }} 6: {{ __('Has sold') }} {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_six }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($sold_amount >= $badges['setting']->author_sold_level_six)
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->power_elite_author_icon }}"  border="0" title="{{ $badges['setting']->author_sold_level_six_label }} : Sold more than {{ $allsettings->site_currency }} {{ $badges['setting']->author_sold_level_six }}+ {{ __('ON') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($collect_amount >= $badges['setting']->author_collect_level_one && $badges['setting']->author_collect_level_two > $collect_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_collect_level_one_icon }}"  border="0" title="{{ __('Collector Level') }} 1: {{ __('Has collected') }} {{ $badges['setting']->author_collect_level_one }}+ {{ __('items on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($collect_amount >= $badges['setting']->author_collect_level_two && $badges['setting']->author_collect_level_three > $collect_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_collect_level_two_icon }}"  border="0" title="{{ __('Collector Level') }} 2: {{ __('Has collected') }} {{ $badges['setting']->author_collect_level_two }}+ {{ __('items on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($collect_amount >= $badges['setting']->author_collect_level_three && $badges['setting']->author_collect_level_four > $collect_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_collect_level_three_icon }}"  border="0"  title="{{ __('Collector Level') }} 3: {{ __('Has collected') }} {{ $badges['setting']->author_collect_level_three }}+ {{ __('items on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($collect_amount >= $badges['setting']->author_collect_level_four && $badges['setting']->author_collect_level_five > $collect_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_collect_level_four_icon }}"  border="0"  title="{{ __('Collector Level') }} 4: {{ __('Has collected') }} {{ $badges['setting']->author_collect_level_four }}+ {{ __('items on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($collect_amount >= $badges['setting']->author_collect_level_five && $badges['setting']->author_collect_level_six > $collect_amount) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_collect_level_five_icon }}"  border="0"  title="{{ __('Collector Level') }} 5: {{ __('Has collected') }} {{ $badges['setting']->author_collect_level_five }}+ {{ __('items on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($collect_amount >= $badges['setting']->author_collect_level_six) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_collect_level_six_icon }}"  border="0" title="{{ __('Collector Level') }} 6: {{ __('Has collected') }} {{ $badges['setting']->author_collect_level_six }}+ {{ __('items on') }} {{ $allsettings->site_title }}">
                                        </li>
                                        @endif
                                        @if($referral_count >= $badges['setting']->author_referral_level_one && $badges['setting']->author_referral_level_two > $referral_count) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_one_icon }}"  border="0" title="{{ __('Affiliate Level') }} 1: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_one }}+ {{ __('Members') }}">
                                        </li>
                                        @endif
                                        
                                        @if($referral_count >= $badges['setting']->author_referral_level_two && $badges['setting']->author_referral_level_three > $referral_count) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_two_icon }}"  border="0" title="{{ __('Affiliate Level') }} 2: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_two }}+ {{ __('Members') }}">
                                        </li>
                                        @endif
                                        
                                        @if($referral_count >= $badges['setting']->author_referral_level_three && $badges['setting']->author_referral_level_four > $referral_count) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_three_icon }}"  border="0"  title="{{ __('Affiliate Level') }} 3: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_three }}+ {{ __('Members') }}">
                                        </li>
                                        @endif
                                        
                                        @if($referral_count >= $badges['setting']->author_referral_level_four && $badges['setting']->author_referral_level_five > $referral_count) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_four_icon }}"  border="0"  title="{{ __('Affiliate Level') }} 4: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_four }}+ {{ __('Members') }}">
                                        </li>
                                        @endif
                                        
                                        @if($referral_count >= $badges['setting']->author_referral_level_five && $badges['setting']->author_referral_level_six > $referral_count) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_five_icon }}"  border="0"  title="{{ __('Affiliate Level') }} 5: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_five }}+ {{ __('Members') }}">
                                        </li>
                                        @endif
                                        @if($referral_count >= $badges['setting']->author_referral_level_six) 
                                        <li>
                                        <img class="lazy other-badges" width="30" height="30" src="{{ url('/') }}/public/storage/badges/{{ $badges['setting']->author_referral_level_six_icon }}"  border="0"  title="{{ __('Affiliate Level') }} 6: {{ __('Has referred') }} {{ $badges['setting']->author_referral_level_six }}+ {{ __('Members') }}">
                                        </li>
                                        @endif
                                    </ul>
                </div></a>
                <a class="btn btn-outline-accent btn-sm btn-block" href="{{ url('/user') }}/{{ $item['item']->username }}"><i class="dwg-briefcase font-size-sm mr-2"></i>{{ __('View Profile') }}</a>
                </div>
                @if($addition_settings->item_sale_count == 1)
              <div class="bg-secondary rounded p-3 mt-2 mb-2"><i class="dwg-download h5 text-muted align-middle mb-0 mt-n1 mr-2"></i><span class="d-inline-block h6 mb-0 mr-1">{{ $item['item']->item_sold }}</span><span class="font-size-sm">{{ __('Sales') }}</span></div>
              @endif
              <div class="bg-secondary rounded p-3 mb-2">
                <div class="star-rating">
                @if($getreview == 0)
                <i class="sr-star dwg-star"></i>
                <i class="sr-star dwg-star"></i>
                <i class="sr-star dwg-star"></i>
                <i class="sr-star dwg-star"></i>
                <i class="sr-star dwg-star"></i>
                @else
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
                @endif
                </div>
                <div class="font-size-ms text-muted">{{ $getreview }} {{ __('Ratings') }}</div>
              </div>
              <div class="bg-secondary rounded p-3 mt-2 mb-2"><i class="dwg-heart h5 text-muted align-middle mb-0 mt-n1 mr-2"></i><span class="d-inline-block h6 mb-0 mr-1">{{ $item['item']->item_liked }}</span><span class="font-size-sm">{{ __('Favourites') }}</span></div>
              <div class="bg-secondary rounded p-3 mb-4"><i class="dwg-chat h5 text-muted align-middle mb-0 mt-n1 mr-2"></i><span class="d-inline-block h6 mb-0 mr-1">{{ $comment_count }}</span><span class="font-size-sm">{{ __('Comments') }}</span></div>
              <ul class="list-unstyled font-size-sm">
                <li class="d-flex justify-content-between mb-3 pb-3 border-bottom"><span class="text-dark font-weight-medium">{{ __('Released') }}</span><span class="text-muted">{{ date("d F Y", strtotime($item['item']->created_item)) }}</span></li>
                <li class="d-flex justify-content-between mb-3 pb-3 border-bottom"><span class="text-dark font-weight-medium">{{ __('Updated') }}</span><span class="text-muted">{{ date("d F Y", strtotime($item['item']->updated_item)) }}</span></li>
                <li class="d-flex justify-content-between mb-3 pb-3 border-bottom"><span class="text-dark font-weight-medium">{{ __('Category') }}</span><span class="text-muted">{{ $category_name }}</span></li>
                <li class="d-flex justify-content-between mb-3 pb-3 border-bottom"><span class="text-dark font-weight-medium">{{ __('Item Type') }}</span><span class="text-muted">{{ Helper::ItemTypeIdGetData($item['item']->item_type_id) }}</span></li>
                @if(count($viewattribute['details']) != 0)
                @foreach($viewattribute['details'] as $view_attribute)
                <li class="d-flex justify-content-between mb-3 pb-3 border-bottom"><span class="text-dark font-weight-medium">{{ $view_attribute->item_attribute_label }}</span><span class="text-muted">@php echo str_replace(',', ',<br />', $view_attribute->item_attribute_values); @endphp </span></li>
                @endforeach
                @endif
                @if($item['item']->item_tags != '')
                 <li class="justify-content-between pb-3 border-bottom"><span class="text-dark font-weight-medium">{{ __('Tags') }}</span><br/>
                 @php $item_tags = explode(',',$item['item']->item_tags); @endphp
                 @foreach($item_tags as $tags)
                 <span class="text-right"><a href="{{ url('/tag') }}/item/{{ strtolower(str_replace(' ','-',$tags)) }}" class="link-color">{{ $tags.',' }}</a></span>
                 @endforeach
                 </li>
                 @endif
              </ul>
              @if(in_array('item-details',$sidebar_ads))
          	<div class="mt-3 mb-2" align="center">
            @php echo html_entity_decode($addition_settings->sidebar_ads); @endphp
          	</div>
         	@endif
            </div>
            </form>
          </aside>
        </div>
      </div>
    </section>
    
    <section class="container mb-5 pb-lg-3">
      @if(count($related['items']) != 0)
      <div class="d-flex flex-wrap justify-content-between align-items-center border-bottom pb-4 mb-4">
        <h2 class="h3 mb-0 pt-2">{{ __('More Related Items') }} {{ __('by') }} {{ $item['item']->username }}</h2>
      </div>
      @endif
      <div class="row pt-2 mx-n2 flash-sale">
        <!-- Product-->
        @php $no = 1; @endphp
        @foreach($related['items'] as $featured)
        @php
        $price = Helper::price_info($featured->item_flash,$featured->regular_price);
        $count_rating = Helper::count_rating($featured->ratings);
        @endphp
        <div class="col-lg-3 col-md-4 col-sm-6 px-2 mb-grid-gutter prod-item">
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
                            <img class="lazy" width="300" height="200" src="{{ Helper::Image_Path($featured->item_preview,'no-image.png') }}"  alt="{{ $featured->item_name }}">
                            @else
                            <img class="lazy" width="300" height="200" src="{{ url('/') }}/public/img/no-image.png"  alt="{{ $featured->item_name }}">
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
                    <img class="lazy" width="26" height="26" src="{{ url('/') }}/public/storage/users/{{ $featured->user_photo }}"  alt="{{ $featured->username }}">
                    @else
                    <img class="lazy" width="26" height="26" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ $featured->username }}">
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
                <div class="font-size-sm mr-2">
                @if($addition_settings->item_sale_count == 1)
                <i class="dwg-download text-muted mr-1"></i>{{ $featured->item_sold }}<span class="font-size-xs ml-1">{{ __('Sales') }}</span>
                @endif
                </div>
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
   @else
   @include('not-found')
   @endif
@include('footer')
@include('script')
@if($item['item']->free_download == 0)
<script type="text/javascript">
$("#qty").change(function(){ 
   var item_price = "{{ $item_price }}";
   var extend_item_price = "{{ $extend_item_price }}";
   var qtyVal = $("#qty").val();
   var first_price = item_price * qtyVal;
   var second_price = extend_item_price * qtyVal;
   var types = "{{ $allsettings->site_currency_position }}";
   if(types == "left")
   {
   $("#regular_price").html("{{ $allsettings->site_currency_symbol }}"+first_price.toFixed(2));
   $("#extend_price").html("{{ $allsettings->site_currency_symbol }}"+second_price.toFixed(2)); 
   }
   else
   {
   $("#regular_price").html(first_price.toFixed(2)+"{{ $allsettings->site_currency_symbol }}");
   $("#extend_price").html(second_price.toFixed(2)+"{{ $allsettings->site_currency_symbol }}"); 
   }
});
</script>
@endif
</body>
</html>