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
    @if(in_array('selling',$avilable))
    @if($allsettings->site_selling_display == 1)
    <div id="right-panel" class="right-panel">

       
                       @include('admin.header')
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Start Selling') }}</h1>
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
                           <form action="{{ route('admin.start-selling') }}" method="post" id="item_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                           <div class="col-md-4">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 1 {{ __('Icon') }} ( 75 X 75px )</label>
                                                <input type="file" id="box1_icon" name="box1_icon" class="form-control-file" @if($setting['setting']->box1_icon == '') data-bvalidator="required" @endif>
                                                @if($setting['setting']->box1_icon != '')
                                                <img class="lazy" width="24" height="24" src="{{ url('/') }}/public/storage/settings/{{ $setting['setting']->box1_icon }}" />
                                                @endif
                                            </div>
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 1 {{ __('Title') }}</label>
                                                <input id="box1_title" name="box1_title" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->box1_title }}" data-bvalidator="required">
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 1 {{ __('Content') }}</label>
                                                <textarea name="box1_text" id="box1_text" rows="6" class="form-control noscroll_textarea" maxlength="160" required>{{ $setting['setting']->box1_text }}</textarea>
                                            </div>
                                             
                                             
                                             
                                            
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 3 {{ __('Icon') }} ( 75 X 75px )</label>
                                                <input type="file" id="box3_icon" name="box3_icon" class="form-control-file" @if($setting['setting']->box3_icon == '') data-bvalidator="required" @endif>
                                                @if($setting['setting']->box3_icon != '')
                                                <img class="lazy" width="24" height="24" src="{{ url('/') }}/public/storage/settings/{{ $setting['setting']->box3_icon }}" />
                                                @endif
                                            </div>
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 3 {{ __('Title') }}</label>
                                                <input id="box3_title" name="box3_title" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->box3_title }}" data-bvalidator="required">
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 3 {{ __('Content') }}</label>
                                                <textarea name="box3_text" id="box3_text" rows="6" class="form-control noscroll_textarea" maxlength="160" required>{{ $setting['setting']->box3_text }}</textarea>
                                            </div>
                                                
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                             <div class="col-md-4">
                             
                             
                             <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                             
                             
                            
                                            
                                           
                                            
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 2 {{ __('Icon') }} ( 75 X 75px )</label>
                                                <input type="file" id="box2_icon" name="box2_icon" class="form-control-file" @if($setting['setting']->box2_icon == '') data-bvalidator="required" @endif>
                                                @if($setting['setting']->box2_icon != '')
                                                <img class="lazy" width="24" height="24" src="{{ url('/') }}/public/storage/settings/{{ $setting['setting']->box2_icon }}" />
                                                @endif
                                            </div>
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 2 {{ __('Title') }}</label>
                                                <input id="box2_title" name="box2_title" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->box2_title }}" data-bvalidator="required">
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 2 {{ __('Content') }}</label>
                                                <textarea name="box2_text" id="box2_text" rows="6" class="form-control noscroll_textarea" maxlength="160" required>{{ $setting['setting']->box2_text }}</textarea>
                                            </div>
                                             
                                              
                                                
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 4 {{ __('Icon') }} ( 75 X 75px )</label>
                                                <input type="file" id="box4_icon" name="box4_icon" class="form-control-file" @if($setting['setting']->box4_icon == '') data-bvalidator="required" @endif>
                                                @if($setting['setting']->box4_icon != '')
                                                <img class="lazy" width="24" height="24" src="{{ url('/') }}/public/storage/settings/{{ $setting['setting']->box4_icon }}" />
                                                @endif
                                            </div>
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 4 {{ __('Title') }}</label>
                                                <input id="box4_title" name="box4_title" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->box4_title }}" data-bvalidator="required">
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 4 {{ __('Content') }}</label>
                                                <textarea name="box4_text" id="box4_text" rows="6" class="form-control noscroll_textarea" maxlength="160" required>{{ $setting['setting']->box4_text }}</textarea>
                                            </div>   
                             
                             
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             
                             
                             
                             <div class="col-md-4">
                                <h4 align="center" class="mt-3 mb-3"> {{ __('Layout Look') }}</h4>
                                <img  class="lazy layout-img" width="390" height="518" src="{{ url('/') }}/public/img/layout.jpg"  />
                             </div>
                             
                             
                             
                             
                             <div class="col-md-12">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                           
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Three Box Title') }}</label>
                                                <input id="three_box_heading" name="three_box_heading" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->three_box_heading }}" data-bvalidator="required">
                                            </div>
                                            
                                            
                                           
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            
                            <div class="col-md-4">
                             
                             
                             <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                             
                             
                            
                                             
                                           
                                            
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 5 {{ __('Icon') }} ( 75 X 75px )</label>
                                                <input type="file" id="box5_icon" name="box5_icon" class="form-control-file" @if($setting['setting']->box5_icon == '') data-bvalidator="required" @endif>
                                                @if($setting['setting']->box5_icon != '')
                                                <img class="lazy" width="24" height="24" src="{{ url('/') }}/public/storage/settings/{{ $setting['setting']->box5_icon }}"  />
                                                @endif
                                            </div>
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 5 {{ __('Title') }}</label>
                                                <input id="box5_title" name="box5_title" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->box5_title }}" data-bvalidator="required">
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 5 {{ __('Content') }}</label>
                                                <textarea name="box5_text" id="box5_text" rows="6" class="form-control noscroll_textarea" maxlength="160" required>{{ $setting['setting']->box5_text }}</textarea>
                                            </div>
                                             
                                        
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             
                             
                             <div class="col-md-4">
                             
                             
                             <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                             
                             
                            
                                            
                                           
                                            
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 6 {{ __('Icon') }} ( 75 X 75px )</label>
                                                <input type="file" id="box6_icon" name="box6_icon" class="form-control-file" @if($setting['setting']->box6_icon == '') data-bvalidator="required" @endif>
                                                @if($setting['setting']->box6_icon != '')
                                                <img class="lazy" width="24" height="24" src="{{ url('/') }}/public/storage/settings/{{ $setting['setting']->box6_icon }}" />
                                                @endif
                                            </div>
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 6 {{ __('Title') }}</label>
                                                <input id="box6_title" name="box6_title" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->box6_title }}" data-bvalidator="required">
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 6 {{ __('Content') }}</label>
                                                <textarea name="box6_text" id="box6_text" rows="6" class="form-control noscroll_textarea" maxlength="160" required>{{ $setting['setting']->box6_text }}</textarea>
                                            </div>
                                             
                                        
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             
                             
                             
                             <div class="col-md-4">
                             
                             
                             <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                             
                             
                            
                                             
                                           
                                            
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 7 {{ __('Icon') }} ( 75 X 75px )</label>
                                                <input type="file" id="box7_icon" name="box7_icon" class="form-control-file" @if($setting['setting']->box7_icon == '') data-bvalidator="required" @endif>
                                                @if($setting['setting']->box7_icon != '')
                                                <img class="lazy" width="24" height="24" src="{{ url('/') }}/public/storage/settings/{{ $setting['setting']->box7_icon }}" />
                                                @endif
                                            </div>
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 7 {{ __('Title') }}</label>
                                                <input id="box7_title" name="box7_title" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->box7_title }}" data-bvalidator="required">
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Box') }} 7 {{ __('Content') }}</label>
                                                <textarea name="box7_text" id="box7_text" rows="6" class="form-control noscroll_textarea" maxlength="160" required>{{ $setting['setting']->box7_text }}</textarea>
                                            </div>
                                             
                                        
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             
                             
                             
                             <div class="col-md-12">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                           
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Title') }} 1</label>
                                                <input id="content_title_one" name="content_title_one" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->content_title_one }}" data-bvalidator="required">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Content') }} 1</label>
                                               
                                                <textarea name="content_text_one" id="summary-ckeditor" rows="6" placeholder="separate keywords with commas" class="form-control noscroll_textarea" maxlength="160" required>{{ html_entity_decode($setting['setting']->content_text_one) }}</textarea>
                                            </div>
                                           
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                             
                             
                             <div class="col-md-12">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                           
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Title') }} 2</label>
                                                <input id="content_title_two" name="content_title_two" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->content_title_two }}" data-bvalidator="required">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Content') }} 2</label>
                                               
                                                
                                                 <textarea name="content_text_two" id="summary-ckeditor2" rows="6" placeholder="separate keywords with commas" class="form-control noscroll_textarea" maxlength="160" required>{{ html_entity_decode($setting['setting']->content_text_two) }}</textarea>
                                            </div>
                                           
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            
                            <div class="col-md-12">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                           
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Title') }} 3</label>
                                                <input id="content_title_three" name="content_title_three" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->content_title_three }}" data-bvalidator="required">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Content') }} 3</label>
                                                
                                                
                                                <textarea name="content_text_three" id="summary-ckeditor3" rows="6" placeholder="separate keywords with commas" class="form-control noscroll_textarea" maxlength="160" required>{{ html_entity_decode($setting['setting']->content_text_three) }}</textarea>
                                            </div>
                                           
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            <div class="col-md-12">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                           
                                            
                                           
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Button Title') }}</label>
                                                <input id="button_title" name="button_title" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->button_title }}" data-bvalidator="required">
                                            </div>
                                            
                                           
                                           
                                                
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                                            <input type="hidden" name="sid" value="1">
                                            <input type="hidden" name="save_box1_icon" value="{{ $setting['setting']->box1_icon }}">
                                            <input type="hidden" name="save_box3_icon" value="{{ $setting['setting']->box3_icon }}">
                                            <input type="hidden" name="save_box2_icon" value="{{ $setting['setting']->box2_icon }}">
                                            <input type="hidden" name="save_box4_icon" value="{{ $setting['setting']->box4_icon }}"> 
                                            <input type="hidden" name="save_box5_icon" value="{{ $setting['setting']->box5_icon }}"> 
                                            <input type="hidden" name="save_box6_icon" value="{{ $setting['setting']->box6_icon }}">
                                            <input type="hidden" name="save_box7_icon" value="{{ $setting['setting']->box7_icon }}"> 
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
