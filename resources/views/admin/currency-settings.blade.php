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
                        <h1>{{ __('Currency Settings') }}</h1>
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
                           <form action="{{ route('admin.currency-settings') }}" method="post" id="setting_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                          
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                           
                                            <?php /*?><div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Currency Code') }} <span class="require">*</span></label>
                                                <input id="site_currency_code" name="site_currency_code" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_currency }}" data-bvalidator="required,maxlen[3]">
                                            </div><?php */?>  
                                            
                                            
                                         <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Multi Currency') }} <span class="require">*</span></label>
                                                <select name="multi_currency" class="form-control" required>
                                                <option value=""></option>
                                                <option value="1" @if($addition_settings->multi_currency == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->multi_currency == 0) selected @endif>{{ __('No') }}</option>
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Currency Symbol / Code Position') }} <span class="require">*</span></label>
                                                <select name="site_currency_position" class="form-control" required>
                                                <option value=""></option>
                                                <option value="left" @if($setting['setting']->site_currency_position == "left") selected @endif>{{ __('Left') }}</option>
                                                <option value="right" @if($setting['setting']->site_currency_position == "right") selected @endif>{{ __('Right') }}</option>
                                                </select>
                                            </div>
                                                
                                            <?php /*?><div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Currency Symbol') }} <span class="require">*</span></label>
                                                <input id="site_currency_symbol" name="site_currency_symbol" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_currency_symbol }}" data-bvalidator="required">
                                            </div> <?php */?>
                                            
                                           <input type="hidden" name="sid" value="1">
                                           <input type="hidden" name="site_currency_code" value="USD">
                                           <input type="hidden" name="site_currency_symbol" value="$">
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
