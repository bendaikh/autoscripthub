<!doctype html>
<html class="no-js" lang="en">

<head>
    @include('admin.stylesheet')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/landing-admin.css') }}?v=20261030-r2">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/landing-messages.css') }}?v=20260828-1">
</head>

<body>
    @include('admin.navigation')

    <div id="right-panel" class="right-panel">
        @include('admin.header')

        @include('admin.warning')

        @php
            $total_messages = $messages->count();
            $read_count = $messages->where('lpm_status', 1)->count();
            $replied_count = $messages->where('lpm_status', 2)->count();
            $statusMap = [
                0 => ['key' => 'unread', 'label' => __('Unread'), 'class' => 'lm-status-unread'],
                1 => ['key' => 'read', 'label' => __('Read'), 'class' => 'lm-status-read'],
                2 => ['key' => 'replied', 'label' => __('Replied'), 'class' => 'lm-status-replied'],
            ];
        @endphp

        <div class="content mt-3 landing-admin landing-messages">
            <div class="landing-hero lm-hero">
                <div>
                    <div class="landing-eyebrow">{{ __('Landing Pages') }}</div>
                    <h1>{{ __('Messages') }}</h1>
                    <p>{{ __('Review contact form submissions from your landing pages in one inbox.') }}</p>
                </div>
                <div class="landing-hero-actions">
                    <a href="{{ route('admin.landing-pages') }}" class="landing-button landing-button-ghost">
                        <i class="fa fa-arrow-left"></i> {{ __('Back to Pages') }}
                    </a>
                </div>
            </div>

            <div class="landing-stats">
                <div class="landing-stat">
                    <span class="lm-stat-icon total"><i class="fa fa-envelope"></i></span>
                    <span class="landing-stat-label">{{ __('Total Messages') }}</span>
                    <span class="landing-stat-value">{{ number_format($total_messages) }}</span>
                </div>
                <div class="landing-stat">
                    <span class="lm-stat-icon unread"><i class="fa fa-circle"></i></span>
                    <span class="landing-stat-label">{{ __('Unread') }}</span>
                    <span class="landing-stat-value">{{ number_format($unread_count) }}</span>
                </div>
                <div class="landing-stat">
                    <span class="lm-stat-icon read"><i class="fa fa-check"></i></span>
                    <span class="landing-stat-label">{{ __('Read') }}</span>
                    <span class="landing-stat-value">{{ number_format($read_count) }}</span>
                </div>
                <div class="landing-stat">
                    <span class="lm-stat-icon replied"><i class="fa fa-reply"></i></span>
                    <span class="landing-stat-label">{{ __('Replied') }}</span>
                    <span class="landing-stat-value">{{ number_format($replied_count) }}</span>
                </div>
            </div>

            <div class="landing-panel">
                <div class="landing-toolbar">
                    <div class="landing-toolbar-main">
                        <h2>{{ __('Inbox') }}</h2>
                        <div class="lm-filter-tabs">
                            <button type="button" class="lm-filter-tab is-active" data-status="all">
                                {{ __('All') }}<span class="lm-filter-count">{{ $total_messages }}</span>
                            </button>
                            <button type="button" class="lm-filter-tab" data-status="unread">
                                {{ __('Unread') }}<span class="lm-filter-count">{{ $unread_count }}</span>
                            </button>
                            <button type="button" class="lm-filter-tab" data-status="read">
                                {{ __('Read') }}<span class="lm-filter-count">{{ $read_count }}</span>
                            </button>
                            <button type="button" class="lm-filter-tab" data-status="replied">
                                {{ __('Replied') }}<span class="lm-filter-count">{{ $replied_count }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.landing-page-messages.delete-multiple') }}" method="POST" id="messagesForm">
                    @csrf
                    <div class="landing-toolbar" style="border-top:0;padding-top:0;">
                        <div class="landing-bulk-actions">
                            <button type="submit" class="landing-button landing-button-danger" onclick="return confirm(@json(__('Are you sure you want to delete selected messages?')));">
                                <i class="fa fa-trash"></i> {{ __('Delete Selected') }}
                            </button>
                            <span class="landing-bulk-count" id="selectedCount">{{ __('Select messages to bulk delete') }}</span>
                        </div>
                    </div>

                    <div class="landing-table-wrap">
                        <table id="lpMessagesTable" class="table landing-table mb-0">
                            <thead>
                                <tr>
                                    <th class="landing-col-check"><input type="checkbox" id="selectAll" class="landing-checkbox" aria-label="{{ __('Select all') }}"></th>
                                    <th>{{ __('Contact') }}</th>
                                    <th>{{ __('Landing Page') }}</th>
                                    <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Message') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($messages as $message)
                                    @php
                                        $status = $statusMap[$message->lpm_status] ?? $statusMap[0];
                                    @endphp
                                    <tr data-status="{{ $status['key'] }}" class="{{ $message->lpm_status == 0 ? 'lm-row-unread' : '' }}">
                                        <td>
                                            <input type="checkbox" name="message_ids[]" value="{{ $message->lpm_id }}" class="landing-checkbox message-checkbox" aria-label="{{ __('Select message') }}">
                                        </td>
                                        <td>
                                            <span class="lm-contact-name">{{ $message->lpm_name }}</span>
                                            <a href="mailto:{{ $message->lpm_email }}" class="lm-contact-email">{{ $message->lpm_email }}</a>
                                            @if($message->lpm_phone)
                                                <span class="lm-contact-phone">{{ $message->lpm_phone }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ url('/landing/' . $message->lp_slug) }}" target="_blank" class="lm-page-link" title="{{ $message->lp_title }}">
                                                {{ \Illuminate\Support\Str::limit($message->lp_title, 28) }}
                                            </a>
                                            <span class="landing-subtle">/landing/{{ $message->lp_slug }}</span>
                                        </td>
                                        <td>{{ $message->lpm_subject ? \Illuminate\Support\Str::limit($message->lpm_subject, 40) : '—' }}</td>
                                        <td>
                                            <div class="lm-message-preview" title="{{ $message->lpm_message }}">
                                                {{ $message->lpm_message }}
                                            </div>
                                        </td>
                                        <td data-search="{{ $status['label'] }}">
                                            <span class="lm-status {{ $status['class'] }}">{{ $status['label'] }}</span>
                                        </td>
                                        <td data-order="{{ strtotime($message->created_at) }}">
                                            <span class="lm-date">{{ date('d M Y', strtotime($message->created_at)) }}</span>
                                            <span class="lm-date">{{ date('h:i A', strtotime($message->created_at)) }}</span>
                                        </td>
                                        <td>
                                            <div class="landing-actions">
                                                <button type="button" class="landing-icon-button" data-toggle="modal" data-target="#messageModal{{ $message->lpm_id }}" title="{{ __('View') }}">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                @if($message->lpm_status == 0)
                                                    <a href="{{ route('admin.landing-page-messages.mark-read', $message->lpm_id) }}" class="landing-icon-button" title="{{ __('Mark as Read') }}">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                @endif
                                                @if($message->lpm_status != 2)
                                                    <a href="{{ route('admin.landing-page-messages.mark-replied', $message->lpm_id) }}" class="landing-icon-button" title="{{ __('Mark as Replied') }}">
                                                        <i class="fa fa-reply"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('admin.landing-page-messages.delete', $message->lpm_id) }}" class="landing-icon-button landing-icon-button-danger" onclick="return confirm(@json(__('Are you sure you want to delete this message?')));" title="{{ __('Delete') }}">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach($messages as $message)
        @php $status = $statusMap[$message->lpm_status] ?? $statusMap[0]; @endphp
        <div class="modal fade lm-modal" id="messageModal{{ $message->lpm_id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Message Details') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="lm-modal-grid">
                            <div class="lm-modal-field">
                                <label>{{ __('Landing Page') }}</label>
                                <div>
                                    <a href="{{ url('/landing/' . $message->lp_slug) }}" target="_blank">{{ $message->lp_title }}</a>
                                </div>
                            </div>
                            <div class="lm-modal-field">
                                <label>{{ __('Date') }}</label>
                                <div>{{ date('d M Y, h:i A', strtotime($message->created_at)) }}</div>
                            </div>
                            <div class="lm-modal-field">
                                <label>{{ __('Name') }}</label>
                                <div>{{ $message->lpm_name }}</div>
                            </div>
                            <div class="lm-modal-field">
                                <label>{{ __('Email') }}</label>
                                <div><a href="mailto:{{ $message->lpm_email }}">{{ $message->lpm_email }}</a></div>
                            </div>
                            @if($message->lpm_phone)
                                <div class="lm-modal-field">
                                    <label>{{ __('Phone') }}</label>
                                    <div><a href="tel:{{ $message->lpm_phone }}">{{ $message->lpm_phone }}</a></div>
                                </div>
                            @endif
                            <div class="lm-modal-field">
                                <label>{{ __('Status') }}</label>
                                <div><span class="lm-status {{ $status['class'] }}">{{ $status['label'] }}</span></div>
                            </div>
                            @if($message->lpm_subject)
                                <div class="lm-modal-field" style="grid-column: 1 / -1;">
                                    <label>{{ __('Subject') }}</label>
                                    <div>{{ $message->lpm_subject }}</div>
                                </div>
                            @endif
                        </div>
                        <div class="lm-modal-field">
                            <label>{{ __('Message') }}</label>
                            <div class="lm-modal-message">{{ $message->lpm_message }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('Close') }}</button>
                        @if($message->lpm_status == 0)
                            <a href="{{ route('admin.landing-page-messages.mark-read', $message->lpm_id) }}" class="btn btn-success">
                                <i class="fa fa-check"></i> {{ __('Mark as Read') }}
                            </a>
                        @endif
                        @if($message->lpm_status != 2)
                            <a href="{{ route('admin.landing-page-messages.mark-replied', $message->lpm_id) }}" class="btn btn-primary">
                                <i class="fa fa-reply"></i> {{ __('Mark as Replied') }}
                            </a>
                        @endif
                        <a href="mailto:{{ $message->lpm_email }}?subject={{ rawurlencode('Re: ' . ($message->lpm_subject ?? __('Your Message'))) }}" class="btn btn-info">
                            <i class="fa fa-envelope"></i> {{ __('Reply via Email') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script src="{{ asset('admin/vendors/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/main.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            'use strict';

            var activeFilter = 'all';

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'lpMessagesTable') {
                    return true;
                }
                if (activeFilter === 'all') {
                    return true;
                }
                var row = settings.aoData[dataIndex].nTr;
                return $(row).data('status') === activeFilter;
            });

            var table = $('#lpMessagesTable').DataTable({
                stateSave: true,
                order: [[6, 'desc']],
                pageLength: 25,
                columnDefs: [
                    { orderable: false, targets: [0, 7] },
                    { searchable: false, targets: [0, 7] }
                ],
                language: {
                    emptyTable: @json(__('No messages yet.')),
                    zeroRecords: @json(__('No matching messages found.'))
                }
            });

            $('.lm-filter-tab').on('click', function () {
                $('.lm-filter-tab').removeClass('is-active');
                $(this).addClass('is-active');
                activeFilter = $(this).data('status');
                table.draw();
            });

            function updateSelectedCount() {
                var count = $('.message-checkbox:checked').length;
                var label = count
                    ? count + ' ' + @json(__('selected'))
                    : @json(__('Select messages to bulk delete'));
                $('#selectedCount').text(label);
            }

            $('#selectAll').on('change', function () {
                var checked = this.checked;
                table.rows({ search: 'applied' }).nodes().to$().find('.message-checkbox').prop('checked', checked);
                updateSelectedCount();
            });

            $(document).on('change', '.message-checkbox', function () {
                updateSelectedCount();
            });

            $('.modal').appendTo('body');
        });
    </script>
</body>

</html>
