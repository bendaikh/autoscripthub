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
                        <h1>{{ __('General Settings') }}</h1>
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
                           <form action="{{ route('admin.general-settings') }}" method="post" id="setting_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                          
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Site Title') }} <span class="require">*</span></label>
                                                <input id="site_title" name="site_title" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_title }}" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Home Page Title') }} <span class="require">*</span></label>
                                                <input id="site_home_title" name="site_home_title" type="text" class="form-control noscroll_textarea" value="{{ $addition_settings->site_home_title }}" data-bvalidator="required">
                                            </div>
                                            
                                             <div class="form-group">
                                                <label for="site_desc" class="control-label mb-1">{{ __('Meta Description (max 160 chars)') }}<span class="require">*</span></label>
                                                
                                            <textarea name="site_desc" id="site_desc" rows="6" placeholder="site description" class="form-control noscroll_textarea" maxlength="160" required>{{ $setting['setting']->site_desc }}</textarea>
                                            </div>
                                                
                                               <div class="form-group">
                                                <label for="site_keywords" class="control-label mb-1">{{ __('Meta Keywords (max 160 chars)') }}<span class="require">*</span></label>
                                                
                                            <textarea name="site_keywords" id="site_keywords" rows="6" placeholder="separate keywords with commas" class="form-control noscroll_textarea" maxlength="160" required>{{ $setting['setting']->site_keywords }}</textarea>
                                            </div>  
                                                
                                                                                      
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Item Auto Approval') }}? <span class="require">*</span></label>
                                                <select name="item_approval" class="form-control" required>
                                                <option value=""></option>
                                                <option value="1" @if($setting['setting']->item_approval == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($setting['setting']->item_approval == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                                <small>({{ __('if "Yes" selected vendor item will published automatically') }}) </small>
                                                
                                            </div> 
                                            
                                            <div class="form-group">
                                              <label for="product_approval" class="control-label mb-1">{{ __('URL Rewriting') }}?<span class="require">*</span></label><br/>
                                              <select name="site_url_rewrite" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($addition_settings->site_url_rewrite == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($addition_settings->site_url_rewrite == 0) selected @endif>{{ __('OFF') }}</option>
                                              </select>
                                              <small>({{ __('if "ON" search engine friendly') }}) </small>
                                            </div>
                                            
                                            <div class="form-group">
                                              <label for="product_approval" class="control-label mb-1">{{ __('Invoice (PDF) Multi-language') }}?<span class="require">*</span></label><br/>
                                              <select name="site_invoice" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($addition_settings->site_invoice == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($addition_settings->site_invoice == 0) selected @endif>{{ __('OFF') }}</option>
                                              </select>
                                              <small>({{ __('if "OFF" keep english language') }}) </small>
                                            </div>
                                                                                        
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Site Email') }}</label>
                                                <input id="office_email" name="office_email" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->office_email }}" required>
                                            </div>
                                                
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Site Phone Number') }}</label>
                                                <input id="office_phone" name="office_phone" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->office_phone }}" required>
                                            </div> 
                                            
                                            <div class="form-group">
                                                <label for="site_desc" class="control-label mb-1">{{ __('Address') }}<span class="require">*</span></label>
                                                
                                            <textarea name="office_address" id="office_address" rows="6" class="form-control noscroll_textarea" required>{{ $setting['setting']->office_address}}</textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                              <label for="product_approval" class="control-label mb-1">{{ __('site email address display on contact page') }}?<span class="require">*</span></label><br/>
                                              <select name="site_email_display" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($addition_settings->site_email_display == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($addition_settings->site_email_display == 0) selected @endif>{{ __('OFF') }}</option>
                                              </select>
                                            </div>   
                                            
                                            <div class="form-group">
                                              <label for="product_approval" class="control-label mb-1">{{ __('site phone number display on contact page') }}?<span class="require">*</span></label><br/>
                                              <select name="site_phone_display" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($addition_settings->site_phone_display == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($addition_settings->site_phone_display == 0) selected @endif>{{ __('OFF') }}</option>
                                              </select>
                                            </div>
                                            
                                            <div class="form-group">
                                              <label for="product_approval" class="control-label mb-1">{{ __('site address display on contact page') }}?<span class="require">*</span></label><br/>
                                              <select name="site_address_display" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($addition_settings->site_address_display == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($addition_settings->site_address_display == 0) selected @endif>{{ __('OFF') }}</option>
                                              </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="banner_heading" class="control-label mb-1">{{ __('Footer Newsletter Content') }} <span class="require">*</span></label>
                                                <input id="site_newsletter" name="site_newsletter" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_newsletter }}" required>
                                            </div> 
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Google Analytics') }}</label>
                                                <input id="google_analytics" name="google_analytics" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->google_analytics }}">
                                                <span>Example : G-XXXXXXXXXX</span>
                                            </div>
                                            
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Multi Language') }}?</label>
                                                <select name="multi_language" class="form-control" required>
                                                <option value=""></option>
                                                <option value="1" @if($setting['setting']->multi_language == "1") selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($setting['setting']->multi_language == "0") selected @endif>{{ __('No') }}</option>
                                                </select>
                                                
                                                
                                            </div> 
                                            <div class="form-group">
                                              <label for="product_approval" class="control-label mb-1">{{ __('New Registration For Email Verification') }}?<span class="require">*</span></label><br/>                                         <select name="email_verification" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($setting['setting']->email_verification == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($setting['setting']->email_verification == 0) selected @endif>{{ __('OFF') }}</option>
                                                        </select>
                                                        <small>({{ __('If selected "OFF" automatically verified customers / vendors') }})</small>
                                            </div>
                                            
                                           <div class="form-group">
                                              <label for="product_approval" class="control-label mb-1">{{ __('Manual Payment Verification') }}?<span class="require">*</span></label><br/>                                         <select name="payment_verification" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($setting['setting']->payment_verification == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($setting['setting']->payment_verification == 0) selected @endif>{{ __('OFF') }}</option>
                                                        </select>
                                                        <small>({{ __('If selected "OFF" users can download file immediately after payment without approval') }})</small>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Cookie Popup') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="cookie_popup" class="form-control" required>
                                                <option value=""></option>
                                                
                                                <option value="1" @if($setting['setting']->cookie_popup == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($setting['setting']->cookie_popup == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                              </div>
                                            
                                            <div class="form-group">
                                                <label for="site_desc" class="control-label mb-1">{{ __('Cookie Popup Text') }} <span class="require">*</span></label>
                                                
                                            <textarea name="cookie_popup_text" id="cookie_popup_text" rows="4" class="form-control noscroll_textarea" required>{{ $setting['setting']->cookie_popup_text}}</textarea>
                                            </div> 
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Cookie Button Text') }} <span class="require">*</span></label>
                                                <input id="cookie_popup_button" name="cookie_popup_button" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->cookie_popup_button }}" required>
                                            </div>
                                            
                                            
                                            
                                              
                                              
                                              <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Regular License Duration') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="regular_license" class="form-control" required>
                                                <option value=""></option>
                                                @foreach($durations as $duration)
                                                <option value="{{ $duration }}" @if($addition_settings->regular_license == $duration) selected @endif>{{ $duration }}</option>
                                                @endforeach
                                                </select>
                                              </div>
                                              
                                              
                                              <div class="form-group">
                                                <label for="site_desc" class="control-label mb-1">{{ __('Live Chat Code') }} (Tawk.to)</label>
                                                
                                            <input type="text" name="site_tawk_chat" id="site_tawk_chat" class="form-control noscroll_textarea" value="{{ $addition_settings->site_tawk_chat}}"/><small><strong>Example:</strong> https://embed.tawk.to/609bc139b1d5182476b83612/1f5g6lj0r</small>
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
                                                <label for="banner_heading" class="control-label mb-1">{{ __('Banner Heading') }} <span class="require">*</span></label>
                                                <input id="site_banner_heading" name="site_banner_heading" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_banner_heading }}" required>
                                            </div>  
                                            
                              <div class="form-group">
                                                <label for="banner_heading" class="control-label mb-1">{{ __('Banner Sub Heading') }} <span class="require">*</span></label>
                                                <input id="site_banner_subheading" name="site_banner_subheading" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_banner_subheading }}" required>
                                            </div>              
                             
                             
                             <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Favicon') }} ( {{ __('max') }} 16 x 16px ) - ( {{ __('ico format only') }} ) <span class="require">*</span></label>
                                                
                                            <input type="file" id="site_favicon" name="site_favicon" class="form-control-file" @if($setting['setting']->site_favicon == '') required @endif>
                                            @if($setting['setting']->site_favicon != '')
                                                <img class="lazy" width="16" height="16" src="{{ url('/') }}/public/storage/settings/{{ $setting['setting']->site_favicon }}" />
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_logo" class="control-label mb-1">{{ __('Header') }} {{ __('Logo') }} <span class="require">*</span></label>
                                                
                                            <input type="file" id="site_logo" name="site_logo" class="form-control-file" @if($setting['setting']->site_logo == '') required @endif>
                                            @if($setting['setting']->site_logo != '')
                                                <img class="lazy" width="84" height="24" src="{{ url('/') }}/public/storage/settings/{{ $setting['setting']->site_logo }}" />
                                                @endif
                                            </div>
                                           
                                            <div class="form-group">
                                                <label for="site_logo" class="control-label mb-1">{{ __('Header') }} {{ __('Logo') }} {{ __('Size') }} - ({{ __('Desktop / Laptop / Tablet') }})<span class="require">*</span></label>
                                                 <div class="clearfix"></div>
                                                 <div class="floatleft">
                                                 <span>{{ __('Width') }}</span><input id="site_desktop_logo_width" name="site_desktop_logo_width" type="text" class="form-control noscroll_textarea" value="{{ $addition_settings->site_desktop_logo_width }}" required>px
                                                 </div>
                                                 <div class="floatleft float2nd">
                                                 <span>{{ __('Height') }}</span><input id="site_desktop_logo_height" name="site_desktop_logo_height" type="text" class="form-control noscroll_textarea" value="{{ $addition_settings->site_desktop_logo_height }}" required>px
                                                 </div>
                                            
                                            </div>
                                            <div class="clearfix barheight"></div>
                                            <div class="form-group">
                                                <label for="site_logo" class="control-label mb-1">{{ __('Header') }} {{ __('Logo') }} {{ __('Size') }} - ({{ __('Mobile') }})<span class="require">*</span></label>
                                                 <div class="clearfix"></div>
                                                 <div class="floatleft">
                                                 <span>{{ __('Width') }}</span><input id="site_mobile_logo_width" name="site_mobile_logo_width" type="text" class="form-control noscroll_textarea" value="{{ $addition_settings->site_mobile_logo_width }}" required>px
                                                 </div>
                                                 <div class="floatleft float2nd">
                                                 <span>{{ __('Height') }}</span><input id="site_mobile_logo_height" name="site_mobile_logo_height" type="text" class="form-control noscroll_textarea" value="{{ $addition_settings->site_mobile_logo_height }}" required>px
                                                 </div>
                                            
                                            </div>
                                            <div class="clearfix barheight"></div>
                                            <div class="form-group">
                                                <label for="site_logo" class="control-label mb-1">{{ __('Footer Logo') }} ({{ __('Size') }} 150 x 43)<span class="require">*</span></label>
                                                
                                            <input type="file" id="site_footer_logo" name="site_footer_logo" class="form-control-file" @if($addition_settings->site_footer_logo == '') required @endif>
                                            @if($addition_settings->site_footer_logo != '')
                                                <img class="lazy" width="84" height="24" src="{{ url('/') }}/public/storage/settings/{{ $addition_settings->site_footer_logo }}"  />
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Banner') }} ({{ __('Size') }} 1920 x 300)<span class="require">*</span></label>
                                                
                                            <input type="file" id="site_banner" name="site_banner" class="form-control-file" @if($setting['setting']->site_banner == '') required @endif>
                                            @if($setting['setting']->site_banner != '')
                                                <img class="lazy" width="131" height="24" src="{{ url('/') }}/public/storage/settings/{{ $setting['setting']->site_banner }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Page Loader') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="site_loader_display" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="1" @if($setting['setting']->site_loader_display == 1) selected @endif>{{ __('ON') }}</option>
                                                <option value="0" @if($setting['setting']->site_loader_display == 0) selected @endif>{{ __('OFF') }}</option>
                                                </select>
                                                
                                             </div>
                                            
                                            <div class="form-group">
                                                <label for="site_loader_image" class="control-label mb-1">{{ __('Page Loader GIF') }} (200 X 200)<span class="require">*</span></label>
                                                
                                            <input type="file" id="site_loader_image" name="site_loader_image" class="form-control-file" @if($setting['setting']->site_loader_image == '') data-bvalidator="required,extension[gif]" data-bvalidator-msg="{{ __('Please select file of type .gif') }}" @else data-bvalidator="extension[gif]" data-bvalidator-msg="{{ __('Please select file of type .gif') }}" @endif>
                                            @if($setting['setting']->site_loader_image != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/settings/{{ $setting['setting']->site_loader_image }}"  />
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Flash Sale End Date') }} <span class="require">*</span></label>
                                                <input id="site_flash_end_date" name="site_flash_end_date" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_flash_end_date }}" required>
                                            </div> 
                                            
                                            
                                            <?php /*?><div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Free File End Date') }} <span class="require">*</span></label>
                                                <input id="site_free_end_date" name="site_free_end_date" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_free_end_date }}" required>
                                            </div> <?php */?>
                                            
                                            <input type="hidden" name="site_free_end_date" value="{{ $setting['setting']->site_free_end_date }}">
                                            <div class="form-group">
                                              <label for="product_approval" class="control-label mb-1">{{ __('Maintenance Mode') }}?<span class="require">*</span></label><br/>                                         <select name="maintenance_mode" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($setting['setting']->maintenance_mode == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($setting['setting']->maintenance_mode == 0) selected @endif>{{ __('OFF') }}</option>
                                                        </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Maintenance Mode Title') }}</label>
                                                <input id="m_mode_title" name="m_mode_title" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->m_mode_title }}" @if($setting['setting']->maintenance_mode == 1) required @endif>
                                                
                                            </div>
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Maintenance Mode Content') }}</label>
                                                <input id="m_mode_content" name="m_mode_content" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->m_mode_content }}" @if($setting['setting']->maintenance_mode == 1) required @endif>
                                                
                                            </div>
                                            <div class="form-group">
                                              <label for="product_approval" class="control-label mb-1">{{ __('Homepage Blog Post Display') }}?<span class="require">*</span></label><br/>                                         <select name="home_blog_display" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($setting['setting']->home_blog_display == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($setting['setting']->home_blog_display == 0) selected @endif>{{ __('OFF') }}</option>
                                              </select>
                                            </div>
                                            
                                             <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Select Product License Details Page') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="item_support_link" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                @foreach($page['view'] as $page)
                                                <option value="{{ $page->page_slug }}" @if($setting['setting']->item_support_link == $page->page_slug) selected @endif>{{ $page->page_title }}</option>
                                                @endforeach
                                                </select>
                                                <small>({{ __('this page used on single product details page') }})</small>
                                             </div>
                                               
                                              <div class="form-group">
                                              <label for="product_approval" class="control-label mb-1">{{ __('Google Recaptcha') }}?<span class="require">*</span></label><br/>
                                              <select name="site_google_recaptcha" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($addition_settings->site_google_recaptcha == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($addition_settings->site_google_recaptcha == 0) selected @endif>{{ __('OFF') }}</option>
                                              </select>
                                            </div> 
                                            
                                            <div class="form-group mt-3">
                                             <label for="site_title" class="control-label mb-1">{{ __('Google Recaptcha Site Key') }}<span class="require">*</span></label>
                                             <input id="google_recaptcha_site_key" name="google_recaptcha_site_key" type="text" class="form-control noscroll_textarea" value="{{ $addition_settings->google_recaptcha_site_key }}" data-bvalidator="required">
                                        </div>
                                        <div class="form-group">
                                             <label for="site_title" class="control-label mb-1">{{ __('Google Recaptcha Secret Key') }}<span class="require">*</span></label>
                                             <input id="google_recaptcha_secret_key" name="google_recaptcha_secret_key" type="text" class="form-control noscroll_textarea" value="{{ $addition_settings->google_recaptcha_secret_key }}" data-bvalidator="required">
                                        </div>
                                            
                                            <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Extended License Duration') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="extended_license" class="form-control" required>
                                                <option value=""></option>
                                                @foreach($durations as $duration)
                                                <option value="{{ $duration }}" @if($addition_settings->extended_license == $duration) selected @endif>{{ $duration }}</option>
                                                @endforeach
                                                </select>
                                              </div>
                                              
                                              
                                              <?php /*?><div class="form-group">
                                              <label for="product_approval" class="control-label mb-1">Free item support price<span class="require">*</span></label><br/>
                                              <select name="site_free_items_price" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($addition_settings->site_free_items_price == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($addition_settings->site_free_items_price == 0) selected @endif>{{ __('OFF') }}</option>
                                              </select>
                                            </div><?php */?> 
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Disable View Source Code') }}<span class="require">*</span></label>
                                                
                                                <select name="disable_view_source" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="1" @if($addition_settings->disable_view_source == "1") selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->disable_view_source == "0") selected @endif>{{ __('No') }}</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="site_free_items_price" value="1">
                                            <div class="form-group">
                                                <label for="site_desc" class="control-label mb-1">{{ __('Custom Js Code') }}</label>
                                            <textarea name="site_custom_js" id="site_custom_js" rows="6" class="form-control noscroll_textarea">{{ $addition_settings->site_custom_js }}</textarea></div><small>{{ __('Example JS Code') }}: <code><br/>$("body").css("background-color","blue");</code></small>
                                            
                                            
                                            
                                                <input type="hidden" name="save_banner" value="{{ $setting['setting']->site_banner }}">
                                                <input type="hidden" name="save_logo" value="{{ $setting['setting']->site_logo }}">
                                                <input type="hidden" name="save_favicon" value="{{ $setting['setting']->site_favicon }}">
                                                
                                                <input type="hidden" name="save_loader_image" value="{{ $setting['setting']->site_loader_image }}">
                                                <input type="hidden" name="sid" value="1">
                                                <input type="hidden" name="save_footer_logo" value="{{ $addition_settings->site_footer_logo }}">
                                                
                                                
                             
                             
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             
                             <div class="col-md-12 no-padding">
                             <div class="card-footer">
                                 <button type="submit" name="submit" class="btn btn-primary btn-sm"><i class="fa fa-dot-circle-o"></i> {{ __('Submit') }}</button>
                                 <button type="reset" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> {{ __('Reset') }} </button>
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


</body>

</html>
