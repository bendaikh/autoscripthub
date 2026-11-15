@php
    $lpT = [\Fickrr\Helpers\LandingPageI18n::class, 't'];
    $lpLang = \Fickrr\Helpers\LandingPageI18n::locale();
    // $cp: page data built by the landing page or shop checkout controller
    $status = $cp['status'];
    $details = $cp['details'] ?? [];
    $amount = rtrim(rtrim(number_format((float) $cp['amount'], 2, '.', ''), '0'), '.');
    $m = $method ?: ['key' => $cp['method'], 'name' => 'Crypto', 'qr' => null, 'id' => '', 'link' => '', 'note' => '', 'enabled' => false];
    $isWallet = $m['key'] === 'usdt';
    $isFiat = in_array($m['key'], \Fickrr\Models\CryptoPay::FIAT, true);
    // Wise is a normal money transfer: the buyer pays the order currency, not USDT
    $asset = $isFiat ? strtoupper($cp['currency'] ?: 'USD') : ($isWallet ? ($m['asset'] ?? 'USDT') : 'USDT');
    $appName = ['kast' => 'KAST', 'wise' => 'Wise'][$m['key']] ?? 'Binance';
    $idLabel = ['kast' => $lpT('KAST tag'), 'wise' => $lpT('Wisetag')][$m['key']] ?? $lpT('Binance Pay ID');
    // Prices in another currency are shown as-is with a note to send the USDT equivalent
    $unit = !empty($cp['currency']) && strtoupper($cp['currency']) !== 'USD' ? strtoupper($cp['currency']) : $asset;
