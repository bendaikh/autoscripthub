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

                                            <!-- What You'll Get After Payment -->
                                            <div class="form-group">
                                                <label>{{ __('What You\'ll Get After Payment') }}</label>
                                                <textarea name="lp_what_you_get" id="what_you_get" class="form-control summernote" rows="6" placeholder="Describe what clients will receive after completing payment (e.g., download link, access credentials, product files, etc.)">{{ old('lp_what_you_get') }}</textarea>
                                                <small class="form-text text-muted">{{ __('This content will be displayed on the landing page to inform customers about what they will receive after payment. You can use HTML formatting.') }}</small>
                                                @if ($errors->has('lp_what_you_get'))
                                                <span class="text-danger">{{ $errors->first('lp_what_you_get') }}</span>
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

                                            <!-- Gallery Images with Descriptions -->
                                            <div class="form-group">
                                                <label>{{ __('Gallery Images') }}</label>
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
                                                <small class="form-text text-muted">Each image can have its own description shown on the landing page</small>
                                            </div>

                                            <!-- Product Delivery Method -->
                                            <div class="form-group">
                                                <label>{{ __('Product Delivery Method') }} <span class="text-danger">*</span></label>
                                                <div class="mt-2">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="delivery_upload" name="lp_delivery_method" value="upload" checked>
                                                        <label class="custom-control-label" for="delivery_upload">
                                                            <i class="fa fa-upload"></i> {{ __('Upload File/Folder') }}
                                                        </label>
                                                    </div>
                                                    <div class="custom-control custom-radio mt-2">
                                                        <input type="radio" class="custom-control-input" id="delivery_link" name="lp_delivery_method" value="link">
                                                        <label class="custom-control-label" for="delivery_link">
                                                            <i class="fa fa-link"></i> {{ __('Provide Download Link') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Product File/Folder Upload -->
                                            <div class="form-group" id="upload_section">
                                                <label>{{ __('Product File/Folder') }} <span class="text-danger">*</span></label>
                                                <input type="file" name="lp_product_file" class="form-control" id="product_file" webkitdirectory directory multiple>
                                                <small class="form-text text-muted">{{ __('Upload the file or folder that will be delivered to customers after purchase') }}</small>
                                                <div class="custom-control custom-checkbox mt-2">
                                                    <input type="checkbox" class="custom-control-input" id="is_single_file" name="is_single_file">
                                                    <label class="custom-control-label" for="is_single_file">
                                                        {{ __('Single File Upload (uncheck for folder)') }}
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Product Link -->
                                            <div class="form-group" id="link_section" style="display: none;">
                                                <label>{{ __('Product Download Link') }} <span class="text-danger">*</span></label>
                                                <input type="url" name="lp_product_link" class="form-control" id="product_link" placeholder="https://example.com/file.zip">
                                                <small class="form-text text-muted">{{ __('Provide a direct download link (e.g., Google Drive, Dropbox, etc.)') }}</small>
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
                    $('#product_file').prop('required', true);
                    $('#product_link').prop('required', false);
                } else {
                    $('#upload_section').hide();
                    $('#link_section').show();
                    $('#product_file').prop('required', false);
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

