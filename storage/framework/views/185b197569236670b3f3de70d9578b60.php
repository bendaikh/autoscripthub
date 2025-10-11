<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    
    <?php echo $__env->make('admin.stylesheet', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>

<body>
    
    <?php echo $__env->make('admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Right Panel -->
    <?php if(in_array('settings',$avilable)): ?>
    <div id="right-panel" class="right-panel">

       
                       <?php echo $__env->make('admin.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1><?php echo e(__('Payment Settings')); ?></h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    
                </div>
            </div>
        </div>
        
        <?php echo $__env->make('admin.warning', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                       
                        
                        
                      
                        <div class="card">
                           <?php if($demo_mode == 'on'): ?>
                           <?php echo $__env->make('admin.demo-mode', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                           <?php else: ?>
                           <form action="<?php echo e(route('admin.payment-settings')); ?>" method="post" id="setting_form" enctype="multipart/form-data">
                           <?php echo e(csrf_field()); ?>

                          <?php endif; ?>
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Exclusive Author Commission')); ?> (<?php echo e(__('Percentage')); ?> %) <span class="require">*</span></label>
                                                <input id="site_exclusive_commission" name="site_exclusive_commission" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->site_exclusive_commission); ?>" required><small>(<?php echo e(__('if admin set 10% so vendor get 90% of earning amount')); ?>)</small>
                                            </div>
                                            
                                           
                                          <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Non Exclusive Author Commission')); ?> (<?php echo e(__('Percentage')); ?> %) <span class="require">*</span></label>
                                                <input id="site_non_exclusive_commission" name="site_non_exclusive_commission" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->site_non_exclusive_commission); ?>" required><small>(<?php echo e(__('if admin set 10% so vendor get 90% of earning amount')); ?>)</small>
                                            </div> 
                                           
                                            
                                            
                                         <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Minium withdrawal amount')); ?> (<?php echo e($setting['setting']->site_currency); ?>)<span class="require">*</span></label>
                                                <input id="site_minimum_withdrawal" name="site_minimum_withdrawal" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->site_minimum_withdrawal); ?>" required>
                                            </div>    
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Processing Fee')); ?> (<?php echo e(__('extra fee')); ?>) <?php echo e(__('Type')); ?><span class="require">*</span></label>
                                                <select name="site_extra_fee_type" id="site_extra_fee_type" class="form-control" required>
                                                <option value=""></option>
                                                <option value="fixed" <?php if($additional['setting']->site_extra_fee_type == 'fixed'): ?> selected <?php endif; ?>><?php echo e(__('Fixed')); ?></option>
                                                <option value="percentage" <?php if($additional['setting']->site_extra_fee_type == 'percentage'): ?> selected <?php endif; ?>><?php echo e(__('Percentage')); ?></option>
                                                </select>
                                            </div>
                             
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Processing Fee')); ?> (<?php echo e(__('extra fee')); ?>) <span id="iffixed" <?php if($additional['setting']->site_extra_fee_type == 'fixed'): ?> class="inline-block" <?php else: ?>  class="display-none" <?php endif; ?>>(<?php echo e($setting['setting']->site_currency); ?>)</span><span id="ifpercentage" <?php if($additional['setting']->site_extra_fee_type == 'percentage'): ?> class="inline-block" <?php else: ?>  class="display-none" <?php endif; ?>>(%)</span><span class="require">*</span></label>
                                                <input id="site_extra_fee" name="site_extra_fee" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->site_extra_fee); ?>" required><small>(<?php echo e(__('if you will set "0" processing fee is OFF')); ?>)</small>
                                            </div>
                                            
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Flash Sale')); ?> (<?php echo e(__('Percentage')); ?> %) <span class="require">*</span></label>
                                                <input id="flash_sale_value" name="flash_sale_value" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->flash_sale_value); ?>"  data-bvalidator="required,number,min[1],max[99]">
                                            </div>
                                                
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                             <div class="col-md-6">
                             
                             
                             <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                           <div class="form-group">
                                              <label for="product_approval" class="control-label mb-1"><?php echo e(__('Affiliate Referral')); ?>?<span class="require">*</span></label><br/>
                                              <select name="affiliate_referral" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" <?php if($additional['setting']->affiliate_referral == 1): ?> selected <?php endif; ?>><?php echo e(__('ON')); ?></option>
                                                        <option value="0" <?php if($additional['setting']->affiliate_referral == 0): ?> selected <?php endif; ?>><?php echo e(__('OFF')); ?></option>
                                              </select>
                                              
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Per Sale Referral Commission')); ?> <?php echo e(__('Type')); ?><span class="require">*</span></label>
                                                <select name="per_sale_referral_commission_type" id="per_sale_referral_commission_type" class="form-control" required>
                                                <option value=""></option>
                                                <option value="fixed" <?php if($additional['setting']->per_sale_referral_commission_type == 'fixed'): ?> selected <?php endif; ?>><?php echo e(__('Fixed')); ?></option>
                                                <option value="percentage" <?php if($additional['setting']->per_sale_referral_commission_type == 'percentage'): ?> selected <?php endif; ?>><?php echo e(__('Percentage')); ?></option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Per Sale Referral Commission')); ?> <span id="nfixed" <?php if($additional['setting']->per_sale_referral_commission_type == 'fixed'): ?> class="inline-block" <?php else: ?>  class="display-none" <?php endif; ?>>(<?php echo e($setting['setting']->site_currency); ?>)</span><span id="npercentage" <?php if($additional['setting']->per_sale_referral_commission_type == 'percentage'): ?> class="inline-block" <?php else: ?>  class="display-none" <?php endif; ?>>(%)</span><span class="require">*</span></label>
                                                <input id="per_sale_referral_commission" name="per_sale_referral_commission" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->per_sale_referral_commission); ?>"  required>
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Sign Up')); ?> <?php echo e(__('Referral Commission')); ?> <span class="require">*</span></label>
                                                <input id="site_referral_commission" name="site_referral_commission" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->site_referral_commission); ?>"  required>
                                            </div>
                                           
                                            
                                            
                              
                                                
                                                
                                                <input type="hidden" name="sid" value="1">
                             
                             
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             
                             
                             
                             <div style="clear:both;"></div>
                             
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Admin Payment Methods')); ?> </label><br/>
                                                <?php $__currentLoopData = $payment_option; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <input id="payment_option" name="payment_option[]" type="checkbox" <?php if(in_array($payment,$get_payment)): ?> checked <?php endif; ?> class="noscroll_textarea" value="<?php echo e($payment); ?>"> <?php echo e($payment); ?> <br/>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                             </div>
                                            
                                      
                                        
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Vendor Payment Methods')); ?> </label><br/>
                                                <?php $__currentLoopData = $vendor_payment_option; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <input id="vendor_payment_option" name="vendor_payment_option[]" type="checkbox" <?php if(in_array($payment,$get_vendor_payment)): ?> checked <?php endif; ?> class="noscroll_textarea" value="<?php echo e($payment); ?>"> <?php echo e($payment); ?> <br/>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                             </div>
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Withdraw Methods')); ?> <span class="require">*</span></label><br/>
                                                <?php $__currentLoopData = $withdraw_option; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $withdraw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <input id="withdraw_option" name="withdraw_option[]" type="checkbox" <?php if(in_array($withdraw->withdrawal_key,$get_withdraw)): ?> checked <?php endif; ?> class="noscroll_textarea" value="<?php echo e($withdraw->withdrawal_key); ?>" data-bvalidator="required"> <?php echo e($withdraw->withdrawal_name); ?><br/>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                             </div>
                                            
                                          
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                             
                             
                             <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Paypal Settings')); ?></h4></div></div>
                             
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Paypal Email ID')); ?> </label><br/>
                                               <input id="paypal_email" name="paypal_email" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->paypal_email); ?>">
                                                
                                                
                                             </div>
                                            
                                      
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Paypal Mode')); ?> </label><br/>
                                               
                                                <select name="paypal_mode" class="form-control">
                                                <option value="1" <?php if($setting['setting']->paypal_mode == 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="0" <?php if($setting['setting']->paypal_mode == 0): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                            
                                          
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                             
                             
                             
                            <?php /*?><input type="hidden" name="two_checkout_mode" value="{{ $setting['setting']->two_checkout_mode }}">
                            <input type="hidden" name="two_checkout_account" value="{{ $setting['setting']->two_checkout_account }}">
                            <input type="hidden" name="two_checkout_publishable" value="{{ $setting['setting']->two_checkout_publishable }}">
                            <input type="hidden" name="two_checkout_private" value="{{ $setting['setting']->two_checkout_private }}"><?php */?>
                             <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('2checkout Settings')); ?></h4>
                             
                             </div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('2checkout Mode')); ?></label><br/>
                                               
                                                <select name="two_checkout_mode" class="form-control">
                                                <option value="true" <?php if($setting['setting']->two_checkout_mode == 'true'): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="false" <?php if($setting['setting']->two_checkout_mode == 'false'): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('2Checkout Account Number')); ?></label><br/>
                                               <input id="two_checkout_account" name="two_checkout_account" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->two_checkout_account); ?>">
                                                
                                                
                                             </div>
                                             
                                             <br/>
                                             <p><?php echo e(__('2checkout callback url')); ?> : <code><?php echo e(url('/')); ?>/2checkout-success</code> <br/> <a href="javascript:void(0);" data-toggle="modal" data-target="#myModal" class="blue-color"><?php echo e(__('How to configure callback url')); ?>?</a></p>
                                            
                                      
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            


                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('2Checkout Publishable Key')); ?></label><br/>
                                               <input id="two_checkout_publishable" name="two_checkout_publishable" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->two_checkout_publishable); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('2Checkout Private Key')); ?></label><br/>
                                               <input id="two_checkout_private" name="two_checkout_private" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->two_checkout_private); ?>">
                                                
                                                
                                             </div>
                                             
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                             <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Paystack Settings')); ?></h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Paystack Default Currency Code')); ?> <span class="require">*</span></label><br/>
                                               <input id="paystack_default_currency" name="paystack_default_currency" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->paystack_default_currency); ?>" data-bvalidator="required">
                                                <small>Example : NGN</small>
                                                
                                             </div>
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Paystack Merchant Email')); ?></label><br/>
                                               <input id="paystack_merchant_email" name="paystack_merchant_email" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->paystack_merchant_email); ?>">
                                                
                                                
                                             </div>
                                    
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Paystack Public Key')); ?></label><br/>
                                               <input id="paystack_public_key" name="paystack_public_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->paystack_public_key); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Paystack Secret Key')); ?></label><br/>
                                               <input id="paystack_secret_key" name="paystack_secret_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->paystack_secret_key); ?>">
                                                
                                                
                                             </div>
                                    
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Bank Settings')); ?></h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    
                                    <div class="form-group">
                                              <div style="height:0px;"></div>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Local Bank Details')); ?></label><br/>
                                               <textarea name="local_bank_details" class="form-control noscroll_textarea" rows="5" cols="20"><?php echo e($setting['setting']->local_bank_details); ?></textarea>
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    
                                    <div class="form-group">
                                              <div style="height:0px;"></div>
                                                
                                             </div>
                                             <div class="form-group">
                                               <strong><?php echo e(__('example')); ?>:<br/><br/>

                                                <?php echo e(__('Bank Name')); ?> : Test Bank<br/>
                                                <?php echo e(__('Branch Name')); ?> : Test Branch<br/>
                                                <?php echo e(__('Branch Code')); ?> : 00000<br/>
                                                <?php echo e(__('IFSC Code')); ?> : 63632EF</strong>
                                              </div>
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                             
                             <div class="col-md-12"><div class="card-body"><h4>Razorpay Settings</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Razorpay Key Id</label><br/>
                                               <input id="razorpay_key" name="razorpay_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->razorpay_key); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Razorpay Secret Key</label><br/>
                                               <input id="razorpay_secret" name="razorpay_secret" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->razorpay_secret); ?>">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                             <div class="col-md-12"><div class="card-body"><h4>Payhere Settings</h4></div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Payhere Mode </label><br/>
                                               
                                                <select name="payhere_mode" class="form-control">
                                                <option value="1" <?php if($additional['setting']->payhere_mode == 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="0" <?php if($additional['setting']->payhere_mode == 0): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Payhere Merchant Id</label><br/>
                                               <input id="payhere_merchant_id" name="payhere_merchant_id" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->payhere_merchant_id); ?>">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-12"><div class="card-body"><h4>Payumoney Settings</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Payumoney Mode </label><br/>
                                               
                                                <select name="payumoney_mode" class="form-control" required>
                                                <option value="1" <?php if($additional['setting']->payumoney_mode == 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="0" <?php if($additional['setting']->payumoney_mode == 0): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Payumoney Merchant Key</label><br/>
                                               <input id="payu_merchant_key" name="payu_merchant_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->payu_merchant_key); ?>">
                                                
                                                
                                             </div>
                                             
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Payumoney Salt Key</label><br/>
                                               <input id="payu_salt_key" name="payu_salt_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->payu_salt_key); ?>">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-12"><div class="card-body"><h4>Iyzico Settings</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Iyzico Mode </label><br/>
                                               
                                                <select name="iyzico_mode" class="form-control" required>
                                                <option value="1" <?php if($additional['setting']->iyzico_mode == 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="0" <?php if($additional['setting']->iyzico_mode == 0): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Iyzico API Key</label><br/>
                                               <input id="iyzico_api_key" name="iyzico_api_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->iyzico_api_key); ?>">
                                                
                                                
                                             </div>
                                             
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Iyzico Secret Key</label><br/>
                                               <input id="iyzico_secret_key" name="iyzico_secret_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->iyzico_secret_key); ?>">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-12"><div class="card-body"><h4>Flutterwave Settings</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Flutterwave Default Currency Code')); ?> <span class="require">*</span></label><br/>
                                               <input id="flutterwave_default_currency" name="flutterwave_default_currency" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->flutterwave_default_currency); ?>" data-bvalidator="required">
                                                <small>Example : NGN</small>
                                                
                                             </div>
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Flutterwave Public Key</label><br/>
                                               <input id="flutterwave_public_key" name="flutterwave_public_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->flutterwave_public_key); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Flutterwave Secret Key</label><br/>
                                               <input id="flutterwave_secret_key" name="flutterwave_secret_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->flutterwave_secret_key); ?>">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-12"><div class="card-body"><h4>Coingate Settings</h4></div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Coingate Mode</label><br/>
                                               
                                                <select name="coingate_mode" class="form-control">
                                                <option value="1" <?php if($additional['setting']->coingate_mode == 1): ?> selected <?php endif; ?>>Live</option>
                                                <option value="0" <?php if($additional['setting']->coingate_mode == 0): ?> selected <?php endif; ?>>Demo</option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                    </div>
                                </div>

                            </div>
                            </div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Coingate Auth Token</label><br/>
                                               <input id="coingate_auth_token" name="coingate_auth_token" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->coingate_auth_token); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-12"><div class="card-body"><h4>iPay Settings</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">iPay Mode</label><br/>
                                               
                                                <select name="ipay_mode" class="form-control" data-bvalidator="required">
                                                <option value="1" <?php if($additional['setting']->ipay_mode == 1): ?> selected <?php endif; ?>>Live</option>
                                                <option value="0" <?php if($additional['setting']->ipay_mode == 0): ?> selected <?php endif; ?>>Demo</option>
                                                </select>
                                                
                                             </div>
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">iPay Vendor ID</label><br/>
                                               <input id="ipay_vendor_id" name="ipay_vendor_id" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->ipay_vendor_id); ?>">
                                                
                                                
                                             </div> 
                                             
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">iPay API / Hash Key</label><br/>
                                               <input id="ipay_hash_key" name="ipay_hash_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->ipay_hash_key); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('PayFast Settings')); ?></h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('PayFast Merchant Id')); ?></label><br/>
                                               <input id="payfast_merchant_id" name="payfast_merchant_id" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->payfast_merchant_id); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('PayFast Merchant Key')); ?></label><br/>
                                               <input id="payfast_merchant_key" name="payfast_merchant_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->payfast_merchant_key); ?>">
                                                
                                                
                                             </div>
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('PayFast Mode')); ?></label><br/>
                                               
                                                <select name="payfast_mode" class="form-control">
                                                <option value="1" <?php if($additional['setting']->payfast_mode == 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="0" <?php if($additional['setting']->payfast_mode == 0): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('CoinPayments')); ?></h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('CoinPayments Merchant ID')); ?></label><br/>
                                               <input id="coinpayments_merchant_id" name="coinpayments_merchant_id" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->coinpayments_merchant_id); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('SSLCommerz Settings')); ?></h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('SSLCommerz Store Id')); ?></label><br/>
                                               <input id="sslcommerz_store_id" name="sslcommerz_store_id" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->sslcommerz_store_id); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('SSLCommerz Store Password')); ?></label><br/>
                                               <input id="sslcommerz_store_password" name="sslcommerz_store_password" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->sslcommerz_store_password); ?>">
                                                
                                                
                                             </div>
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('SSLCommerz Mode')); ?></label><br/>
                                               
                                                <select name="sslcommerz_mode" class="form-control" data-bvalidator="required">
                                                <option value="FALSE" <?php if($additional['setting']->sslcommerz_mode == 'FALSE'): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="TRUE" <?php if($additional['setting']->sslcommerz_mode == 'TRUE'): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Instamojo Settings')); ?></h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Instamojo API Key')); ?></label><br/>
                                               <input id="instamojo_api_key" name="instamojo_api_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->instamojo_api_key); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Instamojo Auth Token')); ?></label><br/>
                                               <input id="instamojo_auth_token" name="instamojo_auth_token" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->instamojo_auth_token); ?>">
                                                
                                                
                                             </div>
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Instamojo Mode')); ?></label><br/>
                                               
                                                <select name="instamojo_mode" class="form-control" data-bvalidator="required">
                                                <option value="1" <?php if($additional['setting']->instamojo_mode == 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="0" <?php if($additional['setting']->instamojo_mode == 0): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Aamarpay Settings')); ?></h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Aamarpay Mode')); ?> </label><br/>
                                               
                                                <select name="aamarpay_mode" class="form-control">
                                                <option value="1" <?php if($additional['setting']->aamarpay_mode == 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="0" <?php if($additional['setting']->aamarpay_mode == 0): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Store ID')); ?> </label><br/>
                                               <input id="aamarpay_store_id" name="aamarpay_store_id" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->aamarpay_store_id); ?>">
                                                
                                                
                                             </div>
                                             
                                             
                                             
                                            
                                      
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    
                                    <div class="form-group">
                                              <div style="height:65px;"></div>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Signature Key')); ?> </label><br/>
                                               <input id="aamarpay_signature_key" name="aamarpay_signature_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->aamarpay_signature_key); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Mollie Settings')); ?></h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Mollie API Key')); ?></label><br/>
                                               <input id="mollie_api_key" name="mollie_api_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->mollie_api_key); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Robokassa Settings')); ?></h4>
                             
                             </div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Shop Identifier')); ?></label><br/>
                                               <input id="shop_identifier" name="shop_identifier" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->shop_identifier); ?>">
                                                
                                                
                                             </div>
                                             
                                             <br/>
                                             <p><?php echo e(__('Robokassa Success Url')); ?> : <code><?php echo e(url('/')); ?>/robokassa-success</code> <br/> <a href="javascript:void(0);" data-toggle="modal" data-target="#myModal_two" class="blue-color"><?php echo e(__('How to configure success url')); ?>?</a></p>
                                            <p><?php echo e(__('Robokassa Failed Url')); ?> : <code><?php echo e(url('/')); ?>/cancel</code> <br/> <a href="javascript:void(0);" data-toggle="modal" data-target="#myModal_two" class="blue-color"><?php echo e(__('How to configure failed url')); ?>?</a></p>
                                      
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            


                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Robokassa Password #1')); ?></label><br/>
                                               <input id="robokassa_password_1" name="robokassa_password_1" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->robokassa_password_1); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                             
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Mercadopago Settings')); ?></h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Mercadopago Public Key')); ?></label><br/>
                                               <input id="mercadopago_client_id" name="mercadopago_client_id" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->mercadopago_client_id); ?>">
                                                
                                                
                                             </div>
                                            
                                          
                                          <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Mercadopago Access Token')); ?></label><br/>
                                               <input id="mercadopago_client_secret" name="mercadopago_client_secret" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->mercadopago_client_secret); ?>">
                                                
                                                
                                             </div>
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Mercadopago Mode')); ?></label><br/>
                                               
                                                <select name="mercadopago_mode" class="form-control">
                                                <option value="1" <?php if($additional['setting']->mercadopago_mode == 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="0" <?php if($additional['setting']->mercadopago_mode == 0): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                            
                                          
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Midtrans Settings')); ?></h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Midtrans Server Key')); ?></label><br/>
                                               <input id="midtrans_server_key" name="midtrans_server_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->midtrans_server_key); ?>">
                                                
                                                
                                             </div>
                                            
                                          
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Midtrans Mode')); ?></label><br/>
                                               
                                                <select name="midtrans_mode" class="form-control">
                                                <option value="1" <?php if($additional['setting']->midtrans_mode == 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="0" <?php if($additional['setting']->midtrans_mode == 0): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                            
                                          
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Coinbase Settings')); ?></h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Coinbase Api Key')); ?></label><br/>
                                               <input id="coinbase_api_key" name="coinbase_api_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->coinbase_api_key); ?>">
                                                
                                                
                                             </div>
                                            
                                            <br/>
                                             
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Coinbase Secret Key')); ?></label><br/>
                                               
                                                <input id="coinbase_secret_key" name="coinbase_secret_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->coinbase_secret_key); ?>">
                                                
                                             </div>
                                            
                                          
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            <div class="col-md-12">
                            <div class="card-body">
                            <div id="pay-invoice">
                            <div class="card-body">
                            <div class="form-group">
                            <p><?php echo e(__('Coinbase Checkout Webhook URL')); ?> : <code><?php echo e(url('/')); ?>/webhooks/coinbase-checkout</code></p>
                            <p><?php echo e(__('Coinbase Subscription Webhook URL')); ?> : <code><?php echo e(url('/')); ?>/webhooks/coinbase-subscription</code></p>
                            <p><?php echo e(__('Coinbase Deposit Webhook URL')); ?> : <code><?php echo e(url('/')); ?>/webhooks/coinbase-deposit</code></p>
                            <p><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal_three" class="blue-color"><?php echo e(__('How to configure webhooks url')); ?>?</a></p>
                             </div>
                            </div>
                            </div>                
                                </div>            
                            </div>
                            
                            
                            <?php /*?><div class="col-md-12"><div class="card-body"><h4>{{ __('Paytm Settings') }}</h4></div></div>
                            
                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Paytm Mode') }}</label><br/>
                                               
                                                <select name="paytm_mode" class="form-control">
                                                <option value=""></option>
                                                <option value="local" @if($additional['setting']->paytm_mode == 'local') selected @endif>{{ __('Demo') }}</option>
                                                <option value="production" @if($additional['setting']->paytm_mode == 'production') selected @endif>{{ __('Live') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Paytm Merchant Id') }}</label><br/>
                                               <input id="paytm_merchant_id" name="paytm_merchant_id" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->paytm_merchant_id }}">
                                                
                                                
                                             </div> 
                                             
                                            
                                          <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Paytm Merchant Key') }}</label><br/>
                                               <input id="paytm_merchant_key" name="paytm_merchant_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->paytm_merchant_key }}">
                                                
                                                
                                             </div> 
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Paytm Merchant Website') }}</label><br/>
                                               <input id="paytm_merchant_website" name="paytm_merchant_website" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->paytm_merchant_website }}"><small>{{ __('example') }} : {{ $setting['setting']->site_title }}</small>
                                                
                                                
                                             </div>  
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Paytm Channel') }}</label><br/>
                                               <input id="paytm_channel" name="paytm_channel" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->paytm_channel }}">
                                                
                                                
                                             </div> 
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Paytm Industry Type') }}</label><br/>
                                               <input id="paytm_industry_type" name="paytm_industry_type" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->paytm_industry_type }}">
                                                
                                                
                                             </div> 
                                            
                                      
                                        
                                    </div>
                                </div>

                            </div>
                            </div><?php */?>
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Cashfree Settings')); ?></h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Cashfree Mode')); ?></label><br/>
                                               
                                                <select name="cashfree_mode" class="form-control">
                                                <option value="1" <?php if($additional['setting']->cashfree_mode == 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="0" <?php if($additional['setting']->cashfree_mode == 0): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Cashfree API Key')); ?></label><br/>
                                               <input id="cashfree_api_key" name="cashfree_api_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->cashfree_api_key); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Cashfree API Secret')); ?></label><br/>
                                               <input id="cashfree_api_secret" name="cashfree_api_secret" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->cashfree_api_secret); ?>">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-12">
                            <div class="card-body">
                            <div id="pay-invoice">
                            <div class="card-body">
                            <div class="form-group">
                            <p><?php echo e(__('Go To Whitelisting URL')); ?> : <code>https://merchant.cashfree.com/merchants/pg/developers/whitelisting?env=prod</code></p>
                            <p><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal_four" class="blue-color"><?php echo e(__('How To Put My Domain')); ?>?</a></p>
                             </div>
                            </div>
                            </div>                
                            </div>            
                            </div>
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('NowPayments Settings')); ?></h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('NowPayments Mode')); ?></label><br/>
                                               
                                                <select name="nowpayments_mode" class="form-control">
                                                <option value="1" <?php if($additional['setting']->nowpayments_mode == 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="0" <?php if($additional['setting']->nowpayments_mode == 0): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('NowPayments API Key')); ?></label><br/>
                                               <input id="nowpayments_api_key" name="nowpayments_api_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->nowpayments_api_key); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('NowPayments IPN Secret')); ?></label><br/>
                                               <input id="nowpayments_ipn_secret" name="nowpayments_ipn_secret" type="text" class="form-control noscroll_textarea" value="<?php echo e($additional['setting']->nowpayments_ipn_secret); ?>">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Dodo Payments Settings')); ?></h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Dodo Payments Mode')); ?></label><br/>
                                               
                                                <select name="dodopayments_mode" class="form-control">
                                                <option value="live" <?php if(config('services.dodopayments.mode', 'test') == 'live'): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="test" <?php if(config('services.dodopayments.mode', 'test') == 'test'): ?> selected <?php endif; ?>><?php echo e(__('Test')); ?></option>
                                                </select>
                                                
                                             </div>
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Dodo Payments API Key')); ?></label><br/>
                                               <input id="dodopayments_api_key" name="dodopayments_api_key" type="text" class="form-control noscroll_textarea" value="<?php echo e(config('services.dodopayments.api_key', '')); ?>">
                                                <small class="form-text text-muted"><?php echo e(__('Stored in .env file (DODOPAYMENTS_API_KEY)')); ?></small>
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            <div class="col-md-12">
                            <div class="card-body">
                            <div id="pay-invoice">
                            <div class="card-body">
                            <div class="form-group">
                            <p><?php echo e(__('Dodo Payments Checkout Webhook URL')); ?> : <code><?php echo e(url('/')); ?>/webhooks/dodopayments-checkout</code></p>
                            <p><?php echo e(__('Dodo Payments Subscription Webhook URL')); ?> : <code><?php echo e(url('/')); ?>/webhooks/dodopayments-subscription</code></p>
                            <p><?php echo e(__('Dodo Payments Deposit Webhook URL')); ?> : <code><?php echo e(url('/')); ?>/webhooks/dodopayments-deposit</code></p>
                            <p><a href="https://docs.dodopayments.com/api-reference/webhooks/create-webhook" target="_blank" class="blue-color"><?php echo e(__('How to configure webhooks')); ?>?</a></p>
                             </div>
                            </div>
                            </div>                
                            </div>            
                            </div>
                            
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4><?php echo e(__('Stripe Settings')); ?></h4></div></div>
                             
                             
                              <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Stripe Mode')); ?> </label><br/>
                                               
                                                <select name="stripe_mode" class="form-control">
                                                <option value="1" <?php if($setting['setting']->stripe_mode == 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                                <option value="0" <?php if($setting['setting']->stripe_mode == 0): ?> selected <?php endif; ?>><?php echo e(__('Demo')); ?></option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Test Publishable Key')); ?> </label><br/>
                                               <input id="test_publish_key" name="test_publish_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->test_publish_key); ?>">
                                                
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Live Publishable Key')); ?> </label><br/>
                                               <input id="live_publish_key" name="live_publish_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->live_publish_key); ?>">
                                                
                                                
                                             </div>
                                            
                                      
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Stripe Payment Type')); ?> </label><br/>
                                               
                                                <select name="stripe_type" class="form-control">
                                                <option value="charges" <?php if($setting['setting']->stripe_type == 'charges'): ?> selected <?php endif; ?>><?php echo e(__('Charges API')); ?></option>
                                                <option value="intents" <?php if($setting['setting']->stripe_type == 'intents'): ?> selected <?php endif; ?>><?php echo e(__('Intents API')); ?></option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Test Secret Key')); ?> </label><br/>
                                               <input id="test_secret_key" name="test_secret_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->test_secret_key); ?>">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><?php echo e(__('Live Secret Key')); ?> </label><br/>
                                               <input id="live_secret_key" name="live_secret_key" type="text" class="form-control noscroll_textarea" value="<?php echo e($setting['setting']->live_secret_key); ?>">
                                                
                                                
                                             </div>
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                             <div class="col-md-12 no-padding">
                             <div class="card-footer">
                                                        <button type="submit" name="submit" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-dot-circle-o"></i> <?php echo e(__('Submit')); ?>

                                                        </button>
                                                        <button type="reset" class="btn btn-danger btn-sm">
                                                            <i class="fa fa-ban"></i> <?php echo e(__('Reset')); ?>

                                                        </button>
                                                    </div>
                             
                             </div>
                             
                            
                            </form>
                            
                                                    
                                                    
                                                 
                            
                        </div> 

                     
                    
                    
                    </div>
                    

                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->
    <?php else: ?>
    <?php echo $__env->make('admin.denied', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <!-- Right Panel -->


   <?php echo $__env->make('admin.javascript', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="myModal" class="modal fade 2checkout" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-body">
            <img class="lazy" width="1223" height="678" src="<?php echo e(url('/')); ?>/public/img/2checkout_info.png"  class="img-responsive">
        </div>
    </div>
  </div>
</div>
<div id="myModal_two" class="modal fade 2checkout" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-body">
            <img class="lazy" width="1223" height="678" src="<?php echo e(url('/')); ?>/resources/views/assets/robokassa_info.png"  class="img-responsive">
        </div>
    </div>
  </div>
</div>
<div id="myModal_three" class="modal fade 2checkout" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-body">
            <img class="lazy" width="1223" height="678" src="<?php echo e(url('/')); ?>/resources/views/assets/coinbase_info.png"  class="img-responsive">
        </div>
    </div>
  </div>
</div>
<div id="myModal_four" class="modal fade 2checkout" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-body">
            <img class="lazy" width="1223" height="678" src="<?php echo e(url('/')); ?>/resources/views/theme/img/cashfree1.png"  class="img-responsive">
            <img class="lazy" width="1223" height="678" src="<?php echo e(url('/')); ?>/resources/views/theme/img/cashfree2.png"  class="img-responsive">
        </div>
    </div>
  </div>
</div>
</body>

</html>
<?php /**PATH C:\laragon\www\autoscripthub\resources\views/admin/payment-settings.blade.php ENDPATH**/ ?>