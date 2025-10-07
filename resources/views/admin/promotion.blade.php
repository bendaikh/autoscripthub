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
    @if(in_array('settings',$avilable))
    <!-- Right Panel -->
    <div id="right-panel" class="right-panel">

       
                       @include('admin.header')
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Promotion Popup') }}</h1>
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
                           <form action="{{ route('admin.promotion') }}" method="post" id="setting_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                            <div class="form-group">
                                              <label for="product_approval" class="control-label mb-1">{{ __('Promotion Popup') }}?<span class="require">*</span></label><br/>
                                              <select name="promotion_popup" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="1" @if($custom_settings->promotion_popup == 1) selected @endif>{{ __('ON') }}</option>
                                                        <option value="0" @if($custom_settings->promotion_popup == 0) selected @endif>{{ __('OFF') }}</option>
                                              </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Promotion Header Bg Color') }} <span class="require">*</span></label>
                                                <input id="pr_head_bg_color" name="pr_head_bg_color" type="text" class="form-control noscroll_textarea" value="{{ $custom_settings->pr_head_bg_color }}" data-bvalidator="required"> <small>({{ __('example color code') }} : #666000 )</small>
                                            </div>
                                            
                                           <div class="form-group">
                                                <label for="site_desc" class="control-label mb-1">{{ __('Promotion Background Image') }} (Upload on horizontal image) Size : 1152 X 624px</label>
                                            <input id="promotion_bg_image" name="promotion_bg_image" type="file" class="form-control noscroll_textarea">
                                            @if($custom_settings->promotion_bg_image != "")
                                            <img src="{{ url('/') }}/public/storage/settings/{{ $custom_settings->promotion_bg_image }}" border="0" width="100" height="100" />
                                            @endif
                                            
                                            </div> 
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Promotion Header Text Color') }} <span class="require">*</span></label>
                                                <input id="pr_head_color" name="pr_head_color" type="text" class="form-control noscroll_textarea" value="{{  $custom_settings->pr_head_color }}" data-bvalidator="required"> <small>({{ __('example color code') }} : #666000 )</small>
                                            </div>
                                             
                                             <div class="form-group">
                                                <label for="site_desc" class="control-label mb-1">{{ __('Promotion Heading') }}</label>
                                            <input id="pr_promo_heading" name="pr_promo_heading" type="text" class="form-control noscroll_textarea" value="{{  $custom_settings->pr_promo_heading }}">
                                            </div> 
                                            
                                            
                                            
                                            
                                           <input type="hidden" name="save_promotion_bg_image" value="{{ $custom_settings->promotion_bg_image }}">  
                                                
                                        
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
                                                <label for="site_desc" class="control-label mb-1">{{ __('Promotion Title One') }}</label>
                                            <input id="pr_promo_title_one" name="pr_promo_title_one" type="text" class="form-control noscroll_textarea" value="{{  $custom_settings->pr_promo_title_one }}">
                                            </div>
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_desc" class="control-label mb-1">{{ __('Promotion Title Two') }}</label>
                                            <input id="pr_promo_title_two" name="pr_promo_title_two" type="text" class="form-control noscroll_textarea" value="{{  $custom_settings->pr_promo_title_two }}">
                                            </div>
                                            
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Promotion End Date') }}</label>
                                                
                                                <input id="pr_promo_date" name="pr_promo_date" type="text" class="form-control noscroll_textarea" value="{{  $custom_settings->pr_promo_date }}">
                                            </div>
                                            
                                            
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Promotion Button Link') }}</label>
                                                <input id="pr_promo_button_link" name="pr_promo_button_link" type="text" class="form-control noscroll_textarea" value="{{ $custom_settings->pr_promo_button_link }}" data-bvalidator="url">
                                                <small>( {{ __('example') }} : {{ url('/shop') }} )</small>
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


</body>

</html>
