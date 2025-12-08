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
                        <h1>{{ __('Campaign Details') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="{{ url('/admin') }}">{{ __('Dashboard') }}</a></li>
                            <li><a href="{{ route('admin.email-marketing.campaigns') }}">{{ __('Campaigns') }}</a></li>
                            <li class="active">{{ __('View') }}</li>
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
                                <strong class="card-title">{{ $campaign->campaign_name }}</strong>
                                <a href="{{ route('admin.email-marketing.campaigns') }}" class="btn btn-secondary btn-sm float-right">
                                    <i class="fa fa-arrow-left"></i> {{ __('Back to Campaigns') }}
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>{{ __('Campaign Information') }}</h5>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">{{ __('Campaign ID') }}</th>
                                                <td>{{ $campaign->id }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Campaign Name') }}</th>
                                                <td>{{ $campaign->campaign_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Template Used') }}</th>
                                                <td>{{ $campaign->template->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Email Subject') }}</th>
                                                <td>{{ $campaign->template->subject ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Recipient Type') }}</th>
                                                <td>
                                                    @if($campaign->recipient_type == 'all')
                                                    <span class="badge badge-primary">All Customers</span>
                                                    @elseif($campaign->recipient_type == 'regular_customers')
                                                    <span class="badge badge-info">Regular Customers</span>
                                                    @elseif($campaign->recipient_type == 'landing_customers')
                                                    <span class="badge badge-success">Landing Page Customers</span>
                                                    @else
                                                    <span class="badge badge-secondary">Specific Emails</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Status') }}</th>
                                                <td>
                                                    @if($campaign->status == 'completed')
                                                    <span class="badge badge-success">Completed</span>
                                                    @elseif($campaign->status == 'sending')
                                                    <span class="badge badge-warning">Sending</span>
                                                    @elseif($campaign->status == 'failed')
                                                    <span class="badge badge-danger">Failed</span>
                                                    @else
                                                    <span class="badge badge-secondary">Draft</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div class="col-md-6">
                                        <h5>{{ __('Campaign Statistics') }}</h5>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">{{ __('Total Recipients') }}</th>
                                                <td><strong>{{ $campaign->total_recipients }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Emails Sent') }}</th>
                                                <td>
                                                    <span class="text-success">
                                                        <i class="fa fa-check-circle"></i> {{ $campaign->emails_sent }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Emails Failed') }}</th>
                                                <td>
                                                    <span class="text-danger">
                                                        <i class="fa fa-times-circle"></i> {{ $campaign->emails_failed }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Success Rate') }}</th>
                                                <td>
                                                    @if($campaign->total_recipients > 0)
                                                    <strong>{{ number_format(($campaign->emails_sent / $campaign->total_recipients) * 100, 2) }}%</strong>
                                                    @else
                                                    <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Created At') }}</th>
                                                <td>{{ date('M d, Y h:i A', strtotime($campaign->created_at)) }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Sent At') }}</th>
                                                <td>
                                                    @if($campaign->sent_at)
                                                    {{ date('M d, Y h:i A', strtotime($campaign->sent_at)) }}
                                                    @else
                                                    <span class="text-muted">Not sent yet</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if($campaign->recipient_type == 'specific' && $campaign->recipient_emails)
                                <hr>
                                <h5>{{ __('Specific Recipients') }}</h5>
                                <div class="alert alert-light">
                                    {{ $campaign->recipient_emails }}
                                </div>
                                @endif

                                @if($campaign->template)
                                <hr>
                                <h5>{{ __('Email Preview') }}</h5>
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <strong>{{ __('Subject:') }}</strong> {{ $campaign->template->subject }}
                                    </div>
                                    <div class="card-body">
                                        {!! $campaign->template->content !!}
                                    </div>
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

