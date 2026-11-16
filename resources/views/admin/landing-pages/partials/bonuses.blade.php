{{-- Bonuses card (Create / Edit landing page). Saved by LandingPageController::saveBonuses(). --}}
@if(\Fickrr\Models\LandingBonus::ready())
@php
    $bnPage = $landing_page ?? null;
    $bnRows = $bnPage ? \Fickrr\Models\LandingBonus::forPage($bnPage->lp_id)->keyBy('id') : collect();
    $bnMax = \Fickrr\Models\LandingBonus::MAX_ITEMS;
    // after a failed save, show what was typed (files have to be chosen again)
    $bnOld = old('bonuses');
    $bnItems = [];
    if (is_array($bnOld)) {
        $bnOrder = (array) old('bonus_order', array_keys($bnOld));
        foreach ($bnOrder as $bnKey) {
            if (!isset($bnOld[$bnKey]) || ($bnOld[$bnKey]['remove'] ?? '0') === '1') { continue; }
            $d = $bnOld[$bnKey];
            $row = isset($d['id']) ? $bnRows->get((int) $d['id']) : null;
            $bnItems[] = ['key' => $bnKey, 'row' => $row, 'title' => $d['title'] ?? '', 'description' => $d['description'] ?? '',
                'delivery' => ($d['delivery'] ?? 'upload') === 'link' ? 'link' : 'upload', 'link' => $d['link'] ?? '',
                'value_label' => $d['value_label'] ?? '', 'badge_text' => $d['badge_text'] ?? '', 'active' => ($d['active'] ?? '1') === '1'];
        }
    } else {
        foreach ($bnRows as $row) {
            $bnItems[] = ['key' => 'b' . $row->id, 'row' => $row, 'title' => $row->title, 'description' => $row->description,
                'delivery' => $row->delivery_method === 'link' ? 'link' : 'upload', 'link' => $row->external_link,
                'value_label' => $row->value_label, 'badge_text' => $row->badge_text, 'active' => (int) $row->is_active === 1];
        }
    }
    $bnEnabled = (string) old('bonus_section_enabled', (int) ($bnPage->bonus_section_enabled ?? 0)) === '1';
    $bnBanner = $bnPage ? \Fickrr\Models\LandingBonus::imageUrl($bnPage->bonus_banner_image ?? null) : null;
@endphp
<div class="card lpb-card mb-4" id="bonuses-card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap" style="gap:8px">
        <strong class="card-title mb-0"><i class="fa fa-gift"></i> {{ __('Bonuses') }}</strong>
        <label class="lpb-switch mb-0">
            <input type="hidden" name="bonus_section_present" value="1">
            <input type="hidden" name="bonus_section_enabled" value="0">
            <input type="checkbox" name="bonus_section_enabled" value="1" @if($bnEnabled) checked @endif>
            <span>{{ __('Show bonuses section') }}</span>
        </label>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">{{ __('Free bonus items with a cover image and a file (or link). Visitors see them before buying; buyers get the files with the main product.') }}</p>
        @if($errors->has('bonuses'))<div class="alert alert-danger py-2">{{ $errors->first('bonuses') }}</div>@endif
        <div class="row">
            <div class="col-md-6 form-group">
                <label>{{ __('Section title') }}</label>
                <input type="text" name="bonus_section_title" class="form-control" maxlength="80" placeholder="{{ __('FREE Bonuses Included') }}" value="{{ old('bonus_section_title', $bnPage->bonus_section_title ?? '') }}">
                <small class="form-text text-muted">{{ __('Leave empty for "FREE Bonuses Included" (translated to the page language).') }}</small>
                @if($errors->has('bonus_section_title'))<span class="text-danger">{{ $errors->first('bonus_section_title') }}</span>@endif
            </div>
            <div class="col-md-6 form-group">
                <label>{{ __('Bonus banner image (optional)') }}</label>
                @if($bnBanner)
                <div class="lpb-current"><img src="{{ $bnBanner }}" alt="" class="lpb-banner-thumb">
                    <label class="mb-0 small"><input type="checkbox" name="remove_bonus_banner" value="1"> {{ __('Remove') }}</label></div>
                @endif
                <input type="file" name="bonus_banner_image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <small class="form-text text-muted">{{ __('One combined mockup of the product and bonuses. JPG, PNG or WebP, max 5 MB, 1600×1000 recommended.') }}</small>
                @if($errors->has('bonus_banner_image'))<span class="text-danger">{{ $errors->first('bonus_banner_image') }}</span>@endif
            </div>
        </div>

        <div id="lpb-items" class="lpb-items">
            @foreach($bnItems as $bnI => $it)
                @include('admin.landing-pages.partials.bonus-item', ['it' => $it, 'pos' => $bnI + 1])
            @endforeach
        </div>
        <button type="button" class="btn btn-primary btn-sm mt-2" id="lpb-add" @if(count($bnItems) >= $bnMax) disabled @endif>
            <i class="fa fa-plus"></i> {{ __('Add Bonus') }}
        </button>
        <small class="text-muted ml-2" id="lpb-count">{{ count($bnItems) }} / {{ $bnMax }}</small>
    </div>
</div>

<template id="lpb-template">
    @include('admin.landing-pages.partials.bonus-item', ['it' => ['key' => '__KEY__', 'row' => null, 'title' => '', 'description' => '', 'delivery' => 'upload', 'link' => '', 'value_label' => '', 'badge_text' => '', 'active' => true], 'pos' => '__POS__'])
