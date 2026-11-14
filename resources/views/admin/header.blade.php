<header id="header" class="header">

            <div class="header-menu">

                <div class="col-sm-7">
                    <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
                    <div class="header-left">
                      @if($custom_settings->author_key == Helper::Key_Owner())
                      <div class="dropdown for-message">
                          <button class="btn btn-secondary dropdown-toggle" type="button" id="message" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-bell"></i>
                            <span class="count bg-danger">{{ Helper::Notification_Count(1) }}</span>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="message" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                            @if(Helper::Notification_NoCount(1) != 0)
                            @foreach(Helper::Notification_Display_Limit(1) as $display)
                            <a class="dropdown-item media" href="{{ URL::to('/admin/notifications') }}/{{ $encrypter->encrypt($display->noti_id) }}">
                                <span class="photo media-left"><img alt="avatar" src="{{ Helper::IDtoFetchImage($display->noti_type,$display->noti_type_id,$display->noti_sender_id) }}"></span>
                                <span class="message media-body">
                                    <span class="name float-left"><p>{!! html_entity_decode($display->noti_message) !!}</p></span>
                                    <p class="time">{{ Helper::Timeer_Ago(strtotime($display->noti_date)) }}</p>
                                </span>
                            </a>
                            @endforeach
                            <a href="{{ URL::to('/admin/notifications') }}" style="text-align:center;"><p class="red">{{ __('View All Notifications') }}</p></a>
                            @else
                            <a class="dropdown-item media" href="{{ URL::to('/admin/notifications') }}" style="text-align:center;">{{ __('View All Notifications') }}</a>
                            @endif
                          </div>
                        </div>
                       @endif 
                    </div>
                </div>

                <div class="col-sm-5">
                    <div class="user-area dropdown float-right">
                        <a href="javascript:void();" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            
                            
                        @if(Auth::user()->user_photo != '')
                        <img class="lazy user-avatar rounded-circle" width="32" height="32" src="{{ url('/') }}/public/storage/users/{{ Auth::user()->user_photo }}"  alt="{{ Auth::user()->name }}" />@else <img class="lazy user-avatar rounded-circle" width="32" height="32" src="{{ url('/') }}/public/img/no-user.png"  alt="{{ Auth::user()->name }}"/>  @endif
                        
                        </a>
