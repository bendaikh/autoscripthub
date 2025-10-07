<div class="menu-area">
       <div class="top-menu-area theme-primary">
            <div class="container">
                <div class="row">
                   <div class="col-lg-3 col-md-3 col-6 v_middle">
                        <div class="logo">
                            @if($allsettings->site_logo != '')
                            <a href="{{ URL::to('/') }}">
                                <img src="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}" alt="{{ $allsettings->site_title }}" width="{{ $addition_settings->site_desktop_logo_width }}"  class="img-fluid">
                            </a>
                            @endif
                        </div>
                    </div>
                    @if(Auth::guest())
                    <div class="col-lg-9 col-md-9 col-6 v_middle">
                        <!-- start .author-area -->
                        <div class="author-area">
                            <div class="author__notification_area">
                                <ul class="topmenu">
                                   @if($addition_settings->verify_mode == 1)
                                   <li><a href="{{ URL::to('/verify') }}">{{ __('Verify Purchase') }}</a></li>
                                   @endif
                                   @if(Auth::guest())
                                   <li><a href="{{ URL::to('/start-selling') }}">{{ __('Start Selling') }}</a></li>
                                    @else
                                    @if(Auth::user()->user_type == 'vendor')
                                    <li><a href="{{ URL::to('/manage-item') }}">{{ __('Start Selling') }}</a></li>
                                    @endif
                                    @endif
                                    @if($allsettings->site_blog_display == 1)
                                    <li>
                                        <a href="{{ URL::to('/blog') }}">{{ __('Blog') }}</a>
                                    </li>
                                    @endif
                                    <li>
                                        <a href="{{ URL::to('/contact') }}">{{ __('Contact') }}</a>
                                    </li>
                                    <li class="has_dropdown">
                                        <div class="icon_wrap">
                                            <span class="dwg-cart"></span>
                                            <span class="notification_count purch theme-button">{{ $cartcount }}</span>
                                        </div>
                                        @if($cartcount != 0)
                                        <div class="dropdowns dropdown--cart carbars">
                                            <div class="cart_area">
                                            @php $subtotall = 0; @endphp
                                            @foreach($cartitem['item'] as $cart)
                                            @php 
                                              $itemprice = $cart->item_single_price * $cart->item_serial_stock;
                                              @endphp
                                                <div class="cart_product">
                                                    <div class="product__info">
                                                        <div class="thumbn">
                                                        <a href="{{ url('/item') }}/{{ $cart->item_slug }}">
                                                            @if($cart->item_thumbnail!='')
                                            <img src="{{ Helper::Image_Path($cart->item_thumbnail,'no-image.png') }}" alt="{{ $cart->item_name }}" class="cart-thumb-small">
                                            @else
                                            <img src="{{ url('/') }}/public/img/no-image.png" alt="{{ $cart->item_name }}" class="cart-thumb-small">
                                            @endif
                                            </a>
                                            </div>
                                            <div class="info">
                                              <a class="title" href="{{ url('/item') }}/{{ $cart->item_slug }}">
                                              @if($addition_settings->item_name_limit != 0)
                                                {{ mb_substr($cart->item_name,0,$addition_settings->item_name_limit,'utf-8').'...' }}
                                                @else
                                                {{ $cart->item_name }}	  
                                                @endif
                                              </a>
                                                            <div class="cat">
                                                                <a href="{{ URL::to('/shop') }}/item-type/{{ $cart->item_type }}" class="theme-color">
                                                                  <span class="dwg-time theme-color"></span> {{ str_replace('-',' ',$cart->item_type) }}</a>         
                                                            </div>
                                                        </div>
                                                     </div>
                                                     <div class="product__action">
                                                        <a href="{{ url('/cart') }}/{{ base64_encode($cart->ord_id) }}" onClick="return confirm('{{ __('Are you sure you want to delete?') }}');"><span class="dwg-trash"></span>
                                                        </a>
                                                       <p>{{ Helper::price_format($allsettings->site_currency_position,$itemprice,$currency_symbol,$multicurrency) }}</p>
                                                    </div>
                                                </div>
                                                @php $subtotall += $itemprice; @endphp
                                               @endforeach 
                                                <div class="total">
                                                    <p>
                                                        <span>{{ __('Total') }} :</span>{{ Helper::price_format($allsettings->site_currency_position,$subtotall,$currency_symbol,$multicurrency) }}</p>
                                                </div>
                                                <div class="cart_action">
                                                    <a class="go_cart" href="{{ url('/cart') }}">{{ __('View Cart') }}</a>
                                                    <a class="go_checkout" href="{{ url('/checkout') }}">{{ __('Checkout') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if($cartcount == 0)
                                        <div class="dropdowns dropdown--cart">
                                            <div class="cart_area">
                                             <p align="center" class="emptycart">{{ __('Your cart is empty!') }}</p> 
                                            </div>
                                        </div>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                            @if($allsettings->multi_language == 1)
                            <div @if(Auth::guest()) class="inline mover15 mover-top10 has_dropdown" @else class="inline au-mover15 au-mover-top10 has_dropdown" @endif>
                                <div class="autor__info">
                                    <p class="name">
                                        {{ $current_locale }}
                                    </p>
                                 </div>
                                 <div class="dropdowns dropdown--author mt-3">
                                    <ul>
                                     @foreach($available_locales as $locale_name => $available_locale) 
                                     <li>
                                            <a href="{{ URL::to('/language') }}/{{ $available_locale }}">{{ $locale_name }}</a>
                                      </li>
                                      @endforeach
                                     </ul>
                                </div>
                            </div>
                            @endif
                            @if($addition_settings->multi_currency == 1)
                            <div @if(Auth::guest()) class="inline mover15 mover-top10 has_dropdown" @else class="inline au-mover15 au-mover-top10 has_dropdown" @endif>
                                <div class="autor__info">
                                    <p class="name">
                                        {{ $currency_title }}
                                    </p>
                                 </div>
                                 <div class="dropdowns dropdown--author mt-3">
                                    <ul>
                                     @foreach($currencyview as $currency) 
                                     <li>
                                            <a href="{{ URL::to('/currency') }}/{{ $currency->currency_code }}">{{ $currency->currency_code }} ({{ $currency->currency_symbol }})</a>
                                      </li>
                                      @endforeach
                                     </ul>
                                </div>
                            </div>
                            @endif
                            <span class="login-btn">
                            <a href="{{ url('/register') }}" class="btn btn--icon btn-ss radius-left md-login mdevice-off">{{ __('Create Account') }}</a>
                            <a href="{{ url('/login') }}" class="btn btn--icon btn-ss radius-right md-login">{{ __('Sign In') }}</a>
                            </span>
                         </div>
                   </div>
                   @endif         
                   @if (Auth::check())
                    <div class="col-lg-9 col-md-9 col-6 v_middle">
                        <div class="author-area">
                            <div class="author__notification_area">
                                <ul class="topmenu">
                                  @if($addition_settings->verify_mode == 1)
                                   <li><a href="{{ URL::to('/verify') }}">{{ __('Verify Purchase') }}</a></li>
                                   @endif 
                                  @if(Auth::user()->user_type == 'vendor')
                                  <li>
                                       <a href="{{ URL::to('/manage-item') }}">{{ __('Start Selling') }}</a>
                                    </li>
                                    @endif
                                    @if($allsettings->site_blog_display == 1)
                                    <li>
                                        <a href="{{ URL::to('/blog') }}">{{ __('Blog') }}</a>
                                    </li>
                                    @endif
                                    <li>
                                        <a href="{{ URL::to('/contact') }}">{{ __('Contact') }}</a>
                                    </li>
                                    @if (Auth::check())
                                    @if(Auth::user()->user_type != 'admin')
                                    @if($addition_settings->conversation_mode == 1)
                                    <li class="has_dropdown">
                                        <div class="icon_wrap">
                                            <a href="{{ URL::to('/messages') }}"><span class="dwg-chat"></span></a>
                                            <span class="notification_count purch theme-button">{{ $msgcount }}</span>
                                        </div>
                                        @if($msgcount != 0)
                                        <div class="dropdowns dropdown--cart carbars">
                                            <div class="cart_area">
                                            @foreach($smsdata['display'] as $msg)
                                                <div class="cart_product">
                                                    <div class="product__info">
                                                        <div class="thumbn">
                                                        <a href="{{ url('/messages') }}/type/{{ $msg->username }}">
                                                           @if($msg->user_photo!='')
                                            <img src="{{ url('/') }}/public/storage/users/{{ $msg->user_photo }}" alt="{{ $msg->username }}" class="cart-thumb-small">
                                            @else
                                            <img src="{{ url('/') }}/public/img/no-image.png" alt="{{ $msg->username }}" class="cart-thumb-small">
                                            @endif
                                            </a>
                                            </div>
                                            <div class="info">
                                              <a class="title" href="{{ url('/messages') }}/type/{{ $msg->username }}">
                                              {{ $msg->username }}
                                              </a>
                                                            <div class="cat">
                                                                
                                                                  <span class="dwg-time"></span> {{ Helper::timeAgo(strtotime($msg->conver_date)) }}       
                                                            </div>
                                                        </div>
                                                     </div>
                                                </div>
                                               @endforeach 
                                                <div class="cart_action">
                                                    <a class="go_checkout fullwidth" href="{{ url('/messages') }}">{{ __('View All Messages') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if($msgcount == 0)
                                        <div class="dropdowns dropdown--cart">
                                            <div class="cart_area">
                                               <p align="center" class="emptycart">{{ __('No message found!') }}</p> 
                                            </div>
                                        </div>
                                        @endif
                                    </li>
                                    @endif
                                    @endif
                                    @endif
                                    @if(Auth::user()->id != 1)
                                    <li class="has_dropdown">
                                        <div class="icon_wrap">
                                            <a href="{{ url('/cart') }}"><span class="dwg-cart"></span></a>
                                            <span class="notification_count purch theme-button">{{ $cartcount }}</span>
                                        </div>
                                        @if($cartcount != 0)
                                        <div class="dropdowns dropdown--cart carbars">
                                            <div class="cart_area">
                                            @php $subtotall = 0; @endphp
                                            @foreach($cartitem['item'] as $cart)
                                            @php 
                                              $itemprice = $cart->item_single_price * $cart->item_serial_stock;
                                              @endphp
                                                <div class="cart_product">
                                                    <div class="product__info">
                                                        <div class="thumbn">
                                                        <a href="{{ url('/item') }}/{{ $cart->item_slug }}">
                                                            @if($cart->item_thumbnail!='')
                                            <img src="{{ Helper::Image_Path($cart->item_thumbnail,'no-image.png') }}" alt="{{ $cart->item_name }}" class="cart-thumb-small">
                                            @else
                                            <img src="{{ url('/') }}/public/img/no-image.png" alt="{{ $cart->item_name }}" class="cart-thumb-small">
                                            @endif
                                            </a>
                                            </div>
                                            <div class="info">
                                              <a class="title" href="{{ url('/item') }}/{{ $cart->item_slug }}">
                                              @if($addition_settings->item_name_limit != 0)
                                                {{ mb_substr($cart->item_name,0,$addition_settings->item_name_limit,'utf-8').'...' }}
                                                @else
                                                {{ $cart->item_name }}	  
                                                @endif
                                              </a>
                                                            <div class="cat">
                                                                <a href="{{ URL::to('/shop') }}/item-type/{{ $cart->item_type }}" class="theme-color">
                                                                  <span class="dwg-time theme-color"></span> {{ str_replace('-',' ',$cart->item_type) }}</a>         
                                                            </div>
                                                        </div>
                                                     </div>
                                                     <div class="product__action">
                                                        <a href="{{ url('/cart') }}/{{ base64_encode($cart->ord_id) }}" onClick="return confirm('{{ __('Are you sure you want to delete?') }}');"><span class="dwg-trash"></span>
                                                        </a>
                                                       <p>{{ Helper::price_format($allsettings->site_currency_position,$itemprice,$currency_symbol,$multicurrency) }}</p>
                                                    </div>
                                                </div>
                                                @php $subtotall += $itemprice; @endphp
                                               @endforeach 
                                                <div class="total">
                                                    <p>
                                                        <span>{{ __('Total') }} :</span>{{ Helper::price_format($allsettings->site_currency_position,$subtotall,$currency_symbol,$multicurrency) }}</p>
                                                </div>
                                                <div class="cart_action">
                                                    <a class="go_cart" href="{{ url('/cart') }}">{{ __('View Cart') }}</a>
                                                    <a class="go_checkout" href="{{ url('/checkout') }}">{{ __('Checkout') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @if($cartcount == 0)
                                        <div class="dropdowns dropdown--cart">
                                            <div class="cart_area">
                                               <p align="center" class="emptycart">{{ __('Your cart is empty!') }}</p> 
                                            </div>
                                        </div>
                                        @endif
                                    </li>
                                    @endif
                                </ul>
                            </div>
                            @if($allsettings->multi_language == 1)
                            <div @if(Auth::guest()) class="inline mover15 mover-top10 has_dropdown" @else class="inline au-mover15 au-mover-top10 has_dropdown" @endif>
                               <div class="autor__info">
                                    <p class="name capletter">
                                        {{ $current_locale }}
                                    </p>
                                </div>
                                <div class="dropdowns dropdown--author mt-3">
                                    <ul>
                                     @foreach($available_locales as $locale_name => $available_locale) 
                                     <li>
                                            <a href="{{ URL::to('/language') }}/{{ $available_locale }}">{{ $locale_name }}</a>
                                      </li>
                                      @endforeach
                                     </ul>
                                </div>
                            </div>
                            @endif
                            @if($addition_settings->multi_currency == 1)
                            <div @if(Auth::guest()) class="inline mover15 mover-top10 has_dropdown" @else class="inline au-mover15 au-mover-top10 has_dropdown" @endif>
                                <div class="autor__info">
                                    <p class="name capletter">
                                        {{ $currency_title }}
                                    </p>
                                 </div>
                                 <div class="dropdowns dropdown--author mt-3">
                                    <ul>
                                     @foreach($currencyview as $currency) 
                                     <li>
                                            <a href="{{ URL::to('/currency') }}/{{ $currency->currency_code }}">{{ $currency->currency_code }} ({{ $currency->currency_symbol }})</a>
                                      </li>
                                      @endforeach
                                     </ul>
                                </div>
                            </div>
                            @endif
                            <div class="author-author__info inline has_dropdown removers">
                                <div class="author__avatar">
                                    @if(Auth::user()->user_photo != '')
                                            <img src="{{ url('/') }}/public/storage/users/{{ Auth::user()->user_photo }}" alt="{{ Auth::user()->name }}">
                                            @else
                                            <img src="{{ url('/') }}/public/img/no-user.png" alt="{{ Auth::user()->name }}">
                                            @endif

                                </div>
                                <div class="autor__info">
                                    <p class="name">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="ammount">{{ Helper::price_format($allsettings->site_currency_position,Auth::user()->earnings,$currency_symbol,$multicurrency) }}</p>
                                </div>
                                <div class="dropdowns dropdown--author">
                                    <ul>
                                      @if(Auth::user()->user_type == 'admin')
                                      <li>
                                            <a href="{{ URL::to('/admin') }}" target="_blank">
                                                <i class="dwg-settings opacity-60 mr-2"></i>{{ __('Admin Panel') }}</a>
                                      </li>
                                      <li>
                                            <a href="{{ url('/logout') }}">
                                                <i class="dwg-sign-out opacity-60 mr-2"></i>{{ __('Logout') }}</a>
                                      </li>
                                      @endif
                                      @if(Auth::user()->user_type == 'vendor')
                                      <li>
                                            <a href="{{ URL::to('/user') }}/{{ Auth::user()->username }}">
                                                <i class="dwg-home opacity-60 mr-2"></i>{{ __('Profile') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/profile-settings') }}">
                                                <i class="dwg-settings opacity-60 mr-2"></i>{{ __('Setting') }}
                                             </a>   
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/purchases') }}">
                                                <i class="dwg-basket opacity-60 mr-2"></i>{{ __('Purchase') }}
                                            </a>    
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/favourites') }}">
                                                <i class="dwg-heart opacity-60 mr-2"></i>{{ __('Favourite') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/coupon') }}">
                                                <i class="dwg-gift opacity-60 mr-2"></i>{{ __('Coupon') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/sales') }}">
                                                <i class="dwg-cart opacity-60 mr-2"></i>{{ __('Sales') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/manage-item') }}">
                                                <i class="dwg-briefcase opacity-60 mr-2"></i>{{ __('Manage Items') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/deposit') }}">
                                                <i class="dwg-money-bag opacity-60 mr-2"></i>{{ __('Deposit') }}</a>
                                        </li>
                                        
                                        <li>
                                            <a href="{{ URL::to('/withdrawal') }}">
                                                <i class="dwg-currency-exchange opacity-60 mr-2"></i>{{ __('Withdrawals') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ url('/logout') }}">
                                                <i class="dwg-sign-out opacity-60 mr-2"></i>{{ __('Logout') }}</a>
                                        </li>
                                      @endif
                                      @if(Auth::user()->user_type == 'customer') 
                                      <li>
                                            <a href="{{ URL::to('/user') }}/{{ Auth::user()->username }}">
                                                <i class="dwg-home opacity-60 mr-2"></i>{{ __('Profile') }}</a>
                                      </li>
                                      <li>
                                            <a href="{{ URL::to('/profile-settings') }}">
                                                <i class="dwg-settings opacity-60 mr-2"></i>{{ __('Setting') }}</a>
                                      </li>
                                      <li>
                                            <a href="{{ URL::to('/purchases') }}">
                                                <i class="dwg-basket opacity-60 mr-2"></i>{{ __('Purchase') }}</a>
                                      </li>
                                      <li>
                                            <a href="{{ URL::to('/favourites') }}">
                                                <i class="dwg-heart opacity-60 mr-2"></i>{{ __('Favourite') }}</a>
                                      </li>
                                      <li>
                                            <a href="{{ URL::to('/deposit') }}">
                                               <i class="dwg-money-bag opacity-60 mr-2"></i>{{ __('Deposit') }}</a>
                                      </li>
                                      
                                      <li>
                                            <a href="{{ URL::to('/withdrawal') }}">
                                                <i class="dwg-currency-exchange opacity-60 mr-2"></i>{{ __('Withdrawals') }}</a>
                                      </li>
                                      <li>
                                            <a href="{{ url('/logout') }}">
                                                <i class="dwg-sign-out opacity-60 mr-2"></i>{{ __('Logout') }}</a>
                                      </li> 
                                      @endif
                                    </ul>
                                </div>
                            </div>
                         </div>
                        <div class="mobile_content ">
                            <span class="dwg-user menu_icon"></span>
                             <!-- offcanvas menu -->
                            <div class="offcanvas-menu closed">
                                <i class="dwg-close close_menu"></i>
                                <div class="author-author__info">
                                    <div class="author__avatar v_middle">
                                        @if(Auth::user()->user_photo != '')
                                            <img src="{{ url('/') }}/public/storage/users/{{ Auth::user()->user_photo }}" alt="{{ Auth::user()->name }}">
                                            @else
                                            <img src="{{ url('/') }}/public/img/no-user.png" alt="{{ Auth::user()->name }}">
                                            @endif
                                    </div>
                                    <div class="autor__info v_middle">
                                        <p class="name">
                                            {{ Auth::user()->name }}
                                        </p>
                                        <p class="ammount">{{ Helper::price_format($allsettings->site_currency_position,Auth::user()->earnings,$currency_symbol,$multicurrency) }}</p>
                                    </div>
                                </div>
                                <div class="author__notification_area">
                                    <ul>
                                      <li>
                                            <a href="{{ url('/cart') }}">
                                                <div class="icon_wrap">
                                                    <span class="dwg-cart"></span>
                                                    <span class="notification_count purch">{{ $cartcount }}</span>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <!--start .author__notification_area -->
                                 <div class="dropdowns dropdown--author">
                                    <ul>
                                      @if(Auth::user()->user_type == 'admin')
                                      <li>
                                            <a href="{{ URL::to('/admin') }}" target="_blank">
                                                <i class="dwg-settings opacity-60 mr-2"></i>{{ __('Admin Panel') }}</a>
                                      </li>
                                      <li>
                                            <a href="{{ url('/logout') }}">
                                                <i class="dwg-sign-out opacity-60 mr-2"></i>{{ __('Logout') }}</a>
                                      </li>
                                      @endif
                                      @if(Auth::user()->user_type == 'vendor')
                                      <li>
                                            <a href="{{ URL::to('/user') }}/{{ Auth::user()->username }}">
                                                <i class="dwg-home opacity-60 mr-2"></i>{{ __('Profile') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/profile-settings') }}">
                                                <i class="dwg-settings opacity-60 mr-2"></i>{{ __('Setting') }}
                                             </a>   
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/purchases') }}">
                                                <i class="dwg-basket opacity-60 mr-2"></i>{{ __('Purchase') }}
                                            </a>    
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/favourites') }}">
                                                <i class="dwg-heart opacity-60 mr-2"></i>{{ __('Favourite') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/coupon') }}">
                                                <i class="dwg-gift opacity-60 mr-2"></i>{{ __('Coupon') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/sales') }}">
                                                <i class="dwg-cart opacity-60 mr-2"></i>{{ __('Sales') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/manage-item') }}">
                                                <i class="dwg-briefcase opacity-60 mr-2"></i>{{ __('Manage Items') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ URL::to('/deposit') }}">
                                                <i class="dwg-money-bag opacity-60 mr-2"></i>{{ __('Deposit') }}</a>
                                        </li>
                                        
                                        <li>
                                            <a href="{{ URL::to('/withdrawal') }}">
                                                <i class="dwg-currency-exchange opacity-60 mr-2"></i>{{ __('Withdrawals') }}</a>
                                        </li>
                                        <li>
                                            <a href="{{ url('/logout') }}">
                                                <i class="dwg-sign-out opacity-60 mr-2"></i>{{ __('Logout') }}</a>
                                        </li>
                                      @endif
                                      @if(Auth::user()->user_type == 'customer') 
                                      <li>
                                            <a href="{{ URL::to('/user') }}/{{ Auth::user()->username }}">
                                                <i class="dwg-home opacity-60 mr-2"></i>{{ __('Profile') }}</a>
                                      </li>
                                      <li>
                                            <a href="{{ URL::to('/profile-settings') }}">
                                                <i class="dwg-settings opacity-60 mr-2"></i>{{ __('Setting') }}</a>
                                      </li>
                                      <li>
                                            <a href="{{ URL::to('/purchases') }}">
                                                <i class="dwg-basket opacity-60 mr-2"></i>{{ __('Purchase') }}</a>
                                      </li>
                                      <li>
                                            <a href="{{ URL::to('/favourites') }}">
                                                <i class="dwg-heart opacity-60 mr-2"></i>{{ __('Favourite') }}</a>
                                      </li>
                                      <li>
                                            <a href="{{ URL::to('/deposit') }}">
                                               <i class="dwg-money-bag opacity-60 mr-2"></i>{{ __('Deposit') }}</a>
                                      </li>
                                      
                                      <li>
                                            <a href="{{ URL::to('/withdrawal') }}">
                                                <i class="dwg-currency-exchange opacity-60 mr-2"></i>{{ __('Withdrawals') }}</a>
                                      </li>
                                      <li>
                                            <a href="{{ url('/logout') }}">
                                                <i class="dwg-sign-out opacity-60 mr-2"></i>{{ __('Logout') }}</a>
                                      </li> 
                                      @endif
                                    </ul>
                                </div>
                             </div>
                        </div>
                        <!-- end /.mobile_content -->
                    </div>
                    @endif
                </div>
                <!-- end /.row -->
            </div>
            <!-- end /.container -->
        </div>
        <!-- end  -->
        <!-- start .mainmenu_area -->
        <div class="mainmenu">
            <!-- start .container -->
            <div class="container">
              <!-- start .row-->
                <div class="row">
                    <!-- start .col-md-12 -->
                    <div class="col-md-12">
                        <div class="navbar-header">
                            <!-- start mainmenu__search -->
                            <div class="mainmenu__search">
                                <form action="{{ route('shop') }}" class="setting_form" method="post" id="profile_form" enctype="multipart/form-data">
                                {{ csrf_field() }} 
                                    <div class="searc-wrap">
                                      <input type="text" name="product_item" id="product_item_top" placeholder="{{ __('Search your products...') }}" class="nnrounded">
                                        <button type="submit" class="search-wrap__btn">
                                            <span class="dwg-search"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <!-- start mainmenu__search -->
                        </div>
                        <nav class="navbar navbar-expand-md navbar-light mainmenu__menu">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                                aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            <div class="collapse navbar-collapse" id="navbarNav">
                                <ul class="navbar-nav">
                                    
                                    <li class="has_dropdown">
                                        <a href="{{ url('/shop') }}">{{ __('All Items') }}</a>
                                        <div class="dropdowns dropdown--menu">
                                            <ul>
                                                <li>
                                                    <a href="{{ url('/featured-items') }}">{{ __('Featured Items') }}</a>
                                                </li>
                                                <li>
                                                    <a href="{{ url('/free-items') }}">{{ __('Free Items') }}</a>
                                                </li>
                                                
                                                <li>
                                                    <a href="{{ url('/new-releases') }}">{{ __('New Releases') }}</a>
                                                </li>
                                                
                                                <li>
                                                    <a href="{{ url('/popular-items') }}">{{ __('Popular Items') }}</a>
                                                </li>
                                                @if($addition_settings->subscription_mode == 1)
                                                <li>
                                                    <a href="{{ url('/subscriber-downloads') }}">{{ __('Subscriber Downloads') }}</a>
                                                </li>
                                                @endif
                                                <li>
                                                    <a href="{{ url('/top-authors') }}">{{ __('Top Authors') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    @if(count($categories['menu']) != 0)
                                    @foreach($categories['menu'] as $menu)
                                    <li class="has_dropdown">
                                        <a href="{{ URL::to('/shop/category/') }}/{{$menu->category_slug}}">{{ $menu->category_name }}</a>
                                        <div class="dropdowns dropdown--menu">
                                            <ul>
                                            @foreach($menu->subcategory as $sub_category)
                                                <li>
                                                    <a href="{{ URL::to('/shop/subcategory/') }}/{{$sub_category->subcategory_slug}}">{{ $sub_category->subcategory_name }}</a>
                                                </li>
                                              @endforeach  
                                            </ul>
                                        </div>
                                    </li>
                                   @endforeach 
                                   @endif
                                   @if(count($allpages['pages']) != 0)
                                   <li class="has_dropdown">
                                        <a href="javascript:void(0);">{{ __('Pages') }}</a>
                                        <div class="dropdowns dropdown--menu">
                                            <ul>
                                               @foreach($allpages['pages'] as $pages)
                                                <li>
                                                    <a href="{{ URL::to('/') }}/{{ $pages->page_slug }}">{{ $pages->page_title }}</a>
                                                </li>
                                              @endforeach
                                                
                                            </ul>
                                        </div>
                                    </li>
                                    @endif
                                    @if($addition_settings->subscription_mode == 1)
                                    <li><a href="{{ url('/subscription') }}">{{ __('Subscription') }}</a></li>
                                    @endif
                                    <li>
                                        <a href="{{ URL::to('/flash-sale') }}" class="red-color">{{ __('Flash Sale') }}</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- /.navbar-collapse -->
                        </nav>
                    </div>
                    <!-- end /.col-md-12 -->
                </div>
                <!-- end /.row-->
            </div>
            <!-- start .container -->
        </div>
        <!-- end /.mainmenu-->
    </div>