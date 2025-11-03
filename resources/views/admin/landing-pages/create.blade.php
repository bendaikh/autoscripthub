<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    @include('admin.stylesheet')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css">
    <style>
        .feature-item {
            margin-bottom: 10px;
        }
        .btn-remove-feature {
            margin-left: 5px;
        }
    </style>
</head>

<body>
    @include('admin.navigation')

    <div id="right-panel" class="right-panel">
        @include('admin.header')

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Add Landing Page') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <a href="{{ route('admin.landing-pages') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> {{ __('Back to List') }}
                            </a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.warning')

        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">{{ __('Create New Landing Page') }}</strong>
                            </div>
                            <div class="card-body">
                                <form action="{{ url('/admin/add-landing-page') }}" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}

                                    <div class="row">
                                        <div class="col-md-8">
                                            <!-- Basic Information -->
                                            <div class="form-group">
                                                <label>{{ __('Page Title') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="lp_title" class="form-control" value="{{ old('lp_title') }}" required>
                                                @if ($errors->has('lp_title'))
                                                <span class="text-danger">{{ $errors->first('lp_title') }}</span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Description') }} <span class="text-danger">*</span></label>
                                                <textarea name="lp_description" id="description" class="form-control summernote" rows="8" required>{{ old('lp_description') }}</textarea>
                                                @if ($errors->has('lp_description'))
                                                <span class="text-danger">{{ $errors->first('lp_description') }}</span>
                                                @endif
                                            </div>

                                            <!-- Features Section -->
                                            <div class="form-group">
                                                <label>{{ __('Key Features') }}</label>
                                                <div id="features-container">
                                                    <div class="feature-item">
                                                        <div class="input-group">
                                                            <input type="text" name="features[]" class="form-control" placeholder="Enter a feature">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-danger btn-remove-feature" onclick="removeFeature(this)">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addFeature()">
                                                    <i class="fa fa-plus"></i> {{ __('Add Feature') }}
                                                </button>
                                            </div>

                                            <!-- Banner Image -->
                                            <div class="form-group">
                                                <label>{{ __('Banner Image') }} (1920x800 recommended)</label>
                                                <input type="file" name="lp_banner_image" class="form-control" accept="image/*">
                                                @if ($errors->has('lp_banner_image'))
                                                <span class="text-danger">{{ $errors->first('lp_banner_image') }}</span>
                                                @endif
                                            </div>

                                            <!-- Gallery Images -->
                                            <div class="form-group">
                                                <label>{{ __('Gallery Images') }} (Multiple)</label>
                                                <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                                                <small class="form-text text-muted">You can select multiple images for the gallery</small>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <!-- Pricing -->
                                            <div class="form-group">
                                                <label>{{ __('Regular Price') }} <span class="text-danger">*</span></label>
                                                <input type="number" name="lp_price" class="form-control" value="{{ old('lp_price', 0) }}" step="0.01" min="0" required>
                                                @if ($errors->has('lp_price'))
                                                <span class="text-danger">{{ $errors->first('lp_price') }}</span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Extended Price') }} (Optional)</label>
                                                <input type="number" name="lp_extended_price" class="form-control" value="{{ old('lp_extended_price') }}" step="0.01" min="0">
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Currency') }}</label>
                                                <select name="lp_currency" class="form-control">
                                                    <option value="USD">USD</option>
                                                    <option value="EUR">EUR</option>
                                                    <option value="GBP">GBP</option>
                                                    <option value="INR">INR</option>
                                                    <option value="AUD">AUD</option>
                                                    <option value="CAD">CAD</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Status') }}</label>
                                                <select name="lp_status" class="form-control">
                                                    <option value="1">{{ __('Active') }}</option>
                                                    <option value="0">{{ __('Inactive') }}</option>
                                                </select>
                                            </div>

                                            <!-- SEO -->
                                            <hr>
                                            <h5>{{ __('SEO Settings') }}</h5>

                                            <div class="form-group">
                                                <label>{{ __('Meta Title') }}</label>
                                                <input type="text" name="lp_meta_title" class="form-control" value="{{ old('lp_meta_title') }}">
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Meta Description') }}</label>
                                                <textarea name="lp_meta_description" class="form-control" rows="3">{{ old('lp_meta_description') }}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Meta Keywords') }}</label>
                                                <input type="text" name="lp_meta_keywords" class="form-control" value="{{ old('lp_meta_keywords') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-save"></i> {{ __('Create Landing Page') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.javascript')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 300,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });
        });

        function addFeature() {
            const container = document.getElementById('features-container');
            const newFeature = document.createElement('div');
            newFeature.className = 'feature-item';
            newFeature.innerHTML = `
                <div class="input-group">
                    <input type="text" name="features[]" class="form-control" placeholder="Enter a feature">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger btn-remove-feature" onclick="removeFeature(this)">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newFeature);
        }

        function removeFeature(button) {
            const container = document.getElementById('features-container');
            if (container.children.length > 1) {
                button.closest('.feature-item').remove();
            } else {
                alert('{{ __("At least one feature field is required") }}');
            }
        }
    </script>
</body>

</html>

