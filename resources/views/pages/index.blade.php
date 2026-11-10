<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $addition_settings->site_home_title }} - {{ $allsettings->site_title }}</title>
@include('meta')
@include('style')
@if(!empty($category['license_slides']))
<link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.css') }}">
@endif
</head>
<body class="home-v2">
@include('header')

@php
  // Palette + icon used by the "Browse Categories" tiles, cycled by position.
  $hvCatStyles = [
    ['bg' => '#eef2ff', 'fg' => '#4f46e5', 'icon' => 'fa-code'],
    ['bg' => '#ecfdf5', 'fg' => '#059669', 'icon' => 'fa-magic'],
    ['bg' => '#fff7ed', 'fg' => '#ea580c', 'icon' => 'fa-bullhorn'],
    ['bg' => '#fef2f2', 'fg' => '#dc2626', 'icon' => 'fa-shopping-cart'],
    ['bg' => '#f0fdfa', 'fg' => '#0d9488', 'icon' => 'fa-comments'],
    ['bg' => '#faf5ff', 'fg' => '#9333ea', 'icon' => 'fa-graduation-cap'],
    ['bg' => '#eff6ff', 'fg' => '#2563eb', 'icon' => 'fa-paint-brush'],
    ['bg' => '#fdf2f8', 'fg' => '#db2777', 'icon' => 'fa-picture-o'],
  ];
  // Nicer icon when the category name is recognisable.
  $hvCatIcons = [
    'script' => 'fa-code',        'theme'   => 'fa-paint-brush', 'ebook'   => 'fa-book',
    'print'  => 'fa-print',       'graphic' => 'fa-picture-o',   'mobile'  => 'fa-mobile',
    'desktop'=> 'fa-desktop',     'app'     => 'fa-th-large',    'plugin'  => 'fa-plug',
    'saas'   => 'fa-cloud',       'ai'      => 'fa-magic',       'course'  => 'fa-graduation-cap',
    'video'  => 'fa-video-camera','music'   => 'fa-music',       'game'    => 'fa-gamepad',
  ];
  $hvCatCounts = $category['counts'] ?? collect();

  // Headline with a gradient accent. Wrap words in [[ ]] inside the banner
  // heading setting to choose the accent; otherwise the last two words are used.
  $hvHeading = trim($allsettings->site_banner_heading);
  if (preg_match('/^(.*?)\[\[(.+?)\]\](.*)$/s', $hvHeading, $hvParts)) {
      $hvHeadingHtml = e($hvParts[1]).'<span class="hv-grad-text">'.e($hvParts[2]).'</span>'.e($hvParts[3]);
  } else {
      $hvWords = preg_split('/\s+/', $hvHeading, -1, PREG_SPLIT_NO_EMPTY);
      if (count($hvWords) > 3) {
          $hvAccent = array_splice($hvWords, -2);
          $hvHeadingHtml = e(implode(' ', $hvWords)).' <span class="hv-grad-text">'.e(implode(' ', $hvAccent)).'</span>';
      } else {
          $hvHeadingHtml = e($hvHeading);
      }
  }
@endphp

