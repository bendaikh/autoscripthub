<!doctype html>
<html>
<body style="margin:0;padding:24px;background:#f6f7fb;font-family:Arial,Helvetica,sans-serif;color:#0b1020">
  <div style="max-width:560px;margin:0 auto;background:#fff;border-radius:14px;padding:28px;border:1px solid #e8ecf3">
    <h2 style="margin:0 0 12px;font-size:20px">Thank you for your purchase! ✅</h2>
    <p style="margin:0 0 18px;line-height:1.6;color:#334155">
      Your order for <strong>{{ $title }}</strong> is ready. Download it here:
    </p>
    <p style="margin:0 0 22px">
      <a href="{{ $link }}" style="display:inline-block;background:#f97316;color:#fff;text-decoration:none;font-weight:bold;padding:12px 22px;border-radius:10px">Download your product</a>
    </p>
    @if(!empty($bonuses))
    <h3 style="margin:22px 0 10px;font-size:16px">🎁 Your FREE bonuses</h3>
    <table role="presentation" cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse">
      @foreach($bonuses as $b)
      <tr>
        <td style="padding:10px 0;border-top:1px solid #eef1f6;vertical-align:middle">
          <div style="font-size:11px;font-weight:bold;color:#f97316;letter-spacing:.04em">{{ $b['badge'] }}</div>
          <div style="font-weight:bold;font-size:14px">{{ $b['title'] }}</div>
          @if(!$b['external'] && $b['name'])<div style="font-size:12px;color:#64748b">{{ $b['name'] }}@if($b['size']) · {{ $b['size'] }}@endif</div>@endif
        </td>
        <td style="padding:10px 0;border-top:1px solid #eef1f6;text-align:right;vertical-align:middle;white-space:nowrap">
          <a href="{{ $b['url'] }}" style="display:inline-block;background:#0b1020;color:#fff;text-decoration:none;font-size:13px;font-weight:bold;padding:8px 14px;border-radius:8px">{{ $b['external'] ? 'Open' : 'Download' }}</a>
        </td>
      </tr>
      @endforeach
    </table>
    <p style="margin:10px 0 0;font-size:12px;color:#94a3b8">Bonus links in this email work for 7 days. You can always get fresh links on your download page.</p>
    @endif
    <p style="margin:22px 0 0;font-size:13px;color:#64748b;line-height:1.6">
      If the button does not work, open this link: <br><a href="{{ $link }}" style="color:#f97316;word-break:break-all">{{ $link }}</a>
    </p>
    <p style="margin:14px 0 0;font-size:12px;color:#94a3b8">Order {{ $orderToken }}@if(!empty($site)) · {{ $site }}@endif</p>
  </div>
</body>
</html>
