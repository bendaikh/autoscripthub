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
    @if(in_array('subscription',$avilable))
    <div id="right-panel" class="right-panel">

        
                       @include('admin.header')
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Edit Pack') }}</h1>
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
                            <div class="card-header">
                                <strong class="card-title">{{ __('Edit Pack') }}</strong>
                            </div>
                             <div class="card-body">
                 @if($demo_mode == 'on')
                                 @include('admin.demo-mode')
                                 @else
                                 <form action="{{ route('admin.edit-subscription') }}" method="post" id="setting_form" enctype="multipart/form-data">
                                 {{ csrf_field() }}
                                 @endif
                                 <div class="col-md-6">
                                        <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Pack Name') }} <span class="require">*</span></label>
                                                <input id="subscr_name" name="subscr_name" type="text" class="form-control" data-bvalidator="required" value="{{ $edit['subscri']->subscr_name }}">
                                            </div>                                   
                                            
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Price') }} ({{ $allsettings->site_currency_symbol }}) <span class="require">*</span></label>
                                                <input id="subscr_price" name="subscr_price" type="text" class="form-control" data-bvalidator="required,min[1]" value="{{ $edit['subscri']->subscr_price }}">
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"> {{ __('Duration') }} <span class="require">*</span></label>
                                                <select name="subscr_duration" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                @foreach($durations as $duration)
                                                <option value="{{ $duration }}" @if($edit['subscri']->subscr_duration == $duration) selected @endif>{{ $duration }}</option>
                                                @endforeach
                                                </select>
                                                
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"> {{ __('Item Upload / Sales Type') }} <span class="require">*</span></label>
                                                <select name="subscr_item_level" id="subscr_item_level" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                @foreach($item_sale_type as $key => $value)
                                                <option value="{{ $key }}" @if($edit['subscri']->subscr_item_level == $key) selected @endif>{{ $value }}</option>
                                                @endforeach
                                                </select>
                                                
                                            </div>
                                            
                                             <div id="limit_item" @if($edit['subscri']->subscr_item_level == 'limited') class="form-group force-block" @else class="form-group force-none" @endif>
                                                <label for="name" class="control-label mb-1">{{ __('Limited No of Items') }} <span class="require">*</span></label>
                                                <input id="subscr_item" name="subscr_item" type="text" class="form-control" data-bvalidator="required,digit,min[1]" value="{{ $edit['subscri']->subscr_item }}">
                                            </div> 
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"> {{ __('Storage Space') }} <span class="require">*</span></label>
                                                <select name="subscr_space_level" id="subscr_space_level" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                @foreach($storage_space as $key => $value)
                                                <option value="{{ $key }}" @if($edit['subscri']->subscr_space_level == $key) selected @endif>{{ $value }}</option>
                                                @endforeach
                                                </select>
                                                
                                            </div>
                                            <div id="limit_space" @if($edit['subscri']->subscr_space_level == 'limited') class="force-block" @else class="force-none" @endif>
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Space Limit') }} <span class="require">*</span></label>
                                                <div class="row">
                                                <div class="col-md-6">
                                                <input id="subscr_space" name="subscr_space" type="text" class="form-control" data-bvalidator="required,digit,min[1]" value="{{ $edit['subscri']->subscr_space }}">
                                                </div>
                                                <div class="col-md-6">
                                                <select name="subscr_space_type" id="subscr_space_type" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                @foreach($storage_space_type as $value)
                                                <option value="{{ $value }}" @if($edit['subscri']->subscr_space_type == $value) selected @endif>{{ $value }}</option>
                                                @endforeach
                                                </select>
                                                </div>
                                                </div>
                                            </div> 
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Download') }} {{ __('Limited No of Items') }} ({{ __('Per Day') }})<span class="require">*</span></label>
                                                <input id="subscr_download_item" name="subscr_download_item" type="text" class="form-control" data-bvalidator="required,digit,min[1]" value="{{ $edit['subscri']->subscr_download_item }}">
                                            </div>
                                      </div>
                                      
                                      <div class="col-md-6">
                                      
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Email Support') }} <span class="require">*</span></label>
                                                <select name="subscr_email_support" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="1" @if($edit['subscri']->subscr_email_support == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($edit['subscri']->subscr_email_support == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                                
                                            </div>       
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Direct Payment Transfer') }} <span class="require">*</span></label>
                                                <select name="subscr_payment_mode" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="1" @if($edit['subscri']->subscr_payment_mode == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if($edit['subscri']->subscr_payment_mode == 0) selected @endif>{{ __('No') }}</option>
                                                </select>
                                            </div>
                                            
                                            
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Display Order') }}</label>
                                                <input id="subscr_order" name="subscr_order" type="text" class="form-control" data-bvalidator="digit,min[0]" value="{{ $edit['subscri']->subscr_order }}">
                                            </div> 
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"> {{ __('Status') }} <span class="require">*</span></label>
                                                <select name="subscr_status" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="1" @if($edit['subscri']->subscr_status == 1) selected @endif>{{ __('Active') }}</option>
                                                <option value="0" @if($edit['subscri']->subscr_status == 0) selected @endif>{{ __('InActive') }}</option>
                                                </select>
                                                
                                            </div>   
                                             
                                        </div>
                                        
                                        <div class="col-md-12 mb-5 mt-5"><h4>{{ __('Design Settings') }}</h4></div>
                                        
                                        <div class="col-md-6">
                                        
                                        <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"> {{ __('Highlight This Pack') }}? <span class="require">*</span></label>
                                                <select name="highlight_pack" id="highlight_pack" class="form-control" data-bvalidator="required">
                                                <option value=""></option>
                                                <option value="0" @if($edit['subscri']->highlight_pack == 0) selected @endif>{{ __('No') }}</option>
                                                <option value="1" @if($edit['subscri']->highlight_pack == 1) selected @endif>{{ __('Yes') }}</option>
                                                </select>
                                                
                                            </div> 
                                        <div id="highbox1" @if($edit['subscri']->highlight_pack == 1) class="force-block" @else class="force-none" @endif>
                                        <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Highlight Background Color') }} <span class="require">*</span></label>
                                                <input id="highlight_bg_color" name="highlight_bg_color" type="text" class="form-control" value="{{ $edit['subscri']->highlight_bg_color }}" data-bvalidator="required">
                                                <small>({{ __('example color code') }} : #666000 )</small>
                                            </div> 
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Highlight Text Color') }} <span class="require">*</span></label>
                                                <input id="highlight_text_color" name="highlight_text_color" type="text" class="form-control" value="{{ $edit['subscri']->highlight_text_color }}" data-bvalidator="required">
                                                <small>({{ __('example color code') }} : #666000 )</small>
                                            </div> 
                                             
                                        </div>
                                        </div>
                                        <div class="col-md-6">
                                        
                                        <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"> {{ __('Extra Info') }} </label>
                                                <input id="extra_info" name="extra_info" type="text" class="form-control" value="{{ $edit['subscri']->extra_info }}">
                                                
                                                
                                            </div>
                                        
                                        <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"> {{ __('Icon Color') }} <span class="require">*</span></label>
                                                <input id="icon_color" name="icon_color" type="text" class="form-control" value="{{ $edit['subscri']->icon_color }}" data-bvalidator="required">
                                                <small>({{ __('example color code') }} : #666000 )</small>
                                                
                                            </div> 
                                        
                                        <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Button Background Color') }} <span class="require">*</span></label>
                                                <input id="button_bg_color" name="button_bg_color" type="text" class="form-control" value="{{ $edit['subscri']->button_bg_color }}" data-bvalidator="required">
                                                <small>({{ __('example color code') }} : #666000 )</small>
                                            </div>  
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Button Text Color') }} <span class="require">*</span></label>
                                                <input id="button_text_color" name="button_text_color" type="text" class="form-control" value="{{ $edit['subscri']->button_text_color }}" data-bvalidator="required">
                                                <small>({{ __('example color code') }} : #666000 )</small>
                                            </div>
                                             
                                        
                                        </div>
                                        
                                        <input type="hidden" name="subscr_id" value="{{ $edit['subscri']->subscr_id }}"> 
                                        <div class="col-md-12">
                                                        <button type="submit" name="submit" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-dot-circle-o"></i> {{ __('Submit') }}
                                                        </button>
                                                        <button type="reset" class="btn btn-danger btn-sm">
                                                            <i class="fa fa-ban"></i> {{ __('Reset') }}
                                                        </button>
                                                    </div>
                                          
                     </form>
                     </div>
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
