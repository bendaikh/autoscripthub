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
        </div>
    </div>

    @include('admin.javascript')
    <script src="{{ asset('admin/vendors/vue/vue.global.prod.js') }}"></script>
    <script src="{{ asset('admin/assets/js/landing-admin-vue.js') }}?v=20260817-2"></script>
</body>

</html>
