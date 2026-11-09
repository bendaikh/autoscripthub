{{--
  License parents under one "License Keys" item, and any non-license
  categories past the header cap under "More".
  $menuVariant: one-desktop | one-mobile | two
  Expects $headerMenus from OsLicenseCatalog::headerMenus().
--}}
@if($menuVariant === 'one-desktop')
  @if(count($headerMenus['license']) != 0)
  <li class="nav-item dropdown d-none d-md-block">
    <a class="nav-link dropdown-toggle" href="javascript:void(0)">{{ __('License Keys') }}</a>
    <ul class="dropdown-menu">
      @foreach($headerMenus['license'] as $menu)
      <li class="dropdown">
        <a class="dropdown-item @if(count($menu->subcategory) != 0) dropdown-toggle @endif" href="{{ URL::to('/shop/category/') }}/{{ $menu->category_slug }}">{{ $menu->category_name }}</a>
        @if(count($menu->subcategory) != 0)
        <ul class="dropdown-menu">
          @foreach($menu->subcategory as $sub_category)
          <li><a class="dropdown-item" href="{{ URL::to('/shop/subcategory/') }}/{{ $sub_category->subcategory_slug }}">{{ $sub_category->subcategory_name }}</a></li>
          @endforeach
        </ul>
        @endif
      </li>
      @endforeach
    </ul>
  </li>
  @endif
  @if(count($headerMenus['more']) != 0)
  <li class="nav-item dropdown d-none d-md-block">
    <a class="nav-link dropdown-toggle" href="javascript:void(0)">{{ __('More') }}</a>
    <ul class="dropdown-menu">
      @foreach($headerMenus['more'] as $menu)
      <li class="dropdown">
        <a class="dropdown-item @if(count($menu->subcategory) != 0) dropdown-toggle @endif" href="{{ URL::to('/shop/category/') }}/{{ $menu->category_slug }}">{{ $menu->category_name }}</a>
        @if(count($menu->subcategory) != 0)
        <ul class="dropdown-menu">
          @foreach($menu->subcategory as $sub_category)
          <li><a class="dropdown-item" href="{{ URL::to('/shop/subcategory/') }}/{{ $sub_category->subcategory_slug }}">{{ $sub_category->subcategory_name }}</a></li>
          @endforeach
        </ul>
        @endif
      </li>
      @endforeach
    </ul>
  </li>
  @endif
@elseif($menuVariant === 'one-mobile')
  @if(count($headerMenus['license']) != 0)
  <li class="nav-item dropdown d-md-none">
    <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown">{{ __('License Keys') }}</a>
    <ul class="dropdown-menu">
      @foreach($headerMenus['license'] as $menu)
      <li><a class="dropdown-item" href="{{ URL::to('/shop/category/') }}/{{ $menu->category_slug }}">{{ $menu->category_name }}</a></li>
      @foreach($menu->subcategory as $sub_category)
      <li><a class="dropdown-item hv-menu-sub" href="{{ URL::to('/shop/subcategory/') }}/{{ $sub_category->subcategory_slug }}">{{ $sub_category->subcategory_name }}</a></li>
      @endforeach
      @endforeach
    </ul>
  </li>
  @endif
  @if(count($headerMenus['more']) != 0)
  <li class="nav-item dropdown d-md-none">
    <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown">{{ __('More') }}</a>
    <ul class="dropdown-menu">
      @foreach($headerMenus['more'] as $menu)
      <li><a class="dropdown-item" href="{{ URL::to('/shop/category/') }}/{{ $menu->category_slug }}">{{ $menu->category_name }}</a></li>
      @foreach($menu->subcategory as $sub_category)
      <li><a class="dropdown-item hv-menu-sub" href="{{ URL::to('/shop/subcategory/') }}/{{ $sub_category->subcategory_slug }}">{{ $sub_category->subcategory_name }}</a></li>
      @endforeach
      @endforeach
    </ul>
  </li>
  @endif
@else
  @if(count($headerMenus['license']) != 0)
  <li class="has_dropdown">
    <a href="javascript:void(0);">{{ __('License Keys') }}</a>
    <div class="dropdowns dropdown--menu">
      <ul>
        @foreach($headerMenus['license'] as $menu)
        <li><a href="{{ URL::to('/shop/category/') }}/{{ $menu->category_slug }}">{{ $menu->category_name }}</a></li>
        @foreach($menu->subcategory as $sub_category)
        <li class="hv-menu-sub"><a href="{{ URL::to('/shop/subcategory/') }}/{{ $sub_category->subcategory_slug }}">{{ $sub_category->subcategory_name }}</a></li>
        @endforeach
        @endforeach
      </ul>
    </div>
  </li>
  @endif
  @if(count($headerMenus['more']) != 0)
  <li class="has_dropdown">
    <a href="javascript:void(0);">{{ __('More') }}</a>
    <div class="dropdowns dropdown--menu">
      <ul>
        @foreach($headerMenus['more'] as $menu)
        <li><a href="{{ URL::to('/shop/category/') }}/{{ $menu->category_slug }}">{{ $menu->category_name }}</a></li>
        @foreach($menu->subcategory as $sub_category)
        <li class="hv-menu-sub"><a href="{{ URL::to('/shop/subcategory/') }}/{{ $sub_category->subcategory_slug }}">{{ $sub_category->subcategory_name }}</a></li>
        @endforeach
        @endforeach
      </ul>
    </div>
  </li>
  @endif
@endif
