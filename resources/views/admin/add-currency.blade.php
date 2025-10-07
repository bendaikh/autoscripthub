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
    @if(in_array('currencies',$avilable))
    <div id="right-panel" class="right-panel">

        @include('admin.header')
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Add Currency') }}</h1>
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
                           <form action="{{ route('admin.add-currency') }}" method="post" id="setting_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                          
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                           
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Currency Name') }} <span class="require">*</span></label>
                                                <input id="currency_name" name="currency_name" type="text" class="form-control" required>
                                                <small>(ex: US Dollar)</small>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Currency Code') }} <span class="require">*</span></label>
                                                <input id="currency_code" name="currency_code" type="text" class="form-control" required>
                                                <small>(ex: USD)</small>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Currency Symbol') }} <span class="require">*</span></label>
                                                <input id="currency_symbol" name="currency_symbol" type="text" class="form-control" required>
                                                <small>(ex: $)</small>
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
                                                <label for="name" class="control-label mb-1">{{ __('Currency Rate') }}<span class="require">*</span></label>
                                                <input id="currency_rate" name="currency_rate" type="text" class="form-control" data-bvalidator="required,number">
                                            </div> 
                             
                                           <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Display Order') }}</label>
                                                <input id="currency_order" name="currency_order" type="text" class="form-control" data-bvalidator="digit">
                                            </div> 
                             
                                   <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"> {{ __('Status') }}<span class="require">*</span></label>
                                                <select name="currency_status" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="1">{{ __('Active') }}</option>
                                                <option value="0">{{ __('InActive') }}</option>
                                                </select>
                                                
                                            </div>           
                                            
                                          <input type="hidden" name="currency_default" value="0">    
                                           
                             
                             
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
        </div>
 

        <!-- .content -->


    </div><!-- /#right-panel -->
    @else
    @include('admin.denied')
    @endif
    <!-- Right Panel -->


   @include('admin.javascript')


</body>

</html>
