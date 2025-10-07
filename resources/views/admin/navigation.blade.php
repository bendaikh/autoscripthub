<aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                @if($allsettings->site_logo != '')
                <a class="navbar-brand" href="{{ url('/') }}"><img class="lazy" width="160" height="45" src="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_logo }}"   alt="{{ $allsettings->site_title }}"/></a>
                @else
                <a class="navbar-brand" href="{{ url('/') }}">{{ substr($allsettings->site_title,0,10) }}</a>
                @endif
                @if($allsettings->site_favicon != '')
                <a class="navbar-brand hidden" href="{{ url('/') }}"><img class="lazy" width="24" height="24" src="{{ url('/') }}/public/storage/settings/{{ $allsettings->site_favicon }}"   alt="{{ $allsettings->site_title }}"/></a>
                @else
                <a class="navbar-brand hidden" href="{{ url('/') }}">{{ substr($allsettings->site_title,0,1) }}</a>
                @endif
                <code>{{ Helper::Current_Version() }}</code>
            </div>
            @if($custom_settings->$dg_ver == 1)
            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    @if(in_array('dashboard',$avilable))
                    <li class="active">
                        <a href="{{ url('/admin') }}"> <i class="menu-icon fa fa-dashboard"></i>{{ __('Dashboard') }} </a>
                    </li>
                    @endif
                    @if(in_array('settings',$avilable))
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-gears"></i>{{ __('Settings') }}</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-gear"></i><a href="{{ url('/admin/general-settings') }}">{{ __('General Settings') }}</a></li>
                            <li><i class="fa fa-gear"></i><a href="{{ url('/admin/font-color-settings') }}">{{ __('Font') }} & {{ __('Color Settings') }}</a></li>
                            <li><i class="fa fa-gear"></i><a href="{{ url('/admin/currency-settings') }}">{{ __('Currency Settings') }}</a></li>
                            <li><i class="fa fa-gear"></i><a href="{{ url('/admin/country-settings') }}">{{ __('Country Settings') }}</a></li>
                            <li><i class="fa fa-gear"></i><a href="{{ url('/admin/email-settings') }}">{{ __('Email Settings') }}</a></li>
                            <li><i class="fa fa-gear"></i><a href="{{ url('/admin/media-settings') }}">{{ __('Media Settings') }}</a></li>
                            <li><i class="fa fa-gear"></i><a href="{{ url('/admin/badges-settings') }}">{{ __('Badges Settings') }}</a></li>
                            <li><i class="fa fa-gear"></i><a href="{{ url('/admin/payment-settings') }}">{{ __('Payment Settings') }}</a></li>
                            <li><i class="fa fa-gear"></i><a href="{{ url('/admin/social-settings') }}">{{ __('Social Settings') }}</a></li>
                            <li><i class="fa fa-gear"></i><a href="{{ url('/admin/limitation-settings') }}">{{ __('Limitation Settings') }}</a></li>
                            <li><i class="fa fa-gear"></i><a href="{{ url('/admin/preferred-settings') }}">{{ __('Preferred Settings') }}</a></li>
                            <li><i class="fa fa-gear"></i><a href="{{ url('/admin/pwa-settings') }}">{{ __('PWA Settings') }}</a></li>
                        </ul>
                    </li>
                   @endif
                   @if(Auth::user()->id == 1) 
                   <li class="menu-item-has-children dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-users"></i>{{ __('User Roles') }}</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-user"></i><a href="{{ url('/admin/administrator') }}">{{ __('Sub Administrators') }}</a></li>
                            <li><i class="fa fa-user"></i><a href="{{ url('/admin/customer') }}">{{ __('Customers') }}</a></li>
                            <li><i class="fa fa-user"></i><a href="{{ url('/admin/vendor') }}">{{ __('Vendors') }}</a></li>
                         </ul>
                    </li>
                    @endif                   
                    @if(in_array('items',$avilable)) 
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-location-arrow"></i>{{ __('Items') }}</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/category') }}">{{ __('Category') }}</a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/sub-category') }}">{{ __('Sub Category') }}</a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/items') }}">{{ __('Items') }}</a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/item-features') }}">{{ __('Item Features') }}</a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/item-type') }}">{{ __('Item Type') }}</a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/attributes') }}">{{ __('Attributes') }}</a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/orders') }}">{{ __('Orders') }}</a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/coupons') }}">{{ __('Coupons') }}</a></li>
                            @if($custom_settings->author_key == Helper::Key_Owner())
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/other-market') }}">{{ __('Other Market Logo') }}</a></li>
                            @endif
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/promotion') }}">{{ __('Promotion Popup') }}</a></li>
                        </ul>
                    </li>
                    @endif
                    @if($custom_settings->author_key == Helper::Key_Owner())
                    <li>
                        <a href="{{ url('/admin/notifications') }}"> <i class="menu-icon fa fa-bell"></i>{{ __('Notifications') }} </a>
                    </li>
                    @endif
                    @if(in_array('reports',$avilable))
                    <li>
                        <a href="{{ url('/admin/reports') }}"> <i class="menu-icon fa fa-file"></i>{{ __('Reports') }} </a>
                    </li>
                    @endif
                    @if(in_array('subscription',$avilable))
                    @if($addition_settings->subscription_mode == 1)
                    <li>
                        <a href="{{ url('/admin/subscription') }}"> <i class="menu-icon fa fa-user"></i>{{ __('Subscription') }} </a>
                    </li>
                    @endif
                    @endif
                    @if(in_array('refund',$avilable))
                    @if($addition_settings->refund_mode == 1)
                    <li>
                        <a href="{{ url('/admin/refund') }}"> <i class="menu-icon fa fa-paper-plane"></i>{{ __('Refund Request') }} </a>
                    </li>
                    @endif
                    @endif
                    @if(in_array('rating',$avilable))
                    <li>
                        <a href="{{ url('/admin/rating') }}"> <i class="menu-icon fa fa-star"></i>{{ __('Rating & Reviews') }} </a>
                    </li>
                    @endif
                    @if(in_array('withdrawal',$avilable)) 
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>{{ __('Withdrawals') }}</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/withdrawal') }}">{{ __('Withdrawal Request') }}</a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/withdrawal-methods') }}">{{ __('Withdraw Methods') }}</a></li>
                        </ul>
                    </li>
                    @endif
                    @if(in_array('deposit',$avilable))
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>{{ __('Deposit') }}</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/deposit') }}">{{ __('Price Details') }}</a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="{{ url('/admin/deposit-details') }}">{{ __('Deposit Details') }}</a></li>
                        </ul>
                    </li>
                    @endif
                    @if(in_array('blog',$avilable))
                    @if($allsettings->site_blog_display == 1)
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-comments-o"></i>{{ __('Blog') }}</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-comments-o"></i><a href="{{ url('/admin/blog-category') }}">{{ __('Category') }}</a></li>
                            <li><i class="menu-icon fa fa-comments-o"></i><a href="{{ url('/admin/post') }}">{{ __('Post') }}</a></li>
                        </ul>
                    </li>
                    @endif
                    @endif
                    @if($addition_settings->google_ads == 1)
                    @if(in_array('ads',$avilable)) 
                    <li>
                        <a href="{{ url('/admin/ads') }}"> <i class="menu-icon fa fa-file-image-o"></i>{{ __('Ads') }} </a>
                    </li>
                    @endif
                    @endif
                    @if(in_array('pages',$avilable))
                    <li>
                        <a href="{{ url('/admin/pages') }}"> <i class="menu-icon fa fa-file-text-o"></i>{{ __('Pages') }} </a>
                    </li>
                    @endif
                    @if(in_array('features',$avilable))
                    @if($allsettings->site_features_display == 1)
                    <li>
                        <a href="{{ url('/admin/highlights') }}"> <i class="menu-icon fa fa-magic"></i>{{ __('Features') }} </a>
                    </li>
                    @endif
                    @endif
                    @if(in_array('selling',$avilable))
                    @if($allsettings->site_selling_display == 1)
                    <li>
                        <a href="{{ url('/admin/start-selling') }}"> <i class="menu-icon fa fa-shopping-cart"></i>{{ __('Start Selling') }} </a>
                    </li>
                    @endif
                    @endif
                    @if(in_array('contact',$avilable))
                    <li>
                        <a href="{{ url('/admin/contact') }}"> <i class="menu-icon fa fa-address-book-o"></i>{{ __('Contact') }} </a>
                    </li>
                    @endif
                    @if(in_array('newsletter',$avilable))
                    @if($allsettings->site_newsletter_display == 1)
                    <li>
                        <a href="{{ url('/admin/newsletter') }}"> <i class="menu-icon fa fa-newspaper-o"></i>{{ __('NEWSLETTER') }} </a>
                    </li>
                    @endif
                    @endif
                    @if(in_array('etemplate',$avilable))
                    <li>
                        <a href="{{ url('/admin/email-template') }}"> <i class="menu-icon fa fa-envelope"></i>{{ __('Email Template') }} </a>
                    </li>
                    @endif
                    @if($addition_settings->multi_currency == 1)
                    @if(in_array('currencies',$avilable))
                    <li>
                        <a href="{{ url('/admin/currencies') }}"> <i class="menu-icon fa fa-money"></i>{{ __('Currencies') }} </a>
                    </li>
                    @endif
                    @endif
                    @if(in_array('ccache',$avilable))
                    <li>
                        <a href="{{ url('/admin/clear-cache') }}" onClick="return confirm('{{ __('Are you sure you want to clear cache') }}?');"> <i class="menu-icon fa fa-trash"></i>{{ __('Clear Cache') }} </a>
                    </li>
                    @endif
                    @if(in_array('upgrade',$avilable))
                    <li>
                        <a href="{{ url('/admin/upgrade') }}"> <i class="menu-icon fa fa-refresh"></i>{{ __('Upgrade') }} </a>
                    </li>
                    @endif
                    @if(in_array('backups',$avilable))
                    <li>
                        <a href="{{ url('/admin/backup') }}"> <i class="menu-icon fa fa-hdd-o"></i>{{ __('Backups') }} </a>
                    </li>
                    @endif
                </ul>
            </div><!-- /.navbar-collapse -->
            @endif
        </nav>
    </aside>
     