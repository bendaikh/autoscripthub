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
                    <h1>{{ __('AI API Connection') }}</h1>
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
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">{{ __('OpenAI Connection') }}</strong>
                        </div>
                        @if($demo_mode == 'on')
                        @include('admin.demo-mode')
                        @else
                        <form action="{{ route('admin.ai-api-connection') }}" method="post" id="setting_form">
                            {{ csrf_field() }}
                        @endif
                            <div class="col-md-6">
                                <div class="card-body">
                                    <div id="pay-invoice">
                                        <div class="card-body">
                                            <p class="text-muted mb-4">
                                                {{ __('Connect OpenAI to auto-generate landing page copy and hero images from a title and the About This Product description.') }}
                                            </p>

                                            <div class="form-group">
                                                <label class="control-label mb-1">{{ __('Enable OpenAI') }} <span class="require">*</span></label>
                                                <select name="openai_enabled" class="form-control" required>
                                                    <option value="1" @if(($ai['setting']->openai_enabled ?? 0) == 1) selected @endif>{{ __('Enable') }}</option>
                                                    <option value="0" @if(($ai['setting']->openai_enabled ?? 0) == 0) selected @endif>{{ __('Disable') }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label mb-1">{{ __('OpenAI API Key') }} <span class="require">*</span></label>
                                                <input type="password" name="openai_api_key" id="openai_api_key" class="form-control"
                                                       value="{{ $ai['setting']->openai_api_key ?? '' }}"
                                                       placeholder="sk-..." autocomplete="off">
                                                <small class="form-text text-muted">
                                                    {{ __('Get your key from') }}
                                                    <a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener">platform.openai.com/api-keys</a>
                                                </small>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label mb-1">{{ __('Text Model') }}</label>
                                                <select name="openai_model" class="form-control">
                                                    @php $model = $ai['setting']->openai_model ?? 'gpt-4o-mini'; @endphp
                                                    <option value="gpt-4o-mini" @if($model == 'gpt-4o-mini') selected @endif>gpt-4o-mini ({{ __('fast / cheapest') }})</option>
                                                    <option value="gpt-4.1-mini" @if($model == 'gpt-4.1-mini') selected @endif>gpt-4.1-mini</option>
                                                    <option value="gpt-4o" @if($model == 'gpt-4o') selected @endif>gpt-4o</option>
                                                    <option value="gpt-4.1" @if($model == 'gpt-4.1') selected @endif>gpt-4.1</option>
                                                    <option value="gpt-5-mini" @if($model == 'gpt-5-mini') selected @endif>gpt-5-mini</option>
                                                    <option value="gpt-5" @if($model == 'gpt-5') selected @endif>gpt-5 ({{ __('best copy') }})</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label mb-1">{{ __('Image Model') }}</label>
                                                <select name="openai_image_model" class="form-control">
                                                    @php $imgModel = $ai['setting']->openai_image_model ?? 'gpt-image-1'; @endphp
                                                    <option value="gpt-image-1-mini" @if($imgModel == 'gpt-image-1-mini') selected @endif>gpt-image-1-mini ({{ __('fastest') }})</option>
                                                    <option value="gpt-image-1" @if($imgModel == 'gpt-image-1') selected @endif>gpt-image-1</option>
                                                    <option value="gpt-image-1.5" @if($imgModel == 'gpt-image-1.5') selected @endif>gpt-image-1.5</option>
                                                    <option value="gpt-image-2" @if($imgModel == 'gpt-image-2') selected @endif>gpt-image-2 ({{ __('best quality') }})</option>
                                                </select>
                                                <small class="form-text text-muted">{{ __('Image generation adds roughly 30-60 seconds to each run.') }}</small>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label mb-1">{{ __('Auto-generate Images') }}</label>
                                                <select name="openai_generate_images" class="form-control">
                                                    <option value="1" @if(($ai['setting']->openai_generate_images ?? 1) == 1) selected @endif>{{ __('Yes') }}</option>
                                                    <option value="0" @if(($ai['setting']->openai_generate_images ?? 1) == 0) selected @endif>{{ __('No (copy only)') }}</option>
                                                </select>
                                                <small class="form-text text-muted">{{ __('When enabled, AI also creates a banner image for the landing page.') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-body">
                                    <div id="pay-invoice">
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                <strong>{{ __('How it works') }}</strong>
                                                <ol class="mb-0 mt-2 pl-3">
                                                    <li>{{ __('Save your OpenAI API key here and enable the connection.') }}</li>
                                                    <li>{{ __('Open Add / Edit Landing Page.') }}</li>
                                                    <li>{{ __('Enter a title and fill in About This Product with real product details, then click Generate with AI.') }}</li>
                                                    <li>{{ __('AI fills copy, features, SEO fields, and optionally the banner image.') }}</li>
                                                    <li>{{ __('Review, set price/delivery, and publish.') }}</li>
                                                </ol>
                                            </div>

                                            @if(!empty($ai['setting']->openai_api_key))
                                            <div class="form-group">
                                                <label class="control-label mb-1">{{ __('Test Connection') }}</label>
                                                <div>
                                                    <button type="button" id="btn-test-openai" class="btn btn-success btn-sm">
                                                        <i class="fa fa-plug"></i> {{ __('Test OpenAI Connection') }}
                                                    </button>
                                                    <span id="openai-test-result" class="ml-2"></span>
                                                </div>
                                            </div>
                                            @endif

                                            <div class="form-group mt-4">
                                                <label class="control-label mb-1">{{ __('Status') }}</label>
                                                @if(($ai['setting']->openai_enabled ?? 0) == 1 && !empty($ai['setting']->openai_api_key))
                                                    <div class="alert alert-success mb-0">
                                                        <i class="fa fa-check-circle"></i> {{ __('OpenAI is enabled and an API key is saved.') }}
                                                    </div>
                                                @elseif(!empty($ai['setting']->openai_api_key))
                                                    <div class="alert alert-warning mb-0">
                                                        <i class="fa fa-exclamation-triangle"></i> {{ __('API key is saved but OpenAI is currently disabled.') }}
                                                    </div>
                                                @else
                                                    <div class="alert alert-secondary mb-0">
                                                        <i class="fa fa-info-circle"></i> {{ __('No API key saved yet.') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 no-padding">
                                <div class="card-footer">
                                    <button type="submit" name="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-dot-circle-o"></i> {{ __('Save Connection') }}
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
        </div>
    </div>
</div>
@else
@include('admin.denied')
@endif
@include('admin.javascript')
<script>
(function () {
    var btn = document.getElementById('btn-test-openai');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var result = document.getElementById('openai-test-result');
        result.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {{ __("Testing...") }}';
        fetch('{{ url('/admin/ai-api-connection/test') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        }).then(function (r) { return r.json(); })
          .then(function (data) {
              if (data.success) {
                  result.innerHTML = '<span class="text-success">' + (data.message || 'OK') + '</span>';
              } else {
                  result.innerHTML = '<span class="text-danger">' + (data.error || 'Failed') + '</span>';
              }
          }).catch(function () {
              result.innerHTML = '<span class="text-danger">{{ __("Request failed") }}</span>';
          });
    });
})();
</script>
</body>
</html>