@endphp
<!DOCTYPE html>
<html lang="{{ $lpLang }}" dir="{{ $lpLang === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $lpT('Complete your payment') }} — {{ $cp['title'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root { --lp-accent: {{ $cp['accent'] }}; --lp-accent-dark: {{ $cp['accentDark'] }}; --lp-accent-rgb: {{ $cp['accentRgb'] }};
                --ink: #0b1020; --muted: #64748b; --line: #e8ecf3; --soft: #f6f7fb; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--soft); color: #334155; font-family: Inter, 'Noto Sans Arabic', system-ui, sans-serif; line-height: 1.55; }
        h1, h2, h3, strong.big { font-family: 'Plus Jakarta Sans', Inter, sans-serif; color: var(--ink); }
        .cp-top { background: var(--ink); color: #fff; }
        .cp-top-in { max-width: 960px; margin: 0 auto; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .cp-top a { color: rgba(255,255,255,.8); text-decoration: none; font-size: .9rem; display: inline-flex; align-items: center; gap: .45rem; }
        .cp-top a:hover { color: #fff; }
        .cp-secure { color: #4ade80; font-size: .88rem; font-weight: 600; display: inline-flex; gap: .4rem; align-items: center; }
        .cp-wrap { max-width: 960px; margin: 0 auto; padding: 28px 18px 60px; }
        .cp-title { margin: 0 0 4px; font-size: clamp(1.4rem, 3vw, 1.9rem); }
        .cp-sub { margin: 0 0 22px; color: var(--muted); }
        .cp-steps { display: flex; gap: 8px; margin: 0 0 22px; padding: 0; list-style: none; flex-wrap: wrap; }
        .cp-steps li { flex: 1 1 0; min-width: 150px; display: flex; align-items: center; gap: .55rem; background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: .6rem .8rem; font-size: .88rem; font-weight: 600; color: var(--muted); }
        .cp-steps li b { flex: 0 0 26px; width: 26px; height: 26px; border-radius: 50%; display: grid; place-items: center; background: var(--soft); color: var(--muted); font-size: .8rem; }
        .cp-steps li.on { color: var(--ink); border-color: rgba(var(--lp-accent-rgb), .5); }
        .cp-steps li.on b { background: linear-gradient(135deg, var(--lp-accent), var(--lp-accent-dark)); color: #fff; }
        .cp-steps li.done b { background: #22c55e; color: #fff; }
        .cp-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 18px; align-items: start; }
        .cp-card { background: #fff; border: 1px solid var(--line); border-radius: 18px; padding: 22px; box-shadow: 0 18px 44px rgba(15, 23, 42, .06); }
        .cp-card h2 { font-size: 1.1rem; margin: 0 0 14px; display: flex; align-items: center; gap: .55rem; }
        .cp-num { flex: 0 0 26px; height: 26px; border-radius: 50%; display: inline-grid; place-items: center; background: linear-gradient(135deg, var(--lp-accent), var(--lp-accent-dark)); color: #fff; font-size: .8rem; }
        .cp-amount { display: flex; align-items: center; justify-content: space-between; gap: 10px; background: var(--soft); border-radius: 14px; padding: 12px 14px; margin-bottom: 16px; }
        .cp-amount small { display: block; color: var(--muted); font-size: .78rem; text-transform: uppercase; letter-spacing: .05em; font-weight: 700; }
        .cp-amount strong { font-size: 1.6rem; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--ink); }
        .cp-qr { display: block; width: 100%; max-width: 260px; margin: 0 auto 14px; border-radius: 14px; border: 1px solid var(--line); background: #fff; padding: 8px; }
        #cpQr { width: 220px; height: 220px; margin: 0 auto 14px; padding: 10px; background: #fff; border: 1px solid var(--line); border-radius: 14px; }
        #cpQr img, #cpQr canvas { width: 100% !important; height: auto !important; }
        .cp-field { margin-bottom: 12px; }
        .cp-field label { display: block; font-size: .8rem; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
        .cp-copy { display: flex; align-items: center; gap: 8px; background: var(--soft); border: 1px solid var(--line); border-radius: 12px; padding: 8px 8px 8px 12px; }
        .cp-copy code { flex: 1; min-width: 0; font-size: .92rem; color: var(--ink); word-break: break-all; font-family: ui-monospace, Menlo, Consolas, monospace; }
        .cp-btn-copy { flex: 0 0 auto; border: 0; border-radius: 9px; background: var(--ink); color: #fff; font-weight: 700; font-size: .8rem; padding: .5rem .75rem; cursor: pointer; }
        .cp-btn-copy.ok { background: #16a34a; }
        .cp-warn { background: #fff7ed; border: 1px solid #fed7aa; color: #9a3412; border-radius: 12px; padding: 10px 12px; font-size: .88rem; margin-top: 12px; }
        .cp-note { background: var(--soft); border-radius: 12px; padding: 10px 12px; font-size: .88rem; margin-top: 12px; }
        .cp-app { display: flex; align-items: center; justify-content: center; gap: .5rem; width: 100%; margin-top: 12px; padding: .8rem 1rem; border-radius: 12px; background: var(--ink); color: #fff; font-weight: 700; text-decoration: none; }
        .cp-drop { position: relative; display: block; border: 2px dashed #cbd5e1; border-radius: 14px; padding: 22px 14px; text-align: center; cursor: pointer; background: var(--soft); transition: border-color .2s, background .2s; }
        .cp-field label.cp-drop { text-transform: none; letter-spacing: 0; font-size: 1rem; font-weight: 500; margin: 0; }
        .cp-drop:hover, .cp-drop.drag { border-color: var(--lp-accent); background: rgba(var(--lp-accent-rgb), .05); }
        .cp-drop input { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; }
        .cp-drop i { font-size: 1.8rem; color: var(--lp-accent); }
        .cp-drop strong { display: block; color: var(--ink); margin-top: 6px; }
        .cp-drop small { color: var(--muted); }
        .cp-preview { display: none; margin-top: 10px; align-items: center; gap: 10px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 8px 10px; font-size: .88rem; color: #166534; word-break: break-all; }
        .cp-preview img { width: 54px; height: 54px; object-fit: cover; border-radius: 8px; }
        .cp-input { width: 100%; border: 1px solid #d1d5db; border-radius: 12px; padding: .7rem .85rem; font: inherit; font-size: .95rem; }
        .cp-input:focus { outline: 0; border-color: var(--lp-accent); box-shadow: 0 0 0 3px rgba(var(--lp-accent-rgb), .15); }
        .cp-submit { width: 100%; border: 0; border-radius: 14px; padding: 1rem; margin-top: 6px; font-weight: 800; font-size: 1rem; color: #fff; cursor: pointer; background: linear-gradient(135deg, var(--lp-accent), var(--lp-accent-dark)); box-shadow: 0 14px 30px rgba(var(--lp-accent-rgb), .3); }
        .cp-submit:disabled { opacity: .6; cursor: wait; }
        .cp-err { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; border-radius: 12px; padding: 10px 12px; margin-bottom: 14px; font-size: .9rem; }
        .cp-wait { text-align: center; padding: 10px 4px; }
        .cp-spin { width: 54px; height: 54px; border-radius: 50%; border: 4px solid rgba(var(--lp-accent-rgb), .2); border-top-color: var(--lp-accent); margin: 4px auto 14px; animation: cpspin 1s linear infinite; }
        @keyframes cpspin { to { transform: rotate(360deg); } }
        .cp-wait h2 { justify-content: center; }
        .cp-wait p { margin: 0 0 8px; }
        .cp-link { background: none; border: 0; color: var(--lp-accent-dark); font-weight: 700; cursor: pointer; text-decoration: underline; font: inherit; }
        .cp-foot { text-align: center; color: var(--muted); font-size: .85rem; margin-top: 22px; }
        @media (max-width: 760px) {
            .cp-grid { grid-template-columns: 1fr; }
            .cp-steps { gap: 6px; }
            .cp-steps li { min-width: 0; flex-direction: column; text-align: center; gap: .3rem; font-size: .74rem; line-height: 1.25; padding: .55rem .35rem; }
        }
        @media (prefers-reduced-motion: reduce) { .cp-spin { animation: none; } }
    </style>
</head>
<body class="{{ $lpLang === 'ar' ? 'is-rtl' : '' }}">
<header class="cp-top">
    <div class="cp-top-in">
        <a href="{{ $cp['backUrl'] }}"><i class="fas fa-arrow-{{ $lpLang === 'ar' ? 'right' : 'left' }}"></i> {{ $cp['title'] }}</a>
        <span class="cp-secure"><i class="fas fa-lock"></i> {{ $lpT('Secure checkout') }}</span>
    </div>
</header>

<main class="cp-wrap">
    <h1 class="cp-title">{{ $lpT('Complete your payment') }}</h1>
    <p class="cp-sub">{{ $lpT('Pay with :method, then upload your payment receipt. Your download unlocks as soon as we confirm it.', ['method' => $m['name']]) }}</p>

    <ol class="cp-steps">
        <li class="{{ $status === 'awaiting' ? 'done' : 'on' }}"><b>1</b> <span>{{ $lpT('Pay') }}</span></li>
        <li class="{{ $status === 'awaiting' ? 'done' : 'on' }}"><b>2</b> <span>{{ $lpT('Upload receipt') }}</span></li>
        <li class="{{ $status === 'awaiting' ? 'on' : '' }}"><b>3</b> <span>{{ $lpT('Get your download') }}</span></li>
    </ol>

    <div class="cp-grid">
        {{-- 1. how to pay --}}
        <section class="cp-card">
            <h2><span class="cp-num">1</span> {{ $lpT('Send the payment') }}</h2>
            <div class="cp-amount">
                <div>
                    <small>{{ $lpT('Amount to send') }}</small>
                    <strong>{{ $amount }} {{ $unit }}</strong>
                </div>
                <button type="button" class="cp-btn-copy" data-copy="{{ $amount }}">{{ $lpT('Copy') }}</button>
            </div>
            @if($unit !== $asset)
            <div class="cp-note" style="margin:-6px 0 14px"><i class="fas fa-exchange-alt"></i> {{ $lpT('Send the equivalent of this amount in :asset.', ['asset' => $asset]) }}</div>
            @endif

            @if($isWallet)
                <div id="cpQr" aria-label="QR"></div>
                <div class="cp-field">
                    <label>{{ $lpT('Network') }}</label>
                    <div class="cp-copy"><code>{{ $m['network'] ?: '—' }}</code></div>
                </div>
                <div class="cp-field">
                    <label>{{ $lpT(':asset address', ['asset' => $asset]) }}</label>
                    <div class="cp-copy"><code id="cpAddress">{{ $m['address'] }}</code><button type="button" class="cp-btn-copy" data-copy="{{ $m['address'] }}">{{ $lpT('Copy') }}</button></div>
                </div>
                <div class="cp-warn"><i class="fas fa-exclamation-triangle"></i>
                    {{ $lpT('Send only :asset on the :network network. Payments sent with another coin or network cannot be recovered.', ['asset' => $asset, 'network' => $m['network'] ?: '—']) }}
                </div>
            @else
                @if($m['qr'])
                <img src="{{ $m['qr'] }}" alt="{{ $m['name'] }} QR" class="cp-qr">
                <p style="text-align:center;margin:-4px 0 14px;font-size:.88rem;color:var(--muted)">{{ $lpT('Scan with the :app app to pay', ['app' => $appName]) }}</p>
                @endif
                @if($m['id'] !== '')
                <div class="cp-field">
                    <label>{{ $idLabel }}</label>
                    <div class="cp-copy"><code>{{ $m['id'] }}</code><button type="button" class="cp-btn-copy" data-copy="{{ $m['id'] }}">{{ $lpT('Copy') }}</button></div>
                </div>
                @endif
                @if($m['link'] !== '')
                <a href="{{ $m['link'] }}" class="cp-app" target="_blank" rel="noopener"><i class="fas fa-external-link-alt"></i> {{ $lpT('Open in the :app app', ['app' => $appName]) }}</a>
                @endif
            @endif
            @if(!empty($m['note']))
            <div class="cp-note"><i class="fas fa-info-circle"></i> {{ $m['note'] }}</div>
            @endif
        </section>

        {{-- 2. receipt / waiting --}}
        <section class="cp-card">
            @if($status === 'awaiting')
            <div class="cp-wait" id="cpWaiting">
                <div class="cp-spin"></div>
                <h2>{{ $lpT('We are checking your payment') }}</h2>
                <p>{{ $lpT('Thank you! Your receipt was received. This page will open your download automatically once the payment is confirmed.') }}</p>
                <p style="color:var(--muted);font-size:.9rem">{{ $lpT('We will also email the download link to :email.', ['email' => $cp['email']]) }}</p>
                <button type="button" class="cp-link" id="cpReupload">{{ $lpT('Upload a different receipt') }}</button>
            </div>
            @endif

            <form id="cpForm" action="{{ $cp['confirmUrl'] }}" method="POST" enctype="multipart/form-data" @if($status === 'awaiting') style="display:none" @endif>
                @csrf
                <h2><span class="cp-num">2</span> {{ $lpT('Upload your payment receipt') }}</h2>
                @if($status === 'rejected')
                <div class="cp-err">{{ $lpT('We could not find this payment. Please check the amount and details, then upload your receipt again or contact us in the live chat.') }}</div>
                @endif
                @if($errors->any())
                <div class="cp-err">{{ $errors->first() }}</div>
                @endif
                <div class="cp-field">
                    <label class="cp-drop" id="cpDrop">
                        <input type="file" name="receipt" id="cpReceipt" accept="image/*,application/pdf" required>
                        <i class="fas fa-cloud-upload-alt"></i>
                        <strong>{{ $lpT('Choose a screenshot or PDF of your payment') }}</strong>
                        <small>{{ $lpT('JPG, PNG or PDF — max 8 MB') }}</small>
                    </label>
                    <div class="cp-preview" id="cpPreview"></div>
                </div>
                <div class="cp-field">
                    <label for="cpRef">{{ $lpT('Transaction / order ID (optional)') }}</label>
                    <input type="text" name="reference" id="cpRef" class="cp-input" maxlength="150" value="{{ old('reference', $details['reference'] ?? '') }}" placeholder="{{ $lpT('Paste it from your app') }}">
                </div>
                <div class="cp-field">
                    <label for="cpPayer">{{ $isFiat ? $lpT('Name on the account you paid from (optional)') : $lpT('Your Binance / KAST name or wallet (optional)') }}</label>
                    <input type="text" name="payer" id="cpPayer" class="cp-input" maxlength="100" value="{{ old('payer', $details['payer'] ?? '') }}">
                </div>
                <button type="submit" class="cp-submit" id="cpSubmit"><i class="fas fa-check-circle"></i> {{ $lpT('I have paid — send receipt') }}</button>
            </form>
        </section>
    </div>

    <p class="cp-foot"><i class="fas fa-shield-alt"></i> {{ $lpT('Order :id • :email', ['id' => $cp['orderLabel'], 'email' => $cp['email']]) }}</p>
</main>

@if($isWallet && $m['address'] !== '')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
(function () {
    var el = document.getElementById('cpQr');
    if (el && window.QRCode) { new QRCode(el, { text: @json($m['address']), width: 400, height: 400, correctLevel: QRCode.CorrectLevel.M }); }
    else if (el) { el.style.display = 'none'; }
})();
</script>
@endif
<script>
(function () {
    var T = { copied: @json($lpT('Copied')), copy: @json($lpT('Copy')), sending: @json($lpT('Uploading…')), big: @json($lpT('This file is larger than 8 MB.')) };
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var text = btn.getAttribute('data-copy');
            var done = function () { btn.textContent = T.copied; btn.classList.add('ok'); setTimeout(function () { btn.textContent = T.copy; btn.classList.remove('ok'); }, 1800); };
            if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(text).then(done, function () {}); }
            else { var t = document.createElement('textarea'); t.value = text; document.body.appendChild(t); t.select(); try { document.execCommand('copy'); done(); } catch (e) {} t.remove(); }
        });
    });

    var input = document.getElementById('cpReceipt'), preview = document.getElementById('cpPreview'), drop = document.getElementById('cpDrop');
    if (input) {
        input.addEventListener('change', function () {
            var f = input.files && input.files[0];
            preview.innerHTML = '';
            if (!f) { preview.style.display = 'none'; return; }
            if (f.size > 8 * 1024 * 1024) { alert(T.big); input.value = ''; preview.style.display = 'none'; return; }
            if (/^image\//.test(f.type)) { var img = document.createElement('img'); img.src = URL.createObjectURL(f); preview.appendChild(img); }
            var name = document.createElement('span'); name.textContent = '✓ ' + f.name; preview.appendChild(name);
            preview.style.display = 'flex';
        });
        ['dragenter', 'dragover'].forEach(function (e) { drop.addEventListener(e, function () { drop.classList.add('drag'); }); });
        ['dragleave', 'drop'].forEach(function (e) { drop.addEventListener(e, function () { drop.classList.remove('drag'); }); });
    }
    var form = document.getElementById('cpForm');
    if (form) form.addEventListener('submit', function () {
        var b = document.getElementById('cpSubmit'); b.disabled = true; b.textContent = T.sending;
    });
    var re = document.getElementById('cpReupload');
    if (re) re.addEventListener('click', function () { document.getElementById('cpWaiting').style.display = 'none'; form.style.display = ''; });

    @if($status === 'awaiting')
    var statusUrl = @json($cp['statusUrl']);
    var successUrl = @json($cp['successUrl']);
    var poll = function () {
        fetch(statusUrl, { cache: 'no-store', credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (d) {
            if (d.status === 'completed') { window.location.href = successUrl; return; }
            if (d.status === 'rejected') { window.location.reload(); return; }
            setTimeout(poll, 10000);
        }).catch(function () { setTimeout(poll, 20000); });
    };
    setTimeout(poll, 8000);
    @endif
})();
</script>
@if(isset($landing_page))
@include('landing-pages.partials.live-chat')
@endif
</body>
</html>
