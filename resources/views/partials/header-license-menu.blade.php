{{--
  License parents under one "License Keys" item, and any non-license
  categories past the header cap under "More".
  Each parent is a sub-list with its sub-categories inside the same panel.
  A long panel scrolls, and grows to two or three columns.
  $menuVariant: one-desktop | one-mobile | two
  Expects $headerMenus from OsLicenseCatalog::headerMenus().
--}}
@php
  $hvPanelClass = function ($groups) {
      $count = count($groups);
      $class = 'hv-menu-panel';
      if ($count > 16) {
          $class .= ' hv-menu-panel--cols-3';
      } elseif ($count > 8) {
          $class .= ' hv-menu-panel--cols-2';
      }
      return $class;
  };
  $hvNarrow = (int) ($headerMenus['narrow'] ?? 2);
  $hvMoreGroups = [];
  foreach (array_slice($headerMenus['visible'] ?? [], $hvNarrow) as $hvMenu) {
      $hvMoreGroups[] = ['menu' => $hvMenu, 'wide' => true];
  }
  foreach ($headerMenus['more'] ?? [] as $hvMenu) {
      $hvMoreGroups[] = ['menu' => $hvMenu, 'wide' => false];
  }
@endphp
@if($menuVariant === 'one-desktop')
  @if(count($headerMenus['license']) != 0)
  <li class="nav-item dropdown d-none d-md-block hv-menu-drop">
    <a class="nav-link dropdown-toggle" href="javascript:void(0)">{{ __('License Keys') }}</a>
    <ul class="dropdown-menu {{ $hvPanelClass($headerMenus['license']) }}">
      @foreach($headerMenus['license'] as $menu)
      <li class="hv-menu-group">
        <a class="dropdown-item hv-menu-parent" href="{{ URL::to('/shop/category/') }}/{{ $menu->category_slug }}">{{ $menu->category_name }}</a>
        @foreach($menu->subcategory as $sub_category)
        <a class="dropdown-item hv-menu-sub" href="{{ URL::to('/shop/subcategory/') }}/{{ $sub_category->subcategory_slug }}">{{ $sub_category->subcategory_name }}</a>
        @endforeach
      </li>
      @endforeach
    </ul>
  </li>
  @endif
  @if(count($hvMoreGroups) != 0)
  <li class="nav-item dropdown d-none d-md-block hv-menu-drop">
    <a class="nav-link dropdown-toggle" href="javascript:void(0)">{{ __('More') }}</a>
    <ul class="dropdown-menu {{ $hvPanelClass($hvMoreGroups) }}">
      @foreach($hvMoreGroups as $group)
      <li class="hv-menu-group{{ $group['wide'] ? ' hv-more-wide' : '' }}">
        <a class="dropdown-item hv-menu-parent" href="{{ URL::to('/shop/category/') }}/{{ $group['menu']->category_slug }}">{{ $group['menu']->category_name }}</a>
        @foreach($group['menu']->subcategory as $sub_category)
        <a class="dropdown-item hv-menu-sub" href="{{ URL::to('/shop/subcategory/') }}/{{ $sub_category->subcategory_slug }}">{{ $sub_category->subcategory_name }}</a>
        @endforeach
      </li>
      @endforeach
    </ul>
  </li>
  @endif
