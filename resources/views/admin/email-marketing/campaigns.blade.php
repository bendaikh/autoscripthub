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
                        <h1>{{ __('Email Campaigns') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="{{ url('/admin') }}">{{ __('Dashboard') }}</a></li>
                            <li><a href="#">{{ __('Email Marketing') }}</a></li>
                            <li class="active">{{ __('Campaigns') }}</li>
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
                                    {{ __('All Email Campaigns') }} 
                                    <span class="badge badge-primary">{{ count($campaigns) }}</span>
                                </strong>
                                <a href="{{ route('admin.email-marketing.create-campaign') }}" class="btn btn-success btn-sm float-right">
                                    <i class="fa fa-paper-plane"></i> {{ __('Send New Campaign') }}
                                </a>
                            </div>
                            <div class="card-body">
                                @if(count($campaigns) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ __('ID') }}</th>
                                                <th>{{ __('Campaign Name') }}</th>
                                                <th>{{ __('Template') }}</th>
                                                <th>{{ __('Recipients') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Sent Date') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($campaigns as $campaign)
                                            <tr>
                                                <td>{{ $campaign->id }}</td>
                                                <td>
                                                    <i class="fa fa-bullhorn"></i> 
                                                    <strong>{{ $campaign->campaign_name }}</strong>
                                                </td>
                                                <td>{{ $campaign->template->name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge badge-info">{{ $campaign->total_recipients }}</span>
                                                    @if($campaign->status == 'completed' || $campaign->status == 'failed')
                                                    <br>
                                                    <small class="text-success">
                                                        <i class="fa fa-check"></i> {{ $campaign->emails_sent }} sent
                                                    </small>
                                                    @if($campaign->emails_failed > 0)
                                                    <br>
                                                    <small class="text-danger">
                                                        <i class="fa fa-times"></i> {{ $campaign->emails_failed }} failed
                                                    </small>
                                                    @endif
                                                    @endif
                                                </td>
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
                                                <td>
                                                    @if($campaign->sent_at)
                                                    {{ date('M d, Y', strtotime($campaign->sent_at)) }}
                                                    <br>
                                                    <small class="text-muted">{{ date('h:i A', strtotime($campaign->sent_at)) }}</small>
                                                    @else
                                                    <span class="text-muted">Not sent yet</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.email-marketing.view-campaign', $campaign->id) }}" 
                                                       class="btn btn-sm btn-info" title="View Details">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.email-marketing.delete-campaign', $campaign->id) }}" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this campaign?')"
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
                                    {{ __('No email campaigns found.') }}
                                    <a href="{{ route('admin.email-marketing.create-campaign') }}" class="alert-link">
                                        {{ __('Send your first campaign') }}
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

