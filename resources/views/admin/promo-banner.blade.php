<!doctype html>
<html class="no-js" lang="en">
<head>
    @include('admin.stylesheet')
</head>
<body>
@include('admin.navigation')
@if(in_array('settings',$avilable))
<div id="right-panel" class="right-panel">
    @include('admin.header')
    <div class="breadcrumbs">
        <div class="col-sm-4">
            <div class="page-header float-left">
                <div class="page-title">
                    <h1>{{ __('Black Friday Banner') }}</h1>
                </div>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="page-header float-right"></div>
        </div>
    </div>
    @include('admin.warning')
    <div class="content mt-3">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-md-12">
                    @if($tableMissing)
                    <div class="alert alert-warning">
                        {{ __('The banner database table does not exist yet. Run the database migration (php artisan migrate), then reload this page.') }}
                    </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">{{ __('Home Page Promotion Banner') }}</strong>
                        </div>
                        @if($demo_mode == 'on')
                        @include('admin.demo-mode')
                        @else
                        <form action="{{ route('admin.promo-banner') }}" method="post" id="setting_form">
                            {{ csrf_field() }}
                        @endif
                            <div class="col-md-6">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ __('Show on Home Page') }} <span class="require">*</span></label>
                                        <select name="enabled" class="form-control" required>
                                            <option value="1" @if(($banner->enabled ?? 0) == 1) selected @endif>{{ __('Show') }}</option>
                                            <option value="0" @if(($banner->enabled ?? 0) == 0) selected @endif>{{ __('Hide') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ __('Small Label') }}</label>
                                        <input type="text" name="badge" class="form-control" maxlength="60" value="{{ old('badge', $banner->badge ?? 'Black Friday') }}" placeholder="Black Friday">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ __('Heading') }} <span class="require">*</span></label>
                                        <input type="text" name="heading" class="form-control" maxlength="191" required value="{{ old('heading', $banner->heading ?? 'Black Friday Sale') }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ __('Discount Text') }}</label>
                                        <input type="text" name="discount_text" class="form-control" maxlength="120" value="{{ old('discount_text', $banner->discount_text ?? '') }}" placeholder="Up to 50% OFF">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ __('Description') }}</label>
                                        <textarea name="subheading" class="form-control" rows="3" maxlength="500">{{ old('subheading', $banner->subheading ?? '') }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ __('Coupon Code') }}</label>
                                        <input type="text" name="coupon_code" class="form-control" maxlength="60" value="{{ old('coupon_code', $banner->coupon_code ?? '') }}" placeholder="BLACKFRIDAY">
                                        <small class="form-text text-muted">{{ __('Optional. Create the same code under Coupons so it works at checkout. Leave empty to hide.') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ __('Button Text') }}</label>
                                        <input type="text" name="button_text" class="form-control" maxlength="60" value="{{ old('button_text', $banner->button_text ?? 'Shop the Sale') }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ __('Button Link') }}</label>
                                        <input type="text" name="button_url" class="form-control" maxlength="500" value="{{ old('button_url', $banner->button_url ?? '/shop') }}" placeholder="/shop">
                                        <small class="form-text text-muted">{{ __('A page on your site like /shop or /flash-sale, or a full https:// link.') }}</small>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ __('Sale Ends At') }}</label>
                                        <input type="datetime-local" name="ends_at" class="form-control" value="{{ old('ends_at', $endsLocal) }}">
                                        <small class="form-text text-muted">{{ __('The countdown counts down to this time and the banner hides itself afterwards. Leave empty for no countdown.') }}</small>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ __('Time Zone') }} <span class="require">*</span></label>
                                        <select name="timezone" class="form-control" required>
                                            @foreach($timezones as $tz)
                                            <option value="{{ $tz }}" @if(old('timezone', $timezone) == $tz) selected @endif>{{ $tz }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 no-padding">
                                <div class="card-footer">
                                    <button type="submit" name="submit" class="btn btn-primary btn-sm" @if($tableMissing) disabled @endif>
                                        <i class="fa fa-dot-circle-o"></i> {{ __('Submit') }}
                                    </button>
                                    <a href="{{ url('/') }}" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-eye"></i> {{ __('View Home Page') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
@include('admin.denied')
@endif
@include('admin.javascript')
</body>
</html>
