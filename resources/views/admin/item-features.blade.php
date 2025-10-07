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
                        <h1>{{ __('Item Features') }}</h1>
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
                           <form action="{{ route('admin.item-features') }}" method="post" id="setting_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                          
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                           
                                            <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Display screenshots') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="show_screenshots" class="form-control" required>
                                                <option value=""></option>
                                                
                                                <option value="1" @if($addition_settings->show_screenshots == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->show_screenshots == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                              </div>
                                            
                                            <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Display video') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="show_video" class="form-control" required>
                                                <option value=""></option>
                                                
                                                <option value="1" @if($addition_settings->show_video == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->show_video == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                              </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Display moneyback') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="show_moneyback" class="form-control" required>
                                                <option value=""></option>
                                                
                                                <option value="1" @if($addition_settings->show_moneyback == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->show_moneyback == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                              </div>
                                              
                                              
                                              <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Display Demo Url') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="show_demo_url" class="form-control" required>
                                                <option value=""></option>
                                                
                                                <option value="1" @if($addition_settings->show_demo_url == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->show_demo_url == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                              </div>
                                              
                                              
                                              <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Extended License Display') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="show_extended_license" class="form-control" required>
                                                <option value=""></option>
                                                
                                                <option value="1" @if($addition_settings->show_extended_license == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->show_extended_license == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                              </div>
                                              
                                              
                                              <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Display Refund Terms') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="show_refund_term" class="form-control" required>
                                                <option value=""></option>
                                                
                                                <option value="1" @if($addition_settings->show_refund_term == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->show_refund_term == 0) selected @endif>{{ __('No') }}</option>
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
                             
                             
                                           <?php /*?><div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Display Free Download') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="show_free_download" class="form-control" required>
                                                <option value=""></option>
                                                
                                                <option value="1" @if($addition_settings->show_free_download == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->show_free_download == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                              </div> <?php */?> 
                                              
                                              <input type="hidden" name="show_free_download" value="1">  
                                            
                                            <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Display Flash Sale') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="show_flash_sale" class="form-control" required>
                                                <option value=""></option>
                                                
                                                <option value="1" @if($addition_settings->show_flash_sale == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->show_flash_sale == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                              </div> 
                                              
                                              
                                              <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Display Tags') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="show_tags" class="form-control" required>
                                                <option value=""></option>
                                                
                                                <option value="1" @if($addition_settings->show_tags == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->show_tags == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                              </div> 
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Display Feature Update') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="show_feature_update" class="form-control" required>
                                                <option value=""></option>
                                                
                                                <option value="1" @if($addition_settings->show_feature_update == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->show_feature_update == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                              </div> 
                                              
                                              
                                              <?php /*?><div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Display Item Support') }}<span class="require">*</span></label><br/>
                                               
                                                <select name="show_item_support" class="form-control" required>
                                                <option value=""></option>
                                                
                                                <option value="1" @if($addition_settings->show_item_support == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($addition_settings->show_item_support == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                              </div><?php */?>
                                            <input type="hidden" name="show_item_support" value="1">
                                           <input type="hidden" name="sid" value="1">
                             
                             
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
