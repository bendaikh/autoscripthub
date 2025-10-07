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
                        <h1>{{ __('Badges Settings') }}</h1>
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
                           <form action="{{ route('admin.badges-settings') }}" method="post" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                          
                         
                          
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Exclusive Author Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="exclusive_author_icon" name="exclusive_author_icon" class="form-control-file" @if($setting['setting']->exclusive_author_icon == '') required @endif>
                                            @if($setting['setting']->exclusive_author_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->exclusive_author_icon }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Trends Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="trends_icon" name="trends_icon" class="form-control-file" @if($setting['setting']->trends_icon == '') required @endif>
                                            @if($setting['setting']->trends_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->trends_icon }}"  />
                                                @endif
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
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Featured Item Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="featured_item_icon" name="featured_item_icon" class="form-control-file" @if($setting['setting']->featured_item_icon == '') required @endif>
                                            @if($setting['setting']->featured_item_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->featured_item_icon }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Free Item Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="free_item_icon" name="free_item_icon" class="form-control-file" @if($setting['setting']->free_item_icon == '') required @endif>
                                            @if($setting['setting']->free_item_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->free_item_icon }}"  />
                                                @endif
                                            </div>
                                                
                                                
                                                <input type="hidden" name="sid" value="1">
                             
                             
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('Year of Membership Badges') }}</h4></div></div>
                             
                             
                             <div class="col-md-6">
                             
                             
                             <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                            
                             
                                        <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">1 {{ __('Year Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="one_year_icon" name="one_year_icon" class="form-control-file" @if($setting['setting']->one_year_icon == '') required @endif>
                                            @if($setting['setting']->one_year_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->one_year_icon }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">2 {{ __('Year Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="two_year_icon" name="two_year_icon" class="form-control-file" @if($setting['setting']->two_year_icon == '') required @endif>
                                            @if($setting['setting']->two_year_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->two_year_icon }}"  />
                                                @endif
                                            </div>
                                                
                                             <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">3 {{ __('Year Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="three_year_icon" name="three_year_icon" class="form-control-file" @if($setting['setting']->three_year_icon == '') required @endif>
                                            @if($setting['setting']->three_year_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->three_year_icon }}"  />
                                                @endif
                                            </div>    
                                                
                                              
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">4 {{ __('Year Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="four_year_icon" name="four_year_icon" class="form-control-file" @if($setting['setting']->four_year_icon == '') required @endif>
                                            @if($setting['setting']->four_year_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->four_year_icon }}"  />
                                                @endif
                                            </div>   
                                                
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">5 {{ __('Year Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="five_year_icon" name="five_year_icon" class="form-control-file" @if($setting['setting']->five_year_icon == '') required @endif>
                                            @if($setting['setting']->five_year_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->five_year_icon }}"  />
                                                @endif
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
                                                <label for="site_favicon" class="control-label mb-1">6 {{ __('Year Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="six_year_icon" name="six_year_icon" class="form-control-file" @if($setting['setting']->six_year_icon == '') required @endif>
                                            @if($setting['setting']->six_year_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->six_year_icon }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">7 {{ __('Year Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="seven_year_icon" name="seven_year_icon" class="form-control-file" @if($setting['setting']->seven_year_icon == '') required @endif>
                                            @if($setting['setting']->seven_year_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->seven_year_icon }}"  />
                                                @endif
                                            </div>
                                                
                                             <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">8 {{ __('Year Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="eight_year_icon" name="eight_year_icon" class="form-control-file" @if($setting['setting']->eight_year_icon == '') required @endif>
                                            @if($setting['setting']->eight_year_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->eight_year_icon }}"  />
                                                @endif
                                            </div>    
                                                
                                              
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">9 {{ __('Year Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="nine_year_icon" name="nine_year_icon" class="form-control-file" @if($setting['setting']->nine_year_icon == '') required @endif>
                                            @if($setting['setting']->nine_year_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->nine_year_icon }}"  />
                                                @endif
                                            </div>   
                                                
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">10 {{ __('Year Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="ten_year_icon" name="ten_year_icon" class="form-control-file" @if($setting['setting']->ten_year_icon == '') required @endif>
                                            @if($setting['setting']->ten_year_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->ten_year_icon }}"  />
                                                @endif
                                            </div>   
                             
                            
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             
                             
                             
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('Sold Author Level') }}</h4></div></div>
                             
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                            
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Sold Level') }} 1 ({{ $allsettings->site_currency }})<span class="require">*</span></label><br/>
                                               <input id="author_sold_level_one" name="author_sold_level_one" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_sold_level_one }}" required>
                                                
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Sold Level') }} 2 ({{ $allsettings->site_currency }})<span class="require">*</span></label><br/>
                                               <input id="author_sold_level_two" name="author_sold_level_two" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_sold_level_two }}" required>
                                                
                                                
                                             </div>
                                             
                                             
                                              <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Sold Level') }} 3 ({{ $allsettings->site_currency }})<span class="require">*</span></label><br/>
                                               <input id="author_sold_level_three" name="author_sold_level_three" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_sold_level_three }}" required>
                                                
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Sold Level') }} 4 ({{ $allsettings->site_currency }})<span class="require">*</span></label><br/>
                                               <input id="author_sold_level_four" name="author_sold_level_four" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_sold_level_four }}" required>
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Sold Level') }} 5 ({{ $allsettings->site_currency }})<span class="require">*</span></label><br/>
                                               <input id="author_sold_level_five" name="author_sold_level_five" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_sold_level_five }}" required>
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Sold Level') }} 6+ ({{ $allsettings->site_currency }})<span class="require">*</span></label><br/>
                                               <input id="author_sold_level_six" name="author_sold_level_six" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_sold_level_six }}" required>
                                               
                                             </div>
                                             
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Power Elite Author Label') }} ({{ __('Sold Level') }} 6+)<span class="require">*</span></label><br/>
                                               <input id="author_sold_level_six_label" name="author_sold_level_six_label" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_sold_level_six_label }}" placeholder="{{ __('Power Elite Author') }}" required>
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
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 1 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_sold_level_one_icon" name="author_sold_level_one_icon" class="form-control-file" @if($setting['setting']->author_sold_level_one_icon == '') required @endif>
                                            @if($setting['setting']->author_sold_level_one_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_sold_level_one_icon }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 2 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_sold_level_two_icon" name="author_sold_level_two_icon" class="form-control-file" @if($setting['setting']->author_sold_level_two_icon == '') required @endif>
                                            @if($setting['setting']->author_sold_level_two_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_sold_level_two_icon }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            
                                             <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 3 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_sold_level_three_icon" name="author_sold_level_three_icon" class="form-control-file" @if($setting['setting']->author_sold_level_three_icon == '') required @endif>
                                            @if($setting['setting']->author_sold_level_three_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_sold_level_three_icon }}"  />
                                                @endif
                                            </div>
                                       
                                        
                                        
                                           <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 4 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_sold_level_four_icon" name="author_sold_level_four_icon" class="form-control-file" @if($setting['setting']->author_sold_level_four_icon == '') required @endif>
                                            @if($setting['setting']->author_sold_level_four_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_sold_level_four_icon }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 5 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_sold_level_five_icon" name="author_sold_level_five_icon" class="form-control-file" @if($setting['setting']->author_sold_level_five_icon == '') required @endif>
                                            @if($setting['setting']->author_sold_level_five_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_sold_level_five_icon }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 6 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_sold_level_six_icon" name="author_sold_level_six_icon" class="form-control-file" @if($setting['setting']->author_sold_level_six_icon == '') required @endif>
                                            @if($setting['setting']->author_sold_level_six_icon != '')
                                                <img class="lazy" width="50" height="50"" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_sold_level_six_icon }}"  />
                                                @endif
                                            </div> 
                                            
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Power Elite Author Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="power_elite_author_icon" name="power_elite_author_icon" class="form-control-file" @if($setting['setting']->power_elite_author_icon == '') required @endif>
                                            @if($setting['setting']->power_elite_author_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->power_elite_author_icon }}"  />
                                                @endif
                                            </div> 
                                      
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                            <div class="col-md-12"><div class="card-body"><h4>{{ __('Collector Author Level') }}</h4></div></div>
                            
                            <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Collected Level') }} 1 (items)<span class="require">*</span></label><br/>
                                               <input id="author_collect_level_one" name="author_collect_level_one" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_collect_level_one }}" required>
                                                
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Collected Level') }} 2 (items)<span class="require">*</span></label><br/>
                                               <input id="author_collect_level_two" name="author_collect_level_two" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_collect_level_two }}" required>
                                                
                                                
                                             </div>
                                             
                                             
                                              <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Collected Level') }} 3 (items)<span class="require">*</span></label><br/>
                                               <input id="author_collect_level_three" name="author_collect_level_three" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_collect_level_three }}" required>
                                                
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Collected Level') }} 4 (items)<span class="require">*</span></label><br/>
                                               <input id="author_collect_level_four" name="author_collect_level_four" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_collect_level_four }}" required>
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Collected Level') }} 5 (items)<span class="require">*</span></label><br/>
                                               <input id="author_collect_level_five" name="author_collect_level_five" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_collect_level_five }}" required>
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Collected Level') }} 6+ (items)<span class="require">*</span></label><br/>
                                               <input id="author_collect_level_six" name="author_collect_level_six" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_collect_level_six }}" required>
                                              
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
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 1 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_collect_level_one_icon" name="author_collect_level_one_icon" class="form-control-file" @if($setting['setting']->author_collect_level_one_icon == '') required @endif>
                                            @if($setting['setting']->author_collect_level_one_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_collect_level_one_icon }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 2 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_collect_level_two_icon" name="author_collect_level_two_icon" class="form-control-file" @if($setting['setting']->author_collect_level_two_icon == '') required @endif>
                                            @if($setting['setting']->author_collect_level_two_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_collect_level_two_icon }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            
                                             <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 3 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_collect_level_three_icon" name="author_collect_level_three_icon" class="form-control-file" @if($setting['setting']->author_collect_level_three_icon == '') required @endif>
                                            @if($setting['setting']->author_collect_level_three_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_collect_level_three_icon }}"  />
                                                @endif
                                            </div>
                                       
                                        
                                        
                                           <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 4 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_collect_level_four_icon" name="author_collect_level_four_icon" class="form-control-file" @if($setting['setting']->author_collect_level_four_icon == '') required @endif>
                                            @if($setting['setting']->author_collect_level_four_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_collect_level_four_icon }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 5 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_collect_level_five_icon" name="author_collect_level_five_icon" class="form-control-file" @if($setting['setting']->author_collect_level_five_icon == '') required @endif>
                                            @if($setting['setting']->author_collect_level_five_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_collect_level_five_icon }}"  />
                                                @endif
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 6 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_collect_level_six_icon" name="author_collect_level_six_icon" class="form-control-file" @if($setting['setting']->author_collect_level_six_icon == '') required @endif>
                                            @if($setting['setting']->author_collect_level_six_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_collect_level_six_icon }}"  />
                                                @endif
                                            </div>  
                                      
                            
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('Referral Author Level') }}</h4></div></div>
                             
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Referred Level') }} 1 ({{ __('Members') }})<span class="require">*</span></label><br/>
                                               <input id="author_referral_level_one" name="author_referral_level_one" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_referral_level_one }}" required>
                                                
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Referred Level') }} 2 ({{ __('Members') }})<span class="require">*</span></label><br/>
                                               <input id="author_referral_level_two" name="author_referral_level_two" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_referral_level_two }}" required>
                                                
                                                
                                             </div>
                                             
                                             
                                              <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Referred Level') }} 3 ({{ __('Members') }})<span class="require">*</span></label><br/>
                                               <input id="author_referral_level_three" name="author_referral_level_three" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_referral_level_three }}" required>
                                                
                                                
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Referred Level') }} 4 ({{ __('Members') }})<span class="require">*</span></label><br/>
                                               <input id="author_referral_level_four" name="author_referral_level_four" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_referral_level_four }}" required>
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Referred Level') }} 5 ({{ __('Members') }})<span class="require">*</span></label><br/>
                                               <input id="author_referral_level_five" name="author_referral_level_five" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_referral_level_five }}" required>
                                             </div>
                                             
                                             
                                             <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Referred Level') }} 6+ ({{ __('Members') }})<span class="require">*</span></label><br/>
                                               <input id="author_referral_level_six" name="author_referral_level_six" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->author_referral_level_six }}" required>
                                              
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
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 1 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_referral_level_one_icon" name="author_referral_level_one_icon" class="form-control-file" @if($setting['setting']->author_referral_level_one_icon == '') required @endif>
                                            @if($setting['setting']->author_referral_level_one_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_referral_level_one_icon }}" />
                                                @endif
                                            </div>
                                            
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 2 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_referral_level_two_icon" name="author_referral_level_two_icon" class="form-control-file" @if($setting['setting']->author_referral_level_two_icon == '') required @endif>
                                            @if($setting['setting']->author_referral_level_two_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_referral_level_two_icon }}" />
                                                @endif
                                            </div>
                                            
                                            
                                            
                                             <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 3 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_referral_level_three_icon" name="author_referral_level_three_icon" class="form-control-file" @if($setting['setting']->author_referral_level_three_icon == '') required @endif>
                                            @if($setting['setting']->author_referral_level_three_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_referral_level_three_icon }}" />
                                                @endif
                                            </div>
                                       
                                        
                                        
                                           <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 4 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_referral_level_four_icon" name="author_referral_level_four_icon" class="form-control-file" @if($setting['setting']->author_referral_level_four_icon == '') required @endif>
                                            @if($setting['setting']->author_referral_level_four_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_referral_level_four_icon }}" />
                                                @endif
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 5 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_referral_level_five_icon" name="author_referral_level_five_icon" class="form-control-file" @if($setting['setting']->author_referral_level_five_icon == '') required @endif>
                                            @if($setting['setting']->author_referral_level_five_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_referral_level_five_icon }}" />
                                                @endif
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">{{ __('Level') }} 6 {{ __('Badge') }}<span class="require">*</span></label>
                                                
                                            <input type="file" id="author_referral_level_six_icon" name="author_referral_level_six_icon" class="form-control-file" @if($setting['setting']->author_referral_level_six_icon == '') required @endif>
                                            @if($setting['setting']->author_referral_level_six_icon != '')
                                                <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/storage/badges/{{ $setting['setting']->author_referral_level_six_icon }}" />
                                                @endif
                                            </div>  
                                      
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                             
                             <input type="hidden" name="save_exclusive_author_icon" value="{{ $setting['setting']->exclusive_author_icon }}">
                            <input type="hidden" name="save_author_sold_level_one_icon" value="{{ $setting['setting']->author_sold_level_one_icon }}">
                            <input type="hidden" name="save_author_sold_level_two_icon" value="{{ $setting['setting']->author_sold_level_two_icon }}">
                            <input type="hidden" name="save_author_sold_level_three_icon" value="{{ $setting['setting']->author_sold_level_three_icon }}">
                            <input type="hidden" name="save_author_sold_level_four_icon" value="{{ $setting['setting']->author_sold_level_four_icon }}">
                            <input type="hidden" name="save_author_sold_level_five_icon" value="{{ $setting['setting']->author_sold_level_five_icon }}">
                            <input type="hidden" name="save_author_sold_level_six_icon" value="{{ $setting['setting']->author_sold_level_six_icon }}">
                            <input type="hidden" name="save_author_collect_level_one_icon" value="{{ $setting['setting']->author_collect_level_one_icon }}">
                            <input type="hidden" name="save_author_collect_level_two_icon" value="{{ $setting['setting']->author_collect_level_two_icon }}">
                            <input type="hidden" name="save_author_collect_level_three_icon" value="{{ $setting['setting']->author_collect_level_three_icon }}">
                            <input type="hidden" name="save_author_collect_level_four_icon" value="{{ $setting['setting']->author_collect_level_four_icon }}">
                            <input type="hidden" name="save_author_collect_level_five_icon" value="{{ $setting['setting']->author_collect_level_five_icon }}">
                            <input type="hidden" name="save_author_collect_level_six_icon" value="{{ $setting['setting']->author_collect_level_six_icon }}">
                            <input type="hidden" name="save_author_referral_level_one_icon" value="{{ $setting['setting']->author_referral_level_one_icon }}">
                            <input type="hidden" name="save_author_referral_level_two_icon" value="{{ $setting['setting']->author_referral_level_two_icon }}">
                            <input type="hidden" name="save_author_referral_level_three_icon" value="{{ $setting['setting']->author_referral_level_three_icon }}">
                            <input type="hidden" name="save_author_referral_level_four_icon" value="{{ $setting['setting']->author_referral_level_four_icon }}">
                            <input type="hidden" name="save_author_referral_level_five_icon" value="{{ $setting['setting']->author_referral_level_five_icon }}">
                            <input type="hidden" name="save_author_referral_level_six_icon" value="{{ $setting['setting']->author_referral_level_six_icon }}">
                            <input type="hidden" name="save_trends_icon" value="{{ $setting['setting']->trends_icon }}">
                            <input type="hidden" name="save_featured_item_icon" value="{{ $setting['setting']->featured_item_icon }}">
                            <input type="hidden" name="save_power_elite_author_icon" value="{{ $setting['setting']->power_elite_author_icon }}">
                            <input type="hidden" name="save_free_item_icon" value="{{ $setting['setting']->free_item_icon }}">
                            <input type="hidden" name="save_one_year_icon" value="{{ $setting['setting']->one_year_icon }}">
                            <input type="hidden" name="save_two_year_icon" value="{{ $setting['setting']->two_year_icon }}">
                            <input type="hidden" name="save_three_year_icon" value="{{ $setting['setting']->three_year_icon }}">
                            <input type="hidden" name="save_four_year_icon" value="{{ $setting['setting']->four_year_icon }}">
                            <input type="hidden" name="save_five_year_icon" value="{{ $setting['setting']->five_year_icon }}">
                            <input type="hidden" name="save_six_year_icon" value="{{ $setting['setting']->six_year_icon }}">
                            <input type="hidden" name="save_seven_year_icon" value="{{ $setting['setting']->seven_year_icon }}">
                            <input type="hidden" name="save_eight_year_icon" value="{{ $setting['setting']->eight_year_icon }}">
                            <input type="hidden" name="save_nine_year_icon" value="{{ $setting['setting']->nine_year_icon }}">
                            <input type="hidden" name="save_ten_year_icon" value="{{ $setting['setting']->ten_year_icon }}">
                            
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
    @include('admin.denied')
    @endif
    <!-- Right Panel -->


   @include('admin.javascript')


</body>

</html>
