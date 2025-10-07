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
                        <h1>{{ __('Edit Sub Administrator') }}</h1>
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
                           <form action="{{ route('admin.edit-administrator') }}" method="post" id="setting_form" enctype="multipart/form-data">
                           {{ csrf_field() }}
                           @endif
                           <div class="col-md-6">
                           <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                       <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Name') }} <span class="require">*</span></label>
                                                <input id="name" name="name" type="text" class="form-control" value="{{ $edit['userdata']->name }}" data-bvalidator="required">
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="control-label mb-1">{{ __('Username') }} <span class="require">*</span></label>
                                                <input id="username" name="username" type="text" class="form-control" value="{{ $edit['userdata']->username }}" data-bvalidator="required">
                                            </div>
                                            <div class="form-group">
                                                    <label for="email" class="control-label mb-1">{{ __('Email') }} <span class="require">*</span></label>
                                                    <input id="email" name="email" type="text" class="form-control" value="{{ $edit['userdata']->email }}" data-bvalidator="email,required">
                                              </div>
                                              <input type="hidden" name="user_type" value="admin">
                                               <div class="form-group">
                                                    <label for="password" class="control-label mb-1">{{ __('Password') }}</label>
                                                    <input id="password" name="password" type="password" class="form-control">
                                                </div>
                                                @if(Auth::user()->id == 1)
                                                 <div class="form-group">
                                                    <label for="earnings" class="control-label mb-1">{{ __('Earnings') }} ({{ $allsettings->site_currency_symbol }})</label>
                                                    <input id="earnings" name="earnings" type="text" class="form-control" value="{{ $edit['userdata']->earnings }}" data-bvalidator="min[0]">
                                                </div>
                                                @endif
                                                <div class="form-group">
                                                                    <label for="customer_earnings" class="control-label mb-1">{{ __('Upload Photo') }}</label>
                                                                    <input type="file" id="user_photo" name="user_photo" class="form-control-file" data-bvalidator="extension[jpg:png:jpeg]" data-bvalidator-msg="{{ __('Please select file of type .jpg, .png or .jpeg') }}">@if($edit['userdata']->user_photo != '')
                                                <img class="lazy userphoto" width="50" height="50" src="{{ url('/') }}/public/storage/users/{{ $edit['userdata']->user_photo }}"  />@else <img class="lazy userphoto" width="50" height="50" src="{{ url('/') }}/public/img/no-user.png"  />  @endif</div>
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
                                    <label for="customer_earnings" class="control-label mb-1"><strong>{{ __('Permission') }}</strong></label><br/><br/>
                                    @php $edituser = explode(',', $edit['userdata']->user_permission); @endphp
                                    @foreach($permission as $key => $value)
                                     <span><input type="checkbox" id="user_permission[]" name="user_permission[]" value="{{ $key }}" @if(in_array($key,$edituser)) checked @endif> {{ $value }}</span><br/>
                                    @endforeach
                                  </div>
                              </div>
                                </div>
                              </div>
                             </div>
                             <input type="hidden" name="save_photo" value="{{ $edit['userdata']->user_photo }}">
                             <input type="hidden" name="save_password" value="{{ $edit['userdata']->password }}">
                             <input type="hidden" name="user_token" value="{{ $edit['userdata']->user_token }}">
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