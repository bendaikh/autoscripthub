<!doctype html>
<html class="no-js" lang="en">

<head>
    @include('admin.stylesheet')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/landing-admin.css') }}?v=20261030-r2">
</head>

<body>
    @include('admin.navigation')

    <div id="right-panel" class="right-panel">
        @include('admin.header')

        @include('admin.warning')

        @php
            $landing_customer_items = [];
            foreach ($customers as $customer) {
                $landing_customer_items[] = [
                    'id' => $customer->id,
                    'email' => $customer->email,
                    'totalPurchases' => (int) $customer->total_purchases,
                    'totalSpent' => (float) $customer->total_spent,
                    'firstPurchaseAt' => $customer->first_purchase_at ? (string) $customer->first_purchase_at : null,
                    'lastPurchaseAt' => $customer->last_purchase_at ? (string) $customer->last_purchase_at : null,
                    'deleteUrl' => url('/admin/landing-customers/delete/' . $customer->id),
                ];
            }

            $total_revenue = (float) $customers->sum('total_spent');
            $average_spend = $customers->count() ? (float) $customers->avg('total_spent') : 0;

            $landing_admin_config = [
                'type' => 'customers',
                'section' => __('Landing Pages'),
                'title' => __('Landing Customers'),
                'description' => __('Review customer value and purchase activity in one place.'),
                'emptyMessage' => __('No customers have purchased from a landing page yet.'),
                'currency' => 'USD',
                'items' => $landing_customer_items,
                'stats' => [
                    ['label' => __('Total Customers'), 'value' => $customers->count()],
                    ['label' => __('Total Orders'), 'value' => (int) $customers->sum('total_purchases')],
                    ['label' => __('Total Revenue'), 'value' => '$' . number_format($total_revenue, 2)],
                    ['label' => __('Average Spend'), 'value' => '$' . number_format($average_spend, 2)],
                ],
                'labels' => [
                    'tableTitle' => __('Customer Directory'),
                    'searchPlaceholder' => __('Search customers...'),
                    'noResults' => __('No matching results'),
                    'customer' => __('Customer'),
                    'purchases' => __('Purchases'),
                    'totalSpent' => __('Total Spent'),
                    'firstPurchase' => __('First Purchase'),
                    'lastPurchase' => __('Last Purchase'),
                    'actions' => __('Actions'),
                    'delete' => __('Delete'),
                    'confirmDelete' => __('Delete ":name"? This action cannot be undone.'),
                    'showing' => __('Showing'),
                    'of' => __('of'),
                    'rowsPerPage' => __('Rows per page'),
                    'pagination' => __('Table pagination'),
                    'previous' => __('Previous page'),
                    'next' => __('Next page'),
                ],
            ];

            $landing_admin_json = json_encode(
                $landing_admin_config,
                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
            );
        @endphp

        <div class="content mt-3">
            <div id="landing-admin-app" v-cloak></div>
            <script id="landing-admin-data" type="application/json">{!! $landing_admin_json !!}</script>

            <div class="card mt-4">
                <div class="card-header"><strong class="card-title">{{ __('Recent orders') }}</strong></div>
                <div class="card-body">
                    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
                    @if(empty($orders) || $orders->isEmpty())
                    <p class="text-muted mb-0">{{ __('No orders yet.') }}</p>
                    @else
                    <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead><tr>
                            <th>{{ __('Date') }}</th><th>{{ __('Customer') }}</th><th>{{ __('Product') }}</th><th>{{ __('Amount') }}</th>
                            <th>{{ __('Status') }}</th><th>{{ __('Bonuses delivered') }}</th><th></th>
                        </tr></thead>
                        <tbody>
                        @foreach($orders as $o)
                            @php $oBonuses = \Fickrr\Support\LandingDelivery::deliveredTitles($o); @endphp
                            <tr>
                                <td style="white-space:nowrap">{{ \Carbon\Carbon::parse($o->completed_at ?: $o->created_at)->format('M d, Y H:i') }}</td>
                                <td>{{ $o->customer_email }}</td>
                                <td>{{ $o->lp_title ?: '#' . $o->lp_id }}<br><small class="text-muted">{{ $o->order_token }} · {{ $o->payment_method }}</small></td>
                                <td style="white-space:nowrap">{{ number_format((float) $o->amount, 2) }} {{ $o->currency }}</td>
                                <td><span class="badge {{ $o->payment_status === 'completed' ? 'badge-success' : ($o->payment_status === 'rejected' ? 'badge-danger' : 'badge-secondary') }}">{{ $o->payment_status }}</span></td>
                                <td>
                                    @if($oBonuses)
                                        @foreach($oBonuses as $bt)<div class="small"><i class="fa fa-gift text-warning"></i> {{ $bt }}</div>@endforeach
                                    @else<span class="text-muted">—</span>@endif
                                </td>
                                <td style="white-space:nowrap">
                                    @if($o->payment_status === 'completed')
                                    <form action="{{ route('admin.landing-orders.resend', $o->id) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Resend the purchase email with the product and bonus links?') }}')">
                                        {{ csrf_field() }}
                                        <button class="btn btn-outline-primary btn-sm"><i class="fa fa-envelope"></i> {{ __('Resend') }}</button>
                                    </form>
                                    <a href="{{ url('/landing/success/' . $o->order_token) }}" target="_blank" class="btn btn-light btn-sm" title="{{ __('Download page') }}"><i class="fa fa-link"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('admin.javascript')
    <script src="{{ asset('admin/vendors/vue/vue.global.prod.js') }}"></script>
    <script src="{{ asset('admin/assets/js/landing-admin-vue.js') }}?v=20260817-2"></script>
</body>

</html>
