<!doctype html>
<html class="no-js" lang="en">
<head>
    @include('admin.stylesheet')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css">
</head>

<body>
    @include('admin.navigation')

    <div id="right-panel" class="right-panel">
        @include('admin.header')

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Edit Email Template') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="{{ url('/admin') }}">{{ __('Dashboard') }}</a></li>
                            <li><a href="{{ route('admin.email-marketing.templates') }}">{{ __('Templates') }}</a></li>
                            <li class="active">{{ __('Edit') }}</li>
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
                                <strong class="card-title">{{ __('Edit Email Template') }}</strong>
                                <a href="{{ route('admin.email-marketing.templates') }}" class="btn btn-secondary btn-sm float-right">
                                    <i class="fa fa-arrow-left"></i> {{ __('Back to Templates') }}
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <strong>{{ __('Available Variables:') }}</strong><br>
                                    <code>{{'{{'}}name{{'}}'}}</code> - Customer name<br>
                                    <code>{{'{{'}}email{{'}}'}}</code> - Customer email<br>
                                    <code>{{'{{'}}site_name{{'}}'}}</code> - Your site name
                                </div>

                                <form action="{{ route('admin.email-marketing.update-template', $template->id) }}" method="POST">
                                    @csrf

                                    <div class="form-group">
                                        <label for="name">{{ __('Template Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $template->name) }}" required>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="subject">{{ __('Email Subject') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" 
                                               value="{{ old('subject', $template->subject) }}" required>
                                        @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="content">{{ __('Email Content') }} <span class="text-danger">*</span></label>
                                        <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" 
                                                  rows="10" required>{{ old('content', $template->content) }}</textarea>
                                        @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                            <option value="active" {{ old('status', $template->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $template->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="notes">{{ __('Notes (Optional)') }}</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $template->notes) }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> {{ __('Update Template') }}
                                        </button>
                                        <a href="{{ route('admin.email-marketing.templates') }}" class="btn btn-secondary">
                                            {{ __('Cancel') }}
                                        </a>
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
            $('#content').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
</body>
</html>

