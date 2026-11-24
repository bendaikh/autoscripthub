{{-- ash_fix7: compact accept row under Buy Now (badges, Telegram, email, confirmation line) --}}
@php
  $btDays = (int) (($bt_item->seller_money_back ?? 0) == 1 && !empty($bt_item->seller_money_back_days) ? $bt_item->seller_money_back_days : 15);
  $btCss = base_path('public/assets/payment-badges.css');
  $btEmail = trim((string) ($allsettings->office_email ?? ''));
  if ($btEmail === '') { $btEmail = 'support@auto-script-hub.com'; }
@endphp
<link rel="stylesheet" href="{{ asset('assets/payment-badges.css') }}?v={{ is_file($btCss) ? filemtime($btCss) : 1 }}">
<style>
.ash-buy-accept{margin:.7rem 0 0;padding:.55rem .7rem;border:1px solid #e3e9ef;border-radius:.5rem;background:#fbfcfe}
.ash-buy-accept .pay-badges{margin:0;align-items:center}
.ash-buy-accept .pay-badges-list{flex-wrap:wrap}
.ash-buy-links{display:flex;flex-wrap:wrap;align-items:center;gap:.35rem .75rem;margin-top:.4rem;font-size:.8rem;line-height:1.3}
.ash-buy-links a{display:inline-flex;align-items:center;gap:.3rem;color:#1f2937;font-weight:600;text-decoration:none}
.ash-buy-links a:hover{text-decoration:underline}
.ash-buy-links .ash-tg{color:#229ED9}
.ash-buy-note{margin:.35rem 0 0;font-size:.78rem;line-height:1.35;color:#4b5563}
.ash-buy-mb{margin:.25rem 0 0;font-size:.75rem;line-height:1.3;color:#166534}
@media (max-width: 575.98px){
  .ash-buy-accept{padding:.5rem .55rem}
  .ash-buy-accept .pay-badges-label{display:block;width:100%;margin:0 0 .2rem}
  .ash-buy-links{gap:.3rem .6rem}
}
</style>
<div class="ash-buy-accept">
  @include('partials.payment-badges', ['pbLabel' => __('We accept'), 'pbAlign' => 'start'])
  <div class="ash-buy-links">
    <a class="ash-tg" href="https://t.me/autoscript_hub" target="_blank" rel="noopener">
      <svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path fill="#229ED9" d="M12 0a12 12 0 100 24 12 12 0 000-24zm5.9 8.2l-2 9.3c-.1.7-.5.8-1.1.5l-3-2.2-1.4 1.4c-.2.2-.3.3-.6.3l.2-3.1 5.6-5c.2-.2 0-.3-.4-.1l-6.9 4.3-3-.9c-.6-.2-.7-.6.1-.9l11.6-4.5c.6-.2 1 .1.9.9z"/></svg>
      Telegram @autoscript_hub
    </a>
    <a href="mailto:{{ $btEmail }}">{{ $btEmail }}</a>
  </div>
  <p class="ash-buy-note">{{ __('Payment confirmation takes a little time to verify the payment, and we contact you after purchase.') }}</p>
  <p class="ash-buy-mb">{{ __(':days-day money-back guarantee', ['days' => $btDays]) }}</p>
</div>
