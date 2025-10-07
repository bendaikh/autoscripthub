<aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <?php if($allsettings->site_logo != ''): ?>
                <a class="navbar-brand" href="<?php echo e(url('/')); ?>"><img class="lazy" width="160" height="45" src="<?php echo e(url('/')); ?>/public/storage/settings/<?php echo e($allsettings->site_logo); ?>"   alt="<?php echo e($allsettings->site_title); ?>"/></a>
                <?php else: ?>
                <a class="navbar-brand" href="<?php echo e(url('/')); ?>"><?php echo e(substr($allsettings->site_title,0,10)); ?></a>
                <?php endif; ?>
                <?php if($allsettings->site_favicon != ''): ?>
                <a class="navbar-brand hidden" href="<?php echo e(url('/')); ?>"><img class="lazy" width="24" height="24" src="<?php echo e(url('/')); ?>/public/storage/settings/<?php echo e($allsettings->site_favicon); ?>"   alt="<?php echo e($allsettings->site_title); ?>"/></a>
                <?php else: ?>
                <a class="navbar-brand hidden" href="<?php echo e(url('/')); ?>"><?php echo e(substr($allsettings->site_title,0,1)); ?></a>
                <?php endif; ?>
                <code><?php echo e(Helper::Current_Version()); ?></code>
            </div>
            <?php if($custom_settings->$dg_ver == 1): ?>
            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <?php if(in_array('dashboard',$avilable)): ?>
                    <li class="active">
                        <a href="<?php echo e(url('/admin')); ?>"> <i class="menu-icon fa fa-dashboard"></i><?php echo e(__('Dashboard')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php if(in_array('settings',$avilable)): ?>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-gears"></i><?php echo e(__('Settings')); ?></a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-gear"></i><a href="<?php echo e(url('/admin/general-settings')); ?>"><?php echo e(__('General Settings')); ?></a></li>
                            <li><i class="fa fa-gear"></i><a href="<?php echo e(url('/admin/font-color-settings')); ?>"><?php echo e(__('Font')); ?> & <?php echo e(__('Color Settings')); ?></a></li>
                            <li><i class="fa fa-gear"></i><a href="<?php echo e(url('/admin/currency-settings')); ?>"><?php echo e(__('Currency Settings')); ?></a></li>
                            <li><i class="fa fa-gear"></i><a href="<?php echo e(url('/admin/country-settings')); ?>"><?php echo e(__('Country Settings')); ?></a></li>
                            <li><i class="fa fa-gear"></i><a href="<?php echo e(url('/admin/email-settings')); ?>"><?php echo e(__('Email Settings')); ?></a></li>
                            <li><i class="fa fa-gear"></i><a href="<?php echo e(url('/admin/media-settings')); ?>"><?php echo e(__('Media Settings')); ?></a></li>
                            <li><i class="fa fa-gear"></i><a href="<?php echo e(url('/admin/badges-settings')); ?>"><?php echo e(__('Badges Settings')); ?></a></li>
                            <li><i class="fa fa-gear"></i><a href="<?php echo e(url('/admin/payment-settings')); ?>"><?php echo e(__('Payment Settings')); ?></a></li>
                            <li><i class="fa fa-gear"></i><a href="<?php echo e(url('/admin/social-settings')); ?>"><?php echo e(__('Social Settings')); ?></a></li>
                            <li><i class="fa fa-gear"></i><a href="<?php echo e(url('/admin/limitation-settings')); ?>"><?php echo e(__('Limitation Settings')); ?></a></li>
                            <li><i class="fa fa-gear"></i><a href="<?php echo e(url('/admin/preferred-settings')); ?>"><?php echo e(__('Preferred Settings')); ?></a></li>
                            <li><i class="fa fa-gear"></i><a href="<?php echo e(url('/admin/pwa-settings')); ?>"><?php echo e(__('PWA Settings')); ?></a></li>
                        </ul>
                    </li>
                   <?php endif; ?>
                   <?php if(Auth::user()->id == 1): ?> 
                   <li class="menu-item-has-children dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-users"></i><?php echo e(__('User Roles')); ?></a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-user"></i><a href="<?php echo e(url('/admin/administrator')); ?>"><?php echo e(__('Sub Administrators')); ?></a></li>
                            <li><i class="fa fa-user"></i><a href="<?php echo e(url('/admin/customer')); ?>"><?php echo e(__('Customers')); ?></a></li>
                            <li><i class="fa fa-user"></i><a href="<?php echo e(url('/admin/vendor')); ?>"><?php echo e(__('Vendors')); ?></a></li>
                         </ul>
                    </li>
                    <?php endif; ?>                   
                    <?php if(in_array('items',$avilable)): ?> 
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-location-arrow"></i><?php echo e(__('Items')); ?></a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/category')); ?>"><?php echo e(__('Category')); ?></a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/sub-category')); ?>"><?php echo e(__('Sub Category')); ?></a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/items')); ?>"><?php echo e(__('Items')); ?></a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/item-features')); ?>"><?php echo e(__('Item Features')); ?></a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/item-type')); ?>"><?php echo e(__('Item Type')); ?></a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/attributes')); ?>"><?php echo e(__('Attributes')); ?></a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/orders')); ?>"><?php echo e(__('Orders')); ?></a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/coupons')); ?>"><?php echo e(__('Coupons')); ?></a></li>
                            <?php if($custom_settings->author_key == Helper::Key_Owner()): ?>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/other-market')); ?>"><?php echo e(__('Other Market Logo')); ?></a></li>
                            <?php endif; ?>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/promotion')); ?>"><?php echo e(__('Promotion Popup')); ?></a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php if($custom_settings->author_key == Helper::Key_Owner()): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/notifications')); ?>"> <i class="menu-icon fa fa-bell"></i><?php echo e(__('Notifications')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php if(in_array('reports',$avilable)): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/reports')); ?>"> <i class="menu-icon fa fa-file"></i><?php echo e(__('Reports')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php if(in_array('subscription',$avilable)): ?>
                    <?php if($addition_settings->subscription_mode == 1): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/subscription')); ?>"> <i class="menu-icon fa fa-user"></i><?php echo e(__('Subscription')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if(in_array('refund',$avilable)): ?>
                    <?php if($addition_settings->refund_mode == 1): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/refund')); ?>"> <i class="menu-icon fa fa-paper-plane"></i><?php echo e(__('Refund Request')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if(in_array('rating',$avilable)): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/rating')); ?>"> <i class="menu-icon fa fa-star"></i><?php echo e(__('Rating & Reviews')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php if(in_array('withdrawal',$avilable)): ?> 
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i><?php echo e(__('Withdrawals')); ?></a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/withdrawal')); ?>"><?php echo e(__('Withdrawal Request')); ?></a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/withdrawal-methods')); ?>"><?php echo e(__('Withdraw Methods')); ?></a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php if(in_array('deposit',$avilable)): ?>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i><?php echo e(__('Deposit')); ?></a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/deposit')); ?>"><?php echo e(__('Price Details')); ?></a></li>
                            <li><i class="menu-icon fa fa-location-arrow"></i><a href="<?php echo e(url('/admin/deposit-details')); ?>"><?php echo e(__('Deposit Details')); ?></a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php if(in_array('blog',$avilable)): ?>
                    <?php if($allsettings->site_blog_display == 1): ?>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-comments-o"></i><?php echo e(__('Blog')); ?></a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="menu-icon fa fa-comments-o"></i><a href="<?php echo e(url('/admin/blog-category')); ?>"><?php echo e(__('Category')); ?></a></li>
                            <li><i class="menu-icon fa fa-comments-o"></i><a href="<?php echo e(url('/admin/post')); ?>"><?php echo e(__('Post')); ?></a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if($addition_settings->google_ads == 1): ?>
                    <?php if(in_array('ads',$avilable)): ?> 
                    <li>
                        <a href="<?php echo e(url('/admin/ads')); ?>"> <i class="menu-icon fa fa-file-image-o"></i><?php echo e(__('Ads')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if(in_array('pages',$avilable)): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/pages')); ?>"> <i class="menu-icon fa fa-file-text-o"></i><?php echo e(__('Pages')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php if(in_array('features',$avilable)): ?>
                    <?php if($allsettings->site_features_display == 1): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/highlights')); ?>"> <i class="menu-icon fa fa-magic"></i><?php echo e(__('Features')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if(in_array('selling',$avilable)): ?>
                    <?php if($allsettings->site_selling_display == 1): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/start-selling')); ?>"> <i class="menu-icon fa fa-shopping-cart"></i><?php echo e(__('Start Selling')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if(in_array('contact',$avilable)): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/contact')); ?>"> <i class="menu-icon fa fa-address-book-o"></i><?php echo e(__('Contact')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php if(in_array('newsletter',$avilable)): ?>
                    <?php if($allsettings->site_newsletter_display == 1): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/newsletter')); ?>"> <i class="menu-icon fa fa-newspaper-o"></i><?php echo e(__('NEWSLETTER')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if(in_array('etemplate',$avilable)): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/email-template')); ?>"> <i class="menu-icon fa fa-envelope"></i><?php echo e(__('Email Template')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php if($addition_settings->multi_currency == 1): ?>
                    <?php if(in_array('currencies',$avilable)): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/currencies')); ?>"> <i class="menu-icon fa fa-money"></i><?php echo e(__('Currencies')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if(in_array('ccache',$avilable)): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/clear-cache')); ?>" onClick="return confirm('<?php echo e(__('Are you sure you want to clear cache')); ?>?');"> <i class="menu-icon fa fa-trash"></i><?php echo e(__('Clear Cache')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php if(in_array('upgrade',$avilable)): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/upgrade')); ?>"> <i class="menu-icon fa fa-refresh"></i><?php echo e(__('Upgrade')); ?> </a>
                    </li>
                    <?php endif; ?>
                    <?php if(in_array('backups',$avilable)): ?>
                    <li>
                        <a href="<?php echo e(url('/admin/backup')); ?>"> <i class="menu-icon fa fa-hdd-o"></i><?php echo e(__('Backups')); ?> </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div><!-- /.navbar-collapse -->
            <?php endif; ?>
        </nav>
    </aside>
     <?php /**PATH C:\laragon\www\autoscripthub\resources\views/admin/navigation.blade.php ENDPATH**/ ?>