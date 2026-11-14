<script>
(function () {
  // A product whose image is missing or fails to load gets a tidy placeholder
  // (gradient with the product's initial) instead of a broken picture.
  function placeholder(name) {
    var letter = (String(name || '?').trim().charAt(0) || '?').toUpperCase();
    var svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 260">' +
      '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#a855f7"/><stop offset="1" stop-color="#ec4899"/></linearGradient></defs>' +
      '<rect width="400" height="260" fill="url(#g)"/>' +
      '<text x="200" y="150" text-anchor="middle" font-family="Arial, sans-serif" font-size="96" font-weight="700" fill="rgba(255,255,255,.9)">' +
      letter.replace(/[<&>"]/g, '') + '</text></svg>';
    return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
  }
  function fix(img) {
    if (img.dataset.lsFixed) return;
    img.dataset.lsFixed = '1';
    img.removeAttribute('srcset');
    img.src = placeholder(img.getAttribute('alt'));
    img.classList.add('ls-placeholder');
  }
  function watch(img) {
    if (img.dataset.lsWatch) return;
    img.dataset.lsWatch = '1';
    if (/\/no-image\.png(\?|$)/.test(img.getAttribute('src') || '')) { fix(img); return; }
    img.addEventListener('error', function () { fix(img); });
    if (img.complete && img.getAttribute('src') && !img.naturalWidth) fix(img);
  }
  function scan() {
    Array.prototype.forEach.call(document.querySelectorAll('.product-card-alt .product-thumb img'), watch);
  }
  scan();
  // infinite scroll appends more cards later
  if ('MutationObserver' in window) {
    var grid = document.getElementById('post-data') || document.body;
    new MutationObserver(scan).observe(grid, { childList: true, subtree: true });
  }
})();
</script>
