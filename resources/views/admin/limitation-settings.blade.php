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
                        <h1>{{ __('Limitation Settings') }}</h1>
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
                           <form action="{{ route('admin.limitation-settings') }}" method="post" id="setting_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                          
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                           
                                              
                                            
                                            <div class="form-group">
                                                <label for="product_per_page" class="control-label mb-1">{{ __('Products / Orders / User Roles per page') }}<span class="require">*</span></label>
                                                <input id="site_item_per_page" name="site_item_per_page" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_item_per_page }}" data-bvalidator="required,min[1]">
                                            </div> 
                                            
                                            <div class="form-group">
                                                <label for="comment_per_page" class="control-label mb-1">{{ __('Comment per page') }}<span class="require">*</span></label>
                                                <input id="site_comment_per_page" name="site_comment_per_page" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_comment_per_page }}" data-bvalidator="required,min[1]">
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
                                                <label for="post_per_page" class="control-label mb-1">{{ __('Post per page') }}<span class="require">*</span></label>
                                                <input id="site_post_per_page" name="site_post_per_page" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_post_per_page }}" data-bvalidator="required,min[1]">
                                            </div> 
                                            
                                             <div class="form-group">
                                                <label for="review_per_page" class="control-label mb-1">{{ __('Review per page') }}<span class="require">*</span></label>
                                                <input id="site_review_per_page" name="site_review_per_page" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_review_per_page }}" data-bvalidator="required,min[1]">
                                            </div> 
                                            
                                           <input type="hidden" name="sid" value="1">
                             
                             
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('Main Menu Category Limitation') }}</h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('How many categories display on main menu') }}? <span class="require">*</span></label><br/>
                                               <input id="site_menu_category" name="site_menu_category" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_menu_category }}" data-bvalidator="required">
                                                
                                                
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
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Category display on order') }}?<span class="require">*</span></label><br/>
                                               
                                                <select name="menu_categories_order" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="asc" @if($setting['setting']->menu_categories_order == 'asc') selected @endif>{{ __('ASC') }}</option>
                                                <option value="desc" @if($setting['setting']->menu_categories_order == 'desc') selected @endif>{{ __('DESC') }}</option>
                                                </select>
                                                
                                             </div>
                                             <small>{{ __('ASC - ascending order') }} | {{ __('DESC - descending order') }}</small>
                                             
                                             
                                             
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Footer Menu Category Limitation') }}</h4></div></div>
                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('How many categories display on footer menu') }}? <span class="require">*</span></label><br/>
                                               <input id="footer_menu_display_categories" name="footer_menu_display_categories" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->footer_menu_display_categories }}" data-bvalidator="required">
                                                
                                                
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
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Category display on order') }}?<span class="require">*</span></label><br/>
                                               
                                                <select name="footer_menu_categories_order" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="asc" @if($setting['setting']->footer_menu_categories_order == 'asc') selected @endif>{{ __('ASC') }}</option>
                                                <option value="desc" @if($setting['setting']->footer_menu_categories_order == 'desc') selected @endif>{{ __('DESC') }}</option>
                                                </select>
                                                
                                             </div>
                                             <small>{{ __('ASC - ascending order') }} | {{ __('DESC - descending order') }}</small>
                                             
                                             
                                             
                                    </div>
                                </div>

                            </div>
                            </div>
                             
                             
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('Home Page Item Limitation') }}</h4></div></div>
                             
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('How many featured items display') }} <span class="require">*</span></label><br/>
                                               <input id="home_featured_items" name="home_featured_items" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->home_featured_items }}" data-bvalidator="required">
                                                
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('How many flash items display') }} <span class="require">*</span></label><br/>
                                               <input id="home_flash_items" name="home_flash_items" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->home_flash_items }}" data-bvalidator="required">
                                                
                                                
                                             </div>
                                            
                                      
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('How many blog post display') }} <span class="require">*</span></label><br/>
                                               <input id="home_blog_post" name="home_blog_post" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->home_blog_post }}" data-bvalidator="required">
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('How many popular items display') }} <span class="require">*</span></label><br/>
                                               <input id="home_popular_items" name="home_popular_items" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->home_popular_items }}" data-bvalidator="required">
                                                
                                                
                                             </div>
                                            
                                          <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('How many new items display') }} <span class="require">*</span></label><br/>
                                               <input id="site_newest_files" name="site_newest_files" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_newest_files }}" data-bvalidator="required">
                                                
                                                
                                             </div>
                                             
                                             
                                             
                                              <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('How many free items display') }} <span class="require">*</span></label><br/>
                                               <input id="home_free_items" name="home_free_items" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->home_free_items }}" data-bvalidator="required">
                                                
                                                
                                             </div>
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                             
                              <div class="col-md-12"><div class="card-body"><h4>{{ __('Shop Page') }}</h4></div></div>
                              
                              
                              <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                             
                                            
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Price range min price') }} <span class="require">*</span></label><br/>
                                               <input id="site_range_min_price" name="site_range_min_price" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_range_min_price }}" data-bvalidator="required">
                                                
                                                
                                             </div>
                                            <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Search Type') }}? <span class="require">*</span></label><br/>
                                               
                                                <select name="shop_search_type" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="normal" @if($addition_settings->shop_search_type == 'normal') selected @endif>{{ __('Normal') }}</option>
                                                <option value="ajax" @if($addition_settings->shop_search_type == 'ajax') selected @endif>{{ __('Ajax') }}</option>
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Price range max price') }} <span class="require">*</span></label><br/>
                                               <input id="site_range_max_price" name="site_range_max_price" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->site_range_max_price }}" data-bvalidator="required">
                                                
                                                
                                             </div>
                                            
                                          
                                             
                                              
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Header Option') }}</h4></div></div>
                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                             
                                            
                                             
                                            <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Header Layout') }}? <span class="require">*</span></label><br/>
                                               
                                                <select name="header_layout" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="layout_one" @if($addition_settings->header_layout == 'layout_one') selected @endif>{{ __('Layout 1') }}</option>
                                                <option value="layout_two" @if($addition_settings->header_layout == 'layout_two') selected @endif>{{ __('Layout 2') }}</option>
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Layout 1') }}</label><br/>
                                               
                                                <img  class="lazy layout-img" width="520" height="150" src="{{ url('/') }}/public/img/layout1.png"  />
                                                
                                             </div>
                                            
                                          <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Layout 2') }}</label><br/>
                                               
                                                <img  class="lazy layout-img" width="520" height="150" src="{{ url('/') }}/public/img/layout2.png"  />
                                                
                                             </div>
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                              <div class="col-md-12"><div class="card-body"><h4>{{ __('Text Limitation') }}</h4></div></div>
                              
                              <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Post Short Description') }} ({{ __('chars length') }}) <span class="require">*</span></label><br/>
                                               <input id="post_short_desc_limit" name="post_short_desc_limit" type="text" class="form-control noscroll_textarea" value="{{ $addition_settings->post_short_desc_limit }}" data-bvalidator="required,digit,min[0]"><small>({{ __('if you will set "0" full text displaying') }})</small>
                                                
                                                
                                             </div>
                                            
                                          <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Author Name') }} ({{ __('chars length') }}) <span class="require">*</span></label><br/>
                                               <input id="author_name_limit" name="author_name_limit" type="text" class="form-control noscroll_textarea" value="{{ $addition_settings->author_name_limit }}" data-bvalidator="required,digit,min[0]"><small>({{ __('if you will set "0" full text displaying') }})</small>
                                                
                                                
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Item Name') }} ({{ __('chars length') }}) <span class="require">*</span></label><br/>
                                               <input id="item_name_limit" name="item_name_limit" type="text" class="form-control noscroll_textarea" value="{{ $addition_settings->item_name_limit }}" data-bvalidator="required,digit,min[0]"><small>({{ __('if you will set "0" full text displaying') }})</small>
                                                
                                                
                                             </div>
                                          
                                    </div>
                                </div>

                            </div>
                            </div>
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('File Accepted Extension') }}</h4></div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Item File Accepted Extension') }} <span class="require">*</span></label><br/>
                                               <input id="item_file_extension" name="item_file_extension" type="text" class="form-control noscroll_textarea" value="{{ $addition_settings->item_file_extension }}" data-bvalidator="required"><small>(  {{ __('example') }} : <span class="red-color">.jpeg,.jpg,.png,.webp,.zip,.mp4,.mp3</span>  )</small>
                                                
                                                
                                             </div>
                                          
                                    </div>
                                </div>

                            </div>
                            </div>
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Footer Widget') }}</h4></div></div>
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                             <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Sale Count') }} <span class="require">*</span></label><br/>
                                               
                                                <select name="item_sale_count" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="1" @if($addition_settings->item_sale_count == 1) selected @endif>{{ __('ON') }}</option>
                                                <option value="0" @if($addition_settings->item_sale_count == 0) selected @endif>{{ __('OFF') }}</option>
                                                </select>
                                                
                                             </div>
                                            
                                          
                                             <div class="form-group">
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Product Count') }} <span class="require">*</span></label><br/>
                                               
                                                <select name="files_count" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="1" @if($addition_settings->files_count == 1) selected @endif>{{ __('ON') }}</option>
                                                <option value="0" @if($addition_settings->files_count == 0) selected @endif>{{ __('OFF') }}</option>
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
                                                <label for="site_loader_display" class="control-label mb-1">{{ __('Members Count') }} <span class="require">*</span></label><br/>
                                               
                                                <select name="members_count" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="1" @if($addition_settings->members_count == 1) selected @endif>{{ __('ON') }}</option>
                                                <option value="0" @if($addition_settings->members_count == 0) selected @endif>{{ __('OFF') }}</option>
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
    <!-- Right Panel -->
    @else
    @include('admin.denied')
    @endif

   @include('admin.javascript')


</body>

</html>
