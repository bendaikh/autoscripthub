<!doctype html>
<html>
<body style="margin:0;padding:24px;background:#f6f7fb;font-family:Arial,Helvetica,sans-serif;color:#0b1020">
  <div style="max-width:520px;margin:0 auto;background:#fff;border-radius:14px;padding:28px;border:1px solid #e8ecf3">
    <h2 style="margin:0 0 12px;font-size:20px">Your payment is confirmed ✅</h2>
    <p style="margin:0 0 18px;line-height:1.6;color:#334155">
      Thank you! We received your crypto payment for <strong>{{ $title }}</strong>. You can download it now:
    </p>
    <p style="margin:0 0 22px">
      <a href="{{ $link }}" style="display:inline-block;background:#f97316;color:#fff;text-decoration:none;font-weight:bold;padding:12px 22px;border-radius:10px">Download your product</a>
    </p>
    <p style="margin:0;font-size:13px;color:#64748b;line-height:1.6">
      If the button does not work, open this link: <br><a href="{{ $link }}" style="color:#f97316;word-break:break-all">{{ $link }}</a>
    </p>
    @if(!empty($site))
    <p style="margin:22px 0 0;font-size:13px;color:#94a3b8">{{ $site }}</p>
    @endif
  </div>
</body>
</html>
