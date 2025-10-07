<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    
    @include('admin.stylesheet')
</head>

<body>
    
    @include('admin.navigation')

    <!-- Right Panel -->
    @if(in_array('settings',$avilable))
    <div id="right-panel" class="right-panel">

       
                       @include('admin.header')
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Payment Settings') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    
                </div>
            </div>
        </div>
        
        @include('admin.warning')

        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                       
                        
                        
                      
                        <div class="card">
                           @if($demo_mode == 'on')
                           @include('admin.demo-mode')
                           @else
                           <form action="{{ route('admin.payment-settings') }}" method="post" id="setting_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                          @endif
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Exclusive Author Commission') }} ({{ __('Percentage') }} %) <span class="require">*</span></label>
                                                <input id="site_exclusive_commission" name="site_exclusive_commission" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_exclusive_commission }}" required><small>({{ __('if admin set 10% so vendor get 90% of earning amount') }})</small>
                                            </div>
                                            
                                           
                                          <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Non Exclusive Author Commission') }} ({{ __('Percentage') }} %) <span class="require">*</span></label>
                                                <input id="site_non_exclusive_commission" name="site_non_exclusive_commission" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_non_exclusive_commission }}" required><small>({{ __('if admin set 10% so vendor get 90% of earning amount') }})</small>
                                            </div> 
                                           
                                            
                                            
                                         <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Minium withdrawal amount') }} ({{ $setting['setting']->site_currency }})<span class="require">*</span></label>
                                                <input id="site_minimum_withdrawal" name="site_minimum_withdrawal" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_minimum_withdrawal }}" required>
                                            </div>    
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Processing Fee') }} ({{ __('extra fee') }}) {{ __('Type') }}<span class="require">*</span></label>
                                                <select name="site_extra_fee_type" id="site_extra_fee_type" class="form-control" required>
                                                <option value=""></option>
                                                <option value="fixed" @if($additional['setting']->site_extra_fee_type == 'fixed') selected @endif>{{ __('Fixed') }}</option>
                                                <option value="percentage" @if($additional['setting']->site_extra_fee_type == 'percentage') selected @endif>{{ __('Percentage') }}</option>
                                                </select>
                                            </div>
                             
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Processing Fee') }} ({{ __('extra fee') }}) <span id="iffixed" @if($additional['setting']->site_extra_fee_type == 'fixed') class="inline-block" @else  class="display-none" @endif>({{ $setting['setting']->site_currency }})</span><span id="ifpercentage" @if($additional['setting']->site_extra_fee_type == 'percentage') class="inline-block" @else  class="display-none" @endif>(%)</span><span class="require">*</span></label>
                                                <input id="site_extra_fee" name="site_extra_fee" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_extra_fee }}" required><small>({{ __('if you will set "0" processing fee is OFF') }})</small>
                                            </div>
                                            
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Flash Sale') }} ({{ __('Percentage') }} %) <span class="require">*</span></label>
                                                <input id="flash_sale_value" name="flash_sale_value" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->flash_sale_value }}"  data-bvalidator="required,number,min[1],max[99]">
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
                                              <label for="product_approval" class="control-label mb-1">{{ __('Affiliate Referral') }}?<span class="require">*</span></label><br/>
                                              <select name="affiliate_referral" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($additional['setting']->affiliate_referral == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($additional['setting']->affiliate_referral == 0) selected @endif>{{ __('OFF') }}</option>
                                              </select>
                                              
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Per Sale Referral Commission') }} {{ __('Type') }}<span class="require">*</span></label>
                                                <select name="per_sale_referral_commission_type" id="per_sale_referral_commission_type" class="form-control" required>
                                                <option value=""></option>
                                                <option value="fixed" @if($additional['setting']->per_sale_referral_commission_type == 'fixed') selected @endif>{{ __('Fixed') }}</option>
                                                <option value="percentage" @if($additional['setting']->per_sale_referral_commission_type == 'percentage') selected @endif>{{ __('Percentage') }}</option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Per Sale Referral Commission') }} <span id="nfixed" @if($additional['setting']->per_sale_referral_commission_type == 'fixed') class="inline-block" @else  class="display-none" @endif>({{ $setting['setting']->site_currency }})</span><span id="npercentage" @if($additional['setting']->per_sale_referral_commission_type == 'percentage') class="inline-block" @else  class="display-none" @endif>(%)</span><span class="require">*</span></label>
                                                <input id="per_sale_referral_commission" name="per_sale_referral_commission" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->per_sale_referral_commission }}"  required>
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Sign Up') }} {{ __('Referral Commission') }} <span class="require">*</span></label>
                                                <input id="site_referral_commission" name="site_referral_commission" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_referral_commission }}"  required>
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Admin Payment Methods') }} </label><br/>
                                                @foreach($payment_option as $payment)
                                                <input id="payment_option" name="payment_option[]" type="checkbox" @if(in_array($payment,$get_payment)) checked @endif class="noscroll_textarea" value="{{ $payment }}"> {{ $payment }} <br/>
                                                @endforeach
                                             </div>
                                            
                                      
                                        
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Vendor Payment Methods') }} </label><br/>
                                                @foreach($vendor_payment_option as $payment)
                                                <input id="vendor_payment_option" name="vendor_payment_option[]" type="checkbox" @if(in_array($payment,$get_vendor_payment)) checked @endif class="noscroll_textarea" value="{{ $payment }}"> {{ $payment }} <br/>
                                                @endforeach
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Withdraw Methods') }} <span class="require">*</span></label><br/>
                                                @foreach($withdraw_option as $withdraw)
                                                <input id="withdraw_option" name="withdraw_option[]" type="checkbox" @if(in_array($withdraw->withdrawal_key,$get_withdraw)) checked @endif class="noscroll_textarea" value="{{ $withdraw->withdrawal_key }}" data-bvalidator="required"> {{ $withdraw->withdrawal_name }}<br/>
                                                @endforeach
                                             </div>
                                            
                                          
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                             
                             
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('Paypal Settings') }}</h4></div></div>
                             
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Paypal Email ID') }} </label><br/>
                                               <input id="paypal_email" name="paypal_email" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->paypal_email }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Paypal Mode') }} </label><br/>
                                               
                                                <select name="paypal_mode" class="form-control">
                                                <option value="1" @if($setting['setting']->paypal_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($setting['setting']->paypal_mode == 0) selected @endif>{{ __('Demo') }}</option>
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
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('2checkout Settings') }}</h4>
                             
                             </div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('2checkout Mode') }}</label><br/>
                                               
                                                <select name="two_checkout_mode" class="form-control">
                                                <option value="true" @if($setting['setting']->two_checkout_mode == 'true') selected @endif>{{ __('Live') }}</option>
                                                <option value="false" @if($setting['setting']->two_checkout_mode == 'false') selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('2Checkout Account Number') }}</label><br/>
                                               <input id="two_checkout_account" name="two_checkout_account" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->two_checkout_account }}">
                                                
                                                
                                             </div>
                                             
                                             <br/>
                                             <p>{{ __('2checkout callback url') }} : <code>{{ url('/') }}/2checkout-success</code> <br/> <a href="javascript:void(0);" data-toggle="modal" data-target="#myModal" class="blue-color">{{ __('How to configure callback url') }}?</a></p>
                                            
                                      
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            


                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('2Checkout Publishable Key') }}</label><br/>
                                               <input id="two_checkout_publishable" name="two_checkout_publishable" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->two_checkout_publishable }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('2Checkout Private Key') }}</label><br/>
                                               <input id="two_checkout_private" name="two_checkout_private" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->two_checkout_private }}">
                                                
                                                
                                             </div>
                                             
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('Paystack Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Paystack Default Currency Code') }} <span class="require">*</span></label><br/>
                                               <input id="paystack_default_currency" name="paystack_default_currency" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->paystack_default_currency }}" data-bvalidator="required">
                                                <small>Example : NGN</small>
                                                
                                             </div>
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Paystack Merchant Email') }}</label><br/>
                                               <input id="paystack_merchant_email" name="paystack_merchant_email" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->paystack_merchant_email }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Paystack Public Key') }}</label><br/>
                                               <input id="paystack_public_key" name="paystack_public_key" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->paystack_public_key }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Paystack Secret Key') }}</label><br/>
                                               <input id="paystack_secret_key" name="paystack_secret_key" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->paystack_secret_key }}">
                                                
                                                
                                             </div>
                                    
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Bank Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    
                                    <div class="form-group">
                                              <div style="height:0px;"></div>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Local Bank Details') }}</label><br/>
                                               <textarea name="local_bank_details" class="form-control noscroll_textarea" rows="5" cols="20">{{ $setting['setting']->local_bank_details }}</textarea>
                                                
                                                
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
                                               <strong>{{ __('example') }}:<br/><br/>

                                                {{ __('Bank Name') }} : Test Bank<br/>
                                                {{ __('Branch Name') }} : Test Branch<br/>
                                                {{ __('Branch Code') }} : 00000<br/>
                                                {{ __('IFSC Code') }} : 63632EF</strong>
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
                                               <input id="razorpay_key" name="razorpay_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->razorpay_key }}">
                                                
                                                
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
                                               <input id="razorpay_secret" name="razorpay_secret" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->razorpay_secret }}">
                                                
                                                
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
                                                <option value="1" @if($additional['setting']->payhere_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($additional['setting']->payhere_mode == 0) selected @endif>{{ __('Demo') }}</option>
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
                                               <input id="payhere_merchant_id" name="payhere_merchant_id" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->payhere_merchant_id }}">
                                                
                                                
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
                                                <option value="1" @if($additional['setting']->payumoney_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($additional['setting']->payumoney_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Payumoney Merchant Key</label><br/>
                                               <input id="payu_merchant_key" name="payu_merchant_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->payu_merchant_key }}">
                                                
                                                
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
                                               <input id="payu_salt_key" name="payu_salt_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->payu_salt_key }}">
                                                
                                                
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
                                                <option value="1" @if($additional['setting']->iyzico_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($additional['setting']->iyzico_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Iyzico API Key</label><br/>
                                               <input id="iyzico_api_key" name="iyzico_api_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->iyzico_api_key }}">
                                                
                                                
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
                                               <input id="iyzico_secret_key" name="iyzico_secret_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->iyzico_secret_key }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Flutterwave Default Currency Code') }} <span class="require">*</span></label><br/>
                                               <input id="flutterwave_default_currency" name="flutterwave_default_currency" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->flutterwave_default_currency }}" data-bvalidator="required">
                                                <small>Example : NGN</small>
                                                
                                             </div>
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Flutterwave Public Key</label><br/>
                                               <input id="flutterwave_public_key" name="flutterwave_public_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->flutterwave_public_key }}">
                                                
                                                
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
                                               <input id="flutterwave_secret_key" name="flutterwave_secret_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->flutterwave_secret_key }}">
                                                
                                                
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
                                                <option value="1" @if($additional['setting']->coingate_mode == 1) selected @endif>Live</option>
                                                <option value="0" @if($additional['setting']->coingate_mode == 0) selected @endif>Demo</option>
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
                                               <input id="coingate_auth_token" name="coingate_auth_token" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->coingate_auth_token }}">
                                                
                                                
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
                                                <option value="1" @if($additional['setting']->ipay_mode == 1) selected @endif>Live</option>
                                                <option value="0" @if($additional['setting']->ipay_mode == 0) selected @endif>Demo</option>
                                                </select>
                                                
                                             </div>
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">iPay Vendor ID</label><br/>
                                               <input id="ipay_vendor_id" name="ipay_vendor_id" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->ipay_vendor_id }}">
                                                
                                                
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
                                               <input id="ipay_hash_key" name="ipay_hash_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->ipay_hash_key }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('PayFast Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('PayFast Merchant Id') }}</label><br/>
                                               <input id="payfast_merchant_id" name="payfast_merchant_id" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->payfast_merchant_id }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('PayFast Merchant Key') }}</label><br/>
                                               <input id="payfast_merchant_key" name="payfast_merchant_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->payfast_merchant_key }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('PayFast Mode') }}</label><br/>
                                               
                                                <select name="payfast_mode" class="form-control">
                                                <option value="1" @if($additional['setting']->payfast_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($additional['setting']->payfast_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('CoinPayments') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('CoinPayments Merchant ID') }}</label><br/>
                                               <input id="coinpayments_merchant_id" name="coinpayments_merchant_id" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->coinpayments_merchant_id }}">
                                                
                                                
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
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('SSLCommerz Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('SSLCommerz Store Id') }}</label><br/>
                                               <input id="sslcommerz_store_id" name="sslcommerz_store_id" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->sslcommerz_store_id }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('SSLCommerz Store Password') }}</label><br/>
                                               <input id="sslcommerz_store_password" name="sslcommerz_store_password" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->sslcommerz_store_password }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('SSLCommerz Mode') }}</label><br/>
                                               
                                                <select name="sslcommerz_mode" class="form-control" data-bvalidator="required">
                                                <option value="FALSE" @if($additional['setting']->sslcommerz_mode == 'FALSE') selected @endif>{{ __('Live') }}</option>
                                                <option value="TRUE" @if($additional['setting']->sslcommerz_mode == 'TRUE') selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Instamojo Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Instamojo API Key') }}</label><br/>
                                               <input id="instamojo_api_key" name="instamojo_api_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->instamojo_api_key }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Instamojo Auth Token') }}</label><br/>
                                               <input id="instamojo_auth_token" name="instamojo_auth_token" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->instamojo_auth_token }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Instamojo Mode') }}</label><br/>
                                               
                                                <select name="instamojo_mode" class="form-control" data-bvalidator="required">
                                                <option value="1" @if($additional['setting']->instamojo_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($additional['setting']->instamojo_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Aamarpay Settings') }}</h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Aamarpay Mode') }} </label><br/>
                                               
                                                <select name="aamarpay_mode" class="form-control">
                                                <option value="1" @if($additional['setting']->aamarpay_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($additional['setting']->aamarpay_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Store ID') }} </label><br/>
                                               <input id="aamarpay_store_id" name="aamarpay_store_id" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->aamarpay_store_id }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Signature Key') }} </label><br/>
                                               <input id="aamarpay_signature_key" name="aamarpay_signature_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->aamarpay_signature_key }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Mollie Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Mollie API Key') }}</label><br/>
                                               <input id="mollie_api_key" name="mollie_api_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->mollie_api_key }}">
                                                
                                                
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
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Robokassa Settings') }}</h4>
                             
                             </div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Shop Identifier') }}</label><br/>
                                               <input id="shop_identifier" name="shop_identifier" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->shop_identifier }}">
                                                
                                                
                                             </div>
                                             
                                             <br/>
                                             <p>{{ __('Robokassa Success Url') }} : <code>{{ url('/') }}/robokassa-success</code> <br/> <a href="javascript:void(0);" data-toggle="modal" data-target="#myModal_two" class="blue-color">{{ __('How to configure success url') }}?</a></p>
                                            <p>{{ __('Robokassa Failed Url') }} : <code>{{ url('/') }}/cancel</code> <br/> <a href="javascript:void(0);" data-toggle="modal" data-target="#myModal_two" class="blue-color">{{ __('How to configure failed url') }}?</a></p>
                                      
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            


                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Robokassa Password #1') }}</label><br/>
                                               <input id="robokassa_password_1" name="robokassa_password_1" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->robokassa_password_1 }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                             
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Mercadopago Settings') }}</h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Mercadopago Public Key') }}</label><br/>
                                               <input id="mercadopago_client_id" name="mercadopago_client_id" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->mercadopago_client_id }}">
                                                
                                                
                                             </div>
                                            
                                          
                                          <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Mercadopago Access Token') }}</label><br/>
                                               <input id="mercadopago_client_secret" name="mercadopago_client_secret" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->mercadopago_client_secret }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Mercadopago Mode') }}</label><br/>
                                               
                                                <select name="mercadopago_mode" class="form-control">
                                                <option value="1" @if($additional['setting']->mercadopago_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($additional['setting']->mercadopago_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                            
                                          
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Midtrans Settings') }}</h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Midtrans Server Key') }}</label><br/>
                                               <input id="midtrans_server_key" name="midtrans_server_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->midtrans_server_key }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Midtrans Mode') }}</label><br/>
                                               
                                                <select name="midtrans_mode" class="form-control">
                                                <option value="1" @if($additional['setting']->midtrans_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($additional['setting']->midtrans_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                            
                                          
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Coinbase Settings') }}</h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Coinbase Api Key') }}</label><br/>
                                               <input id="coinbase_api_key" name="coinbase_api_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->coinbase_api_key }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Coinbase Secret Key') }}</label><br/>
                                               
                                                <input id="coinbase_secret_key" name="coinbase_secret_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->coinbase_secret_key }}">
                                                
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
                            <p>{{ __('Coinbase Checkout Webhook URL') }} : <code>{{ url('/') }}/webhooks/coinbase-checkout</code></p>
                            <p>{{ __('Coinbase Subscription Webhook URL') }} : <code>{{ url('/') }}/webhooks/coinbase-subscription</code></p>
                            <p>{{ __('Coinbase Deposit Webhook URL') }} : <code>{{ url('/') }}/webhooks/coinbase-deposit</code></p>
                            <p><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal_three" class="blue-color">{{ __('How to configure webhooks url') }}?</a></p>
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
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Cashfree Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Cashfree Mode') }}</label><br/>
                                               
                                                <select name="cashfree_mode" class="form-control">
                                                <option value="1" @if($additional['setting']->cashfree_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($additional['setting']->cashfree_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Cashfree API Key') }}</label><br/>
                                               <input id="cashfree_api_key" name="cashfree_api_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->cashfree_api_key }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Cashfree API Secret') }}</label><br/>
                                               <input id="cashfree_api_secret" name="cashfree_api_secret" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->cashfree_api_secret }}">
                                                
                                                
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
                            <p>{{ __('Go To Whitelisting URL') }} : <code>https://merchant.cashfree.com/merchants/pg/developers/whitelisting?env=prod</code></p>
                            <p><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal_four" class="blue-color">{{ __('How To Put My Domain') }}?</a></p>
                             </div>
                            </div>
                            </div>                
                            </div>            
                            </div>
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('NowPayments Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('NowPayments Mode') }}</label><br/>
                                               
                                                <select name="nowpayments_mode" class="form-control">
                                                <option value="1" @if($additional['setting']->nowpayments_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($additional['setting']->nowpayments_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('NowPayments API Key') }}</label><br/>
                                               <input id="nowpayments_api_key" name="nowpayments_api_key" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->nowpayments_api_key }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('NowPayments IPN Secret') }}</label><br/>
                                               <input id="nowpayments_ipn_secret" name="nowpayments_ipn_secret" type="text" class="form-control noscroll_textarea" value="{{ $additional['setting']->nowpayments_ipn_secret }}">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Stripe Settings') }}</h4></div></div>
                             
                             
                              <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Stripe Mode') }} </label><br/>
                                               
                                                <select name="stripe_mode" class="form-control">
                                                <option value="1" @if($setting['setting']->stripe_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($setting['setting']->stripe_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Test Publishable Key') }} </label><br/>
                                               <input id="test_publish_key" name="test_publish_key" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->test_publish_key }}">
                                                
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Live Publishable Key') }} </label><br/>
                                               <input id="live_publish_key" name="live_publish_key" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->live_publish_key }}">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Stripe Payment Type') }} </label><br/>
                                               
                                                <select name="stripe_type" class="form-control">
                                                <option value="charges" @if($setting['setting']->stripe_type == 'charges') selected @endif>{{ __('Charges API') }}</option>
                                                <option value="intents" @if($setting['setting']->stripe_type == 'intents') selected @endif>{{ __('Intents API') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Test Secret Key') }} </label><br/>
                                               <input id="test_secret_key" name="test_secret_key" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->test_secret_key }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Live Secret Key') }} </label><br/>
                                               <input id="live_secret_key" name="live_secret_key" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->live_secret_key }}">
                                                
                                                
                                             </div>
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                             <div class="col-md-12 no-padding">
                             <div class="card-footer">
                                                        <button type="submit" name="submit" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-dot-circle-o"></i> {{ __('Submit') }}
                                                        </button>
                                                        <button type="reset" class="btn btn-danger btn-sm">
                                                            <i class="fa fa-ban"></i> {{ __('Reset') }}
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
    @else
    @include('admin.denied')
    @endif
    <!-- Right Panel -->


   @include('admin.javascript')
<div id="myModal" class="modal fade 2checkout" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-body">
            <img class="lazy" width="1223" height="678" src="{{ url('/') }}/public/img/2checkout_info.png"  class="img-responsive">
        </div>
    </div>
  </div>
</div>
<div id="myModal_two" class="modal fade 2checkout" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-body">
            <img class="lazy" width="1223" height="678" src="{{ url('/') }}/resources/views/assets/robokassa_info.png"  class="img-responsive">
        </div>
    </div>
  </div>
</div>
<div id="myModal_three" class="modal fade 2checkout" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-body">
            <img class="lazy" width="1223" height="678" src="{{ url('/') }}/resources/views/assets/coinbase_info.png"  class="img-responsive">
        </div>
    </div>
  </div>
</div>
<div id="myModal_four" class="modal fade 2checkout" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-body">
            <img class="lazy" width="1223" height="678" src="{{ url('/') }}/resources/views/theme/img/cashfree1.png"  class="img-responsive">
            <img class="lazy" width="1223" height="678" src="{{ url('/') }}/resources/views/theme/img/cashfree2.png"  class="img-responsive">
        </div>
    </div>
  </div>
</div>
</body>

</html>
