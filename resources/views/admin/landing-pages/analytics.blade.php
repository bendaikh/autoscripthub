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

    <div id="right-panel" class="right-panel">
        @include('admin.header')

        <div class="breadcrumbs">
            <div class="col-sm-6">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Landing Page Analytics') }}</h1>
                        <p class="mb-0" style="color:#6c757d;">
                            {{ $landing_page->lp_title }} <code>/landing/{{ $landing_page->lp_slug }}</code>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <a href="{{ route('admin.landing-pages') }}" class="btn btn-secondary btn-sm mr-2">
                                <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                            </a>
                            <a href="{{ url('/landing/' . $landing_page->lp_slug) }}" target="_blank" class="btn btn-info btn-sm">
                                <i class="fa fa-external-link"></i> {{ __('View Landing Page') }}
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
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <strong class="card-title">{{ __('Summary (last') }} {{ $days }} {{ __('days)') }}</strong>
                                <form method="GET" action="{{ route('admin.landing-pages.analytics', $landing_page->lp_id) }}" class="form-inline">
                                    <label class="mr-2">{{ __('Days') }}</label>
                                    <select name="days" class="form-control form-control-sm" onchange="this.form.submit()">
                                        @foreach([7,14,30,60,90,180,365] as $d)
                                            <option value="{{ $d }}" {{ (int)$days === (int)$d ? 'selected' : '' }}>{{ $d }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="alert alert-info">
                                            <strong>{{ __('Visits') }}:</strong> {{ number_format($total_visits) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-success">
                                            <strong>{{ __('Unique Visitors') }}:</strong> {{ number_format($unique_visitors) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-warning">
                                            <strong>{{ __('Pageviews') }}:</strong> {{ number_format($pageviews) }}
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h4>{{ __('Countries') }}</h4>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Country') }}</th>
                                                        <th>{{ __('Visits') }}</th>
                                                        <th>{{ __('Unique') }}</th>
                                                        <th>{{ __('Pageviews') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($countries as $c)
                                                        <tr>
                                                            <td>{{ $c->lpv_country_code ?: __('Unknown') }}</td>
                                                            <td>{{ number_format($c->visits) }}</td>
                                                            <td>{{ number_format($c->unique_visitors) }}</td>
                                                            <td>{{ number_format($c->pageviews) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center">{{ __('No data yet') }}</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h4>{{ __('Top Actions (Events)') }}</h4>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Event') }}</th>
                                                        <th>{{ __('Count') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($events as $e)
                                                        <tr>
                                                            <td><code>{{ $e->lpe_name }}</code></td>
                                                            <td>{{ number_format($e->total) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="2" class="text-center">{{ __('No data yet') }}</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-3" style="color:#6c757d;">
                                            <strong>{{ __('Tracked events by default:') }}</strong>
                                            <div><code>page_view</code>, <code>scroll_depth</code>, <code>buy_now_click</code>, <code>payment_modal_opened</code>, <code>payment_method_selected</code>, <code>initiate_checkout</code>, <code>chat_opened</code>, <code>chat_message_submitted</code>, <code>chat_message_sent</code>, <code>purchase_completed</code>, <code>page_exit</code></div>
                                        </div>
                                    </div>
                                </div>

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


