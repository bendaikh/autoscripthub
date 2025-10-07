<div class="page-title-overlap pt-4" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Profile Settings') }}</li>
            </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Profile Settings') }}</h1>
        </div>
      </div>
    </div>
<div class="container pb-5 mb-2 mb-md-3">
      <div class="row">
        <!-- Sidebar-->
        <aside class="col-lg-4 pt-5 mt-3">
            <div class="d-block d-lg-none p-4">
            <a class="btn btn-outline-accent d-block" href="#account-menu" data-toggle="collapse"><i class="dwg-menu mr-2"></i>{{ __('Account menu') }}</a></div>
          @include('dashboard-menu')
        </aside>
        <!-- Content  -->
        <section class="col-lg-8">
          <!-- Toolbar-->
          <div class="d-none d-lg-flex justify-content-between align-items-center pt-lg-3 pb-4 pb-lg-5 mb-lg-3">
            <h6 class="font-size-base text-light mb-0">{{ __('Update you profile details below') }}</h6><a class="btn btn-primary btn-sm" href="{{ url('/logout') }}"><i class="dwg-sign-out mr-2"></i>{{ __('Logout') }}</a>
          </div>
          @if($addition_settings->subscription_mode == 1)
          @if(Auth::user()->user_type == 'vendor') 
          @if(Auth::user()->user_subscr_type != '')
          <h4 class="h4 py-2 text-center text-sm-left">{{ Auth::user()->user_subscr_type }} {{ __('Membership') }}</h4>
          <div class="row mx-n2 pt-2">
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Total Item Limit') }}</h3>
                    @if(Auth::user()->user_subscr_item_level == 'limited')
                    <p class="h3 mb-2">{{ Auth::user()->user_subscr_item }}</p>
                    @else
                    <p class="h3 mb-2">{{ __('Unlimited') }}</p>
                    @endif
                  </div>
                </div>
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Total Storage Space') }}</h3>
                    @if(Auth::user()->user_subscr_space_level == 'limited')
                    <p class="h3 mb-2">{{ Auth::user()->user_subscr_space }} {{ Auth::user()->user_subscr_space_type }}</p>
                    @else
                    <p class="h3 mb-2">{{ __('Unlimited') }}</p>
                    @endif
                  </div>
                </div>
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Expire On') }}</h3>
                    <p class="h3 mb-2">{{ date('d M Y',strtotime(Auth::user()->user_subscr_date)) }}</p>
                  </div>
                </div>
                @if(Auth::user()->user_subscr_space_level == 'limited')
                <?php /*?><div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Used Storage Space') }}</h3>
                    <p class="h3 mb-2">{{ Helper::formatSizeUnits(Helper::available_space(Auth::user()->id)) }}</p>
                  </div>
                </div><?php */?>
                @endif
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Uploaded Items') }}</h3>
                    <p class="h3 mb-2">{{ Helper::uploaded_item(Auth::user()->id) }}</p>
                  </div>
                </div>
                @if(Auth::user()->user_subscr_item_level == 'limited')
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Available Items Limit') }}</h3>
                    <p class="h3 mb-2">{{ Auth::user()->user_subscr_item - Helper::uploaded_item(Auth::user()->id) }}</p>
                  </div>
                </div>
                @endif
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Download items per day') }}</h3>
                    <p class="h3 mb-2">{{ Auth::user()->user_subscr_download_item }}</p>
                  </div>
                </div>
                <div class="col-md-4 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Today download items limit') }}</h3>
                    <p class="h3 mb-2">{{ Auth::user()->user_today_download_limit }}</p>
                  </div>
                </div>
              </div>
            @endif
            @else
            <div class="row mx-n2 pt-2">
            <div class="col-md-6 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Download items per day') }}</h3>
                    <p class="h3 mb-2">{{ Auth::user()->user_subscr_download_item }}</p>
                  </div>
                </div>
                <div class="col-md-6 col-sm-12 px-2 mb-4">
                  <div class="bg-secondary h-100 rounded-lg p-4 text-center">
                    <h3 class="font-size-sm text-muted">{{ __('Today download items limit') }}</h3>
                    <p class="h3 mb-2">{{ Auth::user()->user_today_download_limit }}</p>
                  </div>
                </div>
            </div>    
            @endif 
            @endif 
          <!-- Profile form-->
          <form action="{{ route('profile-settings') }}" class="needs-validation" id="profile_form" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-sm-12 mb-1">
              <h4>{{ __('Profile Information') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-fn">{{ __('Name') }} <span class="require">*</span></label>
                  <input type="text" id="name" name="name" class="form-control" placeholder="{{ __('Name') }}" value="{{ Auth::user()->name }}" data-bvalidator="required" readonly="readonly">
                  <small class="red">{{ __('To change your Name please contact our') }} <a href="{{ URL::to('/contact') }}" class="text-decord">{{ __('Support') }}</a></small>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-ln">{{ __('Username') }} <span class="require">*</span></label>
                  <input type="text" id="username" name="username" class="form-control" placeholder="{{ __('Username') }}" value="{{ Auth::user()->username }}" data-bvalidator="required" readonly="readonly">
                  <small>{{ __('Your Marketplace URL') }}: {{ URL::to('/') }}/user/<span>{{ Auth::user()->username }}</span></small><br/>
                  <small class="red">{{ __('To change your Username please contact our') }} <a href="{{ URL::to('/contact') }}" class="text-decord">{{ __('Support') }}</a></small>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Email Address') }} <span class="require">*</span></label>
                  <input type="text" id="email" name="email" class="form-control" placeholder="{{ __('Email Address') }}" value="{{ Auth::user()->email }}" data-bvalidator="required,email" readonly="readonly">      <small class="red">{{ __('To change your E-mail address please contact our') }} <a href="{{ URL::to('/contact') }}" class="text-decord">{{ __('Support') }}</a></small>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-pass">{{ __('Password') }}</label>
                  <div class="password-toggle">
                    <input id="password" name="password" type="password" class="form-control">
                    <label class="password-toggle-btn">
                      <input class="custom-control-input" type="checkbox"><i class="dwg-eye password-toggle-indicator"></i><span class="sr-only">{{ __('Show password') }}</span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-pass">{{ __('Confirm Password') }}</label>
                  <div class="password-toggle">
                  <input type="password" name="password_confirmation" id="password-confirm" class="form-control" data-bvalidator="equal[password]" placeholder="">
                   <label class="password-toggle-btn">
                      <input class="custom-control-input" type="checkbox"><i class="dwg-eye password-toggle-indicator"></i><span class="sr-only">{{ __('Show password') }}</span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Website') }}</label>
                  <input type="text" id="website" name="website" class="form-control" placeholder="" value="{{ Auth::user()->website }}">
                </div>
              </div>
              @if(Auth::user()->user_type == 'customer')
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Become a vendor?') }}</label><br/>
                  <input  type="checkbox" name="become-vendor" id="ch2" value="1">
                  <span class="become_vendor"><small>({{ __('if checked you will change to vendor account') }})</small></span>
                </div>
              </div>
              @endif
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Country') }} <span class="require">*</span></label>
                  <select name="country" id="country" class="form-control" data-bvalidator="required">
                                    <option value=""></option>
                                    @foreach($country['country'] as $country)
                                    <option value="{{ $country->country_id }}" @if(Auth::user()->country == $country->country_id ) selected="selected" @endif>{{ $country->country_name }}</option>
                             @endforeach
                     </select>       
                </div>
              </div>
              @if(Auth::user()->user_type == 'vendor')
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Available Freelance Work?') }} <span class="require">*</span></label>
                  <select name="user_freelance" id="user_freelance" class="form-control" data-bvalidator="required">
                       <option value=""></option>
                       <option value="1" @if(Auth::user()->user_freelance == 1 ) selected="selected" @endif>{{ __('Yes') }}</option>
                       <option value="0" @if(Auth::user()->user_freelance == 0 ) selected="selected" @endif>{{ __('No') }}</option>
                  </select>       
                </div>
              </div>
              @endif
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Display Country Badge?') }} <span class="require">*</span></label>
                  <select name="country_badge" id="country_badge" class="form-control" data-bvalidator="required">
                     <option value=""></option>
                     <option value="1" @if(Auth::user()->country_badge == 1 ) selected="selected" @endif>{{ __('Yes') }}</option>
                     <option value="0" @if(Auth::user()->country_badge == 0 ) selected="selected" @endif>{{ __('No') }}</option>
                  </select>       
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Exclusive Author?') }} <span class="require">*</span></label>
                  <select name="exclusive_author" id="exclusive_author" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->exclusive_author == 1 ) selected="selected" @endif>{{ __('Yes') }}</option>
                      <option value="0" @if(Auth::user()->exclusive_author == 0 ) selected="selected" @endif>{{ __('No') }}</option>
                  </select>    
                  <small>({{ __('if selected') }} <strong>"{{ __('Yes') }}"</strong> {{ __('you will get high earning') }})</small>   
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Profile Heading') }}</label>
                  <input type="text" id="profile_heading" class="form-control" name="profile_heading" placeholder="{{ __('Ex: Web Development Service') }}" value="{{ Auth::user()->profile_heading }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('About') }}</label>
                  <textarea name="about" id="about" class="form-control" placeholder="{{ __('Short brief about yourself or your account...') }}">{{ Auth::user()->about }}</textarea>
                </div>
              </div>
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Profile Image & Cover Image') }}</h4>
              </div>
              <div class="col-sm-6">
              <div class="form-group pb-2">
                  <label for="account-confirm-pass">{{ __('Profile Image') }} (100x100 px)</label>
                  <div class="custom-file">
                  <input class="custom-file-input" type="file" id="unp-product-files" name="user_photo" data-bvalidator="extension[jpg:png:jpeg]" data-bvalidator-msg="{{ __('Please select file of type .jpg, .png or .jpeg') }}">
                  <label class="custom-file-label" for="unp-product-files"></label>
                  @if(Auth::user()->user_photo != '')
                  <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/users/{{ Auth::user()->user_photo }}"  alt="{{ Auth::user()->name }}">
                  @else
                  <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ Auth::user()->name }}">
                  @endif
                  </div>
                </div>
              </div> 
              <div class="col-sm-6">
              <div class="form-group pb-2">
                  <label for="account-confirm-pass">{{ __('Cover Image') }} (750x370 px)</label>
                  <div class="custom-file">
                  <input class="custom-file-input" type="file" id="unp-product-files" name="user_banner" data-bvalidator="extension[jpg:png:jpeg]" data-bvalidator-msg="{{ __('Please select file of type .jpg, .png or .jpeg') }}">
                  <label class="custom-file-label" for="unp-product-files"></label>
                  @if(Auth::user()->user_banner != '')
                  <img class="lazy" width="100" height="100" src="{{ url('/') }}/public/storage/users/{{ Auth::user()->user_banner }}"  alt="{{ Auth::user()->name }}">
                  @else
                  <img class="lazy" width="100" height="100" src="{{ url('/') }}/public/img/no-image.png"  alt="{{ Auth::user()->name }}">
                  @endif
                  </div>
                </div>
              </div>
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Social Profiles') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Facebook Url') }}</label>
                  <input type="text" class="form-control" name="facebook_url" value="{{ Auth::user()->facebook_url }}" placeholder="ex: https://www.facebook.com">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Twitter Url') }}</label>
                  <input type="text" class="form-control" name="twitter_url" value="{{ Auth::user()->twitter_url }}" placeholder="ex: https://www.twitter.com">
                </div>
              </div>
              <input type="hidden" name="gplus_url" value="{{ Auth::user()->gplus_url }}" />
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Instagram Url') }}</label>
                  <input type="text" class="form-control" name="instagram_url" value="{{ Auth::user()->instagram_url }}" placeholder="ex: https://www.instagram.com">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Linkedin Url') }}</label>
                  <input type="text" class="form-control" name="linkedin_url" value="{{ Auth::user()->linkedin_url }}" placeholder="ex: https://www.linkedin.com">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Pinterest Url') }}</label>
                  <input type="text" class="form-control" name="pinterest_url" value="{{ Auth::user()->pinterest_url }}" placeholder="ex: https://www.pinterest.com">
                </div>
              </div>
              <div class="col-sm-6">
              </div>
              <?php /*?>@if(Auth::user()->user_type == 'vendor')<?php */?>
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Email Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Item Update Notifications') }}</label><br/>
                  <input type="checkbox" id="opt2" class="" name="item_update_email" value="1" @if(Auth::user()->item_update_email == 1) checked @endif>
                  <span class="become_vendor"><small>{{ __("Send an email when an item I've purchased is updated") }}</small></span>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Item Comment Notifications') }}</label><br/>
                  <input type="checkbox" id="opt3" class="" name="item_comment_email" value="1" @if(Auth::user()->item_comment_email == 1) checked @endif>
                  <span class="become_vendor"><small>{{ __('Send me an email when someone comments on one of my items') }}</small></span>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Item Review Notifications') }}</label><br/>
                  <input type="checkbox" id="opt4" class="" name="item_review_email" value="1" @if(Auth::user()->item_review_email == 1) checked @endif>
                  <span class="become_vendor"><small>{{ __('Send me an email when my items are approved or rejected') }}</small></span>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Buyer Review Notifications') }}</label><br/>
                  <input type="checkbox" id="opt5" class="" name="buyer_review_email" value="1" @if(Auth::user()->buyer_review_email == 1) checked @endif>
                  <span class="become_vendor"><small>{{ __('Send me an email when someone leaves a review with their rating') }}</small></span>
                </div>
              </div>
              <?php /*?>@endif<?php */?>
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Message Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Message Permission') }}</label><br/>
                  <input type="checkbox" id="opt2" class="" name="user_message_permission" value="1" @if(Auth::user()->user_message_permission == 1) checked @endif>
                  <span class="become_vendor"><small>{{ __('Send me messages for customer / vendor') }}</small></span>
                </div>
              </div>
              @if($addition_settings->subscription_mode == 1)
              @if($count_mode == 1)
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Payment Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Payment Methods') }} <span class="require">*</span></label><br/>
                  @foreach($payment_option as $payment)
                  
                  <input type="checkbox" id="opt2" class="" name="user_payment_option[]" value="{{ $payment }}" @if(in_array($payment,$get_payment)) checked @endif data-bvalidator="required">
                  <span class="become_vendor">{{ $payment }}</span><br/>
                  
                  @endforeach
                </div>
              </div>
              @if(in_array('paypal',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Paypal Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Paypal Email ID') }}</label>
                  <input type="text" class="form-control" name="user_paypal_email" value="{{ Auth::user()->user_paypal_email }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Paypal Mode') }} <span class="require">*</span></label>
                  <select name="user_paypal_mode" id="user_paypal_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_paypal_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_paypal_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              @endif
              @if(in_array('2checkout',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('2checkout Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('2checkout Mode') }} <span class="require">*</span></label>
                  <select name="user_two_checkout_mode" id="user_two_checkout_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_two_checkout_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_two_checkout_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('2Checkout Account Number') }}</label>
                  <input type="text" class="form-control" name="user_two_checkout_account" value="{{ Auth::user()->user_two_checkout_account }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('2Checkout Publishable Key') }}</label>
                  <input type="text" class="form-control" name="user_two_checkout_publishable" value="{{ Auth::user()->user_two_checkout_publishable }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('2Checkout Private Key') }}</label>
                  <input type="text" class="form-control" name="user_two_checkout_private" value="{{ Auth::user()->user_two_checkout_private }}">
                </div>
              </div>
              @endif
              <input type="hidden" name="user_paystack_public_key" value="{{ Auth::user()->user_paystack_public_key }}" />
              <input type="hidden" name="user_paystack_secret_key" value="{{ Auth::user()->user_paystack_secret_key }}" />
              <input type="hidden" name="user_paystack_merchant_email" value="{{ Auth::user()->user_paystack_merchant_email }}" />
              <?php /*?><div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Paystack Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Paystack Public Key') }}</label>
                  <input type="text" class="form-control" name="user_paystack_public_key" value="{{ Auth::user()->user_paystack_public_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Paystack Secret Key') }}</label>
                  <input type="text" class="form-control" name="user_paystack_secret_key" value="{{ Auth::user()->user_paystack_secret_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Paystack Merchant Email') }}</label>
                  <input type="text" class="form-control" name="user_paystack_merchant_email" value="{{ Auth::user()->user_paystack_merchant_email }}">
                </div>
              </div><?php */?>
              @if(in_array('razorpay',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Razorpay Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Razorpay Key Id') }}</label>
                  <input type="text" class="form-control" name="user_razorpay_key" value="{{ Auth::user()->user_razorpay_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Razorpay Secret Key') }}</label>
                  <input type="text" class="form-control" name="user_razorpay_secret" value="{{ Auth::user()->user_razorpay_secret }}">
                </div>
              </div>
              @endif
              @if(in_array('payhere',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Payhere Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Payhere Mode') }} <span class="require">*</span></label>
                  <select name="user_payhere_mode" id="user_payhere_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_payhere_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_payhere_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Payhere Merchant Id') }}</label>
                  <input type="text" class="form-control" name="user_payhere_merchant_id" value="{{ Auth::user()->user_payhere_merchant_id }}">
                </div>
              </div>
              @endif
              @if(in_array('payumoney',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Payumoney Settings') }}</h4>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label for="account-email">{{ __('Payumoney Mode') }} <span class="require">*</span></label>
                  <select name="user_payumoney_mode" id="user_payumoney_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_payumoney_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_payumoney_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Payumoney Merchant Key') }}</label>
                  <input type="text" class="form-control" name="user_payu_merchant_key" value="{{ Auth::user()->user_payu_merchant_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Payumoney Salt Key') }}</label>
                  <input type="text" class="form-control" name="user_payu_salt_key" value="{{ Auth::user()->user_payu_salt_key }}">
                </div>
              </div>
              @endif
              @if(in_array('iyzico',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Iyzico Settings') }}</h4>
              </div>
              <div class="col-sm-12">
                <div class="form-group">
                  <label for="account-email">{{ __('Iyzico Mode') }} <span class="require">*</span></label>
                  <select name="user_iyzico_mode" id="user_iyzico_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_iyzico_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_iyzico_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Iyzico API Key') }}</label>
                  <input type="text" class="form-control" name="user_iyzico_api_key" value="{{ Auth::user()->user_iyzico_api_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Iyzico Secret Key') }}</label>
                  <input type="text" class="form-control" name="user_iyzico_secret_key" value="{{ Auth::user()->user_iyzico_secret_key }}">
                </div>
              </div>
              @endif
              @if(in_array('flutterwave',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Flutterwave Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Flutterwave Public Key') }}</label>
                  <input type="text" class="form-control" name="user_flutterwave_public_key" value="{{ Auth::user()->user_flutterwave_public_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Flutterwave Secret Key') }}</label>
                  <input type="text" class="form-control" name="user_flutterwave_secret_key" value="{{ Auth::user()->user_flutterwave_secret_key }}">
                </div>
              </div>
              @endif
              @if(in_array('coingate',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Coingate Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Coingate Mode') }} <span class="require">*</span></label>
                  <select name="user_coingate_mode" id="user_coingate_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_coingate_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_coingate_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Coingate Auth Token') }}</label>
                  <input type="text" class="form-control" name="user_coingate_auth_token" value="{{ Auth::user()->user_coingate_auth_token }}">
                </div>
              </div>
              @endif
              @if(in_array('ipay',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('iPay Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('iPay Mode') }} <span class="require">*</span></label>
                  <select name="user_ipay_mode" id="user_ipay_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_ipay_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_ipay_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Vendor ID') }}</label>
                  <input type="text" class="form-control" name="user_ipay_vendor_id" value="{{ Auth::user()->user_ipay_vendor_id }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('iPay API / Hash Key') }}</label>
                  <input type="text" class="form-control" name="user_ipay_hash_key" value="{{ Auth::user()->user_ipay_hash_key }}">
                </div>
              </div>
              @endif
              @if(in_array('payfast',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('PayFast Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('PayFast Mode') }} <span class="require">*</span></label>
                  <select name="user_payfast_mode" id="user_payfast_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_payfast_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_payfast_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('PayFast Merchant Id') }}</label>
                  <input type="text" class="form-control" name="user_payfast_merchant_id" value="{{ Auth::user()->user_payfast_merchant_id }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('PayFast Merchant Key') }}</label>
                  <input type="text" class="form-control" name="user_payfast_merchant_key" value="{{ Auth::user()->user_payfast_merchant_key }}">
                </div>
              </div>
              @endif
              @if(in_array('coinpayments',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('CoinPayments') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('CoinPayments Merchant ID') }}</label>
                  <input type="text" class="form-control" name="user_coinpayments_merchant_id" value="{{ Auth::user()->user_coinpayments_merchant_id }}">
                </div>
              </div>
              @endif
              @if(in_array('instamojo',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Instamojo Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Instamojo Mode') }} <span class="require">*</span></label>
                  <select name="user_instamojo_mode" id="user_instamojo_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_instamojo_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_instamojo_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Instamojo API Key') }}</label>
                  <input type="text" class="form-control" name="user_instamojo_api_key" value="{{ Auth::user()->user_instamojo_api_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Instamojo Auth Token') }}</label>
                  <input type="text" class="form-control" name="user_instamojo_auth_token" value="{{ Auth::user()->user_instamojo_auth_token }}">
                </div>
              </div>
              @endif
              @if(in_array('aamarpay',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Aamarpay Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Aamarpay Mode') }} <span class="require">*</span></label>
                  <select name="user_aamarpay_mode" id="user_aamarpay_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_aamarpay_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_aamarpay_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Store ID') }}</label>
                  <input type="text" class="form-control" name="user_aamarpay_store_id" value="{{ Auth::user()->user_aamarpay_store_id }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Signature Key') }}</label>
                  <input type="text" class="form-control" name="user_aamarpay_signature_key" value="{{ Auth::user()->user_aamarpay_signature_key }}">
                </div>
              </div>
              @endif
              @if(in_array('mollie',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Mollie Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Mollie API Key') }}</label>
                  <input type="text" class="form-control" name="user_mollie_api_key" value="{{ Auth::user()->user_mollie_api_key }}">
                </div>
              </div>
              @endif
              @if(in_array('robokassa',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Robokassa Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Shop Identifier') }}</label>
                  <input type="text" class="form-control" name="user_shop_identifier" value="{{ Auth::user()->user_shop_identifier }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Robokassa Password #1') }}</label>
                  <input type="text" class="form-control" name="user_robokassa_password_1" value="{{ Auth::user()->user_robokassa_password_1 }}">
                </div>
              </div>
              <div class="col-sm-12">
              <div class="form-group">
              <p>{{ __('Robokassa Success Url') }} : <code>{{ url('/') }}/robokassa-success</code> <br/> <a href="javascript:void(0);" data-toggle="modal" data-target="#myModal_two" class="blue-color">{{ __('How to configure success url') }}?</a></p>
              <p>{{ __('Robokassa Failed Url') }} : <code>{{ url('/') }}/cancel</code> <br/> <a href="javascript:void(0);" data-toggle="modal" data-target="#myModal_two" class="blue-color">{{ __('How to configure failed url') }}?</a></p>
              </div>
              </div>
              @endif
              @if(in_array('mercadopago',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Mercadopago Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Mercadopago Mode') }} <span class="require">*</span></label>
                  <select name="user_mercadopago_mode" id="user_mercadopago_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_mercadopago_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_mercadopago_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Mercadopago Public Key') }}</label>
                  <input type="text" class="form-control" name="user_mercadopago_client_id" value="{{ Auth::user()->user_mercadopago_client_id }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Mercadopago Access Token') }}</label>
                  <input type="text" class="form-control" name="user_mercadopago_client_secret" value="{{ Auth::user()->user_mercadopago_client_secret }}">
                </div>
              </div>
              @endif
              @if(in_array('midtrans',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Midtrans Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Midtrans Mode') }} <span class="require">*</span></label>
                  <select name="user_midtrans_mode" id="user_midtrans_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_midtrans_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_midtrans_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Midtrans Server Key') }}</label>
                  <input type="text" class="form-control" name="user_midtrans_server_key" value="{{ Auth::user()->user_midtrans_server_key }}">
                </div>
              </div>
              @endif
              @if(in_array('coinbase',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Coinbase Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Coinbase Api Key') }}</label>
                  <input type="text" class="form-control" name="user_coinbase_api_key" value="{{ Auth::user()->user_coinbase_api_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Coinbase Secret Key') }}</label>
                  <input type="text" class="form-control" name="user_coinbase_secret_key" value="{{ Auth::user()->user_coinbase_secret_key }}">
                </div>
              </div>
              <div class="col-sm-12">
              <div class="form-group">
              <p>{{ __('Coinbase Checkout Webhook URL') }} : <code>{{ url('/') }}/webhooks/coinbase-checkout</code> </p>
              <p>{{ __('Coinbase Subscription Webhook URL') }} : <code>{{ url('/') }}/webhooks/coinbase-subscription</code> </p>
              <p>{{ __('Coinbase Deposit Webhook URL') }} : <code>{{ url('/') }}/webhooks/coinbase-deposit</code> </p>
              <p><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal_three" class="blue-color">{{ __('How to configure webhooks url') }}?</a></p>
              </div>
              </div>
              @endif
              @if(in_array('cashfree',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Cashfree Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Cashfree Mode') }} <span class="require">*</span></label>
                  <select name="user_cashfree_mode" id="user_cashfree_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_cashfree_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_cashfree_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Cashfree API Key') }}</label>
                  <input type="text" class="form-control" name="user_cashfree_api_key" value="{{ Auth::user()->user_cashfree_api_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Cashfree API Secret') }}</label>
                  <input type="text" class="form-control" name="user_cashfree_api_secret" value="{{ Auth::user()->user_cashfree_api_secret }}">
                </div>
              </div>
              <div class="col-sm-12">
              <div class="form-group">
              <p>{{ __('Go To Whitelisting URL') }} : <code>https://merchant.cashfree.com/merchants/pg/developers/whitelisting?env=prod</code></p>
              <p><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal_four" class="blue-color">{{ __('How To Put My Domain') }}?</a></p>
              </div>
              </div>
              @endif
              @if(in_array('nowpayments',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('NowPayments Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('NowPayments Mode') }} <span class="require">*</span></label>
                  <select name="user_nowpayments_mode" id="user_nowpayments_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_nowpayments_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_nowpayments_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('NowPayments API Key') }}</label>
                  <input type="text" class="form-control" name="user_nowpayments_api_key" value="{{ Auth::user()->user_nowpayments_api_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('NowPayments IPN Secret') }}</label>
                  <input type="text" class="form-control" name="user_nowpayments_ipn_secret" value="{{ Auth::user()->user_nowpayments_ipn_secret }}">
                </div>
              </div>
              @endif
              @if(in_array('stripe',$get_payment))
              <div class="col-sm-12 mt-4 mb-1">
              <h4 class="mt-4">{{ __('Stripe Settings') }}</h4>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Stripe Mode') }} <span class="require">*</span></label>
                  <select name="user_stripe_mode" id="user_stripe_mode" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="1" @if(Auth::user()->user_stripe_mode == 1 ) selected="selected" @endif>{{ __('Live') }}</option>
                      <option value="0" @if(Auth::user()->user_stripe_mode == 0 ) selected="selected" @endif>{{ __('Demo') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Stripe Payment Type') }} <span class="require">*</span></label>
                  <select name="user_stripe_type" id="user_stripe_type" class="form-control" data-bvalidator="required">
                      <option value=""></option>
                      <option value="charges" @if(Auth::user()->user_stripe_type == "charges" ) selected="selected" @endif>{{ __('Charges API') }}</option>
                      <option value="intents" @if(Auth::user()->user_stripe_type == "intents" ) selected="selected" @endif>{{ __('Intents API') }}</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Test Publishable Key') }}</label>
                  <input type="text" class="form-control" name="user_test_publish_key" value="{{ Auth::user()->user_test_publish_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Test Secret Key') }}</label>
                  <input type="text" class="form-control" name="user_test_secret_key" value="{{ Auth::user()->user_test_secret_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Live Publishable Key') }}</label>
                  <input type="text" class="form-control" name="user_live_publish_key" value="{{ Auth::user()->user_live_publish_key }}">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('Live Secret Key') }}</label>
                  <input type="text" class="form-control" name="user_live_secret_key" value="{{ Auth::user()->user_live_secret_key }}">
                </div>
              </div>
              @endif
              @endif
              @endif
              <input type="hidden" name="user_token" value="{{ Auth::user()->user_token }}">
              <input type="hidden" name="id" value="{{ Auth::user()->id }}">
              <input type="hidden" name="save_earnings" value="{{ Auth::user()->earnings }}">
              <input type="hidden" name="save_photo" value="{{ Auth::user()->user_photo }}">
              <input type="hidden" name="save_banner" value="{{ Auth::user()->user_banner }}">
              <input type="hidden" name="save_password" value="{{ Auth::user()->password }}">
              <input type="hidden" name="save_auth_token" value="{{ Auth::user()->user_auth_token }}">
              <div class="col-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                  <button class="btn btn-primary mt-3 mt-sm-0" type="submit">{{ __('Update') }}</button>
                </div>
              </div>
            </div>
          </form>
        </section>
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