<!-- ============================ Hero ============================ -->
<section class="hv-hero">
  <div class="container">
    <div class="row align-items-center">

      <div class="col-lg-6">
        <span class="hv-hero-badge"><i class="fa fa-bolt"></i>{{ __('Trusted Digital Marketplace') }}</span>

        <h1>{!! $hvHeadingHtml !!}</h1>

        <p class="hv-hero-sub">{{ $allsettings->site_banner_subheading }}</p>

        <ul class="hv-pills">
          <li class="hv-pill"><i class="fa fa-download"></i>{{ __('Instant Download') }}</li>
          <li class="hv-pill"><i class="fa fa-refresh"></i>{{ __('Regular Updates') }}</li>
          <li class="hv-pill"><i class="fa fa-certificate"></i>{{ __('Commercial License') }}</li>
          <li class="hv-pill"><i class="fa fa-shield"></i>{{ __('30-Day Money Back') }}</li>
        </ul>

        <form action="{{ route('shop') }}" id="search_form" method="post" class="form-noborder searchbox" enctype="multipart/form-data">
          {{ csrf_field() }}
          <div class="hv-search">
            <div class="hv-search-field">
              <i class="dwg-search"></i>
              <input class="form-control" type="text" id="product_item" name="product_item" placeholder="{{ __('Search for products, scripts, tools...') }}">
            </div>
            @if(count($category['view']) != 0)
            <select name="category_names[]" id="product_cat" class="custom-select">
              <option value="">{{ __('All categories') }}</option>
              @foreach($category['view'] as $cat)
              <option value="{{ 'category_'.$cat->cat_id }}">{{ $cat->category_name }}</option>
              @foreach($cat->subcategory as $sub_category)
              <option value="{{ 'subcategory_'.$sub_category->subcat_id }}"> - {{ $sub_category->subcategory_name }}</option>
              @endforeach
              @endforeach
            </select>
            @endif
            <button class="hv-search-btn" type="submit">{{ __('Search') }}</button>
          </div>
        </form>

        @if(count($category['view']) != 0)
        <div class="hv-popular">
          <span>{{ __('Popular Searches') }}:</span>
          @foreach($category['view']->take(5) as $cat)
          <a href="{{ URL::to('/shop/category/') }}/{{ $cat->category_slug }}">{{ $cat->category_name }}</a>
          @endforeach
        </div>
        @endif
      </div>

      <div class="col-lg-6">
        <div class="hv-visual">
          <div class="hv-dash">
            <div class="hv-dash-bar">
              <span class="hv-dash-dot"></span><span class="hv-dash-dot"></span><span class="hv-dash-dot"></span>
              <span class="hv-dash-title">{{ __('Dashboard') }}</span>
            </div>

            <div class="hv-dash-stats">
              <div class="hv-dash-stat">
                <span>{{ __('Revenue') }}</span><strong>$24,850</strong><em>+12.5%</em>
              </div>
              <div class="hv-dash-stat">
                <span>{{ __('Orders') }}</span><strong>1,250</strong><em>+8.2%</em>
              </div>
              <div class="hv-dash-stat">
                <span>{{ __('Users') }}</span><strong>3,246</strong><em>+5.4%</em>
              </div>
            </div>

            <div class="row no-gutters">
              <div class="col-7 pr-1">
                <div class="hv-dash-panel">
                  <h6>{{ __('Revenue Overview') }}</h6>
                  <svg class="hv-dash-chart" viewBox="0 0 260 88" preserveAspectRatio="none" aria-hidden="true">
                    <defs>
                      <linearGradient id="hvArea" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#a855f7" stop-opacity=".55"/>
                        <stop offset="100%" stop-color="#a855f7" stop-opacity="0"/>
                      </linearGradient>
                    </defs>
                    <path d="M0,70 C25,66 40,44 62,46 C86,48 96,24 120,30 C146,36 156,58 180,52 C206,46 220,16 260,10 L260,88 L0,88 Z" fill="url(#hvArea)"/>
                    <path d="M0,70 C25,66 40,44 62,46 C86,48 96,24 120,30 C146,36 156,58 180,52 C206,46 220,16 260,10" fill="none" stroke="#c084fc" stroke-width="2.5" stroke-linecap="round"/>
                  </svg>
                </div>
              </div>
              <div class="col-5 pl-1">
                <div class="hv-dash-panel">
                  <h6>{{ __('Top Products') }}</h6>
                  <ul class="hv-dash-list">
                    <li><span class="hv-dash-chip" style="background:#4f46e5;"><i class="fa fa-code"></i></span>{{ __('Scripts') }}<b>412</b></li>
                    <li><span class="hv-dash-chip" style="background:#0ea5e9;"><i class="fa fa-paint-brush"></i></span>{{ __('Themes') }}<b>318</b></li>
                    <li><span class="hv-dash-chip" style="background:#10b981;"><i class="fa fa-th-large"></i></span>{{ __('Apps') }}<b>264</b></li>
                    <li><span class="hv-dash-chip" style="background:#f59e0b;"><i class="fa fa-picture-o"></i></span>{{ __('Graphics') }}<b>197</b></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <div class="hv-float hv-float--products">
            <span class="hv-float-icon" style="background:linear-gradient(135deg,#7c3aed,#a855f7);"><i class="fa fa-cube"></i></span>
            <span>
              <strong>{{ $total_files }}+</strong>
              <span>{{ __('Premium Products') }}</span>
            </span>
          </div>

          <div class="hv-float hv-float--customers">
            <span class="hv-float-icon" style="background:linear-gradient(135deg,#f59e0b,#f97316);"><i class="fa fa-star"></i></span>
            <span>
              <strong>{{ Helper::member_count() }}+</strong>
              <span>{{ __('Happy Customers') }}</span>
            </span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============================ Trust bar ============================ -->
