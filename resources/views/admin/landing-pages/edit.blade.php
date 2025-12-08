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

                                            <!-- Gallery Images with Descriptions -->
                                            <div class="form-group">
                                                <label>{{ __('Gallery Images') }}</label>
                                                
                                                @if(count($gallery_images) > 0)
                                                <div class="gallery-preview mb-3">
                                                    <p><strong>{{ __('Current Gallery:') }}</strong></p>
                                                    @foreach($gallery_images as $image)
                                                    <div class="gallery-image-card mb-3 p-3" id="gallery-{{ $image->lpg_id }}" style="border: 1px solid #e0e0e0; border-radius: 5px; background: #fafafa;">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-3">
                                                                <img src="{{ asset('storage/landing-pages/' . $image->lpg_image) }}" alt="Gallery" style="width: 100%; height: 100px; object-fit: cover; border-radius: 5px;">
                                                            </div>
                                                            <div class="col-md-7">
                                                                <label>{{ __('Description') }}</label>
                                                                <textarea name="existing_gallery_descriptions[{{ $image->lpg_id }}]" class="form-control" rows="2" placeholder="Enter image description...">{{ $image->lpg_description ?? '' }}</textarea>
                                                            </div>
                                                            <div class="col-md-2 text-right">
                                                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteGalleryImage({{ $image->lpg_id }})">
                                                                    <i class="fa fa-trash"></i> {{ __('Delete') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @endif

                                                <hr>
                                                <p><strong>{{ __('Add New Images:') }}</strong></p>
                                                <div id="gallery-container">
                                                    <div class="gallery-item-input mb-3 p-3" style="border: 1px solid #e0e0e0; border-radius: 5px; background: #fafafa;">
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <label>{{ __('Image') }}</label>
                                                                <input type="file" name="gallery_images[]" class="form-control" accept="image/*">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label>{{ __('Description') }}</label>
                                                                <textarea name="gallery_descriptions[]" class="form-control" rows="2" placeholder="Enter image description..."></textarea>
                                                            </div>
                                                            <div class="col-md-1 d-flex align-items-end">
                                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeGalleryInput(this)">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-primary mt-2" onclick="addGalleryInput()">
                                                    <i class="fa fa-plus"></i> {{ __('Add Image') }}
                                                </button>
                                                <small class="form-text text-muted d-block mt-2">Each image can have its own description shown on the landing page</small>
                                            </div>

                                            <!-- Product Delivery Method -->
                                            <div class="form-group">
                                                <label>{{ __('Product Delivery Method') }} <span class="text-danger">*</span></label>
                                                <div class="mt-2">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="delivery_upload" name="lp_delivery_method" value="upload" {{ ($landing_page->lp_delivery_method ?? 'upload') == 'upload' ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="delivery_upload">
                                                            <i class="fa fa-upload"></i> {{ __('Upload File/Folder') }}
                                                        </label>
                                                    </div>
                                                    <div class="custom-control custom-radio mt-2">
                                                        <input type="radio" class="custom-control-input" id="delivery_link" name="lp_delivery_method" value="link" {{ ($landing_page->lp_delivery_method ?? 'upload') == 'link' ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="delivery_link">
                                                            <i class="fa fa-link"></i> {{ __('Provide Download Link') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Product File/Folder Upload -->
                                            <div class="form-group" id="upload_section" style="display: {{ ($landing_page->lp_delivery_method ?? 'upload') == 'upload' ? 'block' : 'none' }};">
                                                <label>{{ __('Product File/Folder') }}</label>
                                                @if($landing_page->lp_product_file && ($landing_page->lp_delivery_method ?? 'upload') == 'upload')
                                                <div class="alert alert-success">
                                                    <i class="fa fa-file"></i> {{ __('Current File:') }} <strong>{{ $landing_page->lp_product_file }}</strong>
                                                    <br><small>{{ __('Type:') }} {{ ucfirst($landing_page->lp_product_file_type ?? 'file') }}</small>
                                                </div>
                                                @endif
                                                <input type="file" name="lp_product_file" class="form-control" id="product_file" webkitdirectory directory multiple>
                                                <small class="form-text text-muted">{{ __('Upload new file/folder to replace current one (leave empty to keep current)') }}</small>
                                                <div class="custom-control custom-checkbox mt-2">
                                                    <input type="checkbox" class="custom-control-input" id="is_single_file" name="is_single_file">
                                                    <label class="custom-control-label" for="is_single_file">
                                                        {{ __('Single File Upload (uncheck for folder)') }}
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Product Link -->
                                            <div class="form-group" id="link_section" style="display: {{ ($landing_page->lp_delivery_method ?? 'upload') == 'link' ? 'block' : 'none' }};">
                                                <label>{{ __('Product Download Link') }} <span class="text-danger">*</span></label>
                                                @if($landing_page->lp_product_link && ($landing_page->lp_delivery_method ?? 'upload') == 'link')
                                                <div class="alert alert-success">
                                                    <i class="fa fa-link"></i> {{ __('Current Link:') }} <a href="{{ $landing_page->lp_product_link }}" target="_blank">{{ $landing_page->lp_product_link }}</a>
                                                </div>
                                                @endif
                                                <input type="url" name="lp_product_link" class="form-control" id="product_link" placeholder="https://example.com/file.zip" value="{{ old('lp_product_link', $landing_page->lp_product_link) }}">
                                                <small class="form-text text-muted">{{ __('Provide a direct download link (e.g., Google Drive, Dropbox, etc.)') }}</small>
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

        function addGalleryInput() {
            const container = document.getElementById('gallery-container');
            const newItem = document.createElement('div');
            newItem.className = 'gallery-item-input mb-3 p-3';
            newItem.style.border = '1px solid #e0e0e0';
            newItem.style.borderRadius = '5px';
            newItem.style.background = '#fafafa';
            newItem.innerHTML = `
                <div class="row">
                    <div class="col-md-5">
                        <label>{{ __('Image') }}</label>
                        <input type="file" name="gallery_images[]" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label>{{ __('Description') }}</label>
                        <textarea name="gallery_descriptions[]" class="form-control" rows="2" placeholder="Enter image description..."></textarea>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeGalleryInput(this)">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newItem);
        }

        function removeGalleryInput(button) {
            const container = document.getElementById('gallery-container');
            if (container.children.length > 1) {
                button.closest('.gallery-item-input').remove();
            } else {
                // Clear the inputs instead of removing
                const item = button.closest('.gallery-item-input');
                item.querySelector('input[type="file"]').value = '';
                item.querySelector('textarea').value = '';
            }
        }

        // Toggle between file and folder upload
        $(document).ready(function() {
            // Toggle between upload and link
            $('input[name="lp_delivery_method"]').change(function() {
                if ($(this).val() === 'upload') {
                    $('#upload_section').show();
                    $('#link_section').hide();
                    $('#product_link').prop('required', false);
                } else {
                    $('#upload_section').hide();
                    $('#link_section').show();
                    $('#product_link').prop('required', true);
                }
            });

            // Toggle between single file and folder
            $('#is_single_file').change(function() {
                const fileInput = $('#product_file');
                if ($(this).is(':checked')) {
                    // Single file mode
                    fileInput.removeAttr('webkitdirectory directory multiple');
                } else {
                    // Folder mode
                    fileInput.attr({
                        'webkitdirectory': '',
                        'directory': '',
                        'multiple': ''
                    });
                }
            });
        });
    </script>
</body>

</html>

