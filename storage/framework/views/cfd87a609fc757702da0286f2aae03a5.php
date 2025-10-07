<header class="bg-light box-shadow-sm navbar-sticky">
      <!-- Topbar-->
      
      <?php if($allsettings->site_header_top_bar == 1): ?>
      <div class="topbar topbar-dark bg-dark">
        <div class="container">
          <div>
            <?php if($allsettings->multi_language == 1): ?>
            <div class="topbar-text dropdown disable-autohide"><a class="topbar-link dropdown-toggle capletter" href="javascript:void(0);" data-toggle="dropdown"><?php echo e($current_locale); ?></a>
              <ul class="dropdown-menu">
                <?php $__currentLoopData = $available_locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale_name => $available_locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><a class="dropdown-item pb-1" href="<?php echo e(URL::to('/language')); ?>/<?php echo e($available_locale); ?>"><?php echo e($locale_name); ?></a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </ul>
            </div>
            <?php endif; ?>
            <?php if($addition_settings->multi_currency == 1): ?>
            <div class="topbar-text dropdown disable-autohide"><a class="topbar-link dropdown-toggle capletter" href="javascript:void(0);" data-toggle="dropdown"><?php echo e($currency_title); ?></a>
              <ul class="dropdown-menu">
                <?php $__currentLoopData = $currencyview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><a class="dropdown-item pb-1" href="<?php echo e(URL::to('/currency')); ?>/<?php echo e($currency->currency_code); ?>"><?php echo e($currency->currency_code); ?> (<?php echo e($currency->currency_symbol); ?>)</a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </ul>
            </div>
            <?php endif; ?>
            <div class="topbar-text text-nowrap d-none d-md-inline-block border-left border-light pl-3 ml-3"></div>
          </div>
          <div class="topbar-text dropdown d-md-none ml-auto"><a class="topbar-link dropdown-toggle" href="#" data-toggle="dropdown"><?php if($addition_settings->verify_mode == 1): ?><?php echo e(__('Verify Purchase')); ?> / <?php endif; ?> <?php if($addition_settings->subscription_mode == 1): ?><?php echo e(__('Subscription')); ?> / <?php endif; ?> <?php echo e(__('Contact')); ?><?php if($allsettings->site_blog_display == 1): ?> / <?php echo e(__('Blog')); ?><?php endif; ?></a>
            <ul class="dropdown-menu dropdown-menu-right">
              <?php if($addition_settings->verify_mode == 1): ?> 
              <li><a class="dropdown-item" href="<?php echo e(URL::to('/verify')); ?>"><i class="dwg-check text-muted mr-2"></i><?php echo e(__('Verify Purchase')); ?></a></li>
              <?php endif; ?>
              <?php if(Auth::guest()): ?>
              <li><a class="dropdown-item" href="<?php echo e(URL::to('/start-selling')); ?>"><i class="dwg-cart text-muted mr-2"></i><?php echo e(__('Start Selling')); ?></a></li>
              <?php else: ?>
              <?php if(Auth::user()->user_type == 'vendor'): ?>
              <li><a class="dropdown-item" href="<?php echo e(URL::to('/manage-item')); ?>"><i class="dwg-cart text-muted mr-2"></i><?php echo e(__('Start Selling')); ?></a></li>
              <?php endif; ?>
              <?php endif; ?>
              <?php if($addition_settings->subscription_mode == 1): ?>
              <li><a class="dropdown-item" href="<?php echo e(url('/subscription')); ?>"><i class="dwg-book text-muted mr-2"></i><?php echo e(__('Subscription')); ?></a></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="<?php echo e(URL::to('/contact')); ?>"><i class="dwg-support text-muted mr-2"></i><?php echo e(__('Contact')); ?></a></li>
              <?php if($allsettings->site_blog_display == 1): ?>
              <li><a class="dropdown-item" href="<?php echo e(URL::to('/blog')); ?>"><i class="dwg-image text-muted mr-2"></i><?php echo e(__('Blog')); ?></a></li>
              <?php endif; ?>
            </ul>
          </div>
          <div class="d-none d-md-block ml-3 text-nowrap">
          <?php if($addition_settings->verify_mode == 1): ?>
          <a class="topbar-link ml-3 pl-3 d-none d-md-inline-block" href="<?php echo e(URL::to('/verify')); ?>"><i class="dwg-check mt-n1"></i><?php echo e(__('Verify Purchase')); ?></a>
          <?php endif; ?>
          <?php if($allsettings->site_selling_display == 1): ?>
          <?php if(Auth::guest()): ?>
          <a class="topbar-link ml-3 pl-3 border-left border-light d-none d-md-inline-block" href="<?php echo e(URL::to('/start-selling')); ?>"><i class="dwg-cart mt-n1"></i><?php echo e(__('Start Selling')); ?></a>
          <?php else: ?>
          <?php if(Auth::user()->user_type == 'vendor'): ?>
          <a class="topbar-link ml-3 pl-3 border-left border-light d-none d-md-inline-block" href="<?php echo e(URL::to('/manage-item')); ?>"><i class="dwg-cart mt-n1"></i><?php echo e(__('Start Selling')); ?></a>
          <?php endif; ?>
          <?php endif; ?>
          <?php endif; ?>
          <?php if($addition_settings->subscription_mode == 1): ?>
          <a class="topbar-link ml-3 border-left border-light pl-3 d-none d-md-inline-block" href="<?php echo e(url('/subscription')); ?>"><i class="dwg-book mt-n1"></i><?php echo e(__('Subscription')); ?></a>
          <?php endif; ?>
          <a class="topbar-link ml-3 pl-3 border-left border-light d-none d-md-inline-block" href="<?php echo e(URL::to('/contact')); ?>"><i class="dwg-support mt-n1"></i><?php echo e(__('Contact')); ?></a>
          <?php if($allsettings->site_blog_display == 1): ?>
          <a class="topbar-link ml-3 border-left border-light pl-3 d-none d-md-inline-block" href="<?php echo e(URL::to('/blog')); ?>"><i class="dwg-image mt-n1"></i><?php echo e(__('Blog')); ?></a>
          <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
      <!-- Remove "navbar-sticky" class to make navigation bar scrollable with the page.-->
      <div class="navbar-sticky">
        <div class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="container">
          <?php if($allsettings->site_logo != ''): ?>
          <a class="navbar-brand d-none d-sm-block mr-4 order-lg-1" href="<?php echo e(URL::to('/')); ?>" style="min-width: 7rem;">
             <img class="lazy" width="<?php echo e($addition_settings->site_desktop_logo_width); ?>" height="<?php echo e($addition_settings->site_desktop_logo_height); ?>" src="<?php echo e(url('/')); ?>/public/storage/settings/<?php echo e($allsettings->site_logo); ?>"  alt="<?php echo e($allsettings->site_title); ?>"/>
          </a>
          <?php endif; ?>
          <?php if($allsettings->site_logo != ''): ?>
          <a class="navbar-brand d-sm-none mr-2 order-lg-1" href="<?php echo e(URL::to('/')); ?>" style="min-width: 4.625rem;">
             <img class="lazy" width="<?php echo e($addition_settings->site_mobile_logo_width); ?>" height="<?php echo e($addition_settings->site_mobile_logo_height); ?>" src="<?php echo e(url('/')); ?>/public/storage/settings/<?php echo e($allsettings->site_logo); ?>"  alt="<?php echo e($allsettings->site_title); ?>"/>
          </a>
          <?php endif; ?>
            <div class="navbar-toolbar d-flex align-items-center order-lg-3">
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"><span class="navbar-toggler-icon"></span></button><a class="navbar-tool d-none d-lg-flex" href="#searchBox" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="searchBox"><span class="navbar-tool-tooltip"><?php echo e(__('Search')); ?></span>
                <div class="navbar-tool-icon-box"><i class="navbar-tool-icon dwg-search"></i></div></a>
                <a class="navbar-tool d-none d-lg-flex" href="<?php echo e(URL::to('/favourites')); ?>"><span class="navbar-tool-tooltip"><?php echo e(__('Favourites')); ?></span>
                <div class="navbar-tool-icon-box"><i class="navbar-tool-icon dwg-heart"></i></div></a>
                <?php if(Auth::guest()): ?>
                <a class="navbar-tool ml-1 mr-n1" href="<?php echo e(URL::to('/login')); ?>"><span class="navbar-tool-tooltip"><?php echo e(__('account')); ?></span>
                <div class="navbar-tool-icon-box"><i class="navbar-tool-icon dwg-user"></i></div></a>
                <?php endif; ?>
                <?php if(Auth::check()): ?>
                <div class="navbar-tool dropdown ml-2">
                <a class="navbar-tool-icon-box border remove-hyper dropdown-toggle" data-toggle="dropdown" <?php if(Auth::user()->id == 1): ?> href="<?php echo e(url('/admin')); ?>" target="_blank" <?php else: ?> href="<?php echo e(URL::to('/user')); ?>/<?php echo e(Auth::user()->username); ?>" <?php endif; ?>>         <?php if(!empty(Auth::user()->user_photo)): ?>
                <img class="lazy" width="32" height="32" src="<?php echo e(url('/')); ?>/public/storage/users/<?php echo e(Auth::user()->user_photo); ?>"  alt="<?php echo e(Auth::user()->name); ?>"/>
                <?php else: ?>
                <img class="lazy" width="32" height="32" src="<?php echo e(url('/')); ?>/public/img/no-user.png"  alt="<?php echo e(Auth::user()->name); ?>">
                <?php endif; ?>
                </a>
                <a class="navbar-tool-text ml-n1" <?php if(Auth::user()->id == 1): ?> href="<?php echo e(url('/admin')); ?>" target="_blank" <?php else: ?> href="<?php echo e(URL::to('/user')); ?>/<?php echo e(Auth::user()->username); ?>" <?php endif; ?>>
                <small><?php echo e(Auth::user()->name); ?></small><?php echo e(Helper::price_format($allsettings->site_currency_position,Auth::user()->earnings,$currency_symbol,$multicurrency)); ?>

                </a>
                <div class="dropdown-menu dropdown-menu-right" style="min-width: 14rem;">
                <?php if(Auth::user()->user_type == 'vendor'): ?>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/user')); ?>/<?php echo e(Auth::user()->username); ?>"><i class="dwg-home opacity-60 mr-2"></i><?php echo e(__('Profile')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/profile-settings')); ?>"><i class="dwg-settings opacity-60 mr-2"></i><?php echo e(__('Setting')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/purchases')); ?>"><i class="dwg-basket opacity-60 mr-2"></i><?php echo e(__('Purchase')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/favourites')); ?>"><i class="dwg-heart opacity-60 mr-2"></i><?php echo e(__('Favourite')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/coupon')); ?>"><i class="dwg-gift opacity-60 mr-2"></i><?php echo e(__('Coupon')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/sales')); ?>"><i class="dwg-cart opacity-60 mr-2"></i><?php echo e(__('Sales')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/manage-item')); ?>"><i class="dwg-briefcase opacity-60 mr-2"></i><?php echo e(__('Manage Items')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/deposit')); ?>"><i class="dwg-money-bag opacity-60 mr-2"></i><?php echo e(__('Deposit')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/withdrawal')); ?>"><i class="dwg-currency-exchange opacity-60 mr-2"></i><?php echo e(__('Withdrawals')); ?></a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(url('/logout')); ?>"><i class="dwg-sign-out opacity-60 mr-2"></i><?php echo e(__('Logout')); ?></a>
                <?php endif; ?>
                <?php if(Auth::user()->user_type == 'customer'): ?>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/user')); ?>/<?php echo e(Auth::user()->username); ?>"><i class="dwg-home opacity-60 mr-2"></i><?php echo e(__('Profile')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/profile-settings')); ?>"><i class="dwg-settings opacity-60 mr-2"></i><?php echo e(__('Setting')); ?></a> 
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/purchases')); ?>"><i class="dwg-basket opacity-60 mr-2"></i><?php echo e(__('Purchase')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/favourites')); ?>"><i class="dwg-heart opacity-60 mr-2"></i><?php echo e(__('Favourite')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/deposit')); ?>"><i class="dwg-money-bag opacity-60 mr-2"></i><?php echo e(__('Deposit')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(URL::to('/withdrawal')); ?>"><i class="dwg-currency-exchange opacity-60 mr-2"></i><?php echo e(__('Withdrawals')); ?></a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(url('/logout')); ?>"><i class="dwg-sign-out opacity-60 mr-2"></i><?php echo e(__('Logout')); ?></a>
                <?php endif; ?>
                <?php if(Auth::user()->user_type == 'admin'): ?>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(url('/admin')); ?>"><i class="dwg-settings opacity-60 mr-2"></i><?php echo e(__('Admin Panel')); ?></a>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(url('/logout')); ?>"><i class="dwg-sign-out opacity-60 mr-2"></i><?php echo e(__('Logout')); ?></a>
                <?php endif; ?>
              </div>
              </div>
              <?php endif; ?>
              <?php if(Auth::check()): ?>
              <?php if(Auth::user()->user_type != 'admin'): ?>
              <?php if($addition_settings->conversation_mode == 1): ?>
              <div class="navbar-tool dropdown ml-3"><a class="navbar-tool-icon-box bg-secondary dropdown-toggle" href="<?php echo e(URL::to('/messages')); ?>"><span class="navbar-tool-label"><?php echo e($msgcount); ?></span><i class="navbar-tool-icon dwg-chat"></i></a>
                <!-- Cart dropdown-->
                <?php if($msgcount != 0): ?>
                <div class="dropdown-menu dropdown-menu-right" style="width: 20rem;">
                  <div class="widget widget-cart px-3 pt-2 pb-3">
                    <div data-simplebar data-simplebar-auto-hide="false">
                      <?php $__currentLoopData = $smsdata['display']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <div class="widget-cart-item pb-2 mb-2 border-bottom">
                        <div class="media align-items-center">
                        <a class="d-block mr-2" href="<?php echo e(url('/messages')); ?>/type/<?php echo e($msg->username); ?>">
                        <?php if($msg->user_photo!=''): ?>
                        <img class="lazy rounded-circle" width="40" height="40" src="<?php echo e(url('/')); ?>/public/storage/users/<?php echo e($msg->user_photo); ?>"  alt="<?php echo e($msg->username); ?>"/>
                        <?php else: ?>
                        <img  class="lazy rounded-circle" width="40" height="40" src="<?php echo e(url('/')); ?>/public/img/no-image.png"  alt="<?php echo e($msg->username); ?>"/>
                        <?php endif; ?>
                        </a>
                          <div class="media-body">
                            <h6 class="widget-product-title"><a href="<?php echo e(url('/messages')); ?>/type/<?php echo e($msg->username); ?>"><?php echo e($msg->username); ?></a></h6>
                            <div class="widget-product-meta"><span class="mr-2"><?php echo e(Helper::timeAgo(strtotime($msg->conver_date))); ?></span></div>
                          </div>
                        </div>
                      </div>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     </div>
                    <a class="btn btn-primary btn-sm btn-block" href="<?php echo e(url('/messages')); ?>"><i class="dwg-chat mr-2 font-size-base align-middle"></i><?php echo e(__('View All Messages')); ?></a>
                  </div>
                </div>
                <?php endif; ?>
                
              </div>
              <?php endif; ?>
              <?php endif; ?>
              <?php endif; ?>
              <div class="navbar-tool dropdown ml-3"><a class="navbar-tool-icon-box bg-secondary dropdown-toggle" href="<?php echo e(url('/cart')); ?>"><span class="navbar-tool-label"><?php echo e($cartcount); ?></span><i class="navbar-tool-icon dwg-cart"></i></a>
                <!-- Cart dropdown-->
                <?php if($cartcount != 0): ?>
                <div class="dropdown-menu dropdown-menu-right" style="width: 20rem;">
                  <div class="widget widget-cart px-3 pt-2 pb-3">
                    <div data-simplebar data-simplebar-auto-hide="false">
                      <?php $subtotall = 0; ?>
                      <?php $__currentLoopData = $cartitem['item']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php 
                      $itemprice = $cart->item_single_price * $cart->item_serial_stock;
                      ?>
                      <div class="widget-cart-item pb-2 mb-2 border-bottom">
                        <a href="<?php echo e(url('/cart')); ?>/<?php echo e(base64_encode($cart->ord_id)); ?>" class="close text-danger" onClick="return confirm('<?php echo e(__('Are you sure you want to delete?')); ?>');"><span aria-hidden="true">&times;</span></a>
                        <div class="media align-items-center"><a class="d-block mr-2" href="<?php echo e(url('/item')); ?>/<?php echo e($cart->item_slug); ?>">
                        <?php if($cart->item_thumbnail!=''): ?>
                        <img class="lazy" width="64" height="64" src="<?php echo e(Helper::Image_Path($cart->item_thumbnail,'no-image.png')); ?>"  alt="<?php echo e($cart->item_name); ?>"/>
                        <?php else: ?>
                        <img class="lazy" width="64" height="64" src="<?php echo e(url('/')); ?>/public/img/no-image.png"  alt="<?php echo e($cart->item_name); ?>"/>
                        <?php endif; ?>
                        </a>
                          <div class="media-body">
                            <h6 class="widget-product-title"><a href="<?php echo e(url('/item')); ?>/<?php echo e($cart->item_slug); ?>">
							<?php if($addition_settings->item_name_limit != 0): ?>
							<?php echo e(mb_substr($cart->item_name,0,$addition_settings->item_name_limit,'utf-8').'...'); ?>

							<?php else: ?>
							<?php echo e($cart->item_name); ?>	  
							<?php endif; ?>
							</a></h6>
                            <div class="widget-product-meta"><span class="text-accent mr-2"><?php echo e(Helper::price_format($allsettings->site_currency_position,$itemprice,$currency_symbol,$multicurrency)); ?></span></div>
                          </div>
                        </div>
                      </div>
                      <?php $subtotall += $itemprice; ?>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
                      <div class="font-size-sm mr-2 py-2"><span class="text-muted"><?php echo e(__('Total')); ?>:</span><span class="text-accent font-size-base ml-1"><?php echo e(Helper::price_format($allsettings->site_currency_position,$subtotall,$currency_symbol,$multicurrency)); ?></span></div><a class="btn btn-outline-secondary btn-sm" href="<?php echo e(url('/cart')); ?>"><?php echo e(__('View Cart')); ?><i class="dwg-arrow-right ml-1 mr-n1"></i></a></div><a class="btn btn-primary btn-sm btn-block" href="<?php echo e(url('/checkout')); ?>"><i class="dwg-card mr-2 font-size-base align-middle"></i><?php echo e(__('Checkout')); ?></a>
                  </div>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <div class="collapse navbar-collapse mr-auto order-lg-2" id="navbarCollapse">
              <!-- Search-->
              <div class="input-group-overlay d-lg-none my-3">
                <div class="input-group-prepend-overlay"><span class="input-group-text"><i class="dwg-search"></i></span></div>
                <form action="<?php echo e(route('shop')); ?>" id="search_form1" method="post"  enctype="multipart/form-data">
                <?php echo e(csrf_field()); ?>

                <input class="form-control prepended-form-control" type="text" name="product_item" placeholder="<?php echo e(__('Search your products...')); ?>">
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
                <li class="nav-item dropdown d-none d-md-block"><a class="nav-link dropdown-toggle" href="<?php echo e(url('/shop')); ?>"><?php echo e(__('All Items')); ?></a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo e(url('/')); ?>/featured-items"><?php echo e(__('Featured Items')); ?></a></li>
                    <li><a class="dropdown-item" href="<?php echo e(url('/free-items')); ?>"><?php echo e(__('Free Items')); ?></a></li>
                    <li><a class="dropdown-item" href="<?php echo e(url('/')); ?>/new-releases"><?php echo e(__('New Releases')); ?></a></li>
                    <li><a class="dropdown-item" href="<?php echo e(url('/')); ?>/popular-items"><?php echo e(__('Popular Items')); ?></a></li>
                    <?php if($addition_settings->subscription_mode == 1): ?>
                    <li><a class="dropdown-item" href="<?php echo e(url('/')); ?>/subscriber-downloads"><?php echo e(__('Subscriber Downloads')); ?></a></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item" href="<?php echo e(url('/top-authors')); ?>"><?php echo e(__('Top Authors')); ?></a></li>
                   </ul>
                </li>
                <?php if(count($categories['menu']) != 0): ?>
                <?php $__currentLoopData = $categories['menu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="nav-item dropdown d-none d-md-block"><a class="nav-link dropdown-toggle" href="<?php echo e(URL::to('/shop/category/')); ?>/<?php echo e($menu->category_slug); ?>"><?php echo e($menu->category_name); ?></a>
                  <?php if(count($menu->subcategory) != 0): ?>
                  <ul class="dropdown-menu">
                    <?php $__currentLoopData = $menu->subcategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a class="dropdown-item" href="<?php echo e(URL::to('/shop/subcategory/')); ?>/<?php echo e($sub_category->subcategory_slug); ?>"><?php echo e($sub_category->subcategory_name); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </ul>
                  <?php endif; ?>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
                <?php /* Desktop or laptop menu */ ?>
                <?php /* Mobile menu */ ?>
                <li class="nav-item dropdown d-md-none"><a class="nav-link dropdown-toggle" href="<?php echo e(url('/shop')); ?>" data-toggle="dropdown"><?php echo e(__('All Items')); ?></a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo e(url('/featured-items')); ?>"><?php echo e(__('Featured Items')); ?></a></li>
                    <li><a class="dropdown-item" href="<?php echo e(url('/free-items')); ?>"><?php echo e(__('Free Items')); ?></a></li>
                    <li><a class="dropdown-item" href="<?php echo e(url('/new-releases')); ?>"><?php echo e(__('New Releases')); ?></a></li>
                    <li><a class="dropdown-item" href="<?php echo e(url('/popular-items')); ?>"><?php echo e(__('Popular Items')); ?></a></li>
                    <?php if($addition_settings->subscription_mode == 1): ?>
                    <li><a class="dropdown-item" href="<?php echo e(url('/subscriber-downloads')); ?>"><?php echo e(__('Subscriber Downloads')); ?></a></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item" href="<?php echo e(url('/top-authors')); ?>"><?php echo e(__('Top Authors')); ?></a></li>
                   </ul>
                </li>
                <?php if(count($categories['menu']) != 0): ?>
                <?php $__currentLoopData = $categories['menu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="nav-item dropdown d-md-none"><a class="nav-link dropdown-toggle" href="<?php echo e(URL::to('/shop/category/')); ?>/<?php echo e($menu->category_slug); ?>" data-toggle="dropdown"><?php echo e($menu->category_name); ?></a>
                  <?php if(count($menu->subcategory) != 0): ?>
                  <ul class="dropdown-menu">
                    <?php $__currentLoopData = $menu->subcategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub_category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a class="dropdown-item" href="<?php echo e(URL::to('/shop/subcategory/')); ?>/<?php echo e($sub_category->subcategory_slug); ?>"><?php echo e($sub_category->subcategory_name); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </ul>
                  <?php endif; ?>
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
                <?php /* Mobile menu */ ?>
                <?php if(count($allpages['pages']) != 0): ?>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown"><?php echo e(__('Pages')); ?></a>
                  <ul class="dropdown-menu">
                    <?php $__currentLoopData = $allpages['pages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pages): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a class="dropdown-item" href="<?php echo e(URL::to('/')); ?>/<?php echo e($pages->page_slug); ?>"><?php echo e($pages->page_title); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </ul>
                </li>
                <?php endif; ?>
                
                
                <li class="nav-item dropdown"><a class="nav-link" href="<?php echo e(URL::to('/flash-sale')); ?>"><?php echo e(__('Flash Sale')); ?></a></li>
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
                <form action="<?php echo e(route('shop')); ?>" id="search_form2" method="post" enctype="multipart/form-data">
                <?php echo e(csrf_field()); ?>

                <input class="form-control prepended-form-control" type="text" name="product_item" id="product_item_top" placeholder="<?php echo e(__('Search your products...')); ?>">
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header><?php /**PATH C:\laragon\www\autoscripthub\resources\views/header-layout-one.blade.php ENDPATH**/ ?>