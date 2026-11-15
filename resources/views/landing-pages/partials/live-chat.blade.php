{{-- Live chat bubble for visitors of landing pages and the home page. Answered from Admin > Live Chat. --}}
@php
  $lpT = $lpT ?? fn ($s) => __($s);
  $lcSlug = isset($landing_page) ? (string) $landing_page->lp_slug : '';
  $lcPage = $lcSlug !== '' ? 'landing' : 'home';
  // Inline SVG icons, so the widget does not depend on a Font Awesome version.
  $lcIcons = [
    'chat' => '<path d="M12 3C6.5 3 2 6.6 2 11c0 2.2 1.1 4.2 2.9 5.6L4 21l4.6-2.4c1.1.3 2.2.4 3.4.4 5.5 0 10-3.6 10-8s-4.5-8-10-8zM7.5 12.5a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm4.5 0a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm4.5 0a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>',
    'close' => '<path d="M18.3 5.7a1 1 0 00-1.4 0L12 10.6 7.1 5.7a1 1 0 10-1.4 1.4l4.9 4.9-4.9 4.9a1 1 0 101.4 1.4l4.9-4.9 4.9 4.9a1 1 0 001.4-1.4L13.4 12l4.9-4.9a1 1 0 000-1.4z"/>',
    'send' => '<path d="M3.4 20.4l17.4-7.5a1 1 0 000-1.8L3.4 3.6a1 1 0 00-1.4 1.1L3.8 11 14 12 3.8 13l-1.8 6.3a1 1 0 001.4 1.1z"/>',
    'headset' => '<path d="M12 2a9 9 0 00-9 9v5a3 3 0 003 3h1a1 1 0 001-1v-5a1 1 0 00-1-1H5v-1a7 7 0 0114 0v1h-2a1 1 0 00-1 1v5a1 1 0 001 1h2v.5a1.5 1.5 0 01-1.5 1.5H13a1 1 0 000 2h2.5a3.5 3.5 0 003.5-3.5V11a9 9 0 00-9-9z"/>',
  ];
  $lcIcon = fn ($n, $c = '') => '<svg class="lc-ico '.$c.'" viewBox="0 0 24 24" aria-hidden="true" focusable="false">'.$lcIcons[$n].'</svg>';
  $lcSettings = \Fickrr\Models\LiveChat::ready() ? \Fickrr\Models\LiveChat::settings() : null;
@endphp
@if($lcSettings && (int) $lcSettings->enabled === 1)
@php
  $lcText = [
    'title' => $lpT('Live chat with us'),
    'subtitle' => $lpT('Ask anything before you buy. We usually reply within minutes.'),
    'placeholder' => $lpT('Type your message…'),
    'send' => $lpT('Send'),
    'name' => $lpT('Your name (optional)'),
    'email' => $lpT('Email (optional, in case you leave the page)'),
    'error' => $lpT('Could not send. Please try again.'),
    'open' => $lpT('Live chat with us'),
    'close' => $lpT('Close'),
    'you' => $lpT('You'),
    'team' => $lpT('Support'),
    'waiting' => $lpT('Message sent. Keep this page open — our reply will appear here.'),
  ];
  $lcWelcome = trim((string) ($lcSettings->welcome_message ?? ''));
  if ($lcWelcome !== '') { $lcWelcome = $lpT($lcWelcome); }