<div class="user-menu dropdown-menu">
                            <a class="nav-link" href="{{ url('/admin/edit-profile') }}"><i class="fa fa-user"></i> {{ __('My Profile') }}</a>
                            @if(in_array('settings',$avilable)) 
                            <a class="nav-link" href="{{ url('/admin/general-settings') }}"><i class="fa fa-cog"></i> {{ __('Settings') }}</a>
                            @endif
                            <a class="nav-link" href="{{ url('/logout') }}"><i class="fa fa-power-off"></i> {{ __('Logout') }}</a>
                        </div>
                        </div>
                    @php
                      $lcHeadUnread = \Fickrr\Models\LiveChat::totalUnreadForAdmin();
                      $lcHeadText = [
                        'unread' => __('unread'),
                        'none' => __('No new messages'),
                        'empty' => __('No one is waiting for a reply.'),
                        'now' => __('now'),
                        'newFrom' => __('New message from'),
                      ];
                    @endphp
                    <div class="lch dropdown float-right" id="lch">
                        <button type="button" class="lch-btn @if($lcHeadUnread > 0) has-unread @endif" id="lch-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{ __('Live Chat') }}">
                            <i class="fa fa-comments"></i>
                            <span class="lch-count" id="lch-count" @if($lcHeadUnread < 1) style="display:none" @endif>{{ $lcHeadUnread }}</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right lch-menu" aria-labelledby="lch-btn">
                            <div class="lch-menu-head">
                                <strong>{{ __('Live Chat') }}</strong>
                                <span id="lch-summary">@if($lcHeadUnread > 0){{ $lcHeadUnread }} {{ __('unread') }}@else{{ __('No new messages') }}@endif</span>
                            </div>
                            <div class="lch-list" id="lch-list">
                                <div class="lch-empty">{{ __('No one is waiting for a reply.') }}</div>
                            </div>
                            <button type="button" class="lch-alerts" id="lch-alerts" style="display:none"><i class="fa fa-bell"></i> {{ __('Turn on desktop alerts for new messages') }}</button>
                            <a class="lch-all" href="{{ url('/admin/live-chat') }}">{{ __('Open Live Chat') }} <i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>
                        @if($allsettings->multi_language == 1)
                       <div class="language-select dropdown" id="language-select">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown"  id="language" aria-haspopup="true" aria-expanded="true">
                            <span class="fa fa-language"></span> {{ $current_locale }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="language">
                            @foreach($available_locales as $locale_name => $available_locale)
                            <div class="dropdown-item">
                                <a href="{{ URL::to('/language') }}/{{ $available_locale }}">{{ $locale_name }}</a>
                            </div>
                            @endforeach
                        </div>
                        
                    </div>
                 @endif 
                    

                </div>
            </div>

        </header>
<style>
.lch{margin-right:14px;margin-top:2px}
.lch-btn{position:relative;width:38px;height:38px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;color:#4b5563;font-size:17px;cursor:pointer;transition:background .15s,color .15s}
.lch-btn:hover{background:#f3f4f6;color:#111827}
.lch-btn:focus{outline:none;box-shadow:0 0 0 3px rgba(79,70,229,.2)}
.lch-btn.has-unread{color:#fff;background:#4f46e5;border-color:#4f46e5;animation:lch-pulse 1.8s ease-out infinite}
@keyframes lch-pulse{0%{box-shadow:0 0 0 0 rgba(79,70,229,.55)}70%{box-shadow:0 0 0 10px rgba(79,70,229,0)}100%{box-shadow:0 0 0 0 rgba(79,70,229,0)}}
.lch-count{position:absolute;top:-6px;right:-6px;min-width:20px;height:20px;padding:0 5px;border-radius:999px;background:#ef4444;color:#fff;font-size:11px;font-weight:700;line-height:20px;text-align:center;border:2px solid #fff}
.lch-menu{width:340px;max-width:calc(100vw - 20px);padding:0;border-radius:12px;overflow:hidden;box-shadow:0 14px 40px rgba(0,0,0,.18);border:1px solid #e5e7eb}
.lch-menu-head{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border-bottom:1px solid #f1f2f4}
.lch-menu-head span{font-size:12px;color:#6b7280}
.lch-list{max-height:340px;overflow-y:auto}
.lch-item{display:block;padding:10px 14px;border-bottom:1px solid #f5f5f7;color:#111827!important;text-decoration:none!important}
.lch-item:hover{background:#f9fafb}
.lch-item-top{display:flex;justify-content:space-between;gap:8px}
.lch-item-name{font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lch-item-meta{font-size:11px;color:#9ca3af;white-space:nowrap}
.lch-item-page{font-size:11.5px;color:#4f46e5;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lch-item-last{font-size:13px;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.lch-item .lch-n{display:inline-block;min-width:18px;height:18px;padding:0 5px;border-radius:999px;background:#ef4444;color:#fff;font-size:10.5px;font-weight:700;line-height:18px;text-align:center;margin-left:4px}
.lch-empty{padding:22px 14px;text-align:center;color:#9ca3af;font-size:13px}
.lch-alerts{display:block;width:100%;border:0;border-top:1px solid #f1f2f4;background:#fffbeb;color:#92400e;font-size:12.5px;padding:9px 14px;text-align:left;cursor:pointer}
.lch-all{display:block;padding:11px 14px;text-align:center;font-weight:600;background:#f9fafb;color:#4f46e5!important}
</style>
<script>
(function () {
  if (!window.fetch || window.__lcHead) return;
  window.__lcHead = 1;
  var btn = document.getElementById('lch-btn');
  if (!btn) return;
  var countEl = document.getElementById('lch-count');
  var list = document.getElementById('lch-list');
  var summary = document.getElementById('lch-summary');
  var alertsBtn = document.getElementById('lch-alerts');
  var navBadge = document.getElementById('lc-nav-badge');
  var chatUrl = @json(url('/admin/live-chat'));
  var unreadUrl = @json(url('/admin/live-chat/unread'));
  var L = @json($lcHeadText);
  var onChatPage = location.pathname.indexOf('/admin/live-chat') !== -1;
  var baseTitle = document.title.replace(/^\(\d+\)\s*/, '');
  var lastCount = null, seenIds = {};

  function el(tag, cls, text) { var e = document.createElement(tag); if (cls) e.className = cls; if (text != null) e.textContent = text; return e; }
  function flag(code) {
    if (!code || code.length !== 2) return '';
    return String.fromCodePoint.apply(null, code.toUpperCase().split('').map(function (c) { return 127397 + c.charCodeAt(0); })) + ' ';
  }
  function ago(iso) {
    if (!iso) return '';
    var s = Math.floor((Date.now() - new Date(iso)) / 1000);
    if (s < 60) return L.now;
    if (s < 3600) return Math.floor(s / 60) + ' min';
    if (s < 86400) return Math.floor(s / 3600) + ' h';
    return new Date(iso).toLocaleDateString();
  }
  function beep() {
    try {
      var A = window.AudioContext || window.webkitAudioContext, ctx = new A(), o = ctx.createOscillator(), g = ctx.createGain();
      o.frequency.value = 880; g.gain.value = 0.08; o.connect(g); g.connect(ctx.destination); o.start(); o.stop(ctx.currentTime + 0.18);
    } catch (e) {}
  }
  function desktopAlert(c) {
    if (!('Notification' in window) || Notification.permission !== 'granted') return;
    try {
      var n = new Notification(L.newFrom + ' ' + c.name, { body: c.last, tag: 'lc-' + c.id });
      n.onclick = function () { window.focus(); location.href = chatUrl + '?c=' + c.id; };
    } catch (e) {}
  }
  function updateAlertsButton() {
    alertsBtn.style.display = ('Notification' in window && Notification.permission === 'default') ? '' : 'none';
  }
  alertsBtn.addEventListener('click', function (e) {
    e.preventDefault(); e.stopPropagation();
    if ('Notification' in window) Notification.requestPermission().then(updateAlertsButton);
  });

  function render(d) {
    var n = d.unread || 0;
    countEl.textContent = n > 99 ? '99+' : String(n);
    countEl.style.display = n > 0 ? '' : 'none';
    btn.classList.toggle('has-unread', n > 0);
    summary.textContent = n > 0 ? n + ' ' + L.unread : L.none;
    if (navBadge) { navBadge.textContent = n; navBadge.style.display = n > 0 ? '' : 'none'; }
    document.title = (n > 0 ? '(' + n + ') ' : '') + baseTitle;

    list.innerHTML = '';
    var waiting = d.waiting || [];
    if (!waiting.length) list.appendChild(el('div', 'lch-empty', L.empty));
    waiting.forEach(function (c) {
      var a = el('a', 'lch-item');
      a.href = chatUrl + '?c=' + c.id;
      var top = el('div', 'lch-item-top');
      top.appendChild(el('span', 'lch-item-name', flag(c.country) + c.name));
      var meta = el('span', 'lch-item-meta', ago(c.at));
      meta.appendChild(el('span', 'lch-n', String(c.unread)));
      top.appendChild(meta);
      a.appendChild(top);
      a.appendChild(el('div', 'lch-item-page', c.page || ''));
      a.appendChild(el('div', 'lch-item-last', c.last || ''));
      list.appendChild(a);
    });

    var fresh = waiting.filter(function (c) { return c.last_id && !seenIds[c.last_id]; });
    if (lastCount !== null && n > lastCount && fresh.length) {
      if (!onChatPage) beep();
      if (document.hidden || !onChatPage) desktopAlert(fresh[0]);
    }
    waiting.forEach(function (c) { seenIds[c.last_id] = 1; });
    lastCount = n;
  }
  function check() {
    fetch(unreadUrl, { credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { return r.ok ? r.json() : null; })
      .then(function (d) { if (d && d.ok) render(d); })
      .catch(function () {});
  }
  updateAlertsButton();
  check();
  setInterval(check, 15000);
})();
</script>
        
                    