</template>

<style>
.lpb-card .card-header{background:#fff7ed}
.lpb-switch{display:inline-flex;align-items:center;gap:8px;font-weight:600;cursor:pointer}
.lpb-switch input[type=checkbox]{width:18px;height:18px}
.lpb-current{display:flex;align-items:center;gap:10px;margin-bottom:6px}
.lpb-banner-thumb{width:120px;height:75px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb}
.lpb-item{border:1px solid #e5e7eb;border-radius:12px;padding:14px;margin-bottom:12px;background:#fff;position:relative}
.lpb-item.is-dragging{opacity:.5}
.lpb-item.is-drop{border-color:#f97316;box-shadow:0 0 0 3px rgba(249,115,22,.15)}
.lpb-item.is-inactive{background:#f9fafb}
.lpb-head{display:flex;align-items:center;gap:8px;margin-bottom:10px}
.lpb-handle{cursor:grab;color:#9ca3af;font-size:18px;padding:2px 6px;border-radius:6px}
.lpb-handle:hover{background:#f3f4f6;color:#374151}
.lpb-num{font-weight:800;color:#f97316}
.lpb-head .lpb-tools{margin-left:auto;display:flex;gap:4px;align-items:center}
.lpb-cover{width:72px;height:90px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb}
.lpb-filename{font-size:12.5px;color:#374151;word-break:break-all}
.lpb-err{display:block;color:#dc3545;font-size:12.5px;margin-top:3px}
</style>
<script>
(function () {
  var box = document.getElementById('lpb-items');
  var addBtn = document.getElementById('lpb-add');
  var countEl = document.getElementById('lpb-count');
  var tpl = document.getElementById('lpb-template');
  if (!box || !addBtn || !tpl) return;
  var MAX = {{ (int) $bnMax }};
  var seq = 0;
  function items() { return Array.prototype.filter.call(box.querySelectorAll('.lpb-item'), function (el) { return !el.hasAttribute('data-removed'); }); }
  function refresh() {
    var list = items();
    list.forEach(function (el, i) {
      var num = el.querySelector('.lpb-num'); if (num) num.textContent = 'BONUS #' + (i + 1);
      var badge = el.querySelector('.lpb-badge-input'); if (badge) badge.placeholder = 'BONUS #' + (i + 1);
    });
    addBtn.disabled = list.length >= MAX;
    countEl.textContent = list.length + ' / ' + MAX;
  }
  function bind(el) {
    var del = el.querySelector('.lpb-remove');
    if (del) del.addEventListener('click', function () {
      if (!confirm(@json(__('Remove this bonus? Its files are deleted when you save the page.')))) return;
      if (el.querySelector('.lpb-id')) { el.querySelector('.lpb-remove-flag').value = '1'; el.setAttribute('data-removed', '1'); el.style.display = 'none'; }
      else { el.parentNode.removeChild(el); }
      refresh();
    });
    el.querySelectorAll('.lpb-delivery').forEach(function (r) { r.addEventListener('change', function () { sync(el); }); });
    var act = el.querySelector('.lpb-active');
    if (act) act.addEventListener('change', function () { el.classList.toggle('is-inactive', !act.checked); });
    el.querySelectorAll('.lpb-move').forEach(function (b) {
      b.addEventListener('click', function () {
        var list = items(), i = list.indexOf(el), j = i + (b.getAttribute('data-dir') === 'up' ? -1 : 1);
        if (j < 0 || j >= list.length) return;
        if (j < i) box.insertBefore(el, list[j]); else box.insertBefore(el, list[j].nextSibling);
        refresh();
      });
    });
    var handle = el.querySelector('.lpb-handle');
    handle.addEventListener('mousedown', function () { el.setAttribute('draggable', 'true'); });
    el.addEventListener('dragstart', function (e) { el.classList.add('is-dragging'); e.dataTransfer.effectAllowed = 'move'; try { e.dataTransfer.setData('text/plain', 'bonus'); } catch (x) {} });
    el.addEventListener('dragend', function () { el.classList.remove('is-dragging'); el.removeAttribute('draggable'); box.querySelectorAll('.is-drop').forEach(function (d) { d.classList.remove('is-drop'); }); refresh(); });
    el.addEventListener('dragover', function (e) {
      var dragging = box.querySelector('.is-dragging');
      if (!dragging || dragging === el) return;
      e.preventDefault();
      var r = el.getBoundingClientRect();
      if (e.clientY < r.top + r.height / 2) box.insertBefore(dragging, el); else box.insertBefore(dragging, el.nextSibling);
    });
    sync(el);
  }
  function sync(el) {
    var link = el.querySelector('.lpb-delivery[value="link"]');
    var isLink = link && link.checked;
    el.querySelector('.lpb-file-wrap').style.display = isLink ? 'none' : '';
    el.querySelector('.lpb-link-wrap').style.display = isLink ? '' : 'none';
  }
  addBtn.addEventListener('click', function () {
    if (items().length >= MAX) return;
    var key = 'n' + Date.now().toString(36) + (seq++);
    var html = tpl.innerHTML.split('__KEY__').join(key).split('__POS__').join(String(items().length + 1));
    var wrap = document.createElement('div'); wrap.innerHTML = html.trim();
    var el = wrap.firstChild; box.appendChild(el); bind(el); refresh();
    var t = el.querySelector('input[type=text]'); if (t) t.focus();
  });
  items().forEach(bind);
  refresh();
})();
</script>
@endif