<div class="hv-trust-wrap">
  <div class="container">
    <div class="hv-trust">
      <div class="hv-trust-item">
        <span class="hv-avatars">
          <span class="hv-avatar" style="background:#7c3aed;">A</span>
          <span class="hv-avatar" style="background:#0ea5e9;">M</span>
          <span class="hv-avatar" style="background:#10b981;">S</span>
          <span class="hv-avatar" style="background:#f59e0b;">K</span>
        </span>
        <span class="ml-2">
          <strong>{{ __('Trusted by Customers Worldwide') }}</strong>
          <span class="hv-stars">&#9733;&#9733;&#9733;&#9733;&#9733; <span class="text-muted">{{ __('Rated by our community') }}</span></span>
        </span>
      </div>
      <div class="hv-trust-item">
        <i class="fa fa-bolt"></i>
        <span><strong>{{ __('Instant Access') }}</strong><span>{{ __('After purchase') }}</span></span>
      </div>
      <div class="hv-trust-item">
        <i class="fa fa-lock"></i>
        <span><strong>{{ __('Secure Payment') }}</strong><span>{{ __('100% protected') }}</span></span>
      </div>
      <div class="hv-trust-item">
        <i class="fa fa-refresh"></i>
        <span><strong>{{ __('Regular Updates') }}</strong><span>{{ __('Lifetime free') }}</span></span>
      </div>
      <div class="hv-trust-item">
        <i class="fa fa-life-ring"></i>
        <span><strong>{{ __('24/7 Support') }}</strong><span>{{ __('We are here') }}</span></span>
      </div>
    </div>
  </div>
</div>

@if(in_array('home',$top_ads))
<section class="container pt-5" data-aos="fade-up" data-aos-delay="200">
  <div class="row">
    <div class="col-lg-12" align="center">
      @php echo html_entity_decode($addition_settings->top_ads); @endphp
    </div>
  </div>
</section>
@endif

<!-- ============================ Browse categories ============================ -->
@if(count($category['view']) != 0)
<section class="hv-section hv-browse" data-aos="fade-up" data-aos-delay="200">
  <div class="container">
    <div class="hv-head">
      <div>
        <h2>{{ __('Browse Categories') }}</h2>
        <p>{{ __('Find exactly what you need, organised by category') }}</p>
      </div>
      <a class="hv-head-link" href="{{ URL::to('/shop') }}">{{ __('View All Categories') }}<i class="dwg-arrow-right"></i></a>
    </div>
    <div class="row">
      @foreach($category['view'] as $index => $cat)
      @php
        $style = $hvCatStyles[$index % count($hvCatStyles)];
        $icon  = $style['icon'];
        foreach($hvCatIcons as $needle => $faIcon) {
          if(stripos($cat->category_name, $needle) !== false) { $icon = $faIcon; break; }
        }
        $itemCount = $hvCatCounts[$cat->cat_id] ?? 0;
      @endphp
      <div class="col-lg-2 col-md-3 col-4 mb-2">
        <a class="hv-cat" href="{{ URL::to('/shop/category/') }}/{{ $cat->category_slug }}">
          <span class="hv-cat-icon" style="background:{{ $style['bg'] }};color:{{ $style['fg'] }};"><i class="fa {{ $icon }}"></i></span>
          <h3>{{ $cat->category_name }}</h3>
          <p>{{ $itemCount }} {{ trans_choice('Item|Items', $itemCount) }}</p>
        </a>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- ============================ License categories ============================ -->
