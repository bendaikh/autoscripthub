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
        <div class="col-sm-12">
            <div class="page-header float-left">
                <div class="page-title"><h1>{{ __('Analytics Exclusions') }}</h1></div>
            </div>
        </div>
    </div>
    @include('admin.warning')
    <div class="content mt-3">
        <div class="animated fadeIn">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
            @if(!$ready)
            <div class="alert alert-warning">{{ __('Run the database migration (php artisan migrate), then reload this page.') }}</div>
            @else
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3"><i class="fa fa-user-secret"></i> {{ __('Your own visits are not counted') }}</h5>
                    <ul class="mb-0" style="line-height:1.8">
                        <li>{{ __('While you are logged in as admin, nothing you open is counted.') }}</li>
                        <li><strong id="axDevice">{{ __('Checking this device…') }}</strong> {{ __('Every device and browser you open the admin panel on is remembered and left out — also when you are logged out — and its past visits are removed from the stats.') }}</li>
                        <li>{{ __('Excluded devices also do not load Google Analytics or the Facebook / TikTok pixels.') }}</li>
                        <li>{{ __('For devices where you never open the admin (for example a family phone on your Wi-Fi), add your IP address below.') }}</li>
                    </ul>
                    <p class="text-muted small mt-2 mb-0">{{ __('Excluded devices so far') }}: <strong>{{ $deviceCount }}</strong>
                        <form action="{{ url('/admin/analytics-exclusions/devices/clear') }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Clear the device list?') }}')">{{ csrf_field() }}<button class="btn btn-link btn-sm p-0 ml-2">{{ __('Clear list') }}</button></form>
                    </p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><strong class="card-title">{{ __('Excluded IP addresses') }}</strong></div>
                <div class="card-body">
                    <form action="{{ url('/admin/analytics-exclusions/ip') }}" method="post" class="mb-3">
                        {{ csrf_field() }}
                        <input type="hidden" name="mine" value="1">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> {{ __('Add my current IP') }} ({{ $myIp }})</button>
                    </form>
                    <form action="{{ url('/admin/analytics-exclusions/ip') }}" method="post" class="form-inline mb-3" style="gap:8px">
                        {{ csrf_field() }}
                        <input type="text" name="ip" class="form-control form-control-sm" placeholder="{{ __('IP address') }}" style="width:220px">
                        <input type="text" name="label" class="form-control form-control-sm" maxlength="100" placeholder="{{ __('Note (optional), e.g. Home Wi-Fi') }}" style="width:240px">
                        <button class="btn btn-outline-primary btn-sm">{{ __('Add IP') }}</button>
                    </form>
                    @if($autoIps->count())
                    <div class="alert alert-success py-2">
                        <strong>{{ __('Learned automatically') }}:</strong>
                        @foreach($autoIps as $row)<code class="mr-2">{{ $row->value }}</code>@endforeach
                        <br><small>{{ __('IPs you used the admin panel from in the last 3 days are excluded automatically — this also covers private / incognito windows on the same internet connection.') }}</small>
                    </div>
                    @endif
                    @if($ips->isEmpty())
                    <p class="text-muted mb-0">{{ __('No IP addresses excluded.') }}</p>
                    @else
                    <table class="table table-sm mb-0">
                        <thead><tr><th>{{ __('IP address') }}</th><th>{{ __('Note') }}</th><th>{{ __('Added') }}</th><th></th></tr></thead>
                        <tbody>
                        @foreach($ips as $row)
                        <tr>
                            <td><code>{{ $row->value }}</code> @if($row->value === $myIp)<span class="badge badge-success">{{ __('you now') }}</span>@endif</td>
                            <td>{{ $row->label }}</td>
                            <td>{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('M d, Y') : '' }}</td>
                            <td class="text-right">
                                <form action="{{ url('/admin/analytics-exclusions/' . $row->id . '/delete') }}" method="post" class="d-inline">{{ csrf_field() }}<button class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i></button></form>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                    <p class="text-muted small mt-3 mb-0">{{ __('Mobile and home internet IPs can change. Device exclusion works without an IP; use IPs as an extra safety net.') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@include('admin.javascript')
<script>
(function () {
  var el = document.getElementById('axDevice');
  if (!el) return;
  var on = document.cookie.indexOf('ash_noanalytics=1') !== -1;
  el.textContent = on ? @json(__('✅ This device is excluded.')) : @json(__('This device will be excluded after the page reloads.'));
})();
</script>
</body>
</html>
