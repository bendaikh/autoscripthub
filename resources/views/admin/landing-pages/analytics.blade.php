<!doctype html>
<html class="no-js" lang="en">

<head>
    @include('admin.stylesheet')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/landing-analytics.css') }}?v=20260829-1">
</head>

<body>
    @include('admin.navigation')

    <div id="right-panel" class="right-panel">
        @include('admin.header')

        @include('admin.warning')

        @php
            $formatDuration = function ($seconds) {
                if ($seconds < 60) {
                    return $seconds . 's';
                }
                $minutes = floor($seconds / 60);
                $remaining = $seconds % 60;
                return $minutes . 'm ' . $remaining . 's';
            };

            $maxCountryVisitors = $countries->max('unique_visitors') ?: 1;
            $maxEventTotal = $events->max('total') ?: 1;
            $deviceTotal = max(1, array_sum($devices));
            $deviceLabels = [
                'desktop' => __('Desktop'),
                'mobile' => __('Mobile'),
                'tablet' => __('Tablet'),
                'other' => __('Other'),
            ];
            $deviceIcons = [
                'desktop' => 'fa-desktop',
                'mobile' => 'fa-mobile',
                'tablet' => 'fa-tablet',
                'other' => 'fa-question-circle',
            ];
            $liveUrl = route('admin.landing-pages.analytics.live', array_filter([
                'lp_id' => $landing_page->lp_id,
                'country' => $country_filter ?: null,
            ]));
        @endphp

        <div class="content mt-3 landing-analytics">
            <div class="la-hero">
                <div class="la-hero-main">
                    <div class="la-eyebrow">{{ __('Landing Page Analytics') }}</div>
                    <h1>{{ $landing_page->lp_title }}</h1>
                    <p>
                        <code>/landing/{{ $landing_page->lp_slug }}</code>
                        &middot; {{ __('Last') }} {{ $days }} {{ __('days') }}
                        @if($country_filter)
                            &middot; <span class="la-filter-badge">{{ $country_filter_flag }} {{ $country_filter_name }}</span>
                        @endif
                    </p>
                </div>
                <div class="la-hero-actions">
                    <form method="GET" action="{{ route('admin.landing-pages.analytics', $landing_page->lp_id) }}" class="la-filter-form">
                        <select name="country" class="la-range-select la-country-select" onchange="this.form.submit()">
                            <option value="">{{ __('All countries') }}</option>
                            @foreach($available_countries as $availableCountry)
                                @if($availableCountry->code)
                                    <option value="{{ $availableCountry->code }}" {{ $country_filter === $availableCountry->code ? 'selected' : '' }}>
                                        {{ $availableCountry->flag }} {{ $availableCountry->name }} ({{ number_format($availableCountry->unique_visitors) }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <select name="days" class="la-range-select" onchange="this.form.submit()">
                            @foreach([7, 14, 30, 60, 90, 180, 365] as $d)
                                <option value="{{ $d }}" {{ (int) $days === (int) $d ? 'selected' : '' }}>{{ $d }} {{ __('days') }}</option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('admin.landing-pages') }}" class="la-btn la-btn-ghost">
                        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                    </a>
                    <a href="{{ url('/landing/' . $landing_page->lp_slug) }}" target="_blank" class="la-btn la-btn-light">
                        <i class="fa fa-external-link"></i> {{ __('View Page') }}
                    </a>
                </div>
            </div>

            <div class="la-live-panel" id="laLivePanel">
                <div class="la-live-main">
                    <span class="la-live-dot" aria-hidden="true"></span>
                    <div>
                        <div class="la-live-label">{{ __('Live Visitors') }}</div>
                        <div class="la-live-count" id="laLiveCount">{{ number_format($live_visitors) }}</div>
                        <div class="la-live-sub">{{ __('Active in the last') }} <span id="laLiveWindow">{{ $live_window_minutes }}</span> {{ __('minutes') }}</div>
                    </div>
                </div>
                <div class="la-live-countries" id="laLiveCountries">
                    @forelse($live_countries as $liveCountry)
                        <div class="la-live-country-chip">
                            <span class="la-country-flag">{{ $liveCountry['flag'] }}</span>
                            <span class="la-live-country-name">{{ $liveCountry['name'] }}</span>
                            <strong>{{ number_format($liveCountry['live_visitors']) }}</strong>
                        </div>
                    @empty
                        <div class="la-live-empty" id="laLiveEmpty">{{ __('No active visitors right now') }}</div>
                    @endforelse
                </div>
                <div class="la-live-updated">
                    <i class="fa fa-refresh"></i>
                    <span id="laLiveUpdated">{{ __('Updates every 30 seconds') }}</span>
                </div>
            </div>

            <div class="la-stats">
                <div class="la-stat">
                    <span class="la-stat-icon indigo"><i class="fa fa-eye"></i></span>
                    <span class="la-stat-label">{{ __('Visits') }}</span>
                    <span class="la-stat-value">{{ number_format($total_visits) }}</span>
                    <span class="la-stat-sub">{{ number_format($pageviews) }} {{ __('pageviews') }}</span>
                </div>
                <div class="la-stat">
                    <span class="la-stat-icon sky"><i class="fa fa-users"></i></span>
                    <span class="la-stat-label">{{ __('Unique Visitors') }}</span>
                    <span class="la-stat-value">{{ number_format($unique_visitors) }}</span>
                    <span class="la-stat-sub">{{ $avg_pages_per_visit }} {{ __('pages / visit avg') }}</span>
                </div>
                <div class="la-stat">
                    <span class="la-stat-icon emerald"><i class="fa fa-shopping-cart"></i></span>
                    <span class="la-stat-label">{{ __('Purchases') }}</span>
                    <span class="la-stat-value">{{ number_format($completed_orders) }}</span>
                    <span class="la-stat-sub">{{ $conversion_rate }}% {{ __('conversion rate') }}</span>
                </div>
                <div class="la-stat">
                    <span class="la-stat-icon amber"><i class="fa fa-dollar"></i></span>
                    <span class="la-stat-label">{{ __('Revenue') }}</span>
                    <span class="la-stat-value">${{ number_format($revenue_total, 2) }}</span>
                    <span class="la-stat-sub">{{ number_format($buy_clicks) }} {{ __('buy clicks') }}</span>
                </div>
                <div class="la-stat">
                    <span class="la-stat-icon violet"><i class="fa fa-clock-o"></i></span>
                    <span class="la-stat-label">{{ __('Avg. Time on Page') }}</span>
                    <span class="la-stat-value">{{ $formatDuration($avg_duration_seconds) }}</span>
                    <span class="la-stat-sub">{{ $bounce_rate }}% {{ __('bounce rate') }}</span>
                </div>
                <div class="la-stat">
                    <span class="la-stat-icon rose"><i class="fa fa-credit-card"></i></span>
                    <span class="la-stat-label">{{ __('Checkout Starts') }}</span>
                    <span class="la-stat-value">{{ number_format($checkout_starts) }}</span>
                    <span class="la-stat-sub">{{ __('Payment flow initiated') }}</span>
                </div>
                <div class="la-stat">
                    <span class="la-stat-icon teal"><i class="fa fa-hand-pointer-o"></i></span>
                    <span class="la-stat-label">{{ __('Buy Now Clicks') }}</span>
                    <span class="la-stat-value">{{ number_format($buy_clicks) }}</span>
                    <span class="la-stat-sub">{{ __('Primary CTA engagement') }}</span>
                </div>
                <div class="la-stat">
                    <span class="la-stat-icon cyan"><i class="fa fa-envelope"></i></span>
                    <span class="la-stat-label">{{ __('Contact Messages') }}</span>
                    <span class="la-stat-value">{{ number_format($contact_submissions) }}</span>
                    <span class="la-stat-sub">{{ number_format($contact_sent) }} {{ __('sent') }}</span>
                </div>
                <div class="la-stat">
                    <span class="la-stat-icon slate"><i class="fa fa-globe"></i></span>
                    <span class="la-stat-label">{{ $country_filter ? __('Filtered Country') : __('Countries Reached') }}</span>
                    @if($country_filter)
                        <span class="la-stat-value la-stat-country">{{ $country_filter_flag }} {{ $country_filter_name }}</span>
                        <span class="la-stat-sub">{{ number_format($unique_visitors) }} {{ __('visitors from this country') }}</span>
                    @else
                        <span class="la-stat-value">{{ $countries->count() }}</span>
                        <span class="la-stat-sub">{{ __('Geographic spread') }}</span>
                    @endif
                </div>
            </div>

            @if($country_filter)
                <div class="la-country-filter-banner">
                    <div>
                        <strong>{{ __('Showing analytics for') }} {{ $country_filter_flag }} {{ $country_filter_name }}</strong>
                        <span>{{ __('All metrics below are scoped to visitors from this country.') }}</span>
                    </div>
                    <a href="{{ route('admin.landing-pages.analytics', ['lp_id' => $landing_page->lp_id, 'days' => $days]) }}" class="la-btn la-btn-ghost la-btn-sm">
                        <i class="fa fa-times"></i> {{ __('Clear country filter') }}
                    </a>
                </div>
            @endif

            @if(!$country_filter)
            <div class="row">
                <div class="col-xl-7">
                    <div class="la-panel">
                        <div class="la-panel-head">
                            <div>
                                <h3 class="la-panel-title">{{ __('Visitors by Country') }}</h3>
                                <p class="la-panel-subtitle">{{ __('Unique visitors per country with flags') }}</p>
                            </div>
                        </div>
                        <div class="la-panel-body">
                            <div class="la-chart-wrap la-chart-wrap-countries">
                                <canvas id="laCountriesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="la-panel">
                        <div class="la-panel-head">
                            <div>
                                <h3 class="la-panel-title">{{ __('Country Breakdown') }}</h3>
                                <p class="la-panel-subtitle">{{ __('Click a country to view its analytics') }}</p>
                            </div>
                        </div>
                        <div class="la-panel-body p-0 la-country-list-wrap">
                            <div class="table-responsive">
                                <table class="table la-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Country') }}</th>
                                            <th>{{ __('Visitors') }}</th>
                                            <th>{{ __('Visits') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($countries as $country)
                                            <tr class="la-country-row">
                                                <td>
                                                    <a href="{{ route('admin.landing-pages.analytics', ['lp_id' => $landing_page->lp_id, 'days' => $days, 'country' => $country->code]) }}" class="la-country-link">
                                                        <div class="la-country-cell">
                                                            <span class="la-country-flag">{{ $country->flag }}</span>
                                                            <div>
                                                                <strong>{{ $country->name }}</strong>
                                                                @if($country->code)
                                                                    <div class="la-country-code">{{ $country->code }}</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="la-bar-cell">
                                                        <strong>{{ number_format($country->unique_visitors) }}</strong>
                                                        <div class="la-mini-bar">
                                                            <span style="width: {{ round(($country->unique_visitors / $maxCountryVisitors) * 100) }}%;"></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ number_format($country->visits) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="la-empty">{{ __('No country data yet') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-xl-8">
                    <div class="la-panel">
                        <div class="la-panel-head">
                            <div>
                                <h3 class="la-panel-title">{{ __('Traffic Over Time') }}</h3>
                                <p class="la-panel-subtitle">{{ __('Daily visits, unique visitors, and completed orders') }}</p>
                            </div>
                        </div>
                        <div class="la-panel-body">
                            <div class="la-chart-wrap">
                                <canvas id="laTrafficChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="la-panel">
                        <div class="la-panel-head">
                            <div>
                                <h3 class="la-panel-title">{{ __('Device Breakdown') }}</h3>
                                <p class="la-panel-subtitle">{{ __('Visitor devices in selected period') }}</p>
                            </div>
                        </div>
                        <div class="la-panel-body">
                            <div class="la-chart-wrap la-chart-wrap-sm">
                                <canvas id="laDeviceChart"></canvas>
                            </div>
                            <div class="la-device-grid mt-3">
                                @foreach($devices as $deviceKey => $deviceCount)
                                    <div class="la-device-item">
                                        <span class="la-device-icon"><i class="fa {{ $deviceIcons[$deviceKey] ?? 'fa-question-circle' }}"></i></span>
                                        <div>
                                            <div class="la-device-count">{{ number_format($deviceCount) }}</div>
                                            <div class="la-device-label">{{ $deviceLabels[$deviceKey] ?? ucfirst($deviceKey) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="la-panel">
                        <div class="la-panel-head">
                            <div>
                                <h3 class="la-panel-title">{{ __('Conversion Funnel') }}</h3>
                                <p class="la-panel-subtitle">{{ __('How visitors move from view to purchase') }}</p>
                            </div>
                        </div>
                        <div class="la-panel-body">
                            <div class="la-funnel">
                                @foreach($funnel as $step)
                                    <div class="la-funnel-step">
                                        <div class="la-funnel-label">{{ __($step['label']) }}</div>
                                        <div class="la-funnel-bar-wrap">
                                            <div class="la-funnel-bar" style="width: {{ max(2, $step['rate']) }}%;"></div>
                                        </div>
                                        <div class="la-funnel-meta">
                                            {{ number_format($step['count']) }}
                                            <span>({{ $step['rate'] }}%)</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="la-panel">
                        <div class="la-panel-head">
                            <div>
                                <h3 class="la-panel-title">{{ __('Scroll Depth') }}</h3>
                                <p class="la-panel-subtitle">{{ __('How far visitors scroll down the page') }}</p>
                            </div>
                        </div>
                        <div class="la-panel-body">
                            <div class="la-scroll-grid">
                                @foreach(['25' => '25%', '50' => '50%', '75' => '75%', '90' => '90%'] as $depthKey => $depthLabel)
                                    <div class="la-scroll-item">
                                        <strong>{{ number_format($scroll_depth[$depthKey] ?? 0) }}</strong>
                                        <span>{{ $depthLabel }} {{ __('reached') }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="la-chart-wrap la-chart-wrap-sm mt-3">
                                <canvas id="laScrollChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="la-panel">
                        <div class="la-panel-head">
                            <div>
                                <h3 class="la-panel-title">{{ __('Traffic Sources') }}</h3>
                                <p class="la-panel-subtitle">{{ __('UTM sources and top referrers') }}</p>
                            </div>
                        </div>
                        <div class="la-panel-body p-0">
                            <div class="table-responsive">
                                <table class="table la-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Source / Referrer') }}</th>
                                            <th>{{ __('Visits') }}</th>
                                            <th>{{ __('Unique') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($utm_sources as $source)
                                            <tr>
                                                <td>
                                                    <span class="la-badge">{{ $source->source }}</span>
                                                </td>
                                                <td>{{ number_format($source->visits) }}</td>
                                                <td>{{ number_format($source->unique_visitors) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="la-empty">{{ __('No UTM data yet') }}</td>
                                            </tr>
                                        @endforelse
                                        @foreach($referrers->take(5) as $referrer)
                                            <tr>
                                                <td><small>{{ \Illuminate\Support\Str::limit($referrer->referrer, 70) }}</small></td>
                                                <td>{{ number_format($referrer->visits) }}</td>
                                                <td>{{ number_format($referrer->unique_visitors) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="la-panel">
                        <div class="la-panel-head">
                            <div>
                                <h3 class="la-panel-title">{{ __('Campaign Performance') }}</h3>
                                <p class="la-panel-subtitle">{{ __('UTM campaigns with medium') }}</p>
                            </div>
                        </div>
                        <div class="la-panel-body p-0">
                            <div class="table-responsive">
                                <table class="table la-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Campaign') }}</th>
                                            <th>{{ __('Medium') }}</th>
                                            <th>{{ __('Visits') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($utm_campaigns as $campaign)
                                            <tr>
                                                <td>{{ $campaign->campaign }}</td>
                                                <td><span class="la-badge">{{ $campaign->medium }}</span></td>
                                                <td>{{ number_format($campaign->visits) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="la-empty">{{ __('No campaign data yet') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="la-panel">
                        <div class="la-panel-head">
                            <div>
                                <h3 class="la-panel-title">{{ __('Payment Methods') }}</h3>
                                <p class="la-panel-subtitle">{{ __('Completed orders by payment provider') }}</p>
                            </div>
                        </div>
                        <div class="la-panel-body p-0">
                            <div class="table-responsive">
                                <table class="table la-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Method') }}</th>
                                            <th>{{ __('Orders') }}</th>
                                            <th>{{ __('Revenue') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payment_methods as $method)
                                            <tr>
                                                <td><span class="la-badge success">{{ ucfirst($method->payment_method) }}</span></td>
                                                <td>{{ number_format($method->total) }}</td>
                                                <td>${{ number_format($method->revenue, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="la-empty">{{ __('No completed orders yet') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-7">
                    <div class="la-panel">
                        <div class="la-panel-head">
                            <div>
                                <h3 class="la-panel-title">{{ __('Event Activity') }}</h3>
                                <p class="la-panel-subtitle">{{ __('All tracked user actions on this landing page') }}</p>
                            </div>
                        </div>
                        <div class="la-panel-body p-0">
                            <div class="table-responsive">
                                <table class="table la-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Event') }}</th>
                                            <th>{{ __('Count') }}</th>
                                            <th>{{ __('Share') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($events as $event)
                                            @php
                                                $label = $event_labels[$event->lpe_name] ?? $event->lpe_name;
                                                $share = round(($event->total / $maxEventTotal) * 100, 1);
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ __($label) }}</strong>
                                                    <div><code>{{ $event->lpe_name }}</code></div>
                                                </td>
                                                <td>{{ number_format($event->total) }}</td>
                                                <td>
                                                    <div class="la-bar-cell">
                                                        <div class="la-mini-bar">
                                                            <span style="width: {{ max(4, $share) }}%;"></span>
                                                        </div>
                                                        <small>{{ $share }}%</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="la-empty">{{ __('No events recorded yet') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="la-panel">
                        <div class="la-panel-head">
                            <div>
                                <h3 class="la-panel-title">{{ __('Recent Purchases') }}</h3>
                                <p class="la-panel-subtitle">{{ __('Latest completed orders in this period') }}</p>
                            </div>
                        </div>
                        <div class="la-panel-body p-0">
                            <div class="table-responsive">
                                <table class="table la-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Customer') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Method') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recent_orders as $order)
                                            <tr>
                                                <td>
                                                    <div>{{ \Illuminate\Support\Str::limit($order->customer_email, 28) }}</div>
                                                    <small class="text-muted">{{ $order->completed_at ?: $order->created_at }}</small>
                                                </td>
                                                <td>{{ strtoupper($order->currency) }} {{ number_format($order->amount, 2) }}</td>
                                                <td>{{ ucfirst($order->payment_method) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="la-empty">{{ __('No purchases in this period') }}</td>
                                            </tr>
                                        @endforelse
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

    <script>
        (function () {
            'use strict';

            var dailyData = @json($daily_chart);
            var deviceData = @json($devices);
            var scrollData = @json($scroll_depth);
            var countriesData = @json($countries->take(12)->values());
            var liveUrl = @json($liveUrl);

            var labels = dailyData.map(function (row) { return row.date; });
            var visits = dailyData.map(function (row) { return row.visits; });
            var uniqueVisitors = dailyData.map(function (row) { return row.unique_visitors; });
            var orders = dailyData.map(function (row) { return row.orders; });

            var trafficCanvas = document.getElementById('laTrafficChart');
            if (trafficCanvas && typeof Chart !== 'undefined') {
                new Chart(trafficCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: @json(__('Visits')),
                                data: visits,
                                borderColor: '#6366f1',
                                backgroundColor: 'rgba(99, 102, 241, 0.12)',
                                fill: true,
                                lineTension: 0.25
                            },
                            {
                                label: @json(__('Unique Visitors')),
                                data: uniqueVisitors,
                                borderColor: '#0ea5e9',
                                backgroundColor: 'transparent',
                                fill: false,
                                lineTension: 0.25
                            },
                            {
                                label: @json(__('Orders')),
                                data: orders,
                                borderColor: '#10b981',
                                backgroundColor: 'transparent',
                                fill: false,
                                lineTension: 0.25
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: { position: 'bottom' },
                        scales: {
                            yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }],
                            xAxes: [{ ticks: { maxRotation: 45, minRotation: 0 } }]
                        }
                    }
                });
            }

            var deviceCanvas = document.getElementById('laDeviceChart');
            if (deviceCanvas && typeof Chart !== 'undefined') {
                new Chart(deviceCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: [
                            @json(__('Desktop')),
                            @json(__('Mobile')),
                            @json(__('Tablet')),
                            @json(__('Other'))
                        ],
                        datasets: [{
                            data: [
                                deviceData.desktop || 0,
                                deviceData.mobile || 0,
                                deviceData.tablet || 0,
                                deviceData.other || 0
                            ],
                            backgroundColor: ['#6366f1', '#0ea5e9', '#f59e0b', '#94a3b8'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: { position: 'bottom' }
                    }
                });
            }

            var scrollCanvas = document.getElementById('laScrollChart');
            if (scrollCanvas && typeof Chart !== 'undefined') {
                new Chart(scrollCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['25%', '50%', '75%', '90%'],
                        datasets: [{
                            label: @json(__('Visitors')),
                            data: [
                                scrollData['25'] || 0,
                                scrollData['50'] || 0,
                                scrollData['75'] || 0,
                                scrollData['90'] || 0
                            ],
                            backgroundColor: ['#c7d2fe', '#a5b4fc', '#818cf8', '#6366f1'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: { display: false },
                        scales: {
                            yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }]
                        }
                    }
                });
            }

            var countriesCanvas = document.getElementById('laCountriesChart');
            if (countriesCanvas && typeof Chart !== 'undefined' && countriesData.length) {
                var countryLabels = countriesData.map(function (row) {
                    return row.flag + ' ' + row.name;
                });
                var countryVisitors = countriesData.map(function (row) {
                    return row.unique_visitors;
                });

                new Chart(countriesCanvas.getContext('2d'), {
                    type: 'horizontalBar',
                    data: {
                        labels: countryLabels,
                        datasets: [{
                            label: @json(__('Visitors')),
                            data: countryVisitors,
                            backgroundColor: '#6366f1',
                            borderColor: '#4f46e5',
                            borderWidth: 0
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: { display: false },
                        scales: {
                            xAxes: [{ ticks: { beginAtZero: true, precision: 0 } }],
                            yAxes: [{ ticks: { fontSize: 12 } }]
                        },
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    return @json(__('Visitors')) + ': ' + Number(tooltipItem.xLabel || 0).toLocaleString();
                                }
                            }
                        }
                    }
                });
            }

            function renderLiveCountries(countries) {
                var container = document.getElementById('laLiveCountries');
                if (!container) return;

                if (!countries || !countries.length) {
                    container.innerHTML = '<div class="la-live-empty">' + @json(__('No active visitors right now')) + '</div>';
                    return;
                }

                container.innerHTML = countries.map(function (country) {
                    return '<div class="la-live-country-chip">' +
                        '<span class="la-country-flag">' + country.flag + '</span>' +
                        '<span class="la-live-country-name">' + country.name + '</span>' +
                        '<strong>' + Number(country.live_visitors || 0).toLocaleString() + '</strong>' +
                        '</div>';
                }).join('');
            }

            function refreshLiveVisitors() {
                if (!liveUrl || typeof fetch !== 'function') return;

                fetch(liveUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                    .then(function (response) { return response.json(); })
                    .then(function (payload) {
                        if (!payload || !payload.success || !payload.data) return;

                        var countEl = document.getElementById('laLiveCount');
                        if (countEl) {
                            countEl.textContent = Number(payload.data.total || 0).toLocaleString();
                        }

                        var windowEl = document.getElementById('laLiveWindow');
                        if (windowEl && payload.data.window_minutes) {
                            windowEl.textContent = payload.data.window_minutes;
                        }

                        renderLiveCountries(payload.data.countries || []);

                        var updatedEl = document.getElementById('laLiveUpdated');
                        if (updatedEl) {
                            var updatedAt = payload.data.updated_at ? new Date(payload.data.updated_at) : new Date();
                            updatedEl.textContent = @json(__('Last updated')) + ': ' + updatedAt.toLocaleTimeString();
                        }
                    })
                    .catch(function () {});
            }

            refreshLiveVisitors();
            setInterval(refreshLiveVisitors, 30000);
        })();
    </script>
</body>

</html>
