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
                        <h1>{{ __('Email Settings') }}</h1>
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
                           <form action="{{ route('admin.email-settings') }}" method="post" id="checkout_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                           <div class="col-md-6">
                             <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       <div class="form-group">
                                          <label for="site_title" class="control-label mb-1">{{ __('Sender Name') }} <span class="require">*</span></label>
                                           <input id="sender_name" name="sender_name" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->sender_name }}" data-bvalidator="required">
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Sender Email') }} <span class="require">*</span></label>
                                                <input id="sender_email" name="sender_email" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->sender_email }}" data-bvalidator="required,email"></div><input type="hidden" name="sid" value="1">
                             </div>
                                </div>
                              </div>
                             </div>
                             <div class="col-md-12"><div class="card-body"><h4>{{ __('Mail Configuration') }}</h4></div></div>
                             <div class="col-md-6">
                               <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Mail Driver') }} <span class="require">*</span></label>
                                                <input id="mail_driver" name="mail_driver" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->mail_driver }}" data-bvalidator="required"> <small>{{ __('example') }} : mail or smtp</small></div>
                                    <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Mail Port') }} <span class="require">*</span></label>
                                                <input id="mail_port" name="mail_port" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->mail_port }}" data-bvalidator="required"> <small>{{ __('example') }} : 25</small></div><div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Mail Password') }}</label>
                                                <input id="mail_password" name="mail_password" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->mail_password }}"> <small>{{ __('example') }} : test123</small>
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
                                                <label for="site_title" class="control-label mb-1">{{ __('Mail Host') }} <span class="require">*</span></label>
                                                <input id="mail_host" name="mail_host" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->mail_host }}" data-bvalidator="required"> <small>{{ __('example') }} : mail.mailtrap.io</small></div>
                                   <div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Mail Username') }} </label>
                                                <input id="mail_username" name="mail_username" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->mail_username }}"> <small>{{ __('example') }} : sample@sample.com</small></div><div class="form-group">
                                                <label for="site_title" class="control-label mb-1">{{ __('Mail Encryption') }} </label>
                                                <input id="mail_encryption" name="mail_encryption" type="text" class="form-control noscroll_textarea" value="{{ $setting['setting']->mail_encryption }}"> <small>{{ __('example') }} : tls or ssl</small></div>                  
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
    </div><!-- /#right-panel -->
    @else
    @include('admin.denied')
    @endif
    <!-- Right Panel -->
   @include('admin.javascript')
</body>
</html>