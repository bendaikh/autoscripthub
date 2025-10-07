<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - @if($allsettings->site_blog_display == 1) {{ $edit['post']->post_title }} @else {{ __('404 Not Found') }} @endif</title>
@if($edit['post']->post_allow_seo == 1)
<meta name="Keywords" content="{{ $edit['post']->post_seo_keyword }}">
<meta name="Description" content="{{ $edit['post']->post_seo_desc }}">
@else
@include('meta')
@endif
@include('style')
<meta property="og:image" content="{{ url('/') }}/public/storage/post/{{ $edit['post']->post_image }}"/>
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="{{ url('/') }}/public/storage/post/{{ $edit['post']->post_image }}"/>
<meta name="twitter:image:src" content="{{ url('/') }}/public/storage/post/{{ $edit['post']->post_image }}">
<meta name="twitter:image:width" content= "280" />
<meta name="twitter:image:height" content= "480" />
</head>
<body>
@include('header')
@if($allsettings->site_blog_display == 1)
<section class="bg-position-center-top" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="py-4">
        <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Blog') }}</li>
            </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ $edit['post']->post_title }}</h1>
        </div>
      </div>
      </div>
    </section>
    <div class="container pb-5">
      <div class="row pt-5 mt-md-2">
        <section class="col-lg-8">

          @if(in_array('post-details',$top_ads))
            <div class="mt-2 mb-3 pb-3" align="center">
                <!-- Google AdSense Ad Unit -->
           <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9233828084974503"
                 crossorigin="anonymous"></script>
            <!-- autoscripthub first ad -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-9233828084974503"
                 data-ad-slot="3061456189"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            </div>
          @endif

          <!-- Post meta-->
          <div class="d-flex flex-wrap justify-content-between align-items-center pb-4 mt-n1" data-aos="fade-up" data-aos-delay="200">
            <div class="d-flex align-items-center font-size-sm mb-2"><span class="blog-entry-meta-link">{{ date('d M Y', strtotime($edit['post']->post_date)) }}</span><span class="blog-entry-meta-divider"></span><span class="blog-entry-meta-link"><i class="dwg-eye"></i>{{ $edit['post']->post_view }}</span></div>
            <div class="font-size-sm mb-2"><a class="blog-entry-meta-link text-nowrap" href="#comments" data-scroll><i class="dwg-message"></i>{{ $count }}</a></div>
          </div>
          <!-- Gallery-->
          <div class="cz-gallery row pb-2" data-aos="fade-up" data-aos-delay="200">
            <div class="col-sm-12">
            @if($edit['post']->post_image!='')
            <a class="gallery-item rounded-lg mb-grid-gutter" href="{{ url('/') }}/public/storage/post/{{ $edit['post']->post_image }}" data-sub-html="{{ $edit['post']->post_title }}"><img class="lazy" width="810" height="540" src="{{ url('/') }}/public/storage/post/{{ $edit['post']->post_image }}"  alt="{{ $edit['post']->post_title }}"><span class="gallery-item-caption">{{ $edit['post']->post_title }}</span></a>
            @else
            <img class="lazy" width="810" height="540" src="{{ url('/') }}/public/img/no-image.png"  alt="{{ $edit['post']->post_title }}">
            @endif
            </div>
          </div>
          <!-- Post content-->
          @php echo html_entity_decode($edit['post']->post_desc); @endphp
          <!-- Post tags + sharing-->
          <div class="d-flex flex-wrap justify-content-between pt-2 pb-4 mb-1">
            <div class="mt-3"><span class="d-inline-block align-middle text-muted font-size-sm mr-3 mb-2">{{ __('Share post') }}</span>
            <a class="share-button social-btn sb-facebook mr-2 mb-2" data-share-url="{{ URL::to('/single') }}/{{ $edit['post']->post_slug }}" data-share-network="facebook" data-share-text="{{ $edit['post']->post_short_desc }}" data-share-title="{{ $edit['post']->post_title }}" data-share-via="{{ $allsettings->site_title }}" data-share-tags="" data-share-media="{{ url('/') }}/public/storage/post/{{ $edit['post']->post_image }}" href="javascript:void(0)">
                                                        <i class="dwg-facebook"></i>
                                                    </a>
            <a class="share-button social-btn sb-twitter mr-2 mb-2" data-share-url="{{ URL::to('/single') }}/{{ $edit['post']->post_slug }}" data-share-network="twitter" data-share-text="{{ $edit['post']->post_short_desc }}" data-share-title="{{ $edit['post']->post_title }}" data-share-via="{{ $allsettings->site_title }}" data-share-tags="" data-share-media="{{ url('/') }}/public/storage/post/{{ $edit['post']->post_image }}" href="javascript:void(0)">
                                                        <i class="dwg-twitter"></i>
                                                    </a>                                        
            <a class="share-button social-btn sb-pinterest mr-2 mb-2" data-share-url="{{ URL::to('/single') }}/{{ $edit['post']->post_slug }}" data-share-network="googleplus" data-share-text="{{ $edit['post']->post_short_desc }}" data-share-title="{{ $edit['post']->post_title }}" data-share-via="{{ $allsettings->site_title }}" data-share-tags="" data-share-media="{{ url('/') }}/public/storage/post/{{ $edit['post']->post_image }}" href="javascript:void(0)">
                                                         <i class="dwg-google"></i>
                                                    </a>
            <a class="share-button social-btn sb-linkedin mr-2 mb-2" data-share-url="{{ URL::to('/single') }}/{{ $edit['post']->post_slug }}" data-share-network="linkedin" data-share-text="{{ $edit['post']->post_short_desc }}" data-share-title="{{ $edit['post']->post_title }}" data-share-via="{{ $allsettings->site_title }}" data-share-tags="" data-share-media="{{ url('/') }}/public/storage/post/{{ $edit['post']->post_image }}" href="javascript:void(0)">
                                                        <i class="dwg-linkedin"></i>
                                                    </a>                                        
            </div>
          </div>
          <!-- Post navigation-->
          <nav class="entry-navigation" aria-label="Post navigation">
          @if(!empty($previous_count))
          <a class="entry-navigation-link" href="{{ URL::to('/single') }}/{{ $previous->post_slug }}" data-toggle="popover" data-placement="top" data-trigger="hover" data-html="true" data-content="&lt;div class=&quot;media align-items-center&quot;&gt;&lt;img src={{ url('/') }}/public/storage/post/{{ $previous->post_image }} width=&quot;60&quot; class=&quot;mr-3&quot; alt=&quot;Post thumb&quot;&gt;&lt;div class=&quot;media-body&quot;&gt;&lt;h6  class=&quot;font-size-sm font-weight-semibold mb-0&quot;&gt;{{ $previous->post_title }}&lt;/h6&gt;&lt;span class=&quot;d-block font-size-xs text-muted&quot;&gt;{{ date('d M Y', strtotime($previous->post_date)) }}&lt;/span&gt;&lt;/div&gt;&lt;/div&gt;"><i class="dwg-arrow-left mr-2"></i><span class="d-none d-sm-inline">{{ __('Prev post') }}</span></a>
          @endif
          <a class="entry-navigation-link" href="{{ URL::to('/blog') }}"><i class="dwg-view-list mr-2"></i><span class="d-none d-sm-inline">{{ __('All posts') }}</span></a>
          @if(!empty($next_count))
          <a class="entry-navigation-link" href="{{ URL::to('/single') }}/{{ $next->post_slug }}" data-toggle="popover" data-placement="top" data-trigger="hover" data-html="true" data-content="&lt;div class=&quot;media align-items-center&quot;&gt;&lt;img src={{ url('/') }}/public/storage/post/{{ $next->post_image }} width=&quot;60&quot; class=&quot;mr-3&quot; alt=&quot;Post thumb&quot;&gt;&lt;div class=&quot;media-body&quot;&gt;&lt;h6  class=&quot;font-size-sm font-weight-semibold mb-0&quot;&gt;{{ $next->post_title }}&lt;/h6&gt;&lt;span class=&quot;d-block font-size-xs text-muted&quot;&gt;{{ date('d M Y', strtotime($next->post_date)) }}&lt;/span&gt;&lt;/div&gt;&lt;/div&gt;"><span class="d-none d-sm-inline">{{ __('Next post') }}</span><i class="dwg-arrow-right ml-2"></i></a>
          @endif
          </nav>
          <!-- Comments-->
          @if(Auth::guest())
          <div class="pt-2 mt-5" id="comments">
          <h5>{{ __('You must be') }} <a href="{{ URL::to('/login') }}">{{ __('logged') }}</a> {{ __('in to post a comment') }}</h5>
          </div>
          @endif
          @if (Auth::check())
          <div class="pt-2 mt-5" id="comments">
            <h2 class="h4">{{ __('Comments') }}<span class="badge badge-secondary font-size-sm text-body align-middle ml-2">{{ $count }}</span></h2>
            <!-- comment-->
            @if($count != 0)
            @php $no = 1; @endphp
            @foreach($comment['display'] as $comment)
            <div class="media py-4">
            @if($comment->user_photo != '')
            <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/storage/users/{{ $comment->user_photo }}"  alt="{{ $comment->name }}"/>
            @else
            <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ $comment->name }}"/>
            @endif
              <div class="media-body pl-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h6 class="font-size-md mb-0">{{ $comment->name }}</h6>
                </div>
                <p class="font-size-md mb-1">{{ $comment->comment_content }}</p><span class="font-size-ms text-muted"><i class="dwg-time align-middle mr-2"></i>{{ date('d M Y', strtotime($comment->comment_date)) }}</span>
              </div>
            </div>
            @php $no++; @endphp
            @endforeach
            @endif
            <!-- Post comment form-->
            <div class="card border-0 box-shadow my-2">
              <div class="card-body">
                <div class="media">
                @if(Auth::user()->user_photo != '')
                <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/storage/users/{{ Auth::user()->user_photo }}"  alt="{{ Auth::user()->name }}"/>
                @else
                <img class="lazy rounded-circle" width="50" height="50" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ Auth::user()->name }}"/>
                @endif
                  <form class="media-body needs-validation ml-3" action="{{ route('single') }}" method="post" novalidate>
                                {{ csrf_field() }}
                                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                <input type="hidden" name="post_id" value="{{ $edit['post']->post_id }}">
                    <div class="form-group">
                      <textarea class="form-control" rows="4" placeholder="{{ __('Your text here...') }}" name="comment_content" required></textarea>
                      <div class="invalid-feedback">{{ __('Please write your comment') }}</div>
                    </div>
                    <button class="btn btn-primary btn-sm" type="submit">{{ __('Submit Now') }}</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          @endif
          @if(in_array('post-details',$bottom_ads))
          <div class="mt-2 mb-2" align="center">
             @php echo html_entity_decode($addition_settings->bottom_ads); @endphp
          </div>
          @endif
        </section>
        <aside class="col-lg-4">
          <!-- Sidebar-->
          <div class="cz-sidebar border-left ml-lg-auto" id="blog-sidebar">
            <div class="cz-sidebar-header box-shadow-sm">
              <button class="close ml-auto" type="button" data-dismiss="sidebar" aria-label="Close"><span class="d-inline-block font-size-xs font-weight-normal align-middle">{{ __('Close sidebar') }}</span><span class="d-inline-block align-middle ml-2" aria-hidden="true">&times;</span></button>
            </div>
            <div class="cz-sidebar-body py-lg-1" data-simplebar data-simplebar-auto-hide="true">
              <!-- Categories-->
              <div class="widget widget-links mb-grid-gutter pb-grid-gutter border-bottom">
                <h3 class="widget-title">{{ __('Categories') }}</h3>
                <ul class="widget-list">
                  @foreach($catData['post'] as $post)
                  <li class="widget-list-item"><a class="widget-list-link d-flex justify-content-between align-items-center" href="{{ URL::to('/blog') }}/category/{{ $post->blog_cat_id }}/{{ $post->blog_category_slug }}"><span>{{ $post->blog_category_name }}</span><span class="font-size-xs text-muted ml-3">{{ $category_count->has($post->blog_cat_id) ? count($category_count[$post->blog_cat_id]) : 0 }}</span></a></li>
                  @endforeach
                </ul>
              </div>
              <!-- Trending posts-->
              <div class="widget mb-grid-gutter pb-grid-gutter border-bottom">
                <h3 class="widget-title">{{ __('Latest Posts') }}</h3>
                @foreach($blogPost['latest'] as $post)
                <div class="media align-items-center mb-3">
                <a href="{{ URL::to('/single') }}/{{ $post->post_slug }}" title="{{ $post->post_title }}">
                   @if($post->post_image!='')
                   <img class="lazy rounded" width="64" height="42" src="{{ url('/') }}/public/storage/post/{{ $post->post_image }}" alt="{{ $post->post_title }}">
                   @else
                   <img class="lazy rounded" width="64" height="42" src="{{ url('/') }}/public/img/no-image.png"  alt="{{ $post->post_title }}">
                   @endif
                </a>
                <div class="media-body pl-3">
                    <h6 class="blog-entry-title font-size-sm mb-0"><a href="{{ URL::to('/single') }}/{{ $post->post_slug }}">{{ $post->post_title }}</a></h6><span class="font-size-ms text-muted"><span class='blog-entry-meta-link'><i class="dwg-time"></i>{{ date('d M Y', strtotime($post->post_date)) }}</span></span>
                  </div>
                </div>
                @endforeach 
               </div>
              <!-- Popular tags-->
              <div class="widget mb-grid-gutter">
                <h3 class="widget-title">{{ __('Tags') }}</h3>
                @foreach($post_tags as $tags)
                <a class="btn-tag mr-2 mb-2" href="{{ url('/blog') }}/{{ strtolower(str_replace(' ','-',$tags)) }}">{{ $tags }}</a>
                @endforeach
              </div>
              @if(in_array('post-details',$sidebar_ads))
             <div class="mt-2" align="center">
             @php echo html_entity_decode($addition_settings->sidebar_ads); @endphp
             </div>
             @endif
              <!-- Promo banner-->
            </div>
          </div>
        </aside>
      </div>
    </div>
@else
@include('not-found')
@endif
@include('footer')
@include('script')
</body>
</html>