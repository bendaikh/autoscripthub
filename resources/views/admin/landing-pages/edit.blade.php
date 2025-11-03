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
        .gallery-image {
            position: relative;
            display: inline-block;
            margin: 10px;
        }
        .gallery-image img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        .gallery-image .btn-delete {
            position: absolute;
            top: 5px;
            right: 5px;
        }
        .current-banner img {
            max-width: 400px;
            height: auto;
            border-radius: 5px;
            margin-top: 10px;
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
                        <h1>{{ __('Edit Landing Page') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <a href="{{ url('/landing/' . $landing_page->lp_slug) }}" target="_blank" class="btn btn-info btn-sm mr-2">
                                <i class="fa fa-external-link"></i> {{ __('View Page') }}
                            </a>
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
                                <strong class="card-title">{{ __('Edit Landing Page') }}: {{ $landing_page->lp_title }}</strong>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.update-landing-page') }}" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="lp_id" value="{{ $landing_page->lp_id }}">

                                    <div class="row">
                                        <div class="col-md-8">
                                            <!-- Basic Information -->
                                            <div class="form-group">
                                                <label>{{ __('Page Title') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="lp_title" class="form-control" value="{{ old('lp_title', $landing_page->lp_title) }}" required>
                                                @if ($errors->has('lp_title'))
                                                <span class="text-danger">{{ $errors->first('lp_title') }}</span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Description') }} <span class="text-danger">*</span></label>
                                                <textarea name="lp_description" id="description" class="form-control summernote" rows="8" required>{{ old('lp_description', $landing_page->lp_description) }}</textarea>
                                                @if ($errors->has('lp_description'))
                                                <span class="text-danger">{{ $errors->first('lp_description') }}</span>
                                                @endif
                                            </div>

                                            <!-- Features Section -->
                                            <div class="form-group">
                                                <label>{{ __('Key Features') }}</label>
                                                <div id="features-container">
                                                    @php
                                                        $features = json_decode($landing_page->lp_features, true) ?? [''];
                                                        if (empty($features)) $features = [''];
                                                    @endphp
                                                    @foreach($features as $feature)
                                                    <div class="feature-item">
                                                        <div class="input-group">
                                                            <input type="text" name="features[]" class="form-control" value="{{ $feature }}" placeholder="Enter a feature">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-danger btn-remove-feature" onclick="removeFeature(this)">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addFeature()">
                                                    <i class="fa fa-plus"></i> {{ __('Add Feature') }}
                                                </button>
                                            </div>

                                            <!-- Banner Image -->
                                            <div class="form-group">
                                                <label>{{ __('Banner Image') }} (1920x800 recommended)</label>
                                                @if($landing_page->lp_banner_image)
                                                <div class="current-banner">
                                                    <p><strong>{{ __('Current Banner:') }}</strong></p>
                                                    <img src="{{ asset('storage/landing-pages/' . $landing_page->lp_banner_image) }}" alt="Banner">
                                                </div>
                                                @endif
                                                <input type="file" name="lp_banner_image" class="form-control mt-2" accept="image/*">
                                                @if ($errors->has('lp_banner_image'))
                                                <span class="text-danger">{{ $errors->first('lp_banner_image') }}</span>
                                                @endif
                                                <small class="form-text text-muted">{{ __('Leave empty to keep current image') }}</small>
                                            </div>

                                            <!-- Gallery Images -->
                                            <div class="form-group">
                                                <label>{{ __('Gallery Images') }}</label>
                                                
                                                @if(count($gallery_images) > 0)
                                                <div class="gallery-preview">
                                                    <p><strong>{{ __('Current Gallery:') }}</strong></p>
                                                    @foreach($gallery_images as $image)
                                                    <div class="gallery-image" id="gallery-{{ $image->lpg_id }}">
                                                        <img src="{{ asset('storage/landing-pages/' . $image->lpg_image) }}" alt="Gallery">
                                                        <button type="button" class="btn btn-danger btn-sm btn-delete" onclick="deleteGalleryImage({{ $image->lpg_id }})">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @endif

                                                <input type="file" name="gallery_images[]" class="form-control mt-2" accept="image/*" multiple>
                                                <small class="form-text text-muted">{{ __('Add new images to the gallery') }}</small>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <!-- Pricing -->
                                            <div class="form-group">
                                                <label>{{ __('Regular Price') }} <span class="text-danger">*</span></label>
                                                <input type="number" name="lp_price" class="form-control" value="{{ old('lp_price', $landing_page->lp_price) }}" step="0.01" min="0" required>
                                                @if ($errors->has('lp_price'))
                                                <span class="text-danger">{{ $errors->first('lp_price') }}</span>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Extended Price') }} (Optional)</label>
                                                <input type="number" name="lp_extended_price" class="form-control" value="{{ old('lp_extended_price', $landing_page->lp_extended_price) }}" step="0.01" min="0">
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Currency') }}</label>
                                                <select name="lp_currency" class="form-control">
                                                    <option value="USD" {{ $landing_page->lp_currency == 'USD' ? 'selected' : '' }}>USD</option>
                                                    <option value="EUR" {{ $landing_page->lp_currency == 'EUR' ? 'selected' : '' }}>EUR</option>
                                                    <option value="GBP" {{ $landing_page->lp_currency == 'GBP' ? 'selected' : '' }}>GBP</option>
                                                    <option value="INR" {{ $landing_page->lp_currency == 'INR' ? 'selected' : '' }}>INR</option>
                                                    <option value="AUD" {{ $landing_page->lp_currency == 'AUD' ? 'selected' : '' }}>AUD</option>
                                                    <option value="CAD" {{ $landing_page->lp_currency == 'CAD' ? 'selected' : '' }}>CAD</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Status') }}</label>
                                                <select name="lp_status" class="form-control">
                                                    <option value="1" {{ $landing_page->lp_status == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                    <option value="0" {{ $landing_page->lp_status == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                </select>
                                            </div>

                                            <!-- Landing Page URL -->
                                            <div class="alert alert-info">
                                                <strong>{{ __('Landing Page URL:') }}</strong><br>
                                                <a href="{{ url('/landing/' . $landing_page->lp_slug) }}" target="_blank">
                                                    {{ url('/landing/' . $landing_page->lp_slug) }}
                                                </a>
                                            </div>

                                            <!-- SEO -->
                                            <hr>
                                            <h5>{{ __('SEO Settings') }}</h5>

                                            <div class="form-group">
                                                <label>{{ __('Meta Title') }}</label>
                                                <input type="text" name="lp_meta_title" class="form-control" value="{{ old('lp_meta_title', $landing_page->lp_meta_title) }}">
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Meta Description') }}</label>
                                                <textarea name="lp_meta_description" class="form-control" rows="3">{{ old('lp_meta_description', $landing_page->lp_meta_description) }}</textarea>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ __('Meta Keywords') }}</label>
                                                <input type="text" name="lp_meta_keywords" class="form-control" value="{{ old('lp_meta_keywords', $landing_page->lp_meta_keywords) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-save"></i> {{ __('Update Landing Page') }}
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

        function deleteGalleryImage(imageId) {
            if (!confirm('{{ __("Are you sure you want to delete this image?") }}')) {
                return;
            }

            fetch(`/admin/landing-page-gallery/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`gallery-${imageId}`).remove();
                    alert('{{ __("Image deleted successfully") }}');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __("Failed to delete image") }}');
            });
        }
    </script>
</body>

</html>

