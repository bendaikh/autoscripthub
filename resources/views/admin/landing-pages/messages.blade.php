<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    @include('admin.stylesheet')
    <style>
        .message-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-unread {
            background: #ff4444;
            color: white;
        }
        .status-read {
            background: #4CAF50;
            color: white;
        }
        .status-replied {
            background: #2196F3;
            color: white;
        }
        .message-preview {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        /* Modal fixes */
        .modal {
            z-index: 99999; /* Higher than any other element on the page */
        }
        .modal-backdrop {
            z-index: 99998; /* Just below the modal */
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-dialog {
            margin: 1.75rem auto;
        }
        .modal-content {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .modal-header {
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
        }
        .modal-body {
            padding: 1.5rem;
        }
        .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
        }
        .modal-body strong {
            color: #495057;
            display: block;
            margin-bottom: 0.5rem;
        }
        .modal-body a {
            color: #007bff;
            word-break: break-all;
        }
        .modal-body .bg-light {
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6;
            max-height: 300px;
            overflow-y: auto;
            word-wrap: break-word;
        }
    </style>
</head>

<body>
    @include('admin.navigation')

    <div id="right-panel" class="right-panel">
        @include('admin.header')

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Landing Page Messages') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <a href="{{ route('admin.landing-pages') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> {{ __('Back to Landing Pages') }}
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
                        @if(Session::has('success'))
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle"></i> {{ Session::get('success') }}
                        </div>
                        @endif

                        @if(Session::has('error'))
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-circle"></i> {{ Session::get('error') }}
                        </div>
                        @endif

                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">{{ __('Landing Page Messages') }}</strong>
                                @if($unread_count > 0)
                                <span class="badge badge-danger ml-2">{{ $unread_count }} {{ __('Unread') }}</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.landing-page-messages.delete-multiple') }}" method="POST" id="messagesForm">
                                    @csrf
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Are you sure you want to delete selected messages?') }}');">
                                            <i class="fa fa-trash"></i> {{ __('Delete Selected') }}
                                        </button>
                                    </div>
                                    <table id="example" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"></th>
                                                <th>{{ __('Sno') }}</th>
                                                <th>{{ __('Landing Page') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Email') }}</th>
                                                <th>{{ __('Phone') }}</th>
                                                <th>{{ __('Subject') }}</th>
                                                <th>{{ __('Message') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = 1; @endphp
                                            @foreach($messages as $message)
                                            <tr class="allChecked" style="{{ $message->lpm_status == 0 ? 'background-color: #fff3cd;' : '' }}">
                                                <td><input type="checkbox" name="message_ids[]" value="{{ $message->lpm_id }}"/></td>
                                                <td>{{ $no }}</td>
                                                <td>
                                                    <a href="{{ url('/landing/' . $message->lp_slug) }}" target="_blank">
                                                        {{ $message->lp_title }}
                                                    </a>
                                                </td>
                                                <td><strong>{{ $message->lpm_name }}</strong></td>
                                                <td>{{ $message->lpm_email }}</td>
                                                <td>{{ $message->lpm_phone ?? '---' }}</td>
                                                <td>{{ $message->lpm_subject ?? '---' }}</td>
                                                <td>
                                                    <div class="message-preview" title="{{ $message->lpm_message }}">
                                                        {{ mb_substr($message->lpm_message, 0, 50, 'UTF-8') }}{{ strlen($message->lpm_message) > 50 ? '...' : '' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($message->lpm_status == 0)
                                                    <span class="message-status status-unread">{{ __('Unread') }}</span>
                                                    @elseif($message->lpm_status == 1)
                                                    <span class="message-status status-read">{{ __('Read') }}</span>
                                                    @else
                                                    <span class="message-status status-replied">{{ __('Replied') }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ date('d M Y, h:i A', strtotime($message->created_at)) }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#messageModal{{ $message->lpm_id }}" title="{{ __('View') }}">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                        @if($message->lpm_status == 0)
                                                        <a href="{{ route('admin.landing-page-messages.mark-read', $message->lpm_id) }}" class="btn btn-success btn-sm" title="{{ __('Mark as Read') }}">
                                                            <i class="fa fa-check"></i>
                                                        </a>
                                                        @endif
                                                        @if($message->lpm_status != 2)
                                                        <a href="{{ route('admin.landing-page-messages.mark-replied', $message->lpm_id) }}" class="btn btn-primary btn-sm" title="{{ __('Mark as Replied') }}">
                                                            <i class="fa fa-reply"></i>
                                                        </a>
                                                        @endif
                                                        <a href="{{ route('admin.landing-page-messages.delete', $message->lpm_id) }}" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Are you sure you want to delete this message?') }}');" title="{{ __('Delete') }}">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Message Detail Modal -->
                                            <div class="modal fade" id="messageModal{{ $message->lpm_id }}" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel{{ $message->lpm_id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="messageModalLabel{{ $message->lpm_id }}">{{ __('Message Details') }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <strong>{{ __('Landing Page:') }}</strong>
                                                                    <div class="mt-1">
                                                                        <a href="{{ url('/landing/' . $message->lp_slug) }}" target="_blank" class="text-primary">{{ $message->lp_title }}</a>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <strong>{{ __('Date:') }}</strong>
                                                                    <div class="mt-1 text-muted">
                                                                        {{ date('d M Y, h:i A', strtotime($message->created_at)) }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <strong>{{ __('Name:') }}</strong>
                                                                    <div class="mt-1">
                                                                        {{ $message->lpm_name }}
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <strong>{{ __('Email:') }}</strong>
                                                                    <div class="mt-1">
                                                                        <a href="mailto:{{ $message->lpm_email }}" class="text-primary">{{ $message->lpm_email }}</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if($message->lpm_phone)
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <strong>{{ __('Phone:') }}</strong>
                                                                    <div class="mt-1">
                                                                        <a href="tel:{{ $message->lpm_phone }}" class="text-primary">{{ $message->lpm_phone }}</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            @if($message->lpm_subject)
                                                            <div class="row mb-3">
                                                                <div class="col-md-12">
                                                                    <strong>{{ __('Subject:') }}</strong>
                                                                    <div class="mt-1">
                                                                        {{ $message->lpm_subject }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <strong>{{ __('Message:') }}</strong>
                                                                    <div class="mt-2 p-3 bg-light rounded" style="white-space: pre-wrap; word-wrap: break-word; max-height: 300px; overflow-y: auto;">{{ $message->lpm_message }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                <i class="fa fa-times"></i> {{ __('Close') }}
                                                            </button>
                                                            @if($message->lpm_status == 0)
                                                            <a href="{{ route('admin.landing-page-messages.mark-read', $message->lpm_id) }}" class="btn btn-success">
                                                                <i class="fa fa-check"></i> {{ __('Mark as Read') }}
                                                            </a>
                                                            @endif
                                                            <a href="mailto:{{ $message->lpm_email }}?subject=Re: {{ urlencode($message->lpm_subject ?? 'Your Message') }}" class="btn btn-primary" onclick="window.location.href='{{ route('admin.landing-page-messages.mark-replied', $message->lpm_id) }}';">
                                                                <i class="fa fa-envelope"></i> {{ __('Reply via Email') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @php $no++; @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Basic admin scripts without dashboard components -->
    <script src="{{ asset('admin/vendors/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/main.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/jszip/dist/jszip.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('admin/vendors/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/init-scripts/data-table/datatables-init.js') }}"></script>
    
    <script type="text/javascript">
        $(document).ready(function () { 
            var oTable = $('#example').dataTable({
                stateSave: true,
                order: [[9, 'desc']] // Sort by date descending
            });

            var allPages = oTable.fnGetNodes();

            $('body').on('click', '#selectAll', function () {
                if ($(this).hasClass('allChecked')) {
                    $('input[type="checkbox"]', allPages).prop('checked', false);
                } else {
                    $('input[type="checkbox"]', allPages).prop('checked', true);
                }
                $(this).toggleClass('allChecked');
            });

            // Ensure modals work properly
            // Move all modals to <body> to avoid stacking context issues
            $('.modal').appendTo('body');
        });
    </script>
</body>

</html>

