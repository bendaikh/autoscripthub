<!doctype html>
<html class="no-js" lang="en">
<head>
    @include('admin.stylesheet')
    <style>
    .cpa-tabs{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px}
    .cpa-tabs a{padding:7px 14px;border-radius:999px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-weight:600;font-size:13px;text-decoration:none}
    .cpa-tabs a.active{background:var(--ash-primary,#f97316);border-color:var(--ash-primary,#f97316);color:#fff}
    .cpa-tabs .badge{margin-left:4px}
    .cpa-method{display:inline-flex;align-items:center;gap:6px;font-weight:700}
    .cpa-method .dot{width:10px;height:10px;border-radius:50%}
    .cpa-ref{font-family:monospace;font-size:13px;background:#f3f4f6;border-radius:6px;padding:3px 7px;word-break:break-all;display:inline-block;max-width:280px}
    .cpa-qr{width:120px;height:120px;object-fit:contain;border:1px solid #e5e7eb;border-radius:10px;background:#fff;padding:4px}
    .cpa-box{border:1px solid #e5e7eb;border-radius:12px;padding:16px;height:100%}
    .cpa-box h5{font-weight:800;margin-bottom:12px;display:flex;align-items:center;gap:8px}
    .cpa-help{background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:12px 14px;font-size:13.5px;color:#7c2d12}
    .cpa-help ol{margin:6px 0 0;padding-left:18px}
    </style>
</head>
<body>
@include('admin.navigation')
<div id="right-panel" class="right-panel">
    @include('admin.header')
    <div class="breadcrumbs">
        <div class="col-sm-12">
            <div class="page-header float-left">
                <div class="page-title"><h1>{{ __('Crypto Payments') }}</h1></div>
            </div>
        </div>
    </div>
    @include('admin.warning')
    <div class="content mt-3">
        <div class="animated fadeIn">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

            @if(!$ready)
            <div class="alert alert-warning">{{ __('The crypto payment table does not exist yet. Run the database migration (php artisan migrate), then reload this page.') }}</div>
            @else

            @php
                $cpaStatus = [];
                foreach (['binance' => $binance, 'kast' => $kast, 'usdt' => $usdt] as $cpaKey => $cpaM) {
                    $cpaFlag = $cpaKey === 'usdt' ? 'wallet_enabled' : $cpaKey . '_enabled';
                    $cpaQrCol = ['binance' => 'binance_qr', 'kast' => 'kast_qr', 'usdt' => null][$cpaKey];
                    if ($cpaM && $cpaM['enabled']) {
                        $cpaStatus[$cpaKey] = [true, __('Live: shown at checkout, on landing pages and in "We accept"')];
                    } elseif (!$crypto || (int) ($crypto->$cpaFlag ?? 0) !== 1) {
                        $cpaStatus[$cpaKey] = [false, __('Hidden: "Show to buyers" is set to No')];
                    } elseif ($cpaQrCol && !empty($crypto->$cpaQrCol) && empty($cpaM['qr'])) {
                        $cpaStatus[$cpaKey] = [false, __('Hidden: the QR image file is missing, please upload it again')];
                    } else {
                        $cpaStatus[$cpaKey] = [false, $cpaKey === 'usdt' ? __('Hidden: add the wallet address') : __('Hidden: add a QR code, ID or link')];
                    }
                }
            @endphp
            <div class="card mb-3">
                <div class="card-body py-3">
                    <strong class="d-block mb-2">{{ __('What buyers see right now') }}</strong>
                    <div class="d-flex flex-wrap" style="gap:10px">
                        @foreach(['binance' => 'Binance Pay', 'kast' => 'KAST', 'usdt' => __('USDT (any wallet)')] as $cpaKey => $cpaName)
                        <div style="flex:1 1 260px;border:1px solid {{ $cpaStatus[$cpaKey][0] ? '#bbf7d0' : '#fecaca' }};background:{{ $cpaStatus[$cpaKey][0] ? '#f0fdf4' : '#fef2f2' }};border-radius:10px;padding:10px 12px">
                            <strong>{!! $cpaStatus[$cpaKey][0] ? '&#9989;' : '&#9888;&#65039;' !!} {{ $cpaName }}</strong><br>
                            <small style="color:{{ $cpaStatus[$cpaKey][0] ? '#166534' : '#991b1b' }}">{{ $cpaStatus[$cpaKey][1] }}</small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="cpa-help mb-3">
                <strong>{{ __('How it works') }}</strong>
                <ol>
                    <li>{{ __('The buyer picks Binance Pay, KAST or USDT on a landing page or at the site checkout, then sees your QR code / address and the amount to pay.') }}</li>
                    <li>{{ __('After paying, they upload a screenshot of the payment receipt (and can add the transaction ID). The order appears below under "Waiting for you".') }}</li>
                    <li>{{ __('Look at the receipt and check that the money arrived in your Binance, KAST or wallet app, then click Approve. The buyer gets the download immediately (on the page and by email).') }}</li>
                </ol>
            </div>

            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap" style="gap:8px">
                    <strong class="card-title mb-0">{{ __('Crypto orders') }}</strong>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ url('/admin/crypto-payments?type=landing') }}" class="btn {{ $type === 'landing' ? 'btn-dark' : 'btn-outline-dark' }}">{{ __('Landing pages') }} @if($landingWaiting > 0)<span class="badge badge-danger">{{ $landingWaiting }}</span>@endif</a>
                        <a href="{{ url('/admin/crypto-payments?type=shop') }}" class="btn {{ $type === 'shop' ? 'btn-dark' : 'btn-outline-dark' }}">{{ __('Site checkout') }} @if($shopWaiting > 0)<span class="badge badge-danger">{{ $shopWaiting }}</span>@endif</a>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $tabs = ['awaiting' => __('Waiting for you'), 'pending' => __('Not paid yet'), 'completed' => __('Approved'), 'rejected' => __('Not received')];
                    @endphp
                    <div class="cpa-tabs">
                        @foreach($tabs as $key => $label)
                        <a href="{{ url('/admin/crypto-payments?type=' . $type . '&show=' . $key) }}" class="{{ $filter === $key ? 'active' : '' }}">
                            {{ $label }}
                            @if(!empty($counts[$key]))<span class="badge {{ $key === 'awaiting' ? 'badge-danger' : 'badge-light' }}">{{ $counts[$key] }}</span>@endif
                        </a>
                        @endforeach
                    </div>
                    @if($type === 'shop' && $orders->isNotEmpty())
                    <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead><tr>
                            <th>{{ __('Date') }}</th><th>{{ __('Order') }}</th><th>{{ __('Buyer') }}</th><th>{{ __('Amount') }}</th>
                            <th>{{ __('Method') }}</th><th>{{ __('Receipt') }}</th><th>{{ __('Transaction ID') }}</th><th></th>
                        </tr></thead>
                        <tbody>
                        @foreach($orders as $o)
                            <tr>
                                <td style="white-space:nowrap">{{ \Carbon\Carbon::parse($o->submitted_at ?: $o->created_at)->format('M d, H:i') }}</td>
                                <td>#{{ $o->purchase_token }}</td>
                                <td>{{ $o->email }}</td>
                                <td style="white-space:nowrap"><strong>{{ rtrim(rtrim(number_format((float) $o->amount, 2, '.', ''), '0'), '.') }} {{ $o->currency }}</strong></td>
                                <td><span class="cpa-method"><span class="dot" style="background:{{ ['binance' => '#f0b90b', 'kast' => '#111827', 'usdt' => '#26a17b'][$o->method] ?? '#999' }}"></span>{{ \Fickrr\Models\CryptoPay::NAMES[$o->method] ?? $o->method }}</span></td>
                                <td>
                                    @if(!empty($o->receipt))
                                        @php $rUrl = url('/admin/crypto-payments/shop/' . $o->id . '/receipt'); @endphp
                                        @if(\Illuminate\Support\Str::endsWith(strtolower($o->receipt), '.pdf'))
                                        <a href="{{ $rUrl }}" target="_blank" class="btn btn-light btn-sm"><i class="fa fa-file-pdf-o"></i> {{ __('Open PDF') }}</a>
                                        @else
                                        <a href="{{ $rUrl }}" target="_blank" title="{{ __('Open full size') }}"><img src="{{ $rUrl }}" alt="{{ __('Receipt') }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb"></a>
                                        @endif
                                    @else<span class="text-muted">—</span>@endif
                                </td>
                                <td>
                                    @if(!empty($o->reference))<span class="cpa-ref">{{ $o->reference }}</span>@else<span class="text-muted">—</span>@endif
                                    @if(!empty($o->payer))<br><small class="text-muted">{{ __('Paid from') }}: {{ $o->payer }}</small>@endif
                                </td>
                                <td style="white-space:nowrap">
                                    @if($o->status !== 'completed')
                                    <form action="{{ url('/admin/crypto-payments/shop/' . $o->id . '/approve') }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Did you check that the payment arrived? The buyer will get the download.') }}')">
                                        {{ csrf_field() }}
                                        <button class="btn btn-success btn-sm"><i class="fa fa-check"></i> {{ __('Approve') }}</button>
                                    </form>
                                    @endif
                                    @if(!in_array($o->status, ['completed', 'rejected'], true))
                                    <form action="{{ url('/admin/crypto-payments/shop/' . $o->id . '/reject') }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Mark this payment as not received?') }}')">
                                        {{ csrf_field() }}
                                        <button class="btn btn-outline-danger btn-sm"><i class="fa fa-times"></i> {{ __('Not received') }}</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                    @elseif($orders->isEmpty())
                    <p class="text-muted mb-0">{{ __('No orders here.') }}</p>
                    @else
                    <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead><tr>
                            <th>{{ __('Date') }}</th><th>{{ __('Product') }}</th><th>{{ __('Buyer') }}</th><th>{{ __('Amount') }}</th>
                            <th>{{ __('Method') }}</th><th>{{ __('Receipt') }}</th><th>{{ __('Transaction ID') }}</th><th></th>
                        </tr></thead>
                        <tbody>
                        @foreach($orders as $o)
                            @php $d = \Fickrr\Models\CryptoPay::details($o); @endphp
                            <tr>
                                <td style="white-space:nowrap">{{ \Carbon\Carbon::parse($d['submitted_at'] ?? $o->created_at)->format('M d, H:i') }}</td>
                                <td>{{ $o->lp_title ?: '#' . $o->lp_id }}<br><small class="text-muted">{{ $o->order_token }}</small></td>
                                <td>{{ $o->customer_email }}</td>
                                <td style="white-space:nowrap"><strong>{{ rtrim(rtrim(number_format((float) $o->amount, 2, '.', ''), '0'), '.') }} USDT</strong></td>
                                <td>
                                    <span class="cpa-method"><span class="dot" style="background:{{ ['binance' => '#f0b90b', 'kast' => '#111827', 'usdt' => '#26a17b'][$o->payment_method] ?? '#999' }}"></span>{{ \Fickrr\Models\CryptoPay::NAMES[$o->payment_method] ?? $o->payment_method }}</span>
                                </td>
                                <td>
                                    @if(!empty($d['receipt']))
                                        @php $rUrl = url('/admin/crypto-payments/' . $o->id . '/receipt'); @endphp
                                        @if(\Illuminate\Support\Str::endsWith(strtolower($d['receipt']), '.pdf'))
                                        <a href="{{ $rUrl }}" target="_blank" class="btn btn-light btn-sm"><i class="fa fa-file-pdf-o"></i> {{ __('Open PDF') }}</a>
                                        @else
                                        <a href="{{ $rUrl }}" target="_blank" title="{{ __('Open full size') }}"><img src="{{ $rUrl }}" alt="{{ __('Receipt') }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb"></a>
                                        @endif
                                    @else<span class="text-muted">—</span>@endif
                                </td>
                                <td>
                                    @if(!empty($d['reference']))<span class="cpa-ref">{{ $d['reference'] }}</span>@else<span class="text-muted">—</span>@endif
                                    @if(!empty($d['payer']))<br><small class="text-muted">{{ __('Paid from') }}: {{ $d['payer'] }}</small>@endif
                                </td>
                                <td style="white-space:nowrap">
                                    @if($o->payment_status !== 'completed')
                                    <form action="{{ url('/admin/crypto-payments/' . $o->id . '/approve') }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Did you check that the payment arrived? The buyer will get the download.') }}')">
                                        {{ csrf_field() }}
                                        <button class="btn btn-success btn-sm"><i class="fa fa-check"></i> {{ __('Approve') }}</button>
                                    </form>
                                    @endif
                                    @if(!in_array($o->payment_status, ['completed', 'rejected'], true))
                                    <form action="{{ url('/admin/crypto-payments/' . $o->id . '/reject') }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Mark this payment as not received?') }}')">
                                        {{ csrf_field() }}
                                        <button class="btn btn-outline-danger btn-sm"><i class="fa fa-times"></i> {{ __('Not received') }}</button>
                                    </form>
                                    @endif
                                    @if($o->payment_status === 'completed')
                                    <a href="{{ url('/landing/success/' . $o->order_token) }}" target="_blank" class="btn btn-light btn-sm"><i class="fa fa-link"></i> {{ __('Download page') }}</a>
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

            <div class="card mt-4">
                <div class="card-header"><strong class="card-title">{{ __('Payment details shown to buyers') }}</strong></div>
                @if($demo_mode == 'on')
                @include('admin.demo-mode')
                @else
                <form action="{{ url('/admin/crypto-payments/settings') }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                @endif
                    <div class="card-body">
                        <div class="row">
                            @foreach([
                                ['binance', 'Binance Pay', '#f0b90b', 'binance_pay_id', __('Binance Pay ID or name'), __('e.g. 123456789'), 'binance_link', __('Binance Pay link (optional)'), 'https://...'],
                                ['kast', 'KAST', '#111827', 'kast_tag', __('KAST tag'), '@yourtag', 'kast_link', __('KAST pay link'), 'https://app.kast.xyz/kasttag/yourtag'],
                            ] as [$key, $label, $color, $idField, $idLabel, $idPh, $linkField, $linkLabel, $linkPh])
                            @php $m = $key === 'binance' ? $binance : $kast; @endphp
                            <div class="col-lg-6 mb-3">
                                <div class="cpa-box">
                                    <h5><span style="width:12px;height:12px;border-radius:50%;background:{{ $color }};display:inline-block"></span> {{ $label }}</h5>
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ __('Show to buyers') }}</label>
                                        <select name="{{ $key }}_enabled" class="form-control">
                                            <option value="1" @if(($crypto->{$key . '_enabled'} ?? 1) == 1) selected @endif>{{ __('Yes') }}</option>
                                            <option value="0" @if(($crypto->{$key . '_enabled'} ?? 1) == 0) selected @endif>{{ __('No') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ __('QR code image') }}</label>
                                        <div class="d-flex align-items-start" style="gap:12px">
                                            @if(!empty($m['qr']))
                                            <img src="{{ $m['qr'] }}" alt="QR" class="cpa-qr">
                                            @endif
                                            <div style="flex:1">
                                                <input type="file" name="{{ $key }}_qr" accept="image/png,image/jpeg,image/webp" class="form-control-file">
                                                <small class="form-text text-muted">{{ __('Screenshot of your receive / pay QR code from the app (PNG, JPG, max 4 MB).') }}</small>
                                                @if(!empty($m['qr']))
                                                <label class="mt-1 mb-0" style="font-weight:normal"><input type="checkbox" name="remove_{{ $key }}_qr" value="1"> {{ __('Remove QR code') }}</label>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label mb-1">{{ $idLabel }}</label>
                                        <input type="text" name="{{ $idField }}" class="form-control" maxlength="64" placeholder="{{ $idPh }}" value="{{ old($idField, $crypto->$idField ?? '') }}">
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="control-label mb-1">{{ $linkLabel }}</label>
                                        <input type="url" name="{{ $linkField }}" class="form-control" maxlength="255" placeholder="{{ $linkPh }}" value="{{ old($linkField, $crypto->$linkField ?? '') }}">
                                        <small class="form-text text-muted">{{ __('Buyers on a phone can tap it to open the app.') }}</small>
                                    </div>
                                    @if($m && !$m['enabled'] && ($crypto->{$key . '_enabled'} ?? 1) == 1)
                                    <div class="alert alert-warning mt-3 mb-0 py-2">{{ __('Add a QR code, ID or link so buyers can pay.') }}</div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                            <div class="col-12 mb-3">
                                <div class="cpa-box">
                                    <h5><span style="width:12px;height:12px;border-radius:50%;background:#26a17b;display:inline-block"></span> {{ __('USDT from any wallet or exchange') }}</h5>
                                    <div class="row">
                                        <div class="col-md-3 form-group">
                                            <label class="control-label mb-1">{{ __('Show to buyers') }}</label>
                                            <select name="wallet_enabled" class="form-control">
                                                <option value="1" @if(($crypto->wallet_enabled ?? 1) == 1) selected @endif>{{ __('Yes') }}</option>
                                                <option value="0" @if(($crypto->wallet_enabled ?? 1) == 0) selected @endif>{{ __('No') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label class="control-label mb-1">{{ __('Asset') }}</label>
                                            <input type="text" name="wallet_asset" class="form-control" maxlength="20" placeholder="USDT" value="{{ old('wallet_asset', $crypto->wallet_asset ?? 'USDT') }}">
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label class="control-label mb-1">{{ __('Network') }}</label>
                                            <input type="text" name="wallet_network" class="form-control" maxlength="40" placeholder="Solana" value="{{ old('wallet_network', $crypto->wallet_network ?? '') }}">
                                        </div>
                                        <div class="col-md-5 form-group">
                                            <label class="control-label mb-1">{{ __('Wallet address') }}</label>
                                            <input type="text" name="wallet_address" class="form-control" maxlength="191" style="font-family:monospace" value="{{ old('wallet_address', $crypto->wallet_address ?? '') }}">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ __('Buyers see the address with a copy button and a QR code, plus a warning to send only this asset on this network.') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label class="control-label mb-1">{{ __('Extra note for buyers (optional)') }}</label>
                            <input type="text" name="note" class="form-control" maxlength="500" value="{{ old('note', $crypto->note ?? '') }}" placeholder="{{ __('e.g. Please pay in USDT. Orders are confirmed within 1 hour.') }}">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-dot-circle-o"></i> {{ __('Save') }}</button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@include('admin.javascript')
</body>
</html>
