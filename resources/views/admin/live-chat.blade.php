<!doctype html>
<html class="no-js" lang="en">
<head>
    @include('admin.stylesheet')
    <style>
    .lca-wrap{display:flex;height:calc(100vh - 230px);min-height:480px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden}
    .lca-list{width:320px;flex:0 0 320px;border-right:1px solid #e5e7eb;display:flex;flex-direction:column;min-width:0}
    .lca-tabs{display:flex;border-bottom:1px solid #e5e7eb}
    .lca-tabs button{flex:1;border:0;background:#fff;padding:12px;font-weight:600;color:#6b7280;cursor:pointer;border-bottom:2px solid transparent}
    .lca-tabs button.active{color:#111827;border-bottom-color:var(--ash-primary,#4f46e5)}
    .lca-items{flex:1;overflow-y:auto}
    .lca-item{display:block;width:100%;text-align:left;border:0;border-bottom:1px solid #f1f2f4;background:#fff;padding:12px 14px;cursor:pointer}
    .lca-item:hover{background:#f9fafb}
    .lca-item.active{background:var(--ash-primary-soft,#eef2ff)}
    .lca-item-top{display:flex;justify-content:space-between;align-items:center;gap:8px}
    .lca-item-name{font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .lca-item-time{font-size:11px;color:#9ca3af;white-space:nowrap}
    .lca-item-page{font-size:11.5px;color:var(--ash-primary,#4f46e5);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px}
    .lca-item-last{font-size:13px;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px}
    .lca-item.unread .lca-item-last{color:#111827;font-weight:600}
    .lca-dot{display:inline-block;min-width:20px;height:20px;padding:0 6px;border-radius:999px;background:#ef4444;color:#fff;font-size:11px;font-weight:700;line-height:20px;text-align:center}
    .lca-empty{padding:30px 16px;text-align:center;color:#9ca3af}
    .lca-chat{flex:1;display:flex;flex-direction:column;min-width:0}
    .lca-head{display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid #e5e7eb}
    .lca-head-info{flex:1;min-width:0}
    .lca-head-info strong{display:block;font-size:15px}
    .lca-head-info small{color:#6b7280}
    .lca-msgs{flex:1;overflow-y:auto;padding:16px;background:#f6f7fb;display:flex;flex-direction:column;gap:8px}
    .lca-msg{max-width:75%;display:flex;flex-direction:column}
    .lca-msg.v{align-self:flex-start;align-items:flex-start}
    .lca-msg.a{align-self:flex-end;align-items:flex-end}
    .lca-bubble{padding:9px 13px;border-radius:14px;font-size:14px;line-height:1.45;white-space:pre-wrap;word-break:break-word}
    .lca-msg.v .lca-bubble{background:#fff;border:1px solid #e5e7eb;border-bottom-left-radius:4px}
    .lca-msg.a .lca-bubble{background:var(--ash-primary,#4f46e5);color:#fff;border-bottom-right-radius:4px}
    .lca-time{font-size:11px;color:#9ca3af;margin-top:3px}
    .lca-reply{display:flex;gap:8px;padding:12px;border-top:1px solid #e5e7eb}
    .lca-reply textarea{flex:1;resize:none;border:1px solid #d1d5db;border-radius:10px;padding:10px 12px;font-size:14px;height:46px;max-height:140px}
    .lca-reply button{border-radius:10px;padding:0 20px}
    .lca-placeholder{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#9ca3af;gap:8px}
    .lca-placeholder i{font-size:42px}
    .lca-back{display:none}
    .lc-flag{display:inline-block;width:20px;height:15px;margin-right:6px;vertical-align:-2px;border-radius:2px;box-shadow:0 0 0 1px rgba(0,0,0,.08);background-size:cover}
    .lc-flag-none{box-shadow:none;width:auto;height:auto;color:#9ca3af;vertical-align:0}
    .lc-country{color:#374151;font-weight:600}
    @media (max-width:767.98px){
      .lca-wrap{height:calc(100vh - 180px)}
      .lca-list{width:100%;flex:1 1 auto;border-right:0}
      .lca-chat{display:none}
      .lca-wrap.show-chat .lca-list{display:none}
      .lca-wrap.show-chat .lca-chat{display:flex}
      .lca-back{display:inline-block}
    }
    </style>
</head>
<body>
@include('admin.navigation')
<div id="right-panel" class="right-panel">
    @include('admin.header')
    <div class="breadcrumbs">
        <div class="col-sm-6">
            <div class="page-header float-left">
                <div class="page-title">
                    <h1>{{ __('Live Chat') }}</h1>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="page-header float-right">
                <div class="page-title">
                    <label class="mb-0 mt-3" style="font-weight:normal;cursor:pointer">
                        <input type="checkbox" id="lca-sound" checked> {{ __('Sound for new messages') }}
                    </label>
                </div>
            </div>
        </div>
    </div>
    @include('admin.warning')
    <div class="content mt-3">
        <div class="animated fadeIn">
            @if(!$ready)
            <div class="alert alert-warning">{{ __('The live chat database tables do not exist yet. Run the database migration (php artisan migrate), then reload this page.') }}</div>
            @else
            @if(!$chatSettings || (int) $chatSettings->enabled !== 1)
            <div class="alert alert-info">{{ __('Live chat is switched off, so visitors do not see the chat button. Turn it on in the settings below.') }}</div>
            @endif
            <div class="lca-wrap" id="lca-wrap">
                <div class="lca-list">
                    <div class="lca-tabs">
                        <button type="button" class="active" data-status="open">{{ __('Open') }} <span class="lca-dot" id="lca-open-count" style="display:none"></span></button>
                        <button type="button" data-status="closed">{{ __('Closed') }}</button>
                    </div>
                    <div class="lca-items" id="lca-items"><div class="lca-empty">{{ __('Loading…') }}</div></div>
                </div>
                <div class="lca-chat">
                    <div class="lca-placeholder" id="lca-placeholder">
                        <i class="fa fa-comments-o"></i>
                        <div>{{ __('Choose a conversation to start chatting.') }}</div>
                        <small>{{ __('New messages from landing page visitors appear on the left automatically.') }}</small>
                    </div>
                    <div class="lca-head" id="lca-head" style="display:none">
                        <button type="button" class="btn btn-light btn-sm lca-back" id="lca-back"><i class="fa fa-arrow-left"></i></button>
                        <div class="lca-head-info">
                            <strong id="lca-name"></strong>
                            <small id="lca-meta"></small>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="lca-close"><i class="fa fa-check"></i> {{ __('Close') }}</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="lca-delete"><i class="fa fa-trash"></i></button>
                    </div>
                    <div class="lca-msgs" id="lca-msgs" style="display:none"></div>
                    <form class="lca-reply" id="lca-reply" style="display:none">
                        <textarea id="lca-input" maxlength="2000" placeholder="{{ __('Type your reply… (Enter to send, Shift+Enter for a new line)') }}"></textarea>
                        <button type="submit" class="btn btn-primary" id="lca-send"><i class="fa fa-paper-plane"></i> {{ __('Send') }}</button>
                    </form>
                </div>
            </div>
            @endif

            <div class="card mt-4">
                <div class="card-header"><strong class="card-title">{{ __('Live Chat Settings') }}</strong></div>
                @if($demo_mode == 'on')
                @include('admin.demo-mode')
                @else
                <form action="{{ route('admin.live-chat.settings') }}" method="post">
                    {{ csrf_field() }}
                @endif
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="control-label mb-1">{{ __('Chat button on landing pages') }}</label>
                                <select name="enabled" class="form-control">
                                    <option value="1" @if(($chatSettings->enabled ?? 1) == 1) selected @endif>{{ __('Show') }}</option>
                                    <option value="0" @if(($chatSettings->enabled ?? 1) == 0) selected @endif>{{ __('Hide') }}</option>
                                </select>
                            </div>
                            <div class="col-md-8 form-group">
                                <label class="control-label mb-1">{{ __('Welcome message') }}</label>
                                <input type="text" name="welcome_message" class="form-control" maxlength="500" value="{{ $chatSettings->welcome_message ?? '' }}">
                                <small class="form-text text-muted">{{ __('First message visitors see when they open the chat. Leave empty for none.') }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-sm" @if(!$ready) disabled @endif><i class="fa fa-dot-circle-o"></i> {{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('admin.javascript')
@if($ready)
@php
  $lcaText = [
    'you' => __('You'),
    'confirmDelete' => __('Delete this conversation permanently?'),
    'empty' => __('No conversations yet.'),
    'emptyClosed' => __('No closed conversations.'),
    'failed' => __('Could not send. Please try again.'),
  ];
@endphp
<script>
(function () {
  var base = @json(url('/admin/live-chat'));
  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  var T = @json($lcaText);
  var wantId = parseInt(new URLSearchParams(location.search).get('c') || '0', 10) || 0;
  var status = 'open', current = null, lastId = 0, listTimer = null, msgTimer = null, lastUnread = null, sending = false;
  var items = document.getElementById('lca-items');
  var msgs = document.getElementById('lca-msgs');
  var input = document.getElementById('lca-input');
  var wrap = document.getElementById('lca-wrap');

  function el(tag, cls, text) { var e = document.createElement(tag); if (cls) e.className = cls; if (text != null) e.textContent = text; return e; }
  function ago(iso) {
    if (!iso) return '';
    var d = new Date(iso), s = Math.floor((Date.now() - d) / 1000);
    if (s < 60) return 'now';
    if (s < 3600) return Math.floor(s / 60) + 'm';
    if (s < 86400) return Math.floor(s / 3600) + 'h';
    return d.toLocaleDateString();
  }
  function flagNode(code) {
    var f = document.createElement('span');
    if (code && /^[A-Za-z]{2}$/.test(code)) { f.className = 'flag-icon flag-icon-' + code.toLowerCase() + ' lc-flag'; }
    else { f.className = 'fa fa-globe lc-flag lc-flag-none'; }
    return f;
  }
  function beep() {
    if (!document.getElementById('lca-sound').checked) return;
    try {
      var A = window.AudioContext || window.webkitAudioContext, ctx = new A(), o = ctx.createOscillator(), g = ctx.createGain();
      o.type = 'sine'; o.frequency.value = 880; g.gain.value = 0.08;
      o.connect(g); g.connect(ctx.destination); o.start(); o.stop(ctx.currentTime + 0.18);
    } catch (e) {}
  }
  function api(url, opts) {
    opts = opts || {};
    opts.headers = Object.assign({ 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }, opts.headers || {});
    opts.credentials = 'same-origin';
    opts.cache = 'no-store';
    return fetch(url, opts).then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); });
  }

  function renderList(list) {
    items.innerHTML = '';
    if (!list.length) { items.appendChild(el('div', 'lca-empty', status === 'open' ? T.empty : T.emptyClosed)); return; }
    list.forEach(function (c) {
      var b = el('button', 'lca-item' + (c.unread ? ' unread' : '') + (current && current.id === c.id ? ' active' : ''));
      b.type = 'button';
      var top = el('div', 'lca-item-top');
      var nm = el('span', 'lca-item-name'); nm.appendChild(flagNode(c.country)); nm.appendChild(document.createTextNode(c.name));
      top.appendChild(nm);
      var right = el('span');
      if (c.unread) { right.appendChild(el('span', 'lca-dot', String(c.unread))); right.appendChild(document.createTextNode(' ')); }
      right.appendChild(el('span', 'lca-item-time', ago(c.at)));
      top.appendChild(right);
      b.appendChild(top);
      var pg = el('div', 'lca-item-page'); pg.appendChild(el('span', 'lc-country', c.country_name || ''));
      pg.appendChild(document.createTextNode(c.page ? ' · ' + c.page : ''));
      b.appendChild(pg);
      b.appendChild(el('div', 'lca-item-last', c.last || ''));
      b.addEventListener('click', function () { openConversation(c); });
      items.appendChild(b);
    });
  }
  function loadList() {
    clearTimeout(listTimer);
    api(base + '/conversations?status=' + status).then(function (d) {
      renderList(d.conversations || []);
      if (wantId) {
        var want = (d.conversations || []).filter(function (c) { return c.id === wantId; })[0];
        wantId = 0;
        if (want) openConversation(want);
      }
      var oc = document.getElementById('lca-open-count');
      oc.style.display = d.unread > 0 ? 'inline-block' : 'none';
      oc.textContent = d.unread;
      if (lastUnread !== null && d.unread > lastUnread) beep();
      lastUnread = d.unread;
      if (current) {
        var fresh = (d.conversations || []).filter(function (c) { return c.id === current.id; })[0];
        if (fresh) { current = fresh; showHeader(); }
      }
    }).catch(function () {}).then(function () { listTimer = setTimeout(loadList, 5000); });
  }

  function showHeader() {
    var nameEl = document.getElementById('lca-name');
    nameEl.innerHTML = ''; nameEl.appendChild(flagNode(current.country)); nameEl.appendChild(document.createTextNode(current.name));
    var meta = [current.country_name || ''];
    if (current.email) meta.push(current.email);
    if (current.page) meta.push(current.page + (current.path ? ' (' + current.path + ')' : ''));
    document.getElementById('lca-meta').textContent = meta.join(' · ');
    document.getElementById('lca-close').style.display = current.status === 'open' ? '' : 'none';
  }
  function addMessages(list) {
    var added = false;
    (list || []).forEach(function (m) {
      if (m.id <= lastId) return;
      lastId = m.id;
      var w = el('div', 'lca-msg ' + (m.sender === 'admin' ? 'a' : 'v'));
      w.appendChild(el('div', 'lca-bubble', m.body));
      var d = new Date(m.at);
      w.appendChild(el('div', 'lca-time', (m.sender === 'admin' ? T.you + ' · ' : '') + d.toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })));
      msgs.appendChild(w);
      added = true;
    });
    if (added) msgs.scrollTop = msgs.scrollHeight;
  }
  function loadMessages() {
    clearTimeout(msgTimer);
    if (!current) return;
    var id = current.id;
    api(base + '/' + id + '/messages?after=' + lastId).then(function (d) {
      if (current && current.id === id) addMessages(d.messages);
    }).catch(function () {}).then(function () { if (current && current.id === id) msgTimer = setTimeout(loadMessages, 3000); });
  }
  function openConversation(c) {
    current = c; lastId = 0;
    msgs.innerHTML = '';
    document.getElementById('lca-placeholder').style.display = 'none';
    document.getElementById('lca-head').style.display = '';
    msgs.style.display = '';
    document.getElementById('lca-reply').style.display = '';
    wrap.classList.add('show-chat');
    showHeader();
    Array.prototype.forEach.call(items.querySelectorAll('.lca-item'), function (n) { n.classList.remove('active'); });
    loadMessages();
    setTimeout(loadList, 400);
    input.focus();
  }
  function resetChat() {
    current = null; clearTimeout(msgTimer);
    document.getElementById('lca-placeholder').style.display = '';
    document.getElementById('lca-head').style.display = 'none';
    msgs.style.display = 'none';
    document.getElementById('lca-reply').style.display = 'none';
    wrap.classList.remove('show-chat');
  }

  document.getElementById('lca-reply').addEventListener('submit', function (e) {
    e.preventDefault();
    var text = input.value.trim();
    if (!text || !current || sending) return;
    sending = true;
    document.getElementById('lca-send').disabled = true;
    var body = new URLSearchParams(); body.set('message', text); body.set('after', String(lastId));
    api(base + '/' + current.id + '/reply', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body.toString() })
      .then(function (d) { input.value = ''; addMessages(d.messages); loadList(); })
      .catch(function () { alert(T.failed); })
      .then(function () { sending = false; document.getElementById('lca-send').disabled = false; input.focus(); });
  });
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); document.getElementById('lca-send').click(); }
  });
  document.getElementById('lca-close').addEventListener('click', function () {
    if (!current) return;
    api(base + '/' + current.id + '/close', { method: 'POST' }).then(function () { resetChat(); loadList(); });
  });
  document.getElementById('lca-delete').addEventListener('click', function () {
    if (!current || !confirm(T.confirmDelete)) return;
    api(base + '/' + current.id + '/delete', { method: 'POST' }).then(function () { resetChat(); loadList(); });
  });
  document.getElementById('lca-back').addEventListener('click', resetChat);
  Array.prototype.forEach.call(document.querySelectorAll('.lca-tabs button'), function (b) {
    b.addEventListener('click', function () {
      Array.prototype.forEach.call(document.querySelectorAll('.lca-tabs button'), function (x) { x.classList.remove('active'); });
      b.classList.add('active');
      status = b.getAttribute('data-status');
      resetChat();
      items.innerHTML = '';
      loadList();
    });
  });
  loadList();
})();
</script>
@endif
</body>
</html>
