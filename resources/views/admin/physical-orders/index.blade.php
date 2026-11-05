<!doctype html>
<html lang="en">
<head>
@include('admin.stylesheet')
</head>
<body>
@include('admin.navigation')
<div id="right-panel" class="right-panel">
@include('admin.header')
<div class="breadcrumbs">
  <div class="col-sm-6">
    <div class="page-header float-left"><div class="page-title"><h1>{{ __('Physical orders') }}</h1></div></div>
  </div>
</div>
@include('admin.warning')
<div class="content mt-3">
  <div class="card">
    <div class="card-header">
      <strong>{{ __('Orders for') }} {{ \Fickrr\Support\Store::name() }}</strong>
      <div class="mt-2">
        <a href="{{ url('/admin/physical-orders') }}">{{ __('All') }}</a>
        @foreach($statuses as $name)
          · <a href="{{ url('/admin/physical-orders') }}?status={{ $name }}">{{ \Fickrr\Services\Physical\PhysicalOrderService::statusLabel($name) }}</a>
        @endforeach
      </div>
    </div>
    <div class="card-body">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>{{ __('Order') }}</th>
            <th>{{ __('Customer') }}</th>
            <th>{{ __('City') }}</th>
            <th>{{ __('Total') }}</th>
            <th>{{ __('Payment') }}</th>
            <th>{{ __('Status') }}</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
          <tr>
            <td>{{ $order->order_number }}<br><small>{{ $order->created_at }}</small></td>
            <td>{{ $order->customer_name }}<br><small>{{ $order->customer_phone }}</small></td>
            <td>{{ $order->city }}, {{ $order->country }}</td>
            <td>{{ number_format((float) $order->total, 2) }} {{ $order->currency }}</td>
            <td>{{ $order->payment_method }}<br><small>{{ $order->payment_status }}</small></td>
            <td>@include('partials.physical-status', ['order' => $order])</td>
            <td><a class="btn btn-sm btn-primary" href="{{ url('/admin/physical-orders/'.$order->id) }}">{{ __('Manage') }}</a></td>
          </tr>
          @empty
          <tr><td colspan="7">{{ __('No physical orders yet.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
      {{ $orders->links() }}
    </div>
  </div>
</div>
</div>
@include('admin.javascript')
</body>
</html>
