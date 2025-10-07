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
                        <h1>{{ __('Edit Category') }}</h1>
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
                       <form action="{{ route('admin.edit-category') }}" method="post" id="setting_form" enctype="multipart/form-data">
                        
                        {{ csrf_field() }}
                        @endif
                        <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Name') }} <span class="require">*</span></label>
                                                <input id="category_name" name="category_name" type="text" class="form-control" value="{{ $edit['category']->category_name }}" required>
                                                
                                            </div>
                                            
                                            
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"> {{ __('Status') }} <span class="require">*</span></label>
                                                <select name="category_status" class="form-control" required>
                                                <option value=""></option>
                                                <option value="1" @if($edit['category']->category_status == 1) selected="selected" @endif>{{ __('Active') }}</option>
                                                <option value="0" @if($edit['category']->category_status == 0) selected="selected" @endif>{{ __('InActive') }}</option>
                                                </select>
                                                
                                            </div> 
                                                
                                                <input type="hidden" name="cat_id" value="{{ $edit['category']->cat_id }}">
                                                
                                              <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Display Order') }}</label>
                                                <input id="menu_order" name="menu_order" type="text" class="form-control" value="{{ $edit['category']->menu_order }}">
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
                                                <label for="site_title" class="control-label mb-1"> {{ __('Allow Seo') }}? <span class="require">*</span></label>
                                                <select name="category_allow_seo" id="category_allow_seo" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="1" @if($edit['category']->category_allow_seo == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($edit['category']->category_allow_seo == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                             </div>
                                            
                                          <div id="ifseo" @if($edit['category']->category_allow_seo == 1) class="form-group force-block" @else class="form-group force-none" @endif>
                                     <div class="form-group">
                                           <label for="site_keywords" class="control-label mb-1">{{ __('SEO Meta Keywords') }} ({{ __('max 160 chars') }}) <span class="require">*</span></label>
                                            <textarea name="category_seo_keyword" id="category_seo_keyword" rows="4" class="form-control noscroll_textarea" data-bvalidator="required,maxlen[160]">{{ $edit['category']->category_seo_keyword }}</textarea>
                                       </div> 
                                       <div class="form-group">
                                           <label for="site_desc" class="control-label mb-1">{{ __('SEO Meta Description') }} ({{ __('max 160 chars') }}) <span class="require">*</span></label>
                                              <textarea name="category_seo_desc" id="category_seo_desc" rows="4" class="form-control noscroll_textarea" data-bvalidator="required,maxlen[160]">{{ $edit['category']->category_seo_desc }}</textarea>
                                            </div>
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
