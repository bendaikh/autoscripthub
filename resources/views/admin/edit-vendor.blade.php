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
    @if(Auth::user()->id == 1)
    <div id="right-panel" class="right-panel">

        
                       @include('admin.header')
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Edit Vendor') }}</h1>
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
                           <form action="{{ route('admin.edit-vendor') }}" method="post" id="setting_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                           <div class="col-md-6">
                           <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Name') }} <span class="require">*</span></label>
                                                <input id="name" name="name" type="text" class="form-control" value="{{ $edit['userdata']->name }}" required>
                                            </div>
                                            
                                             <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Username') }} <span class="require">*</span></label>
                                                <input id="username" name="username" type="text" class="form-control" value="{{ $edit['userdata']->username }}" required>
                                            </div>
                                            
                                            
                                                <div class="form-group">
                                                    <label for="email" class="control-label mb-1">{{ __('Email') }} <span class="require">*</span></label>
                                                    <input id="email" name="email" type="email" class="form-control" value="{{ $edit['userdata']->email }}" required>
                                                   
                                                </div>
                                                
                                                <input type="hidden" name="user_type" value="vendor">
                                                
                                                <div class="form-group">
                                                    <label for="password" class="control-label mb-1">{{ __('Password') }}</label>
                                                    <input id="password" name="password" type="password" class="form-control">
                                                    
                                                </div>
                                                
                                                 <div class="form-group">
                                                    <label for="earnings" class="control-label mb-1">{{ __('Earnings') }} ({{ $allsettings->site_currency }})</label>
                                                    <input id="earnings" name="earnings" type="text" class="form-control" value="{{ $edit['userdata']->earnings }}">
                                                    
                                                </div>
                                                
                                                <div class="form-group">
                                                                    <label for="customer_earnings" class="control-label mb-1">{{ __('Upload Photo') }}</label>
                                                                    <input type="file" id="user_photo" name="user_photo" class="form-control-file">
                                                                </div>
                                                @if($edit['userdata']->user_photo != '')
                                                <img class="lazy userphoto" width="50" height="50" src="{{ url('/') }}/public/storage/users/{{ $edit['userdata']->user_photo }}"  />@else <img class="lazy userphoto" width="50" height="50" src="{{ url('/') }}/public/img/no-user.png"  />  @endif
                                                
                                                
                                                
                                                
                                                <?php /*?><input type="hidden" name="verified" value="1"> <?php */?>
                                                
                                                <input type="hidden" name="save_photo" value="{{ $edit['userdata']->user_photo }}">
                                                
                                                <input type="hidden" name="save_password" value="{{ $edit['userdata']->password }}">
                                                
                                                <input type="hidden" name="save_auth_token" value="{{ $edit['userdata']->user_auth_token }}">
                                                
                                                <input type="hidden" name="edit_id" value="{{ $token }}">
                                                
                                                <input type="hidden" name="page_redirect" value="vendor">
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Exclusive Author?') }}<span class="require">*</span></label>
                                                <select name="exclusive_author" class="form-control" required>
                                                <option value=""></option>
                                                <option value="1" @if($edit['userdata']->exclusive_author == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($edit['userdata']->exclusive_author == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                                </div>
                                         @if($addition_settings->subscription_mode == 1)                
                                                <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Subscription Type? <span class="require">*</span></label>
                                                <select name="subscription_type" class="form-control" required>
                                                <option value=""></option>
                                                <option value="none" @if($edit['userdata']->user_subscr_type == 'None') selected @endif>None</option>
                                                @if($addition_settings->free_subscription == 1)
                                                <option value="free" @if($edit['userdata']->user_subscr_type == 'Free') selected @endif>Free</option>
                                                @endif
                                                @foreach($subscribe['userdata'] as $subscribe)
                                                <option value="{{ $subscribe->subscr_id }}" @if($edit['userdata']->user_subscr_id == $subscribe->subscr_id) selected @endif>{{ $subscribe->subscr_name }}</option>
                                                @endforeach
                                                </select>
                                                </div>
                                                <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Account Verification? <span class="require">*</span></label>
                                                <select name="user_document_verified" class="form-control" required>
                                                <option value=""></option>
                                                <option value="1" @if($edit['userdata']->user_document_verified == 1) selected @endif>{{ __('verified') }}</option>
                                                <option value="0" @if($edit['userdata']->user_document_verified == 0) selected @endif>{{ __('unverified') }}</option>
                                                </select>
                                                </div>
                                                <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Payment Status') }}<span class="require">*</span></label>
                                                <select name="user_subscr_payment_status" class="form-control" required>
                                                <option value=""></option>
                                                <option value="pending" @if($edit['userdata']->user_subscr_payment_status == 'pending') selected @endif>{{ __('Pending') }}</option>
                                                <option value="completed" @if($edit['userdata']->user_subscr_payment_status == 'completed') selected @endif>{{ __('Completed') }}</option>
                                                </select>
                                                </div>
                                                @endif 
                                                
                                                <div class="form-group">
                                                    <label for="earnings" class="control-label mb-1">{{ __('Download') }} {{ __('Limited No of Items') }} ({{ __('Per Day') }}) <span class="require">*</span></label>
                                                    <input id="user_subscr_download_item" name="user_subscr_download_item" type="text" class="form-control" value="{{ $edit['userdata']->user_subscr_download_item }}"required>
                                                    
                                                </div>
                                                
                                                
                                                <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Email Verification? <span class="require">*</span></label>
                                                <select name="verified" class="form-control" required>
                                                <option value=""></option>
                                                <option value="1" @if($edit['userdata']->verified == 1) selected @endif>{{ __('verified') }}</option>
                                                <option value="0" @if($edit['userdata']->verified == 0) selected @endif>{{ __('unverified') }}</option>
                                                </select>
                                                </div>
                                    
                                          
                                     </div>
                                </div>
                            </div>
                           </div>
                           @if($check_payment == 1)
                           <div class="col-md-12"></div>
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                           
                              <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Vendor Payment Methods') }} </label><br/>
                                                @foreach($payment_option as $payment)
                                                <input id="user_payment_option" name="user_payment_option[]" type="checkbox" @if(in_array($payment,$get_payment)) checked @endif class="noscroll_textarea" value="{{ $payment }}"> {{ $payment }} <br/>
                                                @endforeach
                                             </div>
                             
                                            
                                      
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            @if(in_array('paypal',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Paypal Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Paypal Email ID') }} </label><br/>
                                               <input id="user_paypal_email" name="user_paypal_email" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_paypal_email }}">
                                                
                                                
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
                                               
                                                <select name="user_paypal_mode" class="form-control">
                                                <option value="1" @if($edit['userdata']->user_paypal_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($edit['userdata']->user_paypal_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                            
                                          
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('2checkout',$get_payment))
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('2checkout Settings') }}</h4>
                             
                             </div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('2checkout Mode') }}</label><br/>
                                               
                                                <select name="user_two_checkout_mode" class="form-control">
                                                <option value="true" @if($edit['userdata']->user_two_checkout_mode == 'true') selected @endif>{{ __('Live') }}</option>
                                                <option value="false" @if($edit['userdata']->user_two_checkout_mode == 'false') selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('2Checkout Account Number') }}</label><br/>
                                               <input id="user_two_checkout_account" name="user_two_checkout_account" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_two_checkout_account }}">
                                                
                                                
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
                                               <input id="user_two_checkout_publishable" name="user_two_checkout_publishable" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_two_checkout_publishable }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('2Checkout Private Key') }}</label><br/>
                                               <input id="user_two_checkout_private" name="user_two_checkout_private" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_two_checkout_private }}">
                                                
                                                
                                             </div>
                                             
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('razorpay',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>Razorpay Settings</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Razorpay Key Id</label><br/>
                                               <input id="user_razorpay_key" name="user_razorpay_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_razorpay_key }}">
                                                
                                                
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
                                               <input id="user_razorpay_secret" name="user_razorpay_secret" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_razorpay_secret }}">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('payhere',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>Payhere Settings</h4></div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Payhere Mode </label><br/>
                                               
                                                <select name="user_payhere_mode" class="form-control">
                                                <option value="1" @if($edit['userdata']->user_payhere_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($edit['userdata']->user_payhere_mode == 0) selected @endif>{{ __('Demo') }}</option>
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
                                               <input id="user_payhere_merchant_id" name="user_payhere_merchant_id" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_payhere_merchant_id }}">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('payumoney',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>Payumoney Settings</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Payumoney Mode </label><br/>
                                               
                                                <select name="user_payumoney_mode" class="form-control" required>
                                                <option value="1" @if($edit['userdata']->user_payumoney_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($edit['userdata']->user_payumoney_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Payumoney Merchant Key</label><br/>
                                               <input id="user_payu_merchant_key" name="user_payu_merchant_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_payu_merchant_key }}">
                                                
                                                
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
                                               <input id="user_payu_salt_key" name="user_payu_salt_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_payu_salt_key }}">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('iyzico',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>Iyzico Settings</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Iyzico Mode </label><br/>
                                               
                                                <select name="user_iyzico_mode" class="form-control" required>
                                                <option value="1" @if($edit['userdata']->user_iyzico_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($edit['userdata']->user_iyzico_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Iyzico API Key</label><br/>
                                               <input id="user_iyzico_api_key" name="user_iyzico_api_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_iyzico_api_key }}">
                                                
                                                
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
                                               <input id="user_iyzico_secret_key" name="user_iyzico_secret_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_iyzico_secret_key }}">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('flutterwave',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>Flutterwave Settings</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Flutterwave Public Key</label><br/>
                                               <input id="user_flutterwave_public_key" name="user_flutterwave_public_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_flutterwave_public_key }}">
                                                
                                                
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
                                               <input id="user_flutterwave_secret_key" name="user_flutterwave_secret_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_flutterwave_secret_key }}">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('coingate',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>Coingate Settings</h4></div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Coingate Mode</label><br/>
                                               
                                                <select name="user_coingate_mode" class="form-control">
                                                <option value="1" @if($edit['userdata']->user_coingate_mode == 1) selected @endif>Live</option>
                                                <option value="0" @if($edit['userdata']->user_coingate_mode == 0) selected @endif>Demo</option>
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
                                               <input id="user_coingate_auth_token" name="user_coingate_auth_token" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_coingate_auth_token }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('ipay',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>iPay Settings</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">iPay Mode</label><br/>
                                               
                                                <select name="user_ipay_mode" class="form-control" data-bvalidator="required">
                                                <option value="1" @if($edit['userdata']->user_ipay_mode == 1) selected @endif>Live</option>
                                                <option value="0" @if($edit['userdata']->user_ipay_mode == 0) selected @endif>Demo</option>
                                                </select>
                                                
                                             </div>
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">iPay Vendor ID</label><br/>
                                               <input id="user_ipay_vendor_id" name="user_ipay_vendor_id" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_ipay_vendor_id }}">
                                                
                                                
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
                                               <input id="user_ipay_hash_key" name="user_ipay_hash_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_ipay_hash_key }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('payfast',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('PayFast Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('PayFast Merchant Id') }}</label><br/>
                                               <input id="user_payfast_merchant_id" name="user_payfast_merchant_id" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_payfast_merchant_id }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('PayFast Merchant Key') }}</label><br/>
                                               <input id="user_payfast_merchant_key" name="user_payfast_merchant_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_payfast_merchant_key }}">
                                                
                                                
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
                                               
                                                <select name="user_payfast_mode" class="form-control">
                                                <option value="1" @if($edit['userdata']->user_payfast_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($edit['userdata']->user_payfast_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('coinpayments',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('CoinPayments') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('CoinPayments Merchant ID') }}</label><br/>
                                               <input id="user_coinpayments_merchant_id" name="user_coinpayments_merchant_id" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_coinpayments_merchant_id }}">
                                                
                                                
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
                            @endif
                            @if(in_array('instamojo',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Instamojo Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Instamojo API Key') }}</label><br/>
                                               <input id="user_instamojo_api_key" name="user_instamojo_api_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_instamojo_api_key }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Instamojo Auth Token') }}</label><br/>
                                               <input id="user_instamojo_auth_token" name="user_instamojo_auth_token" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_instamojo_auth_token }}">
                                                
                                                
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
                                               
                                                <select name="user_instamojo_mode" class="form-control" data-bvalidator="required">
                                                <option value="1" @if($edit['userdata']->user_instamojo_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($edit['userdata']->user_instamojo_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('aamarpay',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Aamarpay Settings') }}</h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Aamarpay Mode') }} </label><br/>
                                               
                                                <select name="user_aamarpay_mode" class="form-control">
                                                <option value="1" @if($edit['userdata']->user_aamarpay_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($edit['userdata']->user_aamarpay_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Store ID') }} </label><br/>
                                               <input id="user_aamarpay_store_id" name="user_aamarpay_store_id" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_aamarpay_store_id }}">
                                                
                                                
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
                                               <input id="user_aamarpay_signature_key" name="user_aamarpay_signature_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_aamarpay_signature_key }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('mollie',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Mollie Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Mollie API Key') }}</label><br/>
                                               <input id="user_mollie_api_key" name="user_mollie_api_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_mollie_api_key }}">
                                                
                                                
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
                            @endif
                            @if(in_array('robokassa',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Robokassa Settings') }}</h4>
                             
                             </div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Shop Identifier') }}</label><br/>
                                               <input id="user_shop_identifier" name="user_shop_identifier" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_shop_identifier }}">
                                                
                                                
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
                                               <input id="user_robokassa_password_1" name="user_robokassa_password_1" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_robokassa_password_1 }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            
                                             
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('mercadopago',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Mercadopago Settings') }}</h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Mercadopago Public Key') }}</label><br/>
                                               <input id="user_mercadopago_client_id" name="user_mercadopago_client_id" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_mercadopago_client_id }}">
                                                
                                                
                                             </div>
                                            
                                          
                                          <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Mercadopago Access Token') }}</label><br/>
                                               <input id="user_mercadopago_client_secret" name="user_mercadopago_client_secret" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_mercadopago_client_secret }}">
                                                
                                                
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
                                               
                                                <select name="user_mercadopago_mode" class="form-control">
                                                <option value="1" @if($edit['userdata']->user_mercadopago_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($edit['userdata']->user_mercadopago_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                            
                                          
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('midtrans',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Midtrans Settings') }}</h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Midtrans Server Key') }}</label><br/>
                                               <input id="user_midtrans_server_key" name="user_midtrans_server_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_midtrans_server_key }}">
                                                
                                                
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
                                               
                                                <select name="user_midtrans_mode" class="form-control">
                                                <option value="1" @if($edit['userdata']->user_midtrans_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($edit['userdata']->user_midtrans_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                            
                                          
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('coinbase',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Coinbase Settings') }}</h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Coinbase Api Key') }}</label><br/>
                                               <input id="user_coinbase_api_key" name="user_coinbase_api_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_coinbase_api_key }}">
                                                
                                                
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
                                               
                                                <input id="user_coinbase_secret_key" name="user_coinbase_secret_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_coinbase_secret_key }}">
                                                
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
                            @endif
                            @if(in_array('cashfree',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Cashfree Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Cashfree Mode') }}</label><br/>
                                               
                                                <select name="user_cashfree_mode" class="form-control">
                                                <option value="1" @if($edit['userdata']->user_cashfree_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($edit['userdata']->user_cashfree_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Cashfree API Key') }}</label><br/>
                                               <input id="user_cashfree_api_key" name="user_cashfree_api_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_cashfree_api_key }}">
                                                
                                                
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
                                               <input id="user_cashfree_api_secret" name="user_cashfree_api_secret" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_cashfree_api_secret }}">
                                                
                                                
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
                            @endif
                            @if(in_array('nowpayments',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('NowPayments Settings') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('NowPayments Mode') }}</label><br/>
                                               
                                                <select name="user_nowpayments_mode" class="form-control">
                                                <option value="1" @if($edit['userdata']->user_nowpayments_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($edit['userdata']->user_nowpayments_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                    
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('NowPayments API Key') }}</label><br/>
                                               <input id="user_nowpayments_api_key" name="user_nowpayments_api_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_nowpayments_api_key }}">
                                                
                                                
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
                                               <input id="user_nowpayments_ipn_secret" name="user_nowpayments_ipn_secret" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_nowpayments_ipn_secret }}">
                                                
                                                
                                             </div>
                                         
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @if(in_array('stripe',$get_payment))
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Stripe Settings') }}</h4></div></div>
                             
                             
                              <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Stripe Mode') }} </label><br/>
                                               
                                                <select name="user_stripe_mode" class="form-control">
                                                <option value="1" @if($edit['userdata']->user_stripe_mode == 1) selected @endif>{{ __('Live') }}</option>
                                                <option value="0" @if($edit['userdata']->user_stripe_mode == 0) selected @endif>{{ __('Demo') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Test Publishable Key') }} </label><br/>
                                               <input id="user_test_publish_key" name="user_test_publish_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_test_publish_key }}">
                                                
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Live Publishable Key') }} </label><br/>
                                               <input id="user_live_publish_key" name="user_live_publish_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_live_publish_key }}">
                                                
                                                
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
                                               
                                                <select name="user_stripe_type" class="form-control">
                                                <option value="charges" @if($edit['userdata']->user_stripe_type == 'charges') selected @endif>{{ __('Charges API') }}</option>
                                                <option value="intents" @if($edit['userdata']->user_stripe_type == 'intents') selected @endif>{{ __('Intents API') }}</option>
                                                </select>
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Test Secret Key') }} </label><br/>
                                               <input id="user_test_secret_key" name="user_test_secret_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_test_secret_key }}">
                                                
                                                
                                             </div>
                                           
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Live Secret Key') }} </label><br/>
                                               <input id="user_live_secret_key" name="user_live_secret_key" type="text" class="form-control noscroll_textarea" value="{{ $edit['userdata']->user_live_secret_key }}">
                                                
                                                
                                             </div>
                                         
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            @endif
                            @endif
                           <div class="col-md-12 no-padding">
                             <div class="card-footer">
                                 <button type="submit" name="submit" class="btn btn-primary btn-sm"><i class="fa fa-dot-circle-o"></i> {{ __('Submit') }}</button>
                                 <button type="reset" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> {{ __('Reset') }} </button>
                                 <a href="{{ url('/customer') }}/{{ $encrypter->encrypt($edit['userdata']->user_token) }}" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-user"></i> Login as vendor </a>
                             </div>
                             </div>
                            </form>
                         </div> 
                     </div>
                </div>
            </div><!-- .animated -->
        </div>
        
        <!-- .content -->


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
