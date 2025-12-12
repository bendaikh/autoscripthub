/* global window, document, navigator, fetch */
(function () {
  'use strict';

  function safeGetConfig() {
    try {
      return window.LP_ANALYTICS_CONFIG || {};
    } catch (e) {
      return {};
    }
  }

  function uuidFallback() {
    var s = '';
    var hex = '0123456789abcdef';
    for (var i = 0; i < 32; i++) s += hex[Math.floor(Math.random() * 16)];
    return s;
  }

  function getVisitorId() {
    var key = 'lp_visitor_id_v1';
    try {
      var existing = window.localStorage.getItem(key);
      if (existing) return existing;
      var id = (window.crypto && window.crypto.randomUUID) ? window.crypto.randomUUID() : uuidFallback();
      window.localStorage.setItem(key, id);
      return id;
    } catch (e) {
      return uuidFallback();
    }
  }

  function parseUtm() {
    var out = {};
    try {
      var params = new URLSearchParams(window.location.search);
      ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function (k) {
        var v = params.get(k);
        if (v) out[k] = v;
      });
    } catch (e) {
      // ignore
    }
    return out;
  }

  function postJSON(url, payload) {
    var body = JSON.stringify(payload);

    // Prefer sendBeacon for unload/visibility events
    if (payload && payload._useBeacon && navigator && typeof navigator.sendBeacon === 'function') {
      try {
        var blob = new Blob([body], { type: 'application/json' });
        navigator.sendBeacon(url, blob);
        return;
      } catch (e) {
        // fall back to fetch
      }
    }

    if (typeof fetch === 'function') {
      try {
        fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: body,
          keepalive: true
        }).catch(function () {});
      } catch (e) {
        // ignore
      }
    }
  }

  var cfg = safeGetConfig();
  var endpoint = cfg.endpoint || '/landing/track';
  var lpId = cfg.lpId;
  var visitorId = getVisitorId();
  var utm = parseUtm();

  var startTs = (window.performance && typeof window.performance.now === 'function')
    ? window.performance.now()
    : Date.now();

  function track(eventName, props, opts) {
    if (!lpId) return;
    var payload = {
      lp_id: lpId,
      visitor_id: visitorId,
      event: eventName,
      properties: props || null,
      url: String(window.location.href || ''),
      referrer: String(document.referrer || '')
    };

    // attach utm only when present
    for (var k in utm) payload[k] = utm[k];

    if (opts && opts.useBeacon) payload._useBeacon = true;
    postJSON(endpoint, payload);
  }

  // Expose helpers globally for inline onclick hooks
  window.lpTrack = track;
  window.lpGetVisitorId = function () { return visitorId; };

  // Basic page view
  try {
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
      track('page_view', { page_type: cfg.pageType || 'landing' });
    } else {
      document.addEventListener('DOMContentLoaded', function () {
        track('page_view', { page_type: cfg.pageType || 'landing' });
      });
    }
  } catch (e) {}

  // Scroll depth tracking
  var sentDepth = {};
  function getScrollPercent() {
    var doc = document.documentElement || document.body;
    var scrollTop = window.pageYOffset || doc.scrollTop || 0;
    var height = Math.max(doc.scrollHeight, document.body ? document.body.scrollHeight : 0) - (window.innerHeight || doc.clientHeight || 0);
    if (height <= 0) return 100;
    return Math.min(100, Math.round((scrollTop / height) * 100));
  }

  function onScroll() {
    var p = getScrollPercent();
    [25, 50, 75, 90].forEach(function (t) {
      if (!sentDepth[t] && p >= t) {
        sentDepth[t] = true;
        track('scroll_depth', { percent: t });
      }
    });
  }

  try {
    window.addEventListener('scroll', onScroll, { passive: true });
  } catch (e) {
    window.addEventListener('scroll', onScroll);
  }

  // Exit / time-on-page
  function sendExit() {
    var nowTs = (window.performance && typeof window.performance.now === 'function')
      ? window.performance.now()
      : Date.now();
    var durationMs = Math.max(0, Math.round(nowTs - startTs));
    track('page_exit', { duration_ms: durationMs, page_type: cfg.pageType || 'landing' }, { useBeacon: true });
  }

  try {
    document.addEventListener('visibilitychange', function () {
      if (document.visibilityState === 'hidden') sendExit();
    });
    window.addEventListener('pagehide', sendExit);
    window.addEventListener('beforeunload', sendExit);
  } catch (e) {}
})();