@endphp
<div id="lc-root" class="lc-root lc-on-{{ $lcPage }}" data-slug="{{ $lcSlug }}" data-page="{{ $lcPage }}"
     data-send="{{ url('/live-chat/send') }}" data-poll="{{ url('/live-chat/poll') }}">
  <div class="lc-panel" id="lc-panel" role="dialog" aria-label="{{ $lcText['title'] }}" hidden>
    <div class="lc-head">
      <div class="lc-avatar">{!! $lcIcon('headset') !!}<span class="lc-online"></span></div>
      <div class="lc-head-text">
        <strong>{{ $lcText['title'] }}</strong>
        <span>{{ $lcText['subtitle'] }}</span>
      </div>
      <button type="button" class="lc-x" id="lc-close" aria-label="{{ $lcText['close'] }}">{!! $lcIcon('close') !!}</button>
    </div>
    <div class="lc-body" id="lc-body" aria-live="polite">
      @if($lcWelcome !== '')
      <div class="lc-msg lc-admin"><div class="lc-bubble">{{ $lcWelcome }}</div></div>
      @endif
    </div>
    <div class="lc-note" id="lc-note" hidden></div>
    <form class="lc-form" id="lc-form" autocomplete="on">
      <div class="lc-who" id="lc-who">
        <input type="text" id="lc-name" maxlength="100" placeholder="{{ $lcText['name'] }}" autocomplete="name">
        <input type="email" id="lc-email" maxlength="150" placeholder="{{ $lcText['email'] }}" autocomplete="email">
      </div>
      <div class="lc-compose">
        <textarea id="lc-input" rows="1" maxlength="2000" placeholder="{{ $lcText['placeholder'] }}" required></textarea>
        <button type="submit" class="lc-send" id="lc-send" aria-label="{{ $lcText['send'] }}">{!! $lcIcon('send') !!}</button>
      </div>
    </form>
  </div>
  <button type="button" class="lc-launcher" id="lc-launcher" aria-label="{{ $lcText['open'] }}" aria-controls="lc-panel" aria-expanded="false">
    {!! $lcIcon('chat', 'lc-ico-open') !!}{!! $lcIcon('close', 'lc-ico-close') !!}
    <span class="lc-badge" id="lc-badge" hidden>0</span>
    <span class="lc-label">{{ $lcText['open'] }}</span>
  </button>
