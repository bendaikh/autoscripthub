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
                        <h1>{{ __('Add Attribute') }}</h1>
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
                           <form action="{{ route('admin.add-attribute') }}" method="post" id="item_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Item Type') }} <span class="require">*</span></label>
                                                <select name="attr_category" class="form-control" required>
                                                  <option value=""></option>
                                                  @foreach($viewData['item_type'] as $item_type)
                                                  <option value="{{ $item_type->item_type_id }}">{{ $item_type->item_type_name }}</option>
                                                  @endforeach
                                                </select>
                                            </div>
                                            
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Attribute Name') }} <span class="require">*</span></label>
                                                 <input id="attr_label" name="attr_label" type="text" class="form-control" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Display Order') }}</label>
                                                 <input id="attr_field_order" name="attr_field_order" type="text" class="form-control">
                                            </div>
                                            
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Status') }} <span class="require">*</span></label>
                                                <select name="attr_field_status" class="form-control" required>
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Attribute Field Type') }} <span class="require">*</span></label>
                                                <select name="attr_field_type" id="attr_field_type" class="form-control" required>
                                                  <option value=""></option>
                                                  @foreach($attr_field_type as $key => $value)
                                                  <option value="{{ $key }}">{{ $value }}</option>
                                                  @endforeach
                                                  </select>
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Attribute Field Values') }} <span class="require">*</span></label>
                                                <textarea name="attr_field_value" id="attr_field_value" rows="3" class="form-control noscroll_textarea" required></textarea>
                                                ({{ __('Value separated by comma example: Firefox,Chrome,Safari') }})
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


    </div>
    @else
    @include('admin.denied')
    @endif

   @include('admin.javascript')


</body>

</html>
