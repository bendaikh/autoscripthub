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
    <div id="right-panel" class="right-panel">
     @include('admin.header')
       <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('PWA Settings') }}</h1>
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
                           <form action="{{ route('admin.pwa-settings') }}" method="post" id="checkout_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                           <div class="col-md-6">
                             <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       <div class="form-group">
                                          <label for="site_title" class="control-label mb-1">{{ __('App Name') }} <span class="require">*</span></label>
                                           <input id="app_name" name="app_name" type="text" class="form-control noscroll_textarea" value="{{ $pwa_settings->app_name }}" data-bvalidator="required">
                                           </div>
                                           <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Background Color') }} <span class="require">*</span></label>
                                                <input id="background_color" name="background_color" type="text" class="form-control noscroll_textarea" value="{{ $pwa_settings->background_color }}" data-bvalidator="required"><small>Example : #ffffff</small></div>
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Short Name') }} <span class="require">*</span></label>
                                                <input id="short_name" name="short_name" type="text" class="form-control noscroll_textarea" value="{{ $pwa_settings->short_name }}" data-bvalidator="required"></div>
                                                <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Theme Color') }} <span class="require">*</span></label>
                                                <input id="theme_color" name="theme_color" type="text" class="form-control noscroll_textarea" value="{{ $pwa_settings->theme_color }}" data-bvalidator="required"><small>Example : #ffffff</small></div>
                             </div>
                                </div>
                              </div>
                             </div>
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('Upload Icons') }} - (PNG Format Only)</h4></div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Icon (72x72)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_icon1" name="pwa_icon1" class="form-control-file" @if($pwa_settings->pwa_icon1 == '') required @endif>
                                            @if($pwa_settings->pwa_icon1 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_icon1 }}" />
                                                @endif
                                            </div>
                                            
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Icon (96x96)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_icon2" name="pwa_icon2" class="form-control-file" @if($pwa_settings->pwa_icon2 == '') required @endif>
                                            @if($pwa_settings->pwa_icon2 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_icon2 }}" />
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Icon (128x128)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_icon3" name="pwa_icon3" class="form-control-file" @if($pwa_settings->pwa_icon3 == '') required @endif>
                                            @if($pwa_settings->pwa_icon3 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_icon3 }}" />
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Icon (144x144)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_icon4" name="pwa_icon4" class="form-control-file" @if($pwa_settings->pwa_icon4 == '') required @endif>
                                            @if($pwa_settings->pwa_icon4 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_icon4 }}" />
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
                                                <label for="site_favicon" class="control-label mb-1">Icon (152x152)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_icon5" name="pwa_icon5" class="form-control-file" @if($pwa_settings->pwa_icon5 == '') required @endif>
                                            @if($pwa_settings->pwa_icon5 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_icon5 }}" />
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Icon (192x192)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_icon6" name="pwa_icon6" class="form-control-file" @if($pwa_settings->pwa_icon6 == '') required @endif>
                                            @if($pwa_settings->pwa_icon6 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_icon6 }}" />
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Icon (384x384)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_icon7" name="pwa_icon7" class="form-control-file" @if($pwa_settings->pwa_icon7 == '') required @endif>
                                            @if($pwa_settings->pwa_icon7 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_icon7 }}" />
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Icon (512x512)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_icon8" name="pwa_icon8" class="form-control-file" @if($pwa_settings->pwa_icon8 == '') required @endif>
                                            @if($pwa_settings->pwa_icon8 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_icon8 }}" />
                                                @endif
                                            </div>
                                                
                                                
                                                
                             
                             
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('Splash Screen') }} - (PNG Format Only)</h4></div></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Screen (640x1136)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_splash1" name="pwa_splash1" class="form-control-file" @if($pwa_settings->pwa_splash1 == '') required @endif>
                                            @if($pwa_settings->pwa_splash1 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_splash1 }}" />
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Screen (750x1334)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_splash2" name="pwa_splash2" class="form-control-file" @if($pwa_settings->pwa_splash2 == '') required @endif>
                                            @if($pwa_settings->pwa_splash2 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_splash2 }}" />
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Screen (828x1792)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_splash3" name="pwa_splash3" class="form-control-file" @if($pwa_settings->pwa_splash3 == '') required @endif>
                                            @if($pwa_settings->pwa_splash3 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_splash3 }}" />
                                                @endif
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Screen (1125x2436)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_splash4" name="pwa_splash4" class="form-control-file" @if($pwa_settings->pwa_splash4 == '') required @endif>
                                            @if($pwa_settings->pwa_splash4 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_splash4 }}" />
                                                @endif
                                            </div>
                                            
                                             
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Screen (1242x2208)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_splash5" name="pwa_splash5" class="form-control-file" @if($pwa_settings->pwa_splash5 == '') required @endif>
                                            @if($pwa_settings->pwa_splash5 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_splash5 }}" />
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
                                                <label for="site_favicon" class="control-label mb-1">Screen (1242x2688)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_splash6" name="pwa_splash6" class="form-control-file" @if($pwa_settings->pwa_splash6 == '') required @endif>
                                            @if($pwa_settings->pwa_splash6 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_splash6 }}" />
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Screen (1536x2048)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_splash7" name="pwa_splash7" class="form-control-file" @if($pwa_settings->pwa_splash7 == '') required @endif>
                                            @if($pwa_settings->pwa_splash7 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_splash7 }}" />
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Screen (1668x2224)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_splash8" name="pwa_splash8" class="form-control-file" @if($pwa_settings->pwa_splash8 == '') required @endif>
                                            @if($pwa_settings->pwa_splash8 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_splash8 }}" />
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Screen (1668x2388)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_splash9" name="pwa_splash9" class="form-control-file" @if($pwa_settings->pwa_splash9 == '') required @endif>
                                            @if($pwa_settings->pwa_splash9 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_splash9 }}" />
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="site_favicon" class="control-label mb-1">Screen (2048x2732)<span class="require">*</span></label>
                                                
                                            <input type="file" id="pwa_splash10" name="pwa_splash10" class="form-control-file" @if($pwa_settings->pwa_splash10 == '') required @endif>
                                            @if($pwa_settings->pwa_splash10 != '')
                                                <img height="50" src="{{ url('/') }}/images/icons/{{ $pwa_settings->pwa_splash10 }}" />
                                                @endif
                                            </div>
                                            
                                           
                                    </div>
                                </div>

                            </div>
                            </div>
                             <input type="hidden" name="sid" value="1">
                             <input type="hidden" name="save_pwa_icon1" value="{{ $pwa_settings->pwa_icon1 }}">
                             <input type="hidden" name="save_pwa_icon2" value="{{ $pwa_settings->pwa_icon2 }}">
                             <input type="hidden" name="save_pwa_icon3" value="{{ $pwa_settings->pwa_icon3 }}">
                             <input type="hidden" name="save_pwa_icon4" value="{{ $pwa_settings->pwa_icon4 }}">
                             <input type="hidden" name="save_pwa_icon5" value="{{ $pwa_settings->pwa_icon5 }}">
                             <input type="hidden" name="save_pwa_icon6" value="{{ $pwa_settings->pwa_icon6 }}">
                             <input type="hidden" name="save_pwa_icon7" value="{{ $pwa_settings->pwa_icon7 }}">
                             <input type="hidden" name="save_pwa_icon8" value="{{ $pwa_settings->pwa_icon8 }}">
                             
                             <input type="hidden" name="save_pwa_splash1" value="{{ $pwa_settings->pwa_splash1 }}">
                             <input type="hidden" name="save_pwa_splash2" value="{{ $pwa_settings->pwa_splash2 }}">
                             <input type="hidden" name="save_pwa_splash3" value="{{ $pwa_settings->pwa_splash3 }}">
                             <input type="hidden" name="save_pwa_splash4" value="{{ $pwa_settings->pwa_splash4 }}">
                             <input type="hidden" name="save_pwa_splash5" value="{{ $pwa_settings->pwa_splash5 }}">
                             <input type="hidden" name="save_pwa_splash6" value="{{ $pwa_settings->pwa_splash6 }}">
                             <input type="hidden" name="save_pwa_splash7" value="{{ $pwa_settings->pwa_splash7 }}">
                             <input type="hidden" name="save_pwa_splash8" value="{{ $pwa_settings->pwa_splash8 }}">
                             <input type="hidden" name="save_pwa_splash9" value="{{ $pwa_settings->pwa_splash9 }}">
                             <input type="hidden" name="save_pwa_splash10" value="{{ $pwa_settings->pwa_splash10 }}">
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
    <!-- Right Panel -->
   @include('admin.javascript')
</body>
</html>