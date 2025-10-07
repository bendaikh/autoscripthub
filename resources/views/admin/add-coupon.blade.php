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
    @if(in_array('items',$avilable))
    <div id="right-panel" class="right-panel">
      @include('admin.header')
       <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Add Coupon') }}</h1>
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
                        <form action="{{ route('admin.add-coupon') }}" method="post" id="checkout_form" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        @endif
                         <div class="col-md-6">
                           <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Coupon Code') }} <span class="require">*</span></label>
                                                <input id="coupon_code" name="coupon_code" type="text" class="form-control noscroll_textarea" value="" data-bvalidator="required"> 
                                            </div>
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Start Date') }} <span class="require">*</span></label>
                                                <input id="site_flash_end_date" name="coupon_start_date" type="text" class="form-control noscroll_textarea" value="" data-bvalidator="required"> 
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('End Date') }} <span class="require">*</span></label>
                                                <input id="site_free_end_date" name="coupon_end_date" type="text" class="form-control noscroll_textarea" value="" data-bvalidator="required"> 
                                            </div>
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Status') }} <span class="require">*</span></label>
                                                <select name="coupon_status" class="form-control" data-bvalidator="required">
                                                 <option value=""></option>
                                                 <option value="1">{{ __('Active') }}</option>
                                                 <option value="0">{{ __('InActive') }}</option>
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Type') }} <span class="require">*</span></label>
                                                <select name="discount_type" class="form-control" data-bvalidator="required">
                                                 <option value=""></option>
                                                 <option value="percentage">{{ __('Percentage') }}</option>
                                                 <option value="fixed">{{ __('Fixed') }}</option>
                                                 </select>
                                            </div>
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Value') }} <span class="require">*</span></label>
                                                <input id="coupon_value" name="coupon_value" type="text" class="form-control noscroll_textarea" value="" data-bvalidator="required,min[1]">
                                            </div>
                                       
                                            
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Vendor') }} <span class="require">*</span></label>
                                                <select name="user_id" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                @foreach($getvendor['view'] as $user)
                                                <option value="{{ $user->id }}">{{ $user->username }}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            
                                           
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