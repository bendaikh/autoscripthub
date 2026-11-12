<script>
(function () {
  if (window.hvInitCardFormLoader) return;
  var LOCK_SVG = '<svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path fill="#e0a106" d="M12 2a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2h-1V7a5 5 0 0 0-5-5zm-3 8V7a3 3 0 0 1 6 0v3H9z"/></svg>';

  function nodeOf(value) {
    if (!value) return null;
    if (value.nodeType === 1) return value;
    return document.querySelector(value);
  }

  function isVisible(el) {
    if (!el) return false;
    var style = window.getComputedStyle(el);
    if (style.display === 'none' || style.visibility === 'hidden') return false;
    return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
  }

  window.hvInitCardFormLoader = function (options) {
    options = options || {};
    var texts = options.texts || {};
    var root = nodeOf(options.root) || document.body;
    var hostSelectors = options.hosts || {};
    var errorSelector = options.errorSelector || '';
    var ignoreFrameLoad = options.ignoreFrameLoad || { stripe: true };
    var sync = typeof options.sync === 'function' ? options.sync : function () {};
    var onStatus = typeof options.onStatus === 'function' ? options.onStatus : function () {};
    var getMethod = options.getMethod || function () {
      var checked = document.querySelector('input[name="payment_method"]:checked');
      return checked ? checked.value : '';
    };
    var awaiting = {};
    var readyFor = {};
    var generation = {};
    var shownFor = {};
    var watched = typeof WeakSet === 'function' ? new WeakSet() : null;
    var timer = null;
    var timerMethod = '';
    var loader = document.getElementById('hv-card-loader');

    if (!loader) {
      loader = document.createElement('div');
      loader.id = 'hv-card-loader';
      loader.className = 'hv-card-loader';
      loader.setAttribute('role', 'status');
      loader.setAttribute('aria-live', 'polite');
      loader.hidden = true;
      loader.innerHTML = '<div class="hv-card-loading"><p class="hv-card-loader-label">' + (texts.label || '') + '</p><div class="hv-card-spin" aria-hidden="true"></div><p class="hv-card-loader-title">' + (texts.title || '') + '</p><p class="hv-card-loader-sub">' + (texts.sub || '') + '</p><div class="hv-card-bar" aria-hidden="true"><span></span></div><div class="hv-card-skel"></div><div class="hv-card-skel"></div><div class="hv-card-skel"></div><div class="hv-card-skel"></div><div class="hv-card-skel-row"><div class="hv-card-skel"></div><div class="hv-card-skel"></div></div><button type="button" class="hv-card-wait" disabled>' + (texts.wait || '') + '</button><p class="hv-card-pci">' + LOCK_SVG + (texts.pci || '') + '</p></div><div class="hv-card-fail"><p>' + (texts.fail || '') + '</p><p><a href="" id="hv-card-retry">' + (texts.retry || '') + '</a></p></div>';
      root.appendChild(loader);
      var retry = loader.querySelector('#hv-card-retry');
      if (retry) {
        retry.addEventListener('click', function (event) {
          event.preventDefault();
          window.location.reload();
        });
      }
    }

    function hostFor(method) {
      return nodeOf(hostSelectors[method]);
    }

    function framesOf(host) {
      var frames = [];
      if (!host) return frames;
      var own = host.querySelectorAll ? host.querySelectorAll('iframe') : [];
      for (var i = 0; i < own.length; i++) frames.push(own[i]);
      if (host.shadowRoot) {
        var inner = host.shadowRoot.querySelectorAll('iframe');
        for (var j = 0; j < inner.length; j++) frames.push(inner[j]);
      }
      return frames;
    }

    function framesSettled(host) {
      var frames = framesOf(host);
      if (!frames.length) return false;
      for (var i = 0; i < frames.length; i++) {
        if (frames[i].getAttribute('data-hv-loaded') !== '1') return false;
      }
      return true;
    }

    function errorNodes() {
      if (!errorSelector) return [];
      return root.querySelectorAll(errorSelector);
    }

    function hostError() {
      var nodes = errorNodes();
      for (var i = 0; i < nodes.length; i++) {
        if (isVisible(nodes[i]) && (nodes[i].textContent || '').replace(/^\s+|\s+$/g, '') !== '') return nodes[i];
      }
      return null;
    }

    function release(method) {
      var wasWaiting = !!awaiting[method];
      awaiting[method] = false;
      if (timerMethod === method) {
        window.clearTimeout(timer);
        timer = null;
        timerMethod = '';
      }
      var host = hostFor(method);
      var changed = false;
      if (host && host.classList.contains('is-loading')) {
        host.classList.remove('is-loading');
        changed = true;
      }
      if (loader && host && loader.parentNode === host && !loader.hasAttribute('hidden')) {
        loader.hidden = true;
        changed = true;
      }
      if (wasWaiting || changed) {
        sync();
        onStatus('released', method);
      }
    }

    function showLoader(method) {
      var host = hostFor(method);
      if (!host || !isVisible(host) || getMethod() !== method) {
        if (awaiting[method]) release(method);
        return;
      }
      if (hostError()) {
        release(method);
        return;
      }
      if (!ignoreFrameLoad[method] && framesSettled(host)) {
        window.hvCardFormReady(method, generation[method] || 0);
        return;
      }
      if (readyFor[method]) return;
      var gen = generation[method] || 0;
      if (awaiting[method] && shownFor[method] === gen && !loader.hidden && loader.parentNode === host && !loader.classList.contains('is-failed')) return;
      shownFor[method] = gen;
      awaiting[method] = true;
      host.classList.add('hv-card-stage', 'is-loading');
      host.classList.remove('is-ready');
      host.insertBefore(loader, host.firstChild);
      if (loader.hasAttribute('hidden')) loader.hidden = false;
      loader.classList.remove('is-failed');
      var bar = loader.querySelector('.hv-card-bar span');
      if (bar) { bar.style.animation = 'none'; void bar.offsetWidth; bar.style.animation = ''; }
      window.clearTimeout(timer);
      timerMethod = method;
      timer = window.setTimeout(function () {
        if (!awaiting[method] || (generation[method] || 0) !== gen) return;
        loader.classList.add('is-failed');
        onStatus('timeout', method);
      }, 15000);
      sync();
      onStatus('loading', method);
    }

    window.hvCardAwaiting = function (method) {
      return !!awaiting[method];
    };
    window.hvCardFormGeneration = function (method) {
      return generation[method] || 0;
    };
    window.hvCardFormReady = function (method, gen) {
      if (!method) method = getMethod();
      if (!hostSelectors[method]) return;
      if (gen != null && gen !== (generation[method] || 0)) return;
      if (readyFor[method] && !awaiting[method]) return;
      awaiting[method] = false;
      readyFor[method] = true;
      if (timerMethod === method) {
        window.clearTimeout(timer);
        timer = null;
        timerMethod = '';
      }
      var host = hostFor(method);
      if (host) {
        host.classList.remove('is-loading');
        host.classList.add('hv-card-stage', 'is-ready');
      }
      if (loader) loader.classList.remove('is-failed');
      sync();
      onStatus('ready', method);
      if (options.revealImmediately) {
        if (loader) loader.hidden = true;
        return;
      }
      window.setTimeout(function () {
        if (awaiting[getMethod()]) return;
        if (loader && readyFor[method]) loader.hidden = true;
      }, 380);
    };

    function armFrame(iframe, method) {
      if (!iframe || iframe.getAttribute('data-hv-load') || ignoreFrameLoad[method]) return;
      iframe.setAttribute('data-hv-load', '1');
      generation[method] = (generation[method] || 0) + 1;
      readyFor[method] = false;
      var gen = generation[method];
      iframe.addEventListener('load', function () {
        iframe.setAttribute('data-hv-loaded', '1');
        window.hvCardFormReady(method, gen);
      });
    }

    function bindTree(node, method) {
      if (!node || (watched && watched.has(node))) return;
      if (watched) watched.add(node);
      var obs = new MutationObserver(function (records) {
        var hostNow = hostFor(method);
        var relevant = false;
        for (var i = 0; i < records.length; i++) {
          var target = records[i].target;
          if (!target || !hostNow) continue;
          if (loader && (target === loader || (loader.contains && loader.contains(target)))) continue;
          var inHost = target === hostNow || (hostNow.contains && hostNow.contains(target));
          var inError = errorSelector && target.closest && (target.matches(errorSelector) || target.closest(errorSelector));
          if (inHost || inError) relevant = true;
        }
        if (!relevant) return;
        if (!ignoreFrameLoad[method]) {
          var frames = framesOf(hostFor(method));
          for (var f = 0; f < frames.length; f++) armFrame(frames[f], method);
        }
        var host = hostFor(method);
        if (host) {
          var nested = host.querySelectorAll ? host.querySelectorAll('*') : [];
          for (var n = 0; n < nested.length; n++) {
            if (nested[n].shadowRoot) bindTree(nested[n].shadowRoot, method);
          }
          if (host.shadowRoot) bindTree(host.shadowRoot, method);
        }
        if (getMethod() !== method) return;
        if (!host || !isVisible(host)) {
          if (awaiting[method]) release(method);
          return;
        }
        if (hostError()) {
          release(method);
          return;
        }
        showLoader(method);
      });
      obs.observe(node, { childList: true, subtree: true, attributes: true, attributeFilter: ['style', 'class', 'hidden'] });
    }

    function scanShadows(node, method) {
      if (!node || !node.querySelectorAll) return;
      var all = node.querySelectorAll('*');
      for (var i = 0; i < all.length; i++) {
        if (!all[i].shadowRoot) continue;
        bindTree(all[i].shadowRoot, method);
        if (!ignoreFrameLoad[method]) {
          var frames = all[i].shadowRoot.querySelectorAll('iframe');
          for (var f = 0; f < frames.length; f++) armFrame(frames[f], method);
        }
        scanShadows(all[i].shadowRoot, method);
      }
    }

    function watchHost(method) {
      var host = hostFor(method);
      if (!host || host.getAttribute('data-hv-watch') === method) return;
      host.setAttribute('data-hv-watch', method);
      bindTree(host, method);
      if (errorSelector && root) {
        var errors = root.querySelectorAll(errorSelector);
        for (var e = 0; e < errors.length; e++) {
          (function (el) {
            new MutationObserver(function () {
              if (getMethod() === method && hostError()) release(method);
            }).observe(el, { attributes: true, childList: true, subtree: true, characterData: true });
          })(errors[e]);
        }
      }
      scanShadows(host, method);
      if (!ignoreFrameLoad[method]) {
        var frames = framesOf(host);
        for (var i = 0; i < frames.length; i++) armFrame(frames[i], method);
      }
      if (method === 'whop') {
        var shadowTries = 0;
        var shadowTimer = window.setInterval(function () {
          shadowTries += 1;
          var current = hostFor(method);
          if (!current || shadowTries > 40 || readyFor[method]) {
            window.clearInterval(shadowTimer);
            return;
          }
          scanShadows(current, method);
          if (!ignoreFrameLoad[method] && current.shadowRoot) {
            var nestedFrames = current.shadowRoot.querySelectorAll('iframe');
            for (var s = 0; s < nestedFrames.length; s++) armFrame(nestedFrames[s], method);
          }
        }, 250);
      }
    }

    Object.keys(hostSelectors).forEach(watchHost);

    if (options.stripeRemount && options.tileSelector) {
      var tiles = document.querySelectorAll(options.tileSelector);
      for (var t = 0; t < tiles.length; t++) {
        (function (tile) {
          tile.addEventListener('click', function () {
            var method = tile.getAttribute('data-method') || '';
            if (method !== 'stripe' || !hostFor('stripe')) return;
            generation.stripe = (generation.stripe || 0) + 1;
            readyFor.stripe = false;
            awaiting.stripe = true;
          }, true);
          tile.addEventListener('click', function () {
            var method = tile.getAttribute('data-method') || '';
            Object.keys(hostSelectors).forEach(function (name) {
              if (name !== method) awaiting[name] = false;
            });
            if (!hostSelectors[method]) {
              window.clearTimeout(timer);
              timer = null;
              timerMethod = '';
              var loading = document.querySelectorAll('.hv-card-stage.is-loading');
              for (var i = 0; i < loading.length; i++) loading[i].classList.remove('is-loading');
              if (loader) loader.hidden = true;
              sync();
              return;
            }
            window.setTimeout(function () { showLoader(method); }, 0);
          });
        })(tiles[t]);
      }
    }

    if (options.formSelector) {
      var form = document.querySelector(options.formSelector);
      if (form) {
        form.addEventListener('submit', function (event) {
          if (!awaiting[getMethod()]) return;
          event.preventDefault();
          event.stopImmediatePropagation();
        }, true);
      }
    }

    (options.immediate || []).forEach(function (method) {
      if (hostFor(method)) showLoader(method);
    });

    return {
      show: function (method) {
        readyFor[method] = false;
        showLoader(method);
      },
      ready: function (method, gen) {
        window.hvCardFormReady(method, gen);
      },
      release: function (method) {
        release(method);
      },
      fail: function (method) {
        if (!awaiting[method] || !loader) return;
        loader.classList.add('is-failed');
        onStatus('timeout', method);
      }
    };
  };
})();
</script>
