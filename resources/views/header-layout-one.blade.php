<header class="bg-light box-shadow-sm navbar-sticky">
      <!-- Topbar-->
      
      @if($allsettings->site_header_top_bar == 1)
      <div class="topbar topbar-dark bg-dark">
        <div class="container">
          <div>
            @if($allsettings->multi_language == 1)
            <div class="topbar-text dropdown disable-autohide"><a class="topbar-link dropdown-toggle capletter" href="javascript:void(0);" data-toggle="dropdown">{{ $current_locale }}</a>
              <ul class="dropdown-menu">
                @foreach($available_locales as $locale_name => $available_locale)
                <li><a class="dropdown-item pb-1" href="{{ URL::to('/language') }}/{{ $available_locale }}">{{ $locale_name }}</a></li>
                @endforeach
              </ul>
            </div>
            @endif
            @if($addition_settings->multi_currency == 1)
            <div class="topbar-text dropdown disable-autohide"><a class="topbar-link dropdown-toggle capletter" href="javascript:void(0);" data-toggle="dropdown">{{ $currency_title }}</a>
              <ul class="dropdown-menu">
                @foreach($currencyview as $currency)
                <li><a class="dropdown-item pb-1" href="{{ URL::to('/currency') }}/{{ $currency->currency_code }}">{{ $currency->currency_code }} ({{ $currency->currency_symbol }})</a></li>
                @endforeach
              </ul>
            </div>
            @endif
            <div class="topbar-text text-nowrap d-none d-md-inline-block border-left border-light pl-3 ml-3"></div>
          </div>
          <div class="topbar-text dropdown d-md-none ml-auto"><a class="topbar-link dropdown-toggle" href="#" data-toggle="dropdown">@if($addition_settings->verify_mode == 1){{ __('Verify Purchase') }} / @endif @if($addition_settings->subscription_mode == 1){{ __('Subscription') }} / @endif {{ __('Contact') }}@if($allsettings->site_blog_display == 1) / {{ __('Blog') }}@endif</a>
            <ul class="dropdown-menu dropdown-menu-right">
              @if($addition_settings->verify_mode == 1) 
              <li><a class="dropdown-item" href="{{ URL::to('/verify') }}"><i class="dwg-check text-muted mr-2"></i>{{ __('Verify Purchase') }}</a></li>
              @endif
              {{-- @if(Auth::guest())
              <li><a class="dropdown-item" href="{{ URL::to('/start-selling') }}"><i class="dwg-cart text-muted mr-2"></i>{{ __('Start Selling') }}</a></li>
              @else
              @if(Auth::user()->user_type == 'vendor')
              <li><a class="dropdown-item" href="{{ URL::to('/manage-item') }}"><i class="dwg-cart text-muted mr-2"></i>{{ __('Start Selling') }}</a></li>
              @endif
              @endif --}}
              @if($addition_settings->subscription_mode == 1)
              <li><a class="dropdown-item" href="{{ url('/subscription') }}"><i class="dwg-book text-muted mr-2"></i>{{ __('Subscription') }}</a></li>
              @endif
              <li><a class="dropdown-item" href="{{ URL::to('/contact') }}"><i class="dwg-support text-muted mr-2"></i>{{ __('Contact') }}</a></li>
              @if($allsettings->site_blog_display == 1)
              <li><a class="dropdown-item" href="{{ URL::to('/blog') }}"><i class="dwg-image text-muted mr-2"></i>{{ __('Blog') }}</a></li>
              @endif
            </ul>
          </div>
          <div class="d-none d-md-block ml-3 text-nowrap">
          @if($addition_settings->verify_mode == 1)
          <a class="topbar-link ml-3 pl-3 d-none d-md-inline-block" href="{{ URL::to('/verify') }}"><i class="dwg-check mt-n1"></i>{{ __('Verify Purchase') }}</a>
          @endif
          {{-- @if($allsettings->site_selling_display == 1)
          @if(Auth::guest())
          <a class="topbar-link ml-3 pl-3 border-left border-light d-none d-md-inline-block" href="{{ URL::to('/start-selling') }}"><i class="dwg-cart mt-n1"></i>{{ __('Start Selling') }}</a>
          @else
          @if(Auth::user()->user_type == 'vendor')
          <a class="topbar-link ml-3 pl-3 border-left border-light d-none d-md-inline-block" href="{{ URL::to('/manage-item') }}"><i class="dwg-cart mt-n1"></i>{{ __('Start Selling') }}</a>
          @endif
          @endif
          @endif --}}
          @if($addition_settings->subscription_mode == 1)
          <a class="topbar-link ml-3 border-left border-light pl-3 d-none d-md-inline-block" href="{{ url('/subscription') }}"><i class="dwg-book mt-n1"></i>{{ __('Subscription') }}</a>
          @endif
          <a class="topbar-link ml-3 pl-3 border-left border-light d-none d-md-inline-block" href="{{ URL::to('/contact') }}"><i class="dwg-support mt-n1"></i>{{ __('Contact') }}</a>
          @if($allsettings->site_blog_display == 1)
          <a class="topbar-link ml-3 border-left border-light pl-3 d-none d-md-inline-block" href="{{ URL::to('/blog') }}"><i class="dwg-image mt-n1"></i>{{ __('Blog') }}</a>
          @endif
          </div>
        </div>
      </div>
      @endif
      <!-- Remove "navbar-sticky" class to make navigation bar scrollable with the page.-->
      <div class="navbar-sticky">
        <div class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="container">
          @if($allsettings->site_logo != '')
          <a class="navbar-brand d-none d-sm-block mr-4 order-lg-1" href="{{ URL::to('/') }}" style="min-width: 7rem;">
             <img class="lazy" width="{{ $addition_settings->site_desktop_logo_width }}" height="{{ $addition_settings->site_desktop_logo_height }}" src="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}"  alt="{{ $allsettings->site_title }}"/>
          </a>
          @endif
          @if($allsettings->site_logo != '')
          <a class="navbar-brand d-sm-none mr-2 order-lg-1" href="{{ URL::to('/') }}" style="min-width: 4.625rem;">
             <img class="lazy" width="{{ $addition_settings->site_mobile_logo_width }}" height="{{ $addition_settings->site_mobile_logo_height }}" src="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}"  alt="{{ $allsettings->site_title }}"/>
          </a>
          @endif
            <div class="navbar-toolbar d-flex align-items-center order-lg-3">
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"><span class="navbar-toggler-icon"></span></button><a class="navbar-tool d-none d-lg-flex" href="#searchBox" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="searchBox"><span class="navbar-tool-tooltip">{{ __('Search') }}</span>
                <div class="navbar-tool-icon-box"><i class="navbar-tool-icon dwg-search"></i></div></a>
                <a class="navbar-tool d-none d-lg-flex" href="{{ URL::to('/favourites') }}"><span class="navbar-tool-tooltip">{{ __('Favourites') }}</span>
                <div class="navbar-tool-icon-box"><i class="navbar-tool-icon dwg-heart"></i></div></a>
                @if(Auth::guest())
                <a class="navbar-tool ml-1 mr-n1" href="{{ URL::to('/login') }}"><span class="navbar-tool-tooltip">{{ __('account') }}</span>
                <div class="navbar-tool-icon-box"><i class="navbar-tool-icon dwg-user"></i></div></a>
                @endif
                @if (Auth::check())
                <div class="navbar-tool dropdown ml-2">
                <a class="navbar-tool-icon-box border remove-hyper dropdown-toggle" data-toggle="dropdown" @if(Auth::user()->id == 1) href="{{ url('/admin') }}" target="_blank" @else href="{{ URL::to('/user') }}/{{ Auth::user()->username }}" @endif>         @if(!empty(Auth::user()->user_photo))
                <img class="lazy" width="32" height="32" src="{{ url('/') }}/public/storage/users/{{ Auth::user()->user_photo }}"  alt="{{ Auth::user()->name }}"/>
                @else
                <img class="lazy" width="32" height="32" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ Auth::user()->name }}">
                @endif
                </a>
                <a class="navbar-tool-text ml-n1" @if(Auth::user()->id == 1) href="{{ url('/admin') }}" target="_blank" @else href="{{ URL::to('/user') }}/{{ Auth::user()->username }}" @endif>
                <small>{{ Auth::user()->name }}</small>{{ Helper::price_format($allsettings->site_currency_position,Auth::user()->earnings,$currency_symbol,$multicurrency) }}
                </a>
                <div class="dropdown-menu dropdown-menu-right" style="min-width: 14rem;">
                @if(Auth::user()->user_type == 'vendor')
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/user') }}/{{ Auth::user()->username }}"><i class="dwg-home opacity-60 mr-2"></i>{{ __('Profile') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/profile-settings') }}"><i class="dwg-settings opacity-60 mr-2"></i>{{ __('Setting') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/purchases') }}"><i class="dwg-basket opacity-60 mr-2"></i>{{ __('Purchase') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/favourites') }}"><i class="dwg-heart opacity-60 mr-2"></i>{{ __('Favourite') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/coupon') }}"><i class="dwg-gift opacity-60 mr-2"></i>{{ __('Coupon') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/sales') }}"><i class="dwg-cart opacity-60 mr-2"></i>{{ __('Sales') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/manage-item') }}"><i class="dwg-briefcase opacity-60 mr-2"></i>{{ __('Manage Items') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/deposit') }}"><i class="dwg-money-bag opacity-60 mr-2"></i>{{ __('Deposit') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/withdrawal') }}"><i class="dwg-currency-exchange opacity-60 mr-2"></i>{{ __('Withdrawals') }}</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item d-flex align-items-center" href="{{ url('/logout') }}"><i class="dwg-sign-out opacity-60 mr-2"></i>{{ __('Logout') }}</a>
                @endif
                @if(Auth::user()->user_type == 'customer')
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/user') }}/{{ Auth::user()->username }}"><i class="dwg-home opacity-60 mr-2"></i>{{ __('Profile') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/profile-settings') }}"><i class="dwg-settings opacity-60 mr-2"></i>{{ __('Setting') }}</a> 
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/purchases') }}"><i class="dwg-basket opacity-60 mr-2"></i>{{ __('Purchase') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/favourites') }}"><i class="dwg-heart opacity-60 mr-2"></i>{{ __('Favourite') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/deposit') }}"><i class="dwg-money-bag opacity-60 mr-2"></i>{{ __('Deposit') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ URL::to('/withdrawal') }}"><i class="dwg-currency-exchange opacity-60 mr-2"></i>{{ __('Withdrawals') }}</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item d-flex align-items-center" href="{{ url('/logout') }}"><i class="dwg-sign-out opacity-60 mr-2"></i>{{ __('Logout') }}</a>
                @endif
                @if(Auth::user()->user_type == 'admin')
                <a class="dropdown-item d-flex align-items-center" href="{{ url('/admin') }}"><i class="dwg-settings opacity-60 mr-2"></i>{{ __('Admin Panel') }}</a>
                <a class="dropdown-item d-flex align-items-center" href="{{ url('/logout') }}"><i class="dwg-sign-out opacity-60 mr-2"></i>{{ __('Logout') }}</a>
                @endif
              </div>
              </div>
              @endif
              @if (Auth::check())
              @if(Auth::user()->user_type != 'admin')
              @if($addition_settings->conversation_mode == 1)
              <div class="navbar-tool dropdown ml-3"><a class="navbar-tool-icon-box bg-secondary dropdown-toggle" href="{{ URL::to('/messages') }}"><span class="navbar-tool-label">{{ $msgcount }}</span><i class="navbar-tool-icon dwg-chat"></i></a>
                <!-- Cart dropdown-->
                @if($msgcount != 0)
                <div class="dropdown-menu dropdown-menu-right" style="width: 20rem;">
                  <div class="widget widget-cart px-3 pt-2 pb-3">
                    <div data-simplebar data-simplebar-auto-hide="false">
                      @foreach($smsdata['display'] as $msg)
                      <div class="widget-cart-item pb-2 mb-2 border-bottom">
                        <div class="media align-items-center">
                        <a class="d-block mr-2" href="{{ url('/messages') }}/type/{{ $msg->username }}">
                        @if($msg->user_photo!='')
                        <img class="lazy rounded-circle" width="40" height="40" src="{{ url('/') }}/public/storage/users/{{ $msg->user_photo }}"  alt="{{ $msg->username }}"/>
                        @else
                        <img  class="lazy rounded-circle" width="40" height="40" src="{{ url('/') }}/public/img/no-image.png"  alt="{{ $msg->username }}"/>
                        @endif
                        </a>
                          <div class="media-body">
                            <h6 class="widget-product-title"><a href="{{ url('/messages') }}/type/{{ $msg->username }}">{{ $msg->username }}</a></h6>
                            <div class="widget-product-meta"><span class="mr-2">{{ Helper::timeAgo(strtotime($msg->conver_date)) }}</span></div>
                          </div>
                        </div>
                      </div>
                      @endforeach
                     </div>
                    <a class="btn btn-primary btn-sm btn-block" href="{{ url('/messages') }}"><i class="dwg-chat mr-2 font-size-base align-middle"></i>{{ __('View All Messages') }}</a>
                  </div>
                </div>
                @endif
                
              </div>
              @endif
              @endif
              @endif
              <div class="navbar-tool dropdown ml-3"><a class="navbar-tool-icon-box bg-secondary dropdown-toggle" href="{{ url('/cart') }}"><span class="navbar-tool-label">{{ $cartcount }}</span><i class="navbar-tool-icon dwg-cart"></i></a>
                <!-- Cart dropdown-->
                @if($cartcount != 0)
                <div class="dropdown-menu dropdown-menu-right" style="width: 20rem;">
                  <div class="widget widget-cart px-3 pt-2 pb-3">
                    <div data-simplebar data-simplebar-auto-hide="false">
                      @php $subtotall = 0; @endphp
                      @foreach($cartitem['item'] as $cart)
                      @php 
                      $itemprice = $cart->item_single_price * $cart->item_serial_stock;
                      @endphp
                      <div class="widget-cart-item pb-2 mb-2 border-bottom">
                        <a href="{{ url('/cart') }}/{{ base64_encode($cart->ord_id) }}" class="close text-danger" onClick="return confirm('{{ __('Are you sure you want to delete?') }}');"><span aria-hidden="true">&times;</span></a>
                        <div class="media align-items-center"><a class="d-block mr-2" href="{{ url('/item') }}/{{ $cart->item_slug }}">
                        @if($cart->item_thumbnail!='')
                        <img class="lazy" width="64" height="64" src="{{ Helper::Image_Path($cart->item_thumbnail,'no-image.png') }}"  alt="{{ $cart->item_name }}"/>
                        @else
                        <img class="lazy" width="64" height="64" src="{{ url('/') }}/public/img/no-image.png"  alt="{{ $cart->item_name }}"/>
                        @endif
                        </a>
                          <div class="media-body">
                            <h6 class="widget-product-title"><a href="{{ url('/item') }}/{{ $cart->item_slug }}">
							@if($addition_settings->item_name_limit != 0)
							{{ mb_substr($cart->item_name,0,$addition_settings->item_name_limit,'utf-8').'...' }}
							@else
							{{ $cart->item_name }}	  
							@endif
							</a></h6>
                            <div class="widget-product-meta"><span class="text-accent mr-2">{{ Helper::price_format($allsettings->site_currency_position,$itemprice,$currency_symbol,$multicurrency) }}</span></div>
                          </div>
                        </div>
                      </div>
                      @php $subtotall += $itemprice; @endphp
                      @endforeach
                     </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
                      <div class="font-size-sm mr-2 py-2"><span class="text-muted">{{ __('Total') }}:</span><span class="text-accent font-size-base ml-1">{{ Helper::price_format($allsettings->site_currency_position,$subtotall,$currency_symbol,$multicurrency) }}</span></div><a class="btn btn-outline-secondary btn-sm" href="{{ url('/cart') }}">{{ __('View Cart') }}<i class="dwg-arrow-right ml-1 mr-n1"></i></a></div><a class="btn btn-primary btn-sm btn-block" href="{{ url('/checkout') }}"><i class="dwg-card mr-2 font-size-base align-middle"></i>{{ __('Checkout') }}</a>
                  </div>
                </div>
                @endif
              </div>
            </div>
            <div class="collapse navbar-collapse mr-auto order-lg-2" id="navbarCollapse">
              <!-- Search-->
              <div class="input-group-overlay d-lg-none my-3">
                <div class="input-group-prepend-overlay"><span class="input-group-text"><i class="dwg-search"></i></span></div>
                <form action="{{ route('shop') }}" id="search_form1" method="post"  enctype="multipart/form-data">
                {{ csrf_field() }}
                <input class="form-control prepended-form-control" type="text" name="product_item" placeholder="{{ __('Search your products...') }}">
                </form>
              </div>
              <!-- Primary menu-->
              <ul class="navbar-nav">
                <?php /*?><li class="nav-item dropdown"><a class="nav-link" href="{{ URL::to('/') }}">{{ __('Home') }}</a>
                </li><?php */?>
                <?php /*?><li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown">{{ __('Categories') }}</a>
                  <ul class="dropdown-menu">
                   @foreach($categories['menu'] as $menu)
                    <li class="dropdown">
                    <a @if(count($menu->subcategory) != 0)  class="mobiledev dropdown-item dropdown-toggle" data-toggle="dropdown" @else class="mobiledev dropdown-item" @endif href="{{ URL::to('/shop/category/') }}/{{$menu->category_slug}}">{{ $menu->category_name }}</a>
                    <a @if(count($menu->subcategory) != 0)  class="desktopdev dropdown-item dropdown-toggle"  @else class="desktopdev dropdown-item" @endif href="{{ URL::to('/shop/category/') }}/{{$menu->category_slug}}">{{ $menu->category_name }}</a>
                      @if(count($menu->subcategory) != 0)
                      <ul class="dropdown-menu">
                        @foreach($menu->subcategory as $sub_category)
                        <li><a class="dropdown-item" href="{{ URL::to('/shop/subcategory/') }}/{{$sub_category->subcategory_slug}}">{{ $sub_category->subcategory_name }}</a></li>
                        @endforeach
                      </ul>
                      @endif
                    </li>
                    <li class="dropdown-divider"></li>
                   @endforeach  
                  </ul>
                </li><?php */?>
                <?php /* Desktop or laptop menu */ ?>
                <li class="nav-item dropdown d-none d-md-block"><a class="nav-link dropdown-toggle" href="{{ url('/shop') }}">{{ __('All Items') }}</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ url('/') }}/featured-items">{{ __('Featured Items') }}</a></li>
                    <li><a class="dropdown-item" href="{{ url('/free-items') }}">{{ __('Free Items') }}</a></li>
                    <li><a class="dropdown-item" href="{{ url('/') }}/new-releases">{{ __('New Releases') }}</a></li>
                    <li><a class="dropdown-item" href="{{ url('/') }}/popular-items">{{ __('Popular Items') }}</a></li>
                    @if($addition_settings->subscription_mode == 1)
                    <li><a class="dropdown-item" href="{{ url('/') }}/subscriber-downloads">{{ __('Subscriber Downloads') }}</a></li>
                    @endif
                    <li><a class="dropdown-item" href="{{ url('/top-authors') }}">{{ __('Top Authors') }}</a></li>
                   </ul>
                </li>
                @if(count($categories['menu']) != 0)
                @foreach($categories['menu'] as $menu)
                <li class="nav-item dropdown d-none d-md-block"><a class="nav-link dropdown-toggle" href="{{ URL::to('/shop/category/') }}/{{$menu->category_slug}}">{{ $menu->category_name }}</a>
                  @if(count($menu->subcategory) != 0)
                  <ul class="dropdown-menu">
                    @foreach($menu->subcategory as $sub_category)
                    <li><a class="dropdown-item" href="{{ URL::to('/shop/subcategory/') }}/{{$sub_category->subcategory_slug}}">{{ $sub_category->subcategory_name }}</a></li>
                    @endforeach
                  </ul>
                  @endif
                </li>
                @endforeach
                @endif
                <?php /* Desktop or laptop menu */ ?>
                <?php /* Mobile menu */ ?>
                <li class="nav-item dropdown d-md-none"><a class="nav-link dropdown-toggle" href="{{ url('/shop') }}" data-toggle="dropdown">{{ __('All Items') }}</a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ url('/featured-items') }}">{{ __('Featured Items') }}</a></li>
                    <li><a class="dropdown-item" href="{{ url('/free-items') }}">{{ __('Free Items') }}</a></li>
                    <li><a class="dropdown-item" href="{{ url('/new-releases') }}">{{ __('New Releases') }}</a></li>
                    <li><a class="dropdown-item" href="{{ url('/popular-items') }}">{{ __('Popular Items') }}</a></li>
                    @if($addition_settings->subscription_mode == 1)
                    <li><a class="dropdown-item" href="{{ url('/subscriber-downloads') }}">{{ __('Subscriber Downloads') }}</a></li>
                    @endif
                    <li><a class="dropdown-item" href="{{ url('/top-authors') }}">{{ __('Top Authors') }}</a></li>
                   </ul>
                </li>
                @if(count($categories['menu']) != 0)
                @foreach($categories['menu'] as $menu)
                <li class="nav-item dropdown d-md-none"><a class="nav-link dropdown-toggle" href="{{ URL::to('/shop/category/') }}/{{$menu->category_slug}}" data-toggle="dropdown">{{ $menu->category_name }}</a>
                  @if(count($menu->subcategory) != 0)
                  <ul class="dropdown-menu">
                    @foreach($menu->subcategory as $sub_category)
                    <li><a class="dropdown-item" href="{{ URL::to('/shop/subcategory/') }}/{{$sub_category->subcategory_slug}}">{{ $sub_category->subcategory_name }}</a></li>
                    @endforeach
                  </ul>
                  @endif
                </li>
                @endforeach
                @endif
                <?php /* Mobile menu */ ?>
                @if(count($allpages['pages']) != 0)
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown">{{ __('Pages') }}</a>
                  <ul class="dropdown-menu">
                    @foreach($allpages['pages'] as $pages)
                    <li><a class="dropdown-item" href="{{ URL::to('/') }}/{{ $pages->page_slug }}">{{ $pages->page_title }}</a></li>
                    @endforeach
                  </ul>
                </li>
                @endif
                
                
                <li class="nav-item dropdown"><a class="nav-link" href="{{ URL::to('/flash-sale') }}">{{ __('Flash Sale') }}</a></li>
               </ul>
            </div>
          </div>
        </div>
        <!-- Search collapse-->
        <div class="search-box collapse" id="searchBox">
          <div class="card pt-2 pb-4 border-0 rounded-0">
            <div class="container">
              <div class="input-group-overlay">
                <div class="input-group-prepend-overlay"><span class="input-group-text"><i class="dwg-search"></i></span></div>
                <form action="{{ route('shop') }}" id="search_form2" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input class="form-control prepended-form-control" type="text" name="product_item" id="product_item_top" placeholder="{{ __('Search your products...') }}">
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>