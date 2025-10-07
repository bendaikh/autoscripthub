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
                        <h1>{{ __('Edit Currency') }}</h1>
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
                           <form action="{{ route('admin.edit-currency') }}" method="post" id="setting_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                          
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                           
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Currency Name') }} <span class="require">*</span></label>
                                                <input id="currency_name" name="currency_name" type="text" class="form-control" value="{{ $edit->currency_name }}" required>
                                                <small>(ex: US Dollar)</small>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Currency Code') }} <span class="require">*</span></label>
                                                <input id="currency_code" name="currency_code" type="text" class="form-control" value="{{ $edit->currency_code }}" required>
                                                <small>(ex: USD)</small>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Currency Symbol') }} <span class="require">*</span></label>
                                                <input id="currency_symbol" name="currency_symbol" type="text" class="form-control" value="{{ $edit->currency_symbol }}" required>
                                                <small>(ex: $)</small>
                                            </div>
                                            
                                             <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Display Order') }}</label>
                                                <input id="currency_order" name="currency_order" type="text" class="form-control" data-bvalidator="digit" value="{{ $edit->currency_order }}">
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
                                          @if($edit->currency_code == "USD")
                                          <input type="hidden" name="currency_rate" value="1">
                                          @else
                                          <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Currency Rate') }}<span class="require">*</span></label>
                                                <input id="currency_rate" name="currency_rate" type="text" class="form-control" value="{{ $edit->currency_rate }}" data-bvalidator="required,number">
                                            </div> 
                                          @endif
                             
                                   <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"> {{ __('Status') }}<span class="require">*</span></label>
                                                <select name="currency_status" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="1" @if($edit->currency_status == 1) selected @endif>{{ __('Active') }}</option>
                                                <option value="0" @if($edit->currency_status == 0) selected @endif>{{ __('InActive') }}</option>
                                                </select>
                                                
                                            </div>           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"> {{ __('Default Currency') }}?<span class="require">*</span></label>
                                                <select name="currency_default" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="1" @if($edit->currency_default == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($edit->currency_default == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                              
                                            </div>
                                          
                                        <input type="hidden" name="currency_token" value="{{ $edit->currency_token }}">   
                             
                             
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
