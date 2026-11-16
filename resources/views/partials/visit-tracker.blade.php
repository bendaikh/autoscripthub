{{-- Records a page visit for the admin dashboard (Visits chart, Countries, Realtime). --}}
<script>
(function () {
  if (window.__hvVisit) return;
  window.__hvVisit = 1;
  if (document.cookie.indexOf('ash_noanalytics=1') !== -1) return;
  var path = location.pathname || '/';
  if (path.indexOf('/admin') === 0 || path.indexOf('/visit') === 0) return;
  var vid = '';
  try { vid = localStorage.getItem('hv_vid') || ''; } catch (e) {}
  if (!vid) {
    vid = Math.random().toString(16).slice(2) + Date.now().toString(16);
    try { localStorage.setItem('hv_vid', vid); } catch (e) {}
  }
  var body = new URLSearchParams();
  body.set('path', path.slice(0, 180));
  body.set('ref', (document.referrer || '').slice(0, 180));
  body.set('vid', vid.slice(0, 80));
  var payload = body.toString();
  var url = '{{ url('/visit') }}';
  if (navigator.sendBeacon) {
    navigator.sendBeacon(url, new Blob([payload], { type: 'application/x-www-form-urlencoded' }));
  } else {
    fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: payload, credentials: 'same-origin', keepalive: true }).catch(function () {});
  }
})();
</script>
