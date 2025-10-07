<!doctype html>
<html class="no-js" lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ __('Login') }}</title>
@include('meta')
@include('style')
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
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Login') }}</li>
            </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Login') }}</h1>
        </div>
      </div>
      </div>
    </section>
<div class="container py-4 py-lg-5 my-4">
      <div class="row">
        <div class="col-md-6 mx-auto">
          <div class="card border-0 box-shadow">
            <div class="card-body">
            @if($allsettings->display_social_login == 1)
              <h2 class="h4 mb-1">{{ __('Login') }}</h2>
              <div class="py-3">
                <h3 class="d-inline-block align-middle font-size-base font-weight-semibold mb-2 mr-2">{{ __('With social account') }}:</h3>
                <div class="d-inline-block align-middle">
                <a class="social-btn sb-google mr-2 mb-2" href="{{ url('/login/google') }}" data-toggle="tooltip" title="Sign in with Google"><i class="dwg-google"></i></a>
                <a class="social-btn sb-facebook mr-2 mb-2" href="{{ url('/login/facebook') }}" data-toggle="tooltip" title="Sign in with Facebook"><i class="dwg-facebook"></i></a>
                </div>
              </div>
              <hr>
              <h3 class="font-size-base pt-4 pb-2">{{ __('Or using login form below') }}</h3>
              @endif
              <form action="{{ route('login') }}" method="POST" id="login_form" class="@if($allsettings->display_social_login == 0) py-3 @endif">
                @csrf
                <div class="input-group-overlay form-group">
                  <div class="input-group-prepend-overlay"><span class="input-group-text"><i class="dwg-mail"></i></span></div>
                  <input class="form-control prepended-form-control" type="text" name="email" placeholder="{{ __('E-Mail Address / Username') }}" data-bvalidator="required">
                </div>
                <div class="input-group-overlay form-group">
                  <div class="input-group-prepend-overlay"><span class="input-group-text"><i class="dwg-locked"></i></span></div>
                  <div class="password-toggle">
                    <input class="form-control prepended-form-control" type="password" name="password" placeholder="{{ __('Password') }}" data-bvalidator="required">
                    <label class="password-toggle-btn">
                      <input class="custom-control-input" type="checkbox"><i class="dwg-eye password-toggle-indicator"></i><span class="sr-only">{{ __('Show password') }}</span>
                    </label>
                  </div>
                </div>
                <div class="d-flex flex-wrap justify-content-between">
                  <div>
                  <span class="text15">{{ __("Don't have an account?") }}</span> <a href="{{ URL::to('/register') }}" class="nav-link-inline font-size-sm">{{ __('Sign Up Now') }}</a>
                  </div><a class="nav-link-inline font-size-sm" href="{{ URL::to('/forgot') }}">{{ __('Forgot Your Password') }}?</a>
                </div>
                <hr class="mt-4">
                <div class="text-right pt-4">
                  <button class="btn btn-primary" type="submit"><i class="dwg-sign-in mr-2 ml-n21"></i>{{ __('Login') }}</button>
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