</div>
<style>
.lc-root{--lc-accent:var(--lp-accent,#4f46e5);--lc-accent-dark:var(--lp-accent-dark,#3730a3);position:fixed;bottom:20px;right:20px;z-index:1040;font-family:inherit}
.is-rtl .lc-root{right:auto;left:20px}
.lc-launcher{position:relative;display:flex;align-items:center;gap:10px;height:56px;padding:0 20px 0 18px;border:0;border-radius:999px;background:var(--lc-accent);color:#fff;font-weight:700;font-size:15px;box-shadow:0 10px 30px rgba(0,0,0,.25);cursor:pointer;transition:transform .15s ease,background .15s ease;margin-left:auto}
.is-rtl .lc-launcher{margin-left:0;margin-right:auto}
.lc-launcher:hover{transform:translateY(-2px);background:var(--lc-accent-dark)}
.lc-ico{width:1em;height:1em;fill:currentColor;display:inline-block;vertical-align:middle}
.lc-launcher .lc-ico{width:22px;height:22px}
.lc-on-home{--lc-accent:#7c3aed;--lc-accent-dark:#db2777;--lp-accent-rgb:124,58,237}
.lc-launcher .lc-ico-close{display:none}
.lc-root.is-open .lc-launcher{width:56px;padding:0;justify-content:center}
.lc-root.is-open .lc-launcher .lc-ico-open,.lc-root.is-open .lc-launcher .lc-label{display:none}
.lc-root.is-open .lc-launcher .lc-ico-close{display:inline-block}
.lc-badge{position:absolute;top:-4px;right:-4px;min-width:22px;height:22px;padding:0 6px;border-radius:999px;background:#ef4444;color:#fff;font-size:12px;font-weight:800;line-height:22px;text-align:center;border:2px solid #fff}
.lc-panel{position:absolute;bottom:72px;right:0;width:360px;max-width:calc(100vw - 32px);height:520px;max-height:calc(100vh - 110px);display:flex;flex-direction:column;background:#fff;color:#111827;border-radius:18px;box-shadow:0 24px 60px rgba(0,0,0,.28);overflow:hidden;animation:lc-in .18s ease-out}
.is-rtl .lc-panel{right:auto;left:0}
.lc-panel[hidden]{display:none}
@keyframes lc-in{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.lc-head{display:flex;align-items:center;gap:12px;padding:16px;background:linear-gradient(135deg,var(--lc-accent),var(--lc-accent-dark));color:#fff}
.lc-avatar{position:relative;flex:0 0 42px;width:42px;height:42px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:18px}
.lc-online{position:absolute;bottom:1px;right:1px;width:11px;height:11px;border-radius:50%;background:#22c55e;border:2px solid #fff}
.lc-head-text{flex:1;min-width:0;display:flex;flex-direction:column;line-height:1.3}
.lc-head-text strong{font-size:16px}
.lc-head-text span{font-size:12.5px;opacity:.9}
.lc-x{background:transparent;border:0;color:#fff;font-size:18px;opacity:.85;cursor:pointer;padding:4px}
.lc-x:hover{opacity:1}
.lc-body{flex:1;overflow-y:auto;padding:16px;background:#f6f7fb;display:flex;flex-direction:column;gap:8px}
.lc-msg{display:flex;flex-direction:column;max-width:85%}
.lc-msg.lc-visitor{align-self:flex-end;align-items:flex-end}
.lc-msg.lc-admin{align-self:flex-start;align-items:flex-start}
.is-rtl .lc-msg.lc-visitor{align-self:flex-start;align-items:flex-start}
.is-rtl .lc-msg.lc-admin{align-self:flex-end;align-items:flex-end}
.lc-bubble{padding:9px 13px;border-radius:16px;font-size:14px;line-height:1.45;white-space:pre-wrap;word-break:break-word}
.lc-admin .lc-bubble{background:#fff;border:1px solid #e5e7eb;border-bottom-left-radius:4px}
.lc-visitor .lc-bubble{background:var(--lc-accent);color:#fff;border-bottom-right-radius:4px}
.lc-time{font-size:11px;color:#9ca3af;margin-top:3px;padding:0 4px}
.lc-note{padding:8px 14px;font-size:12.5px;background:#fffbeb;color:#92400e;border-top:1px solid #fde68a}
.lc-note.is-error{background:#fef2f2;color:#991b1b;border-color:#fecaca}
.lc-form{border-top:1px solid #e5e7eb;padding:10px;background:#fff}
.lc-who{display:flex;gap:6px;margin-bottom:8px}
.lc-who[hidden]{display:none}
.lc-who input{flex:1;min-width:0;border:1px solid #e5e7eb;border-radius:10px;padding:8px 10px;font-size:13px;outline:none}
.lc-compose{display:flex;align-items:flex-end;gap:8px}
.lc-compose textarea{flex:1;resize:none;border:1px solid #e5e7eb;border-radius:12px;padding:10px 12px;font-size:14px;line-height:1.4;max-height:110px;outline:none;font-family:inherit}
.lc-who input:focus,.lc-compose textarea:focus{border-color:var(--lc-accent);box-shadow:0 0 0 3px rgba(var(--lp-accent-rgb,79,70,229),.15)}
.lc-send{flex:0 0 42px;width:42px;height:42px;border:0;border-radius:50%;background:var(--lc-accent);color:#fff;font-size:16px;cursor:pointer}
.lc-send:disabled{opacity:.5;cursor:default}
.is-rtl .lc-send .lc-ico{transform:scaleX(-1)}
@media (max-width:575.98px){
  .lc-root{bottom:14px;right:14px}
  .is-rtl .lc-root{left:14px}
  .lc-launcher{height:52px;padding:0 16px}
  .lc-launcher .lc-label{display:none}
  .lc-root:not(.is-open) .lc-launcher{width:52px;padding:0;justify-content:center}
  .lc-root.is-open{top:0;left:0;right:0;bottom:0}
  .lc-root.is-open .lc-panel{position:fixed;inset:0;width:100%;max-width:none;height:100%;max-height:none;border-radius:0}
  .lc-root.is-open .lc-launcher{display:none}
  .lc-who{flex-direction:column}
  .lc-who input,.lc-compose textarea{font-size:16px}
}
</style>
<script>
(function () {
  var root = document.getElementById('lc-root');
  if (!root || !window.fetch) return;
  var T = @json($lcText);
  var slug = root.getAttribute('data-slug');
  var sendUrl = root.getAttribute('data-send');
  var pollUrl = root.getAttribute('data-poll');
  var page = root.getAttribute('data-page') || 'landing';
  var key = 'lc_token_' + (slug || page);
  var store = {
    get: function (k) { try { return localStorage.getItem(k) || ''; } catch (e) { return ''; } },
    set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) {} }
  };
  var token = store.get(key);
  var lastId = 0, open = false, timer = null, sending = false;
  var panel = document.getElementById('lc-panel');
  var body = document.getElementById('lc-body');
  var form = document.getElementById('lc-form');
  var input = document.getElementById('lc-input');
  var who = document.getElementById('lc-who');
  var note = document.getElementById('lc-note');
  var badge = document.getElementById('lc-badge');
  var launcher = document.getElementById('lc-launcher');
  var sendBtn = document.getElementById('lc-send');
  if (token) who.hidden = true;

  function fmtTime(iso) {
    var d = new Date(iso);
    if (isNaN(d)) return '';
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }
  function addMessages(list) {
    var added = false;
    (list || []).forEach(function (m) {
      if (m.id <= lastId) return;
      lastId = m.id;
      var wrap = document.createElement('div');
      wrap.className = 'lc-msg ' + (m.sender === 'admin' ? 'lc-admin' : 'lc-visitor');
      var b = document.createElement('div');
      b.className = 'lc-bubble';
      b.textContent = m.body;
      var t = document.createElement('div');
      t.className = 'lc-time';
      t.textContent = (m.sender === 'admin' ? T.team : T.you) + ' · ' + fmtTime(m.at);
      wrap.appendChild(b); wrap.appendChild(t);
      body.appendChild(wrap);
      if (m.sender === 'admin') note.hidden = true;
      added = true;
    });
    if (added) body.scrollTop = body.scrollHeight;
  }
  function setBadge(n) {
    badge.hidden = !(n > 0);
    badge.textContent = n > 9 ? '9+' : String(n);
  }
  function showNote(text, isError) {
    note.textContent = text;
    note.className = 'lc-note' + (isError ? ' is-error' : '');
    note.hidden = false;
  }
  function schedule() {
    clearTimeout(timer);
    if (!token) return;
    timer = setTimeout(poll, open ? 4000 : 20000);
  }
  function poll() {
    if (!token) return;
    fetch(pollUrl + '?token=' + encodeURIComponent(token) + '&after=' + lastId + (open ? '&open=1' : ''), { credentials: 'same-origin', cache: 'no-store' })
      .then(function (r) {
        if (r.status === 404) { token = ''; store.set(key, ''); who.hidden = false; return null; }
        return r.ok ? r.json() : null;
      })
      .then(function (d) {
        if (d && d.ok) {
          addMessages(d.messages);
          if (!open) setBadge(d.unread || 0);
        }
      })
      .catch(function () {})
      .then(schedule);
  }
  function toggle(force) {
    open = typeof force === 'boolean' ? force : !open;
    panel.hidden = !open;
    root.classList.toggle('is-open', open);
    launcher.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) {
      setBadge(0);
      body.scrollTop = body.scrollHeight;
      setTimeout(function () { input.focus(); }, 50);
      if (token) poll();
    } else {
      schedule();
    }
  }
  launcher.addEventListener('click', function () { toggle(); });
  document.getElementById('lc-close').addEventListener('click', function () { toggle(false); });
  input.addEventListener('input', function () {
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 110) + 'px';
  });
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); form.requestSubmit ? form.requestSubmit() : form.dispatchEvent(new Event('submit', { cancelable: true })); }
  });
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var text = input.value.trim();
    if (!text || sending) return;
    sending = true; sendBtn.disabled = true;
    var data = new URLSearchParams();
    data.set('message', text);
    data.set('lp_slug', slug);
    data.set('page', page);
    data.set('after', String(lastId));
    if (token) {
      data.set('token', token);
    } else {
      data.set('name', document.getElementById('lc-name').value.trim());
      data.set('email', document.getElementById('lc-email').value.trim());
    }
    var first = !token;
    fetch(sendUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' }, body: data.toString(), credentials: 'same-origin' })
      .then(function (r) { return r.json().catch(function () { return {}; }); })
      .then(function (d) {
        if (!d || !d.ok) throw new Error('send');
        token = d.token; store.set(key, token);
        who.hidden = true;
        input.value = ''; input.style.height = 'auto';
        addMessages(d.messages);
        if (first) {
          showNote(T.waiting, false);
          if (typeof window.lpTrack === 'function') window.lpTrack('live_chat_started');
        }
        schedule();
      })
      .catch(function () { showNote(T.error, true); })
      .then(function () { sending = false; sendBtn.disabled = false; });
  });
  if (token) poll();
})();
</script>
@endif
