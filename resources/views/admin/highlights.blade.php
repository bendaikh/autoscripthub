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
    @if(in_array('features',$avilable))
    @if($allsettings->site_features_display == 1)
    <div id="right-panel" class="right-panel">

       
                       @include('admin.header')
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Features') }}</h1>
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
                           <form action="{{ route('admin.highlights') }}" method="post" id="item_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Font Awesome Icon') }} 1 <small>( {{ __('example') }} : <strong>fa fa-magic</strong> )</small></label>
                                                <input id="site_icon1" name="site_icon1" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_icon1 }}" data-bvalidator="required">
                                                
                                            </div>
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Heading') }}</label>
                                                <input id="site_text1" name="site_text1" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_text1 }}" data-bvalidator="required">
                                            </div>
                                             
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Sub Heading') }}</label>
                                                <input id="site_sub_text1" name="site_sub_text1" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_sub_text1 }}" data-bvalidator="required">
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Font Awesome Icon') }} 2 <small>( {{ __('example') }} : <strong>fa fa-phone</strong> )</small></label>
                                                <input id="site_icon2" name="site_icon2" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_icon2 }}" data-bvalidator="required">
                                            </div>
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Heading') }}</label>
                                                <input id="site_text2" name="site_text2" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_text2 }}" data-bvalidator="required">
                                            </div>
                                             
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Sub Heading') }}</label>
                                                <input id="site_sub_text2" name="site_sub_text2" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_sub_text2 }}" data-bvalidator="required">
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Font Awesome Icon') }} 3 <small>( {{ __('example') }} : <strong>fa fa-code</strong> )</small></label>
                                                <input id="site_icon3" name="site_icon3" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_icon3 }}" data-bvalidator="required">
                                            </div>
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Heading') }}</label>
                                                <input id="site_text3" name="site_text3" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_text3 }}" data-bvalidator="required">
                                            </div>
                                             
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Sub Heading') }}</label>
                                                <input id="site_sub_text3" name="site_sub_text3" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_sub_text3 }}" data-bvalidator="required">
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Font Awesome Icon') }} 4 <small>( {{ __('example') }} : <strong>fa fa-lock</strong> )</small></label>
                                                <input id="site_icon4" name="site_icon4" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_icon4 }}" data-bvalidator="required">
                                            </div>
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Heading') }}</label>
                                                <input id="site_text4" name="site_text4" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_text4 }}" data-bvalidator="required">
                                            </div>
                                             
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Sub Heading') }}</label>
                                                <input id="site_sub_text4" name="site_sub_text4" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_sub_text4 }}" data-bvalidator="required">
                                            </div>
                                             <input type="hidden" name="sid" value="1">
                                                
                                                
                                        
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
    @include('admin.404')
    @endif
    @else
    @include('admin.denied')
    @endif
    <!-- Right Panel -->


   @include('admin.javascript')


</body>

</html>
