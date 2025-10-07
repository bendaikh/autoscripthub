<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Register') }}</title>
@include('meta')
@include('style')
@if($addition_settings->site_google_recaptcha == 1)
{!! RecaptchaV3::initJs() !!}
@endif
</head>
<body>
@include('header')
<section class="bg-position-center-top" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
      <div class="py-4">
        <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
        <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
              <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Register') }}</li>
            </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Register') }}</h1>
        </div>
      </div>
      </div>
    </section>
<div class="container py-4 py-lg-5 my-4">
      <div class="row">
        <div class="col-md-6 mx-auto">
          <div class="card border-0 box-shadow">
            <div class="card-body">
              <h2 class="h4 mb-3">{{ __('Create Your Account') }}</h2>
              <p class="font-size-sm text-muted mb-4">{{ __('Please fill the following fields with appropriate information to register a new Marketplace account.') }}</p>
              <form method="POST" action="{{ route('register') }}" id="login_form" class="needs-validation" novalidate>
                @csrf
                <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-fn">{{ __('Your Name') }} <span class="required">*</span></label>
                  <input id="name" type="text" class="form-control" name="name" placeholder="{{ __('Enter your name') }}" value="{{ old('name') }}" data-bvalidator="required" autocomplete="name" autofocus>    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                       @enderror
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-ln">{{ __('Username') }} <span class="required">*</span></label>
                  <input id="username" type="text" name="username" class="form-control" placeholder="{{ __('Enter your username') }}" data-bvalidator="required">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-email">{{ __('E-Mail Address') }} <span class="required">*</span></label>
                  <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{ __('Enter your email address') }}"  autocomplete="email" data-bvalidator="email,required">
                         @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-pass">{{ __('Password') }} <span class="required">*</span></label>
                  <div class="password-toggle">
                    <input id="password" type="password" class="form-control" name="password" placeholder="{{ __('Enter your password') }}" autocomplete="new-password" data-bvalidator="required,minlen[6]">@error('password')
                        <span class="invalid-feedback" role="alert">
                             <strong>{{ $message }}</strong>
                       </span>
                     @enderror
                    <label class="password-toggle-btn">
                      <input class="custom-control-input" type="checkbox"><i class="dwg-eye password-toggle-indicator"></i><span class="sr-only">{{ __('Show password') }}</span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
               <div class="form-group">
                <label for="email_ad">{{ __('User Type') }} <span class="required">*</span></label>
                 <select id="user_type" class="form-control" name="user_type" data-bvalidator="required">
                 <option value=""></option>
                 <option value="{{ $encrypter->encrypt('customer') }}">{{ __('Customer') }}</option>
                 <option value="{{ $encrypter->encrypt('vendor') }}">{{ __('Vendor') }}</option>
                </select>
                </div>
             </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="account-confirm-pass">{{ __('Confirm Password') }} <span class="required">*</span></label>
                  <div class="password-toggle">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Enter your confirm password') }}" data-bvalidator="equal[password],required,minlen[6]" autocomplete="new-password">
                    <label class="password-toggle-btn">
                      <input class="custom-control-input" type="checkbox"><i class="dwg-eye password-toggle-indicator"></i><span class="sr-only">{{ __('Show password') }}</span>
                    </label>
                  </div>
                </div>
              </div>
              @if($addition_settings->site_google_recaptcha == 1)
              <div class="col-sm-12">
              <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                            <div class="col-sm-12">
                                {!! RecaptchaV3::field('register') !!}
                                @if ($errors->has('g-recaptcha-response'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
              </div>
              @endif
              <div class="col-sm-12">
                <div class="form-group">
                  <input  type="checkbox" name="register_rules" id="ch2" value="1" data-bvalidator="required">
                  <span class="become_vendor">{{ __('I agree to the') }} <a href="{{ URL::to('/terms-and-conditions') }}">{{ __('terms & conditions') }}</a>, <a href="{{ URL::to('/privacy-policy') }}">{{ __('privacy policy') }}</a> {{ __('and') }} <a href="{{ URL::to('/subscription') }}">{{ __('membership terms') }}</a></span>
                </div>
              </div>
              <div class="col-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                  <div class="custom-checkbox d-block">
                    <a href="{{ URL::to('/login') }}" class="nav-link-inline font-size-sm">{{ __('Already have an account?') }} {{ __('Login') }}</a>
                  </div>
                  <button class="btn btn-primary mt-3 mt-sm-0" type="submit">{{ __('Register') }}</button>
                </div> 
              </div>
            </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
@include('footer')
@include('script')
</body>
</html>