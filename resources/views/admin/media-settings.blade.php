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
                        <h1>{{ __('Media Settings') }}</h1>
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
                           <form action="{{ route('admin.media-settings') }}" method="post" id="checkout_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                          
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <?php /*?><div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Maximum Upload Image Size (KB)') }}<span class="require">*</span></label>
                                                <input id="site_max_image_size" name="site_max_image_size" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_max_image_size }}" data-bvalidator="required,digit,min[1]"> <small>{{ __('example') }} : 1000</small>
                                            </div><?php */?>
                                            
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Image Quality') }}<span class="require">*</span></label>
                                                <input id="image_quality" name="image_quality" type="text" class="form-control noscroll_textarea" value="{{ $addition_settings->image_quality }}" data-bvalidator="required,digit,min[1],max[100]"> <small>{{ __('example') }} : 1 to 100</small>
                                            </div>
                                            
                                            <input type="hidden" name="site_max_image_size" value="1000000000">
                                            <input type="hidden" name="site_max_file_size" value="1000000000">
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Watermark') }}?<span class="require">*</span></label>
                                                <select name="watermark_option" class="form-control" required>
                                                <option value=""></option>
                                                <option value="1" @if($setting['setting']->watermark_option == "1") selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($setting['setting']->watermark_option == "0") selected @endif>{{ __('No') }}</option>
                                                </select>
                                                
                                                
                                            </div>
                                            
                                             <div id="ifwatermark" @if($addition_settings->watermark_repeat == "0") class="force-block form-group" @else class="force-none form-group" @endif>
                                                <label for="site_title" class="control-label mb-1">{{ __('Watermark Position') }}?<span class="require">*</span></label>
                                                <select name="watermark_position" class="form-control" required>
                                                <option value=""></option>
                                                <option value="top-left" @if($addition_settings->watermark_position == "top-left") selected @endif>Top Left</option>
                                                <option value="top-right" @if($addition_settings->watermark_position == "top-right") selected @endif>Top Right</option>
                                                <option value="bottom-left" @if($addition_settings->watermark_position == "bottom-left") selected @endif>Bottom Left</option>
                                                <option value="bottom-right" @if($addition_settings->watermark_position == "bottom-right") selected @endif>Bottom Right</option>
                                                <option value="center" @if($addition_settings->watermark_position == "center") selected @endif>Center</option>
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
                                           
                                           
                                          
                             
                               
                            <?php /*?> <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Maximum Upload File Size (KB)') }}<span class="require">*</span></label>
                                                <input id="site_max_file_size" name="site_max_file_size" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_max_file_size }}" data-bvalidator="required,digit,min[1]"> <small>{{ __('example') }} : 1000</small>
                                            </div><?php */?>
                             
                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Large File Storage') }} (zip, mp3, mp4)</label>
                                                <select name="site_s3_storage" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="0" @if($setting['setting']->site_s3_storage == 0) selected @endif>{{ __('My Server') }}</option>
                                                <option value="1" @if($setting['setting']->site_s3_storage == 1) selected @endif>{{ __('Amazon S3 Storage') }}</option>
                                               <option value="2" @if($setting['setting']->site_s3_storage == 2) selected @endif>{{ __('Wasabi Storage') }}</option>
                                                 <?php /*?><option value="3" @if($setting['setting']->site_s3_storage == 3) selected @endif>{{ __('Dropbox Storage') }}</option>
                                                <option value="4" @if($setting['setting']->site_s3_storage == 4) selected @endif>{{ __('Google Storage') }}</option><?php */?>
                                                </select>
                                                
                                                
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Watermark Repeat') }}?<span class="require">*</span></label>
                                                <select name="watermark_repeat" id="watermark_repeat" class="form-control" required>
                                                <option value=""></option>
                                                <option value="1" @if($addition_settings->watermark_repeat == "1") selected @endif>Yes</option>
                                                <option value="0" @if($addition_settings->watermark_repeat == "0") selected @endif>No</option>
                                                </select>
                                                
                                                
                                            </div> 
                                    
                                          <div class="form-group">
                                                <label for="site_logo" class="control-label mb-1">{{ __('Watermark Image') }}</label>
                                                
                                            <input type="file" id="site_watermark" name="site_watermark" class="form-control-file">
                                            @if($setting['setting']->site_watermark != '')
                                                <img class="lazy" width="150" height="150" src="{{ url('/') }}/public/storage/settings/{{ $setting['setting']->site_watermark }}" />
                                                @endif
                                            </div>
                             
                             </div>
                                </div>

                            </div>
                             
                             <input type="hidden" name="sid" value="1">
                             
                             </div>
                             
                             
                             <div class="col-md-12"><h5>{{ __('S3 Storage Configuration (If amazon s3 storage selected)') }}</h5></div>
                             
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('AWS ACCESS KEY ID') }}</label>
                                                <input id="aws_access_key_id" name="aws_access_key_id" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->aws_access_key_id }}">
                                            </div>
                                        
                                         <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('AWS SECRET ACCESS KEY') }}</label>
                                                <input id="aws_secret_access_key" name="aws_secret_access_key" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->aws_secret_access_key }}"> 
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
                                                <label for="site_title" class="control-label mb-1">{{ __('AWS DEFAULT REGION') }}</label>
                                                <input id="aws_default_region" name="aws_default_region" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->aws_default_region }}">
                                                <small>{{ __('example') }} : us-east-2</small>
                                            </div>
                                        
                                         <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('AWS BUCKET') }}</label>
                                                <input id="aws_bucket" name="aws_bucket" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->aws_bucket }}"> <small>{{ __('example') }} : {{ __('yourbucketname') }}</small>
                                            </div>
                                           
                                    </div>
                                </div>

                            </div>
                            </div> 
                            
                            <div class="col-md-12"><h5>{{ __('Wasabi Storage Configuration (If wasabi storage selected)') }}</h5></div>
                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('WASABI ACCESS KEY ID') }}</label>
                                                <input id="wasabi_access_key_id" name="wasabi_access_key_id" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->wasabi_access_key_id }}">
                                            </div>
                                        
                                         <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('WASABI SECRET ACCESS KEY') }}</label>
                                                <input id="wasabi_secret_access_key" name="wasabi_secret_access_key" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->wasabi_secret_access_key }}"> 
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
                                                <label for="site_title" class="control-label mb-1">{{ __('WASABI DEFAULT REGION') }}</label>
                                                <input id="wasabi_default_region" name="wasabi_default_region" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->wasabi_default_region }}">
                                                <small>Example : us-east-2</small>
                                            </div>
                                        
                                         <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('WASABI BUCKET') }}</label>
                                                <input id="wasabi_bucket" name="wasabi_bucket" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->wasabi_bucket }}"> <small>Example : yourbucketname</small>
                                            </div>
                                           
                                    </div>
                                </div>

                            </div>
                            </div>
                             
                            <?php /*?><div class="col-md-12"><h5>{{ __('Dropbox Storage Configuration (If dropbox storage selected)') }}</h5></div>
                            <div class="col-md-6">
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('DROPBOX API') }}</label>
                                                <input id="dropbox_api" name="dropbox_api" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->dropbox_api }}">
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
                                                <label for="site_title" class="control-label mb-1">{{ __('DROPBOX TOKEN') }}</label>
                                                <input id="dropbox_token" name="dropbox_token" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->dropbox_token }}">
                                            </div>
                                        </div>
                                </div>
                             </div>
                            </div>
                            <div class="col-md-12"><h5>{{ __('Google Drive Storage Configuration (If google drive storage selected)') }}</h5></div>
                             <div class="col-md-6">
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                       <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('GOOGLE DRIVE CLIENT ID') }}</label>
                                                <input id="google_drive_client_id" name="google_drive_client_id" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->google_drive_client_id }}">
                                            </div>
                                        <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('GOOGLE DRIVE CLIENT SECRET') }}</label>
                                        <input id="google_drive_client_secret" name="google_drive_client_secret" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->google_drive_client_secret }}">
                                        </div></div>
                                </div>
                             </div>
                            </div>
                            <div class="col-md-6">
                            <div class="card-body">
                                
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('GOOGLE DRIVE REFRESH TOKEN') }}</label>
                                                <input id="google_drive_refresh_token" name="google_drive_refresh_token" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->google_drive_refresh_token }}">
                                            </div>
                                        <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('GOOGLE DRIVE FOLDER ID') }}</label>
                                        <input id="google_drive_folder_id" name="google_drive_folder_id" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->google_drive_folder_id }}">
                                        </div></div>
                                </div>
                             </div>
                            </div><?php */?>
                            <input type="hidden" name="save_watermark" value="{{ $setting['setting']->site_watermark }}">
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