@elseif($menuVariant === 'one-mobile')
  @if(count($headerMenus['license']) != 0)
  <li class="nav-item dropdown d-md-none">
    <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown">{{ __('License Keys') }}</a>
    <ul class="dropdown-menu hv-menu-panel">
      @foreach($headerMenus['license'] as $menu)
      <li class="hv-menu-group">
        <a class="dropdown-item hv-menu-parent" href="{{ URL::to('/shop/category/') }}/{{ $menu->category_slug }}">{{ $menu->category_name }}</a>
        @foreach($menu->subcategory as $sub_category)
        <a class="dropdown-item hv-menu-sub" href="{{ URL::to('/shop/subcategory/') }}/{{ $sub_category->subcategory_slug }}">{{ $sub_category->subcategory_name }}</a>
        @endforeach
      </li>
      @endforeach
    </ul>
  </li>
  @endif
  @if(count($hvMoreGroups) != 0)
  <li class="nav-item dropdown d-md-none">
    <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown">{{ __('More') }}</a>
    <ul class="dropdown-menu hv-menu-panel">
      @foreach($hvMoreGroups as $group)
      <li class="hv-menu-group{{ $group['wide'] ? ' hv-more-wide' : '' }}">
        <a class="dropdown-item hv-menu-parent" href="{{ URL::to('/shop/category/') }}/{{ $group['menu']->category_slug }}">{{ $group['menu']->category_name }}</a>
        @foreach($group['menu']->subcategory as $sub_category)
        <a class="dropdown-item hv-menu-sub" href="{{ URL::to('/shop/subcategory/') }}/{{ $sub_category->subcategory_slug }}">{{ $sub_category->subcategory_name }}</a>
        @endforeach
      </li>
      @endforeach
    </ul>
  </li>
  @endif
@else
  @if(count($headerMenus['license']) != 0)
  <li class="has_dropdown hv-menu-drop">
    <a href="javascript:void(0);">{{ __('License Keys') }}</a>
    <div class="dropdowns dropdown--menu {{ $hvPanelClass($headerMenus['license']) }}">
      <ul>
        @foreach($headerMenus['license'] as $menu)
        <li class="hv-menu-group">
          <a href="{{ URL::to('/shop/category/') }}/{{ $menu->category_slug }}">{{ $menu->category_name }}</a>
          @foreach($menu->subcategory as $sub_category)
          <a class="hv-menu-sub" href="{{ URL::to('/shop/subcategory/') }}/{{ $sub_category->subcategory_slug }}">{{ $sub_category->subcategory_name }}</a>
          @endforeach
        </li>
        @endforeach
      </ul>
    </div>
  </li>
  @endif
  @if(count($hvMoreGroups) != 0)
  <li class="has_dropdown hv-menu-drop">
    <a href="javascript:void(0);">{{ __('More') }}</a>
    <div class="dropdowns dropdown--menu {{ $hvPanelClass($hvMoreGroups) }}">
      <ul>
        @foreach($hvMoreGroups as $group)
        <li class="hv-menu-group{{ $group['wide'] ? ' hv-more-wide' : '' }}">
          <a href="{{ URL::to('/shop/category/') }}/{{ $group['menu']->category_slug }}">{{ $group['menu']->category_name }}</a>
          @foreach($group['menu']->subcategory as $sub_category)
          <a class="hv-menu-sub" href="{{ URL::to('/shop/subcategory/') }}/{{ $sub_category->subcategory_slug }}">{{ $sub_category->subcategory_name }}</a>
          @endforeach
        </li>
        @endforeach
      </ul>
    </div>
  </li>
  @endif
@endif
@once
<script>
(function () {
  function place(drop) {
    var panel = drop.querySelector('.hv-menu-panel');
    if (!panel) return;
    panel.style.left = '0px';
    panel.style.right = 'auto';
    var rect = panel.getBoundingClientRect();
    if (rect.width < 20) return;
    var overflowRight = rect.right - (window.innerWidth - 8);
    var overflowLeft = 8 - rect.left;
    if (overflowRight > 0) panel.style.left = (-overflowRight) + 'px';
    else if (overflowLeft > 0) panel.style.left = overflowLeft + 'px';
  }
  function bind() {
    document.querySelectorAll('.hv-menu-drop').forEach(function (drop) {
      if (drop.getAttribute('data-hv-placed')) return;
      drop.setAttribute('data-hv-placed', '1');
      drop.addEventListener('mouseenter', function () { place(drop); });
      drop.addEventListener('focusin', function () { place(drop); });
    });
  }
  function start() { bind(); }
  window.addEventListener('resize', function () {
    document.querySelectorAll('.hv-menu-drop').forEach(place);
  });
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
  else start();
})();
</script>
@endonce
