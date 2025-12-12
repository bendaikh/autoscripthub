<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    @include('admin.stylesheet')
</head>

<body>
    @include('admin.navigation')

    <!-- Right Panel -->
    <div id="right-panel" class="right-panel">
        @include('admin.header')

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Landing Pages') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <a href="{{ route('admin.landing-page-messages') }}" class="btn btn-info btn-sm mr-2">
                                <i class="fa fa-comments"></i> {{ __('View Messages') }}
                                @php
                                    $unread_count = \Fickrr\Models\LandingPageMessage::getUnreadCount();
                                @endphp
                                @if($unread_count > 0)
                                <span class="badge badge-danger">{{ $unread_count }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.add-landing-page') }}" class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i> {{ __('Add Landing Page') }}
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
                                <strong class="card-title">{{ __('All Landing Pages') }}</strong>
                            </div>
                            <div class="card-body">
                                <table id="example" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('Title') }}</th>
                                            <th>{{ __('Slug') }}</th>
                                            <th>{{ __('Price') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('URL') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($landing_pages as $page)
                                        <tr>
                                            <td>{{ $page->lp_id }}</td>
                                            <td>{{ mb_substr($page->lp_title, 0, 40, 'UTF-8') }}{{ strlen($page->lp_title) > 40 ? '...' : '' }}</td>
                                            <td>{{ $page->lp_slug }}</td>
                                            <td>{{ $page->lp_currency }} {{ number_format($page->lp_price, 2) }}</td>
                                            <td>
                                                @if($page->lp_status == 1)
                                                <span class="badge badge-success">{{ __('Active') }}</span>
                                                @else
                                                <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ url('/landing/' . $page->lp_slug) }}" target="_blank" class="btn btn-info btn-sm">
                                                    <i class="fa fa-external-link"></i> {{ __('View') }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.edit-landing-page', $page->lp_id) }}" class="btn btn-success btn-sm">
                                                    <i class="fa fa-edit"></i> {{ __('Edit') }}
                                                </a>
                                                <a href="{{ route('admin.duplicate-landing-page', $page->lp_id) }}" 
                                                   class="btn btn-info btn-sm" 
                                                   title="{{ __('Duplicate Landing Page') }}">
                                                    <i class="fa fa-copy"></i> {{ __('Duplicate') }}
                                                </a>
                                                <a href="{{ url('/admin/landing-pages/' . $page->lp_id) }}" 
                                                   class="btn btn-danger btn-sm" 
                                                   onClick="return confirm('{{ __('Are you sure you want to delete this landing page') }}?');">
                                                    <i class="fa fa-trash"></i> {{ __('Delete') }}
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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

