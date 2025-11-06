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
                        <h1>{{ __('Landing Page Customers') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="{{ url('/admin') }}">{{ __('Dashboard') }}</a></li>
                            <li class="active">{{ __('Customers') }}</li>
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
                                    {{ __('All Landing Page Customers') }} 
                                    <span class="badge badge-primary">{{ count($customers) }}</span>
                                </strong>
                            </div>
                            <div class="card-body">
                                @if(count($customers) > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>{{ __('ID') }}</th>
                                                <th>{{ __('Email') }}</th>
                                                <th>{{ __('Total Purchases') }}</th>
                                                <th>{{ __('Total Spent') }}</th>
                                                <th>{{ __('First Purchase') }}</th>
                                                <th>{{ __('Last Purchase') }}</th>
                                                <th>{{ __('Actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($customers as $customer)
                                            <tr>
                                                <td>{{ $customer->id }}</td>
                                                <td>
                                                    <i class="fa fa-envelope"></i> 
                                                    <strong>{{ $customer->email }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">{{ $customer->total_purchases }}</span>
                                                </td>
                                                <td>
                                                    <strong>${{ number_format($customer->total_spent, 2) }}</strong>
                                                </td>
                                                <td>
                                                    @if($customer->first_purchase_at)
                                                    {{ date('M d, Y', strtotime($customer->first_purchase_at)) }}
                                                    <br>
                                                    <small class="text-muted">{{ date('h:i A', strtotime($customer->first_purchase_at)) }}</small>
                                                    @else
                                                    -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($customer->last_purchase_at)
                                                    {{ date('M d, Y', strtotime($customer->last_purchase_at)) }}
                                                    <br>
                                                    <small class="text-muted">{{ date('h:i A', strtotime($customer->last_purchase_at)) }}</small>
                                                    @else
                                                    -
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteCustomer({{ $customer->id }}, '{{ $customer->email }}')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> {{ __('No customers found yet. Customers will appear here when they make purchases from landing pages.') }}
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Statistics Card -->
                        @if(count($customers) > 0)
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">{{ __('Statistics') }}</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body">
                                                <h4 class="mb-0">{{ count($customers) }}</h4>
                                                <small>{{ __('Total Customers') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body">
                                                <h4 class="mb-0">{{ $customers->sum('total_purchases') }}</h4>
                                                <small>{{ __('Total Orders') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body">
                                                <h4 class="mb-0">${{ number_format($customers->sum('total_spent'), 2) }}</h4>
                                                <small>{{ __('Total Revenue') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body">
                                                <h4 class="mb-0">${{ $customers->count() > 0 ? number_format($customers->avg('total_spent'), 2) : '0.00' }}</h4>
                                                <small>{{ __('Avg. per Customer') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.javascript')
    
    <script>
        function deleteCustomer(id, email) {
            if (confirm('Are you sure you want to delete customer: ' + email + '?')) {
                window.location.href = '/admin/landing-customers/delete/' + id;
            }
        }
    </script>
</body>
</html>