@if(!empty($category['license_slides']))
<section class="hv-section hv-section--soft hv-license" data-aos="fade-up" data-aos-delay="200">
  <div class="container">
    <div class="hv-head">
      <div>
        <h2>{{ __('License Keys') }}</h2>
        <p>{{ __('Operating systems, office, security and more, sold at one price') }}</p>
      </div>
    </div>
    <div class="hv-license-row">
    <div class="hv-license-wrap">
      <button type="button" class="hv-license-arrow hv-license-prev" aria-label="{{ __('Previous') }}">&#8249;</button>
      <div class="owl-carousel hv-license-slider">
        @foreach($category['license_slides'] as $index => $slide)
        @php
          $style = $hvCatStyles[$index % count($hvCatStyles)];
          $icon = $style['icon'];
          foreach ($hvCatIcons as $needle => $faIcon) {
            if (stripos($slide['name'], $needle) !== false) { $icon = $faIcon; break; }
          }
          $slideImage = $slide['image'] ?? null;
          $slideImageUrl = null;
          if (!empty($slideImage)) {
            if (preg_match('#^https?://#i', $slideImage) || str_starts_with($slideImage, '/')) {
              $slideImageUrl = $slideImage;
            } else {
              foreach (['category', 'settings'] as $folder) {
                if (is_file(base_path('public/storage/'.$folder.'/'.basename($slideImage)))) {
                  $slideImageUrl = url('/').'/public/storage/'.$folder.'/'.basename($slideImage);
                  break;
                }
              }
            }
          }
          $slideCount = (int) ($slide['count'] ?? 0);
          $slideUrl = URL::to('/shop/'.$slide['kind'].'/'.$slide['slug']);
        @endphp
        <div class="hv-license-slide">
          <a class="hv-cat hv-license-card" href="{{ $slideUrl }}">
            @if($slideImageUrl)
            <span class="hv-license-photo"><img src="{{ $slideImageUrl }}" alt="{{ $slide['name'] }}"></span>
            @else
            <span class="hv-cat-icon" style="background:{{ $style['bg'] }};color:{{ $style['fg'] }};"><i class="fa {{ $icon }}"></i></span>
            @endif
            <h3>{{ $slide['name'] }}</h3>
            <p>{{ $slideCount }} {{ trans_choice('Item|Items', $slideCount) }}</p>
          </a>
        </div>
        @endforeach
      </div>
      <button type="button" class="hv-license-arrow hv-license-next" aria-label="{{ __('Next') }}">&#8250;</button>
    </div>
    <div class="hv-license-dots" role="tablist" aria-label="{{ __('License Keys') }}"></div>
    </div>
    @if(count($category['license_items'] ?? []) != 0)
    <div class="hv-head hv-license-products-head">
      <div>
        <h2>{{ __('License Products') }}</h2>
        <p>{{ __('Newest keys from these categories') }}</p>
      </div>
    </div>
    <div class="hv-license-row">
    <div class="hv-license-wrap">
      <button type="button" class="hv-license-arrow hv-license-prev" aria-label="{{ __('Previous') }}">&#8249;</button>
      <div class="owl-carousel hv-license-slider hv-license-products">
        @foreach($category['license_items'] as $licenseItem)
        @php
          $price = Helper::catalog_price($licenseItem);
          $price_exact = true;
          $count_rating = Helper::count_rating($licenseItem->ratings);
          $hvTag = $hvTagClass = null;
        @endphp
        <div class="hv-license-product-slide">
          @include('partials.product-card', ['featured' => $licenseItem])
        </div>
        @endforeach
      </div>
      <button type="button" class="hv-license-arrow hv-license-next" aria-label="{{ __('Next') }}">&#8250;</button>
    </div>
    <div class="hv-license-dots" role="tablist" aria-label="{{ __('License Products') }}"></div>
    </div>
    @endif
  </div>
</section>
@endif

