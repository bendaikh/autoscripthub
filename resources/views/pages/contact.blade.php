<!DOCTYPE HTML>
<html lang="en">
<head>
@php
  $pm = \Fickrr\Support\PageMeta::forSlug('contact');
  if ($pm) { $seo_title = $pm['title']; $seo_desc = $pm['desc']; }
@endphp
<title>{{ $seo_title ?? ($allsettings->site_title.' - '.__('Contact')) }}</title>
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
              <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ __('Contact') }}</li>
            </ol>
          </nav>
        </div>
        <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
          <h1 class="h3 mb-0 text-white">{{ __('Contact') }}</h1>
        </div>
      </div>
      </div>
    </section>
   <section class="container pt-grid-gutter">
      <div class="row justify-content-center contact-info-row">
        <div class="col-xl-4 col-md-6 mb-grid-gutter">
          <a class="card h-100" href="https://t.me/autoscript_hub" target="_blank" rel="noopener" style="text-decoration:none">
            <div class="card-body text-center"><i class="dwg-support h3 mt-2 mb-4 text-primary"></i>
              <h3 class="h6 mb-3">{{ __('Telegram support') }}</h3>
              <p class="font-size-sm text-muted mb-0">@autoscript_hub · {{ __('fastest reply') }}</p>
            </div>
          </a>
        </div>
        @if($addition_settings->site_email_display == 1)
        <div class="col-xl-4 col-md-6 mb-grid-gutter">
          <div class="card h-100">
            <div class="card-body text-center"><i class="dwg-mail h3 mt-2 mb-4 text-primary"></i>
              <h3 class="h6 mb-3">{{ __('Email') }}</h3>
              <p class="font-size-sm text-muted">{{ $allsettings->office_email }}</p>
            </div>
          </div>
        </div>
        @endif
        @php
          $ashPhone = trim((string) ($allsettings->office_phone ?? ''));
          $ashPhoneOk = $addition_settings->site_phone_display == 1 && $ashPhone !== '' && !preg_match('/9876543210|000 000/', $ashPhone);
        @endphp
        @if($ashPhoneOk)
        <div class="col-xl-4 col-md-6 mb-grid-gutter">
          <div class="card h-100">
            <div class="card-body text-center"><i class="dwg-phone h3 mt-2 mb-4 text-primary"></i>
              <h3 class="h6 mb-2">{{ __('Phone Number') }}</h3>
              <p class="font-size-sm text-muted">{{ $ashPhone }}</p>
            </div>
          </div>
        </div>
        @endif
      </div>
    </section>
    <div class="container py-4 py-lg-5 border-top">
          <h2 class="h4 mb-4">{{ __('Write To Us') }}</h2>
          <form method="POST" action="{{ route('contact') }}" id="contact_form"  class="needs-validation mb-3" novalidate>
          @csrf
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="cf-name">{{ __('Name') }} <span class="text-danger">*</span></label>
                  <input class="form-control" type="text" id="from_name" name="from_name" placeholder="{{ __('Your name') }}" data-bvalidator="required">
                  <div class="invalid-feedback">{{ __('Please fill in your full name') }}</div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="cf-email">{{ __('Email') }} <span class="text-danger">*</span></label>
                  <input class="form-control" type="text" id="cf-email" name="from_email" placeholder="{{ __('you@example.com') }}" data-bvalidator="email,required">
                  <div class="invalid-feedback">{{ __('Please provide valid email address') }}</div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="cf-phone">{{ __('Phone Number') }} <span class="text-muted font-size-xs">({{ __('optional') }})</span></label>
                  <input class="form-control" type="text" id="cf-phone" name="from_phone" placeholder="{{ __('Phone number') }}">
                  <div class="invalid-feedback">{{ __('Please provide valid phone number') }}</div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label for="cf-subject">{{ __('Subject') }}</label>
                  <input class="form-control" type="text" id="cf-subject" name="subject" placeholder="{{ __('Provide short title of your request') }}">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="cf-message">{{ __('Message') }} <span class="text-danger">*</span></label>
              <textarea class="form-control" id="cf-message" rows="6" name="message_text" placeholder="{{ __('Please describe in detail your request') }}" data-bvalidator="required"></textarea>
              <div class="invalid-feedback">{{ __('Please write a message') }}</div>
            </div>
            @if($addition_settings->site_google_recaptcha == 1)
            <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                            {!! RecaptchaV3::field('register') !!}
                                @if ($errors->has('g-recaptcha-response'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                    </span>
                                @endif
                        </div>
            @endif
            <button class="btn btn-primary capital" type="submit">{{ __('send message') }}</button>
          </form>
    </div>
    
    <div class="container px-0">
      @if(in_array('contact',$top_ads))
      <div class="row">
          <div class="col-lg-12 mt-4" align="center">
             @php echo html_entity_decode($addition_settings->top_ads); @endphp
          </div>
       </div>   
       @endif
      
        @if(in_array('contact',$bottom_ads))
        <div class="row">
          <div class="col-lg-12 mt-2 mb-2" align="center">
             @php echo html_entity_decode($addition_settings->bottom_ads); @endphp
          </div>
       </div>   
       @endif
    </div>
@include('footer')
@include('script')
</body>
</html>
