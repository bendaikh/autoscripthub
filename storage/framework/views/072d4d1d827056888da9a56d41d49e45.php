<header id="header" class="header">

            <div class="header-menu">

                <div class="col-sm-7">
                    <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
                    <div class="header-left">
                      <?php if($custom_settings->author_key == Helper::Key_Owner()): ?>
                      <div class="dropdown for-message">
                          <button class="btn btn-secondary dropdown-toggle" type="button" id="message" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bell"></i>
                            <span class="count bg-danger"><?php echo e(Helper::Notification_Count(1)); ?></span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="message" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                            <?php if(Helper::Notification_NoCount(1) != 0): ?>
                            <?php $__currentLoopData = Helper::Notification_Display_Limit(1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $display): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a class="dropdown-item media" href="<?php echo e(URL::to('/admin/notifications')); ?>/<?php echo e($encrypter->encrypt($display->noti_id)); ?>">
                                <span class="photo media-left"><img alt="avatar" src="<?php echo e(Helper::IDtoFetchImage($display->noti_type,$display->noti_type_id,$display->noti_sender_id)); ?>"></span>
                                <span class="message media-body">
                                    <span class="name float-left"><p><?php echo html_entity_decode($display->noti_message); ?></p></span>
                                    <p class="time"><?php echo e(Helper::Timeer_Ago(strtotime($display->noti_date))); ?></p>
                                </span>
                            </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(URL::to('/admin/notifications')); ?>" style="text-align:center;"><p class="red"><?php echo e(__('View All Notifications')); ?></p></a>
                            <?php else: ?>
                            <a class="dropdown-item media" href="<?php echo e(URL::to('/admin/notifications')); ?>" style="text-align:center;"><?php echo e(__('View All Notifications')); ?></a>
                            <?php endif; ?>
                          </div>
                        </div>
                       <?php endif; ?> 
                    </div>
                </div>

                <div class="col-sm-5">
                    <div class="user-area dropdown float-right">
                        <a href="javascript:void();" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            
                            
                        <?php if(Auth::user()->user_photo != ''): ?>
                        <img class="lazy user-avatar rounded-circle" width="32" height="32" src="<?php echo e(url('/')); ?>/public/storage/users/<?php echo e(Auth::user()->user_photo); ?>"  alt="<?php echo e(Auth::user()->name); ?>" /><?php else: ?> <img class="lazy user-avatar rounded-circle" width="32" height="32" src="<?php echo e(url('/')); ?>/public/img/no-user.png"  alt="<?php echo e(Auth::user()->name); ?>"/>  <?php endif; ?>
                        
                        </a>
<div class="user-menu dropdown-menu">
                            <a class="nav-link" href="<?php echo e(url('/admin/edit-profile')); ?>"><i class="fa fa-user"></i> <?php echo e(__('My Profile')); ?></a>
                            <?php if(in_array('settings',$avilable)): ?> 
                            <a class="nav-link" href="<?php echo e(url('/admin/general-settings')); ?>"><i class="fa fa-cog"></i> <?php echo e(__('Settings')); ?></a>
                            <?php endif; ?>
                            <a class="nav-link" href="<?php echo e(url('/logout')); ?>"><i class="fa fa-power-off"></i> <?php echo e(__('Logout')); ?></a>
                        </div>
                        </div>
                        <?php if($allsettings->multi_language == 1): ?>
                       <div class="language-select dropdown" id="language-select">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown"  id="language" aria-haspopup="true" aria-expanded="true">
                            <span class="fa fa-language"></span> <?php echo e($current_locale); ?>

                        </a>
                        <div class="dropdown-menu" aria-labelledby="language">
                            <?php $__currentLoopData = $available_locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale_name => $available_locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="dropdown-item">
                                <a href="<?php echo e(URL::to('/language')); ?>/<?php echo e($available_locale); ?>"><?php echo e($locale_name); ?></a>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        
                    </div>
                 <?php endif; ?> 
                    

                </div>
            </div>

        </header>
        
                    <?php /**PATH C:\laragon\www\autoscripthub\resources\views/admin/header.blade.php ENDPATH**/ ?>