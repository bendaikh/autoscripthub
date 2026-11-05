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
  <div class="col-sm-8">
    <div class="page-header float-left">
      <div class="page-title"><h1>{{ $order->order_number }}</h1></div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="page-header float-right">
      <a class="btn btn-secondary btn-sm" href="{{ url('/admin/physical-orders') }}">{{ __('Back') }}</a>
    </div>
  </div>
</div>
@include('admin.warning')
<div class="content mt-3">
  <div class="row">
    <div class="col-md-7">
      <div class="card">
        <div class="card-header"><strong>{{ __('Order') }}</strong> @include('partials.physical-status', ['order' => $order])</div>
        <div class="card-body">
          <p>{{ $order->customer_name }} · {{ $order->customer_email }} · {{ $order->customer_phone }}</p>
          <p>{{ $order->address }}<br>{{ $order->city }}, {{ $order->country }}</p>
          <p>{{ __('Payment') }}: {{ $order->payment_method }} ({{ $order->payment_status }})</p>
          <p>{{ __('Source') }}: {{ $order->source }}</p>
          <table class="table">
            <thead><tr><th>{{ __('Item') }}</th><th>{{ __('SKU') }}</th><th>{{ __('Qty') }}</th><th>{{ __('Price') }}</th></tr></thead>
            <tbody>
            @foreach($items as $item)
              <tr>
                <td>{{ $item->name }}@if($item->variant_name)<br><small>{{ $item->variant_name }}</small>@endif</td>
                <td>{{ $item->sku }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format((float) $item->unit_price, 2) }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
          <p>{{ __('Subtotal') }} {{ number_format((float) $order->subtotal, 2) }} {{ $order->currency }}</p>
          <p>{{ __('Shipping') }} {{ number_format((float) $order->shipping_fee, 2) }} {{ $order->currency }}</p>
          <p><strong>{{ __('Total') }} {{ number_format((float) $order->total, 2) }} {{ $order->currency }}</strong></p>
        </div>
      </div>
    </div>
    <div class="col-md-5">
      <div class="card">
        <div class="card-header"><strong>{{ __('Update status') }}</strong></div>
        <div class="card-body">
          @if($statuses === [])
            <p>{{ __('This order is') }} {{ \Fickrr\Services\Physical\PhysicalOrderService::statusLabel($order->fulfillment_status) }}. {{ __('No further change is available.') }}</p>
          @else
          <form method="post" action="{{ url('/admin/physical-orders/'.$order->id) }}">
            @csrf
            <div class="form-group">
              <label for="fulfillment_status">{{ __('Move to') }}</label>
              <select class="form-control" id="fulfillment_status" name="fulfillment_status" required>
                @foreach($statuses as $name)
                <option value="{{ $name }}">{{ \Fickrr\Services\Physical\PhysicalOrderService::statusLabel($name) }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="tracking_number">{{ __('Tracking number') }}</label>
              <input class="form-control" id="tracking_number" name="tracking_number" value="{{ $order->tracking_number }}">
            </div>
            <div class="form-group">
              <label for="admin_note">{{ __('Note') }}</label>
              <textarea class="form-control" id="admin_note" name="admin_note" rows="3">{{ $order->admin_note }}</textarea>
            </div>
            <button class="btn btn-primary" type="submit">{{ __('Save status') }}</button>
          </form>
          @endif
          <p class="text-muted mt-3 mb-0">{{ __('Confirming the order reduces stock. Cancelling or marking a confirmed order returned puts that stock back. Stock cannot go below zero.') }}</p>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@include('admin.javascript')
</body>
</html>