<!-- ============================ Featured ============================ -->
@if(count($featured['items']) != 0)
<section class="hv-section hv-section--soft" data-aos="fade-up" data-aos-delay="200">
  <div class="container">
    <div class="hv-head">
      <div>
        <h2>{{ __('Featured Products') }}</h2>
        <p>{{ __('Hand-picked tools our team recommends') }}</p>
      </div>
      <a class="hv-head-link" href="{{ URL::to('/') }}/featured-items">{{ __('View All Products') }}<i class="dwg-arrow-right"></i></a>
    </div>
    <div class="row mx-n2 flash-sale">
      @foreach($featured['items'] as $index => $featured)
      @php
        $price = Helper::catalog_price($featured);
        $price_exact = Helper::is_single_price_item($featured);
        $count_rating = Helper::count_rating($featured->ratings);
        $hvTag = $hvTagClass = null;
        if($index === 0)                    { $hvTag = __('Top Seller'); $hvTagClass = 'hv-tag--seller'; }
        elseif($featured->item_flash == 1)  { $hvTag = __('Trending');   $hvTagClass = 'hv-tag--trending'; }
        elseif($featured->free_download==1) { $hvTag = __('Free');       $hvTagClass = 'hv-tag--free'; }
        elseif($index === 1)                { $hvTag = __('Best Value'); $hvTagClass = 'hv-tag--value'; }
      @endphp
      @include('partials.product-card')
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- ============================ Popular ============================ -->
@if(count($popular['items']) != 0)
<section class="hv-section flash-sale" data-aos="fade-up" data-aos-delay="200">
  <div class="container">
    <div class="hv-head">
      <div>
        <h2>{{ __('Popular Items') }}</h2>
        <p>{{ __('What other customers are buying right now') }}</p>
      </div>
      <a class="hv-head-link" href="{{ URL::to('/') }}/popular-items">{{ __('View All Products') }}<i class="dwg-arrow-right"></i></a>
    </div>
    <div class="row mx-n2">
      @foreach($popular['items'] as $featured)
      @php
        $price = Helper::catalog_price($featured);
        $price_exact = Helper::is_single_price_item($featured);
        $count_rating = Helper::count_rating($featured->ratings);
        $hvTag = $hvTagClass = null;
      @endphp
      @include('partials.product-card')
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- ============================ Flash sale ============================ -->
@if(count($flash['items']) != 0)
<section class="hv-section hv-section--soft flash-sale" data-aos="fade-up" data-aos-delay="200">
  <div class="container">
    <div class="hv-head">
      <div>
        <h2>{{ __('Flash Sale') }}</h2>
        <p>{{ __('Limited-time discounts, while they last') }}</p>
      </div>
      <a class="hv-head-link" href="{{ URL::to('/') }}/flash-sale">{{ __('View All Products') }}<i class="dwg-arrow-right"></i></a>
    </div>
    <div class="row mx-n2">
      @foreach($flash['items'] as $featured)
      @php
        $price = Helper::catalog_price($featured);
        $price_exact = Helper::is_single_price_item($featured);
        $count_rating = Helper::count_rating($featured->ratings);
        $hvTag = __('Sale'); $hvTagClass = 'hv-tag--trending';
      @endphp
      @include('partials.product-card')
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- ============================ Free items ============================ -->
@if(count($free['items']) != 0)
<section class="hv-section flash-sale" data-aos="fade-up" data-aos-delay="200">
  <div class="container">
    <div class="hv-head">
      <div>
        <h2>{{ __('Free Items') }}</h2>
        <p>{{ __('Great tools that cost you nothing') }}</p>
      </div>
      <a class="hv-head-link" href="{{ URL::to('/') }}/free-items">{{ __('View All Products') }}<i class="dwg-arrow-right"></i></a>
    </div>
    <div class="row mx-n2">
      @foreach($free['items'] as $featured)
      @php
        $price = Helper::catalog_price($featured);
        $price_exact = Helper::is_single_price_item($featured);
        $count_rating = Helper::count_rating($featured->ratings);
        $hvTag = __('Free'); $hvTagClass = 'hv-tag--free';
      @endphp
      @include('partials.product-card')
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- ============================ New releases ============================ -->
@if(count($newest['items']) != 0)
<section class="hv-section hv-section--soft" data-aos="fade-up" data-aos-delay="200">
  <div class="container">
    <div class="hv-head">
      <div>
        <h2>{{ __('New Releases') }}</h2>
        <p>{{ __('The latest additions to the marketplace') }}</p>
      </div>
      <a class="hv-head-link" href="{{ URL::to('/new-releases') }}">{{ __('View All Products') }}<i class="dwg-arrow-right"></i></a>
    </div>
    <div class="row">
      @foreach($newest['items'] as $featured)
      @php $price = Helper::catalog_price($featured);
        $price_exact = Helper::is_single_price_item($featured); @endphp
      <div class="col-lg-4 col-md-6 mb-3">
        <div class="hv-mini">
          <a href="{{ URL::to('/item') }}/{{ $featured->item_slug }}">
            @if($featured->item_preview!='')
            <img class="lazy" src="{{ Helper::Image_Path($featured->item_preview,'no-image.png') }}" alt="{{ $featured->item_name }}" width="64" height="48">
            @else
            <img class="lazy" src="{{ url('/') }}/public/img/no-image.png" alt="{{ $featured->item_name }}" width="64" height="48">
            @endif
          </a>
          <div class="media-body">
            <h6><a href="{{ URL::to('/item') }}/{{ $featured->item_slug }}">{{ $featured->item_name }}</a></h6>
            <div class="hv-mini-price">
              @if($featured->free_download == 0)
              {{ Helper::price_format($allsettings->site_currency_position,$price,$currency_symbol,$multicurrency,$price_exact) }}
              @if($featured->item_flash == 1)<del class="price-old ml-1">{{ Helper::price_format($allsettings->site_currency_position,($price_exact ? $featured->regular_price : $featured->extended_price),$currency_symbol,$multicurrency,$price_exact) }}</del>@endif
              @else
              {{ __('Free') }}
              @endif
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- ============================ Why choose us / stats ============================ -->
@if($allsettings->site_features_display == 1)
<section class="hv-section" data-aos="fade-up" data-aos-delay="200">
  <div class="container">
    <div class="hv-stats">
      <div class="row align-items-center">
        <div class="col-lg-5 mb-4 mb-lg-0">
          <div class="hv-stats-label">{{ __('Why Choose') }} {{ $allsettings->site_title }}</div>
          <h2>{{ __('Built for Entrepreneurs, Loved by Professionals') }}</h2>
          <p>{{ __('We provide premium digital products that help you build, automate and scale your business with ease.') }}</p>
          <a class="hv-btn hv-btn--primary" href="{{ URL::to('/shop') }}">{{ __('Explore All Products') }}<i class="dwg-arrow-right"></i></a>
        </div>
        <div class="col-lg-7">
          <div class="row">
            <div class="col-6 col-md-3">
              <div class="hv-stat">
                <span class="hv-stat-icon"><i class="fa fa-users"></i></span>
                <strong>{{ Helper::member_count() }}+</strong>
                <span>{{ __('Happy Customers') }}</span>
                <small>{{ __('And growing every week') }}</small>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="hv-stat">
                <span class="hv-stat-icon"><i class="fa fa-cube"></i></span>
                <strong>{{ $total_files }}+</strong>
                <span>{{ __('Premium Products') }}</span>
                <small>{{ __('New items added regularly') }}</small>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="hv-stat">
                <span class="hv-stat-icon"><i class="fa fa-bolt"></i></span>
                <strong>99.9%</strong>
                <span>{{ __('Uptime & Reliability') }}</span>
                <small>{{ __('Always available') }}</small>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="hv-stat">
                <span class="hv-stat-icon"><i class="fa fa-life-ring"></i></span>
                <strong>24/7</strong>
                <span>{{ __('Customer Support') }}</span>
                <small>{{ __('We are here for you') }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endif

<!-- ============================ Testimonials + CTA ============================ -->
@php
  $hvReviews = collect($review['data'] ?? [])->filter(function ($r) {
      return !empty($r->rating_comment);
  })->take(3);
  // Shown until real reviews exist. Edit or delete these three entries freely.
  $hvFallbackReviews = [
    ['rating' => 5, 'comment' => __('Excellent products and amazing support. The scripts are high quality and easy to customise. Highly recommended.'), 'name' => 'John D.', 'role' => __('Entrepreneur')],
    ['rating' => 5, 'comment' => __('Saved hundreds of hours simply by using ready-made tools from this marketplace. They have everything I need for my business.'), 'name' => 'Sarah K.', 'role' => __('Agency Owner')],
    ['rating' => 5, 'comment' => __('Fast delivery, regular updates and great customer support. My go-to marketplace for digital products.'), 'name' => 'Michael T.', 'role' => __('Developer')],
  ];
@endphp
<section class="hv-section hv-section--soft" data-aos="fade-up" data-aos-delay="200">
  <div class="container">
    <div class="hv-head">
      <div>
        <h2>{{ __('What Our Customers Say') }}</h2>
        <p>{{ __('Real feedback from people building with our products') }}</p>
      </div>
      @if($hvReviews->count() > 0)
      <a class="hv-head-link" href="{{ URL::to('/shop') }}">{{ __('View All Reviews') }}<i class="dwg-arrow-right"></i></a>
      @endif
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="row">
          @if($hvReviews->count() > 0)
            @foreach($hvReviews as $hvReview)
            <div class="col-md-4 mb-4 mb-lg-0">
              <div class="hv-quote">
                <div class="hv-stars">
                  @for($star = 1; $star <= 5; $star++){!! $star <= $hvReview->rating ? '&#9733;' : '&#9734;' !!}@endfor
                </div>
                <p>{{ $hvReview->rating_comment }}</p>
                <div class="hv-quote-user">
                  @if(!empty($hvReview->user_photo))
                  <img src="{{ url('/') }}/public/storage/users/{{ $hvReview->user_photo }}" alt="{{ $hvReview->username }}" width="36" height="36">
                  @else
                  <span class="hv-quote-avatar">{{ strtoupper(mb_substr($hvReview->username, 0, 1)) }}</span>
                  @endif
                  <span>
                    <strong>{{ $hvReview->username }}</strong>
                    <span>{{ __('Verified Buyer') }}</span>
                  </span>
                </div>
              </div>
            </div>
            @endforeach
          @else
            @foreach($hvFallbackReviews as $hvReview)
            <div class="col-md-4 mb-4 mb-lg-0">
              <div class="hv-quote">
                <div class="hv-stars">
                  @for($star = 1; $star <= 5; $star++){!! $star <= $hvReview['rating'] ? '&#9733;' : '&#9734;' !!}@endfor
                </div>
                <p>{{ $hvReview['comment'] }}</p>
                <div class="hv-quote-user">
                  <span class="hv-quote-avatar">{{ mb_substr($hvReview['name'], 0, 1) }}</span>
                  <span>
                    <strong>{{ $hvReview['name'] }}</strong>
                    <span>{{ $hvReview['role'] }}</span>
                  </span>
                </div>
              </div>
            </div>
            @endforeach
          @endif
        </div>
      </div>

      <div class="col-lg-4">
        <div class="hv-cta">
          <h3>{{ __('Join Entrepreneurs Already Growing with') }} {{ $allsettings->site_title }}</h3>
          <p>{{ __('Get instant access to premium scripts, themes and tools built to move your business forward.') }}</p>
          <div>
            <a class="hv-btn hv-btn--light" href="{{ Auth::guest() ? URL::to('/register') : URL::to('/shop') }}">{{ __('Start Shopping Now') }}<i class="dwg-arrow-right"></i></a>
          </div>
          <small><i class="fa fa-shield"></i>{{ __('30-Day Money Back Guarantee') }}</small>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============================ Blog ============================ -->
@if($allsettings->site_blog_display == 1)
@if($allsettings->home_blog_display == 1)
@if(count($blog['data']) != 0)
<section class="hv-section homeblog" data-aos="fade-up" data-aos-delay="200">
  <div class="container">
    <div class="hv-head">
      <div>
        <h2>{{ __('From Our Blog') }}</h2>
        <p>{{ __('Guides, updates and tips from the team') }}</p>
      </div>
      <a class="hv-head-link" href="{{ URL::to('/blog') }}">{{ __('Read More Posts') }}<i class="dwg-arrow-right"></i></a>
    </div>
    <div class="row">
      @foreach($blog['data'] as $post)
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100">
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
      @endforeach
    </div>
  </div>
</section>
@endif
@endif
@endif

<!-- ============================ Newsletter ============================ -->
@if($allsettings->site_newsletter_display == 1)
<section class="pb-5" data-aos="fade-up" data-aos-delay="200">
  <div class="container">
    <div class="hv-news">
      <span class="hv-news-icon"><i class="dwg-mail"></i></span>
      <div class="hv-news-text">
        <h3>{{ __('Stay Updated with New Products & Offers') }}</h3>
        <p>{{ $allsettings->site_newsletter }}</p>
      </div>
      <form class="hv-news-form validate" action="{{ route('newsletter') }}" method="post" name="mc-embedded-subscribe-form" id="home_news_form" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input class="form-control" type="email" value="" placeholder="{{ __('Enter your email address') }}" data-bvalidator="required" name="news_email">
        <button type="submit" name="subscribe">{{ __('Subscribe') }}</button>
      </form>
    </div>
  </div>
</section>
@endif

@if(in_array('home',$bottom_ads))
<section class="container pb-5" data-aos="fade-up" data-aos-delay="200">
  <div class="row">
    <div class="col-lg-12" align="center">
      @php echo html_entity_decode($addition_settings->bottom_ads); @endphp
    </div>
  </div>
</section>
@endif

@if($custom_settings->promotion_popup == 1 && (empty($custom_settings->pr_promo_date) || strtotime($custom_settings->pr_promo_date . ' 23:59:59') >= time()))
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
@if(!empty($category['license_slides']))
<script src="{{ asset('assets/js/vendor/owl.carousel.min.js') }}"></script>
<script>
jQuery(function ($) {
  // This Owl build calls jQuery's .andSelf(), removed in jQuery 3 (the theme
  // loads 3.4.1). The call throws after Owl adds .owl-loading { opacity: 0 },
  // so the cards stay in the HTML but the slider is an empty block.
  if (!$.fn.andSelf && $.fn.addBack) {
    $.fn.andSelf = $.fn.addBack;
  }
  $('.hv-license-row').each(function () {
    var $row = $(this);
    var $slider = $row.find('.hv-license-slider');
    var $dots = $row.find('.hv-license-dots');
    if (!$slider.length || typeof $.fn.owlCarousel !== 'function') {
      $slider.addClass('is-static');
      return;
    }
    var count = $slider.children().length;
    $slider.owlCarousel({
      items: 1,
      margin: 16,
      loop: count > 1,
      autoplay: count > 1,
      autoplayTimeout: 4200,
      autoplayHoverPause: true,
      smartSpeed: 450,
      mouseDrag: true,
      touchDrag: true,
      nav: false,
      dots: false,
      responsive: {
        0: { items: 1 },
        576: { items: 2 },
        768: { items: 3 },
        1100: { items: Math.min(4, count) }
      }
    });
    $slider.trigger('refresh.owl.carousel');
    if (count < 2) {
      $row.find('.hv-license-arrow, .hv-license-dots').hide();
      return;
    }
    for (var i = 0; i < count; i++) {
      $dots.append('<button type="button" class="hv-license-dot' + (i === 0 ? ' is-active' : '') + '" data-index="' + i + '" aria-label="' + (i + 1) + '"></button>');
    }
    function realIndex(event) {
      var clones = event.relatedTarget && event.relatedTarget._clones ? event.relatedTarget._clones.length / 2 : 0;
      var index = event.item.index - clones;
      index = ((index % count) + count) % count;
      return index;
    }
    $slider.on('changed.owl.carousel', function (event) {
      if (!event.namespace) return;
      var index = realIndex(event);
      $dots.children().removeClass('is-active').eq(index).addClass('is-active');
    });
    $dots.on('click', '.hv-license-dot', function () {
      $slider.trigger('to.owl.carousel', [$(this).data('index'), 400, true]);
    });
    $row.find('.hv-license-next').on('click', function () { $slider.trigger('next.owl.carousel'); });
    $row.find('.hv-license-prev').on('click', function () { $slider.trigger('prev.owl.carousel'); });
  });
});
</script>
<script>
(function () {
  // AOS measures offsets before Owl builds the license sliders, so every
  // fade-up section below them never receives .aos-animate. Refresh after
  // the sliders change the page height.
  function refreshAos() {
    if (window.AOS && typeof window.AOS.refreshHard === 'function') {
      window.AOS.refreshHard();
    }
  }
  if (window.jQuery) {
    jQuery('.hv-license-slider').on('initialized.owl.carousel refreshed.owl.carousel', function () {
      window.setTimeout(refreshAos, 50);
    });
    jQuery(function () {
      window.setTimeout(refreshAos, 600);
      window.setTimeout(refreshAos, 2000);
    });
    jQuery(window).on('load', refreshAos);
  }
})();
</script>
@endif
</body>
</html>
