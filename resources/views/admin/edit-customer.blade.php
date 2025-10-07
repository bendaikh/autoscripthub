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
    @if(Auth::user()->id == 1)
    <div id="right-panel" class="right-panel">

        
                       @include('admin.header')
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Edit Customer') }}</h1>
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
                       <form action="{{ route('admin.edit-customer') }}" method="post" id="setting_form" enctype="multipart/form-data">
                        
                        {{ csrf_field() }}

                        <div class="card">
                           
                           
                           
                           <div class="col-md-6">
                           
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       
                                        
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Name') }} <span class="require">*</span></label>
                                                <input id="name" name="name" type="text" class="form-control" value="{{ $edit['userdata']->name }}" required>
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Username') }} <span class="require">*</span></label>
                                                <input id="username" name="username" type="text" class="form-control" value="{{ $edit['userdata']->username }}" required>
                                            </div>
                                            
                                            
                                                <div class="form-group">
                                                    <label for="email" class="control-label mb-1">{{ __('Email') }} <span class="require">*</span></label>
                                                    <input id="email" name="email" type="email" class="form-control" value="{{ $edit['userdata']->email }}" required>
                                                   
                                                </div>
                                                
                                                <input type="hidden" name="user_type" value="customer">
                                                
                                                <div class="form-group">
                                                    <label for="password" class="control-label mb-1">{{ __('Password') }}</label>
                                                    <input id="password" name="password" type="password" class="form-control">
                                                    
                                                </div>
                                                
                                                 <div class="form-group">
                                                    <label for="earnings" class="control-label mb-1">{{ __('Earnings') }} ({{ $allsettings->site_currency }})</label>
                                                    <input id="earnings" name="earnings" type="text" class="form-control" value="{{ $edit['userdata']->earnings }}">
                                                    
                                                </div>
                                                
                                                
                                                <div class="form-group">
                                                    <label for="earnings" class="control-label mb-1">{{ __('Download') }} {{ __('Limited No of Items') }} ({{ __('Per Day') }}) <span class="require">*</span></label>
                                                    <input id="user_subscr_download_item" name="user_subscr_download_item" type="text" class="form-control" value="{{ $edit['userdata']->user_subscr_download_item }}"required>
                                                    
                                                </div>
                                                
                                                <div class="form-group">
                                                                    <label for="customer_earnings" class="control-label mb-1">{{ __('Upload Photo') }}</label>
                                                                    <input type="file" id="user_photo" name="user_photo" class="form-control-file">
                                                                </div>
                                                @if($edit['userdata']->user_photo != '')
                                                <img class="lazy userphoto" width="50" height="50" src="{{ url('/') }}/public/storage/users/{{ $edit['userdata']->user_photo }}"  />@else <img class="lazy userphoto" width="50" height="50" src="{{ url('/') }}/public/img/no-user.png"  />  @endif
                                                
                                                
                                                <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">Email Verification? <span class="require">*</span></label>
                                                <select name="verified" class="form-control" required>
                                                <option value=""></option>
                                                <option value="1" @if($edit['userdata']->verified == 1) selected @endif>{{ __('verified') }}</option>
                                                <option value="0" @if($edit['userdata']->verified == 0) selected @endif>{{ __('unverified') }}</option>
                                                </select>
                                                </div>
                                                
                                                <?php /*?><input type="hidden" name="verified" value="1"><?php */?>
                                                
                                                <input type="hidden" name="save_photo" value="{{ $edit['userdata']->user_photo }}">
                                                
                                                <input type="hidden" name="save_password" value="{{ $edit['userdata']->password }}">
                                                
                                                <input type="hidden" name="save_auth_token" value="{{ $edit['userdata']->user_auth_token }}">
                                                
                                                <input type="hidden" name="edit_id" value="{{ $token }}">
                                                
                                                <input type="hidden" name="page_redirect" value="customer">
                                        
                                    </div>
                                </div>

                            </div>
                            </div>
                            
                            
                            
                             <div class="col-md-6">
                             
                             
                             
                             
                             </div>
                            
                            
                            <div class="card-footer">
                                                        <button type="submit" name="submit" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-dot-circle-o"></i> {{ __('Submit') }}
                                                        </button>
                                                        <button type="reset" class="btn btn-danger btn-sm">
                                                            <i class="fa fa-ban"></i> {{ __('Reset') }}
                                                        </button>
                                                        <a href="{{ url('/customer') }}/{{ $encrypter->encrypt($edit['userdata']->user_token) }}" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-user"></i> Login as Customer </a>
                                                    </div>
                                                    
                                                    
                                                 
                            
                        </div> 

                    
                    </form> 
                    
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
