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
    @if(in_array('ads',$avilable))
    <div id="right-panel" class="right-panel">

       
                       @include('admin.header')
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Ads') }}</h1>
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
                           <form action="{{ route('admin.ads') }}" method="post" id="setting_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                          <div class="col-md-12 mt-3"><h5>{{ __('Top Ads') }}</h5></div>
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><strong>{{ __('Pages') }}</strong> </label><br/>
                                                @foreach($ads_pages as $key => $value)
                                                <input id="top_ads_pages" name="top_ads_pages[]" type="checkbox" class="noscroll_textarea" value="{{ $key }}" @if(in_array($key,$top_ads_pages)) checked @endif> {{ $value }} @if($key == 'pages') (ex: about us, faq,...ect) @endif<br/>
                                                @endforeach
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Example Code') }} </label>
                                                <br> <img src="{{ url('/') }}/resources/views/admin/template/images/ins.png">
                                            </div> 
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Ads Code') }}</label>
                                                <textarea name="top_ads" id="top_ads" rows="6" class="form-control noscroll_textarea">{{ $additional['setting']->top_ads }}</textarea>
                                            </div> 
                                            
                                           
                             
                             
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             <div class="col-md-12 mt-3"><h5>{{ __('Sidebar Ads') }}</h5></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><strong>{{ __('Pages') }}</strong> </label><br/>
                                                @foreach($ads_pages as $key => $value)
                                                <span @if($key == 'shop' || $key == 'item-details' || $key == 'blog' || $key == 'post-details' || $key == 'top-authors')  @else style="display:none;" @endif>
                                                <input id="sidebar_ads_pages" name="sidebar_ads_pages[]" type="checkbox" class="noscroll_textarea"  value="{{ $key }}" @if(in_array($key,$sidebar_ads_pages)) checked @endif> {{ $value }} @if($key == 'pages') (ex: about us, faq,...ect) @endif <br/></span>
                                                @endforeach
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Example Code') }} </label>
                                                <br> <img src="{{ url('/') }}/resources/views/admin/template/images/ins.png">
                                            </div> 
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Ads Code') }} </label>
                                                <textarea name="sidebar_ads" id="sidebar_ads" rows="6" class="form-control noscroll_textarea">{{ $additional['setting']->sidebar_ads }}</textarea>
                                            </div> 
                                            
                                           
                             
                             
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             <div class="col-md-12 mt-3"><h5>{{ __('Bottom Ads') }}</h5></div>
                             <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                           
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1"><strong>{{ __('Pages') }}</strong> </label><br/>
                                                @foreach($ads_pages as $key => $value)
                                                <input id="bottom_ads_pages" name="bottom_ads_pages[]" type="checkbox" class="noscroll_textarea" value="{{ $key }}" @if(in_array($key,$bottom_ads_pages)) checked @endif> {{ $value }} @if($key == 'pages') (ex: about us, faq,...ect) @endif<br/>
                                                @endforeach
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Example Code') }} </label>
                                                <br> <img src="{{ url('/') }}/resources/views/admin/template/images/ins.png">
                                            </div> 
                                            
                                            
                                            <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Ads Code') }}</label>
                                                <textarea name="bottom_ads" id="bottom_ads" rows="6" class="form-control noscroll_textarea">{{ $additional['setting']->bottom_ads }}</textarea>
                                            </div> 
                                            
                                           
                             
                             
                             </div>
                                </div>

                            </div>
                             
                             
                             
                             </div>
                             <input type="hidden" name="sid" value="1">
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
