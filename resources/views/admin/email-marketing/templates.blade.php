<!doctype html>
<html class="no-js" lang="en">
<head>
    @include('admin.stylesheet')
</head>

<body>
    @include('admin.navigation')

    <div id="right-panel" class="right-panel">
        @include('admin.header')

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Email Marketing Templates') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="{{ url('/admin') }}">{{ __('Dashboard') }}</a></li>
                            <li><a href="#">{{ __('Email Marketing') }}</a></li>
                            <li class="active">{{ __('Templates') }}</li>
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
                                <strong class="card-title">
                                    {{ __('All Email Templates') }} 
                                    <span class="badge badge-primary">{{ count($templates) }}</span>
                                </strong>
                                <a href="{{ route('admin.email-marketing.create-template') }}" class="btn btn-success btn-sm float-right">
                                    <i class="fa fa-plus"></i> {{ __('Create New Template') }}
                                </a>
                            </div>
                            <div class="card-body">
                                @if(count($templates) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ __('ID') }}</th>
                                                <th>{{ __('Template Name') }}</th>
                                                <th>{{ __('Subject') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Created') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($templates as $template)
                                            <tr>
                                                <td>{{ $template->id }}</td>
                                                <td>
                                                    <i class="fa fa-envelope"></i> 
                                                    <strong>{{ $template->name }}</strong>
                                                </td>
                                                <td>{{ $template->subject }}</td>
                                                <td>
                                                    @if($template->status == 'active')
                                                    <span class="badge badge-success">Active</span>
                                                    @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ date('M d, Y', strtotime($template->created_at)) }}
                                                    <br>
                                                    <small class="text-muted">{{ date('h:i A', strtotime($template->created_at)) }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.email-marketing.edit-template', $template->id) }}" 
                                                       class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.email-marketing.delete-template', $template->id) }}" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this template?')"
                                                       title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> 
                                    {{ __('No email templates found.') }}
                                    <a href="{{ route('admin.email-marketing.create-template') }}" class="alert-link">
                                        {{ __('Create your first template') }}
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.javascript')
</body>
</html>

