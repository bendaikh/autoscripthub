<!doctype html>
<html class="no-js" lang="en">

<head>
    @include('admin.stylesheet')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/landing-admin.css') }}?v=20260831-3">
</head>

<body>
    @include('admin.navigation')

    <div id="right-panel" class="right-panel">
        @include('admin.header')

        @include('admin.warning')

        @php
            $unread_count = \Fickrr\Models\LandingPageMessage::getUnreadCount();

            $landing_page_items = [];
            $country_options_map = [];

            foreach ($landing_pages as $page) {
                $targetCountry = strtoupper(trim((string) ($page->lp_target_country ?? '')));
                $targetCountryName = $targetCountry !== ''
                    ? \Fickrr\Helpers\Helper::countryNameFromCode($targetCountry)
                    : '';
                $targetCountryFlag = $targetCountry !== ''
                    ? \Fickrr\Helpers\Helper::countryFlagEmoji($targetCountry)
                    : '';

                if ($targetCountry !== '' && !isset($country_options_map[$targetCountry])) {
                    $country_options_map[$targetCountry] = [
                        'code' => $targetCountry,
                        'name' => $targetCountryName,
                        'flag' => $targetCountryFlag,
                        'label' => trim($targetCountryFlag . ' ' . $targetCountryName . ' (' . $targetCountry . ')'),
                    ];
                }

                $adsStatus = strtolower(trim((string) ($page->lp_ads_status ?? 'off')));
                if (!in_array($adsStatus, ['off', 'published', 'paused'], true)) {
                    $adsStatus = 'off';
                }

                $sourceUrl = trim((string) ($page->lp_source_url ?? ''));
                if ($sourceUrl !== '' && !preg_match('#^https?://#i', $sourceUrl)) {
                    $sourceUrl = '';
                }

                $landing_page_items[] = [
                    'id' => $page->lp_id,
                    'title' => $page->lp_title,
                    'slug' => $page->lp_slug,
                    'price' => (float) $page->lp_price,
                    'currency' => $page->lp_currency ?: 'USD',
                    'status' => (bool) $page->lp_status,
                    'adsStatus' => $adsStatus,
                    'sourceUrl' => $sourceUrl,
                    'targetCountry' => $targetCountry,
                    'targetCountryName' => $targetCountryName,
                    'targetCountryFlag' => $targetCountryFlag,
                    'publicUrl' => url('/landing/' . $page->lp_slug),
                    'editUrl' => route('admin.edit-landing-page', $page->lp_id),
                    'analyticsUrl' => route('admin.landing-pages.analytics', $page->lp_id),
                    'duplicateUrl' => route('admin.duplicate-landing-page', $page->lp_id),
                    'adsStatusUrl' => route('admin.landing-pages.ads-status', $page->lp_id),
                    'deleteUrl' => route('admin.delete-landing-page', $page->lp_id),
                ];
            }

            $country_options = array_values($country_options_map);
            usort($country_options, function ($a, $b) {
                return strcasecmp($a['name'], $b['name']);
            });

            $landing_admin_config = [
                'type' => 'pages',
                'section' => __('Landing Pages'),
                'title' => __('Landing Pages'),
                'description' => __('Create, manage, and measure your product landing pages.'),
                'emptyMessage' => __('No landing pages yet.'),
                'currency' => 'USD',
                'items' => $landing_page_items,
                'countries' => $country_options,
                'createUrl' => route('admin.add-landing-page'),
                'messagesUrl' => route('admin.landing-page-messages'),
                'bulkDeleteUrl' => route('admin.landing-pages.bulk-delete'),
                'csrfToken' => csrf_token(),
                'unreadCount' => $unread_count,
                'adsStatuses' => [
                    ['value' => 'off', 'label' => __('Not on ads')],
                    ['value' => 'published', 'label' => __('Running on ads')],
                    ['value' => 'paused', 'label' => __('Paused')],
                ],
                'labels' => [
                    'messages' => __('Messages'),
                    'create' => __('New Landing Page'),
                    'tableTitle' => __('All Landing Pages'),
                    'searchPlaceholder' => __('Search pages...'),
                    'filterCountry' => __('Target country'),
                    'allCountries' => __('All countries'),
                    'filterAds' => __('Ads status'),
                    'allAdsStatuses' => __('All ads statuses'),
                    'noResults' => __('No matching results'),
                    'totalPages' => __('Total Pages'),
                    'activePages' => __('Active Pages'),
                    'inactivePages' => __('Inactive Pages'),
                    'totalValue' => __('Catalog Value'),
                    'page' => __('Page'),
                    'price' => __('Price'),
                    'country' => __('Country'),
                    'status' => __('Status'),
                    'ads' => __('Ads'),
                    'publicUrl' => __('Public URL'),
                    'sourceUrl' => __('Product source'),
                    'noSourceUrl' => __('No product source link'),
                    'actions' => __('Actions'),
                    'active' => __('Online'),
                    'inactive' => __('Offline'),
                    'activeHint' => __('Visible to visitors at its public URL.'),
                    'inactiveHint' => __('Offline: the public URL returns 404 until you set the page to Active in Edit.'),
                    'inactiveOnAds' => __('Ads point to an offline page'),
                    'confirmDuplicate' => __('Duplicate ":name"? A copy will be created with its own URL.'),
                    'adsOff' => __('Not on ads'),
                    'adsPublished' => __('Running on ads'),
                    'adsPaused' => __('Paused'),
                    'adsUpdateFailed' => __('Could not update ads status. Please try again.'),
                    'edit' => __('Edit'),
                    'analytics' => __('Analytics'),
                    'duplicate' => __('Duplicate'),
                    'delete' => __('Delete'),
                    'selectAll' => __('Select all on this page'),
                    'selected' => __('selected'),
                    'bulkDelete' => __('Delete selected'),
                    'confirmBulkDelete' => __('Delete :count selected landing page(s)? This cannot be undone.'),
                    'noneSelected' => __('Select at least one landing page to delete.'),
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
    <script src="{{ asset('admin/assets/js/landing-admin-vue.js') }}?v=20261030-2"></script>
</body>

</html>
