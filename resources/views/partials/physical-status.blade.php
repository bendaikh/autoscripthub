@php
  $status = $order->fulfillment_status ?? 'pending';
  $colors = [
    'pending' => '#b45309',
    'confirmed' => '#1d4ed8',
    'shipped' => '#0f766e',
    'delivered' => '#047857',
    'cancelled' => '#b91c1c',
    'returned' => '#6b7280',
  ];
@endphp
<span style="display:inline-block;padding:0.15rem 0.55rem;border-radius:999px;background:{{ $colors[$status] ?? '#374151' }};color:#fff;font-size:12px;letter-spacing:.02em;">
  {{ \Fickrr\Services\Physical\PhysicalOrderService::statusLabel($status) }}
</span>
