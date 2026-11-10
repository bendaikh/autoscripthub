{{--
  Popular Items slider card only.
  Expects: $featured (item row), $trendRank (1-based).
  Price and cart URL follow partials/product-card.blade.php.
--}}
@php
  $checkif_purchased = Helper::if_purchased($featured->item_token);
  if (Helper::is_single_price_item($featured)) {
      $price = Helper::catalog_price($featured);
      $price_exact = true;
  }
  $price_exact = !empty($price_exact ?? false);
  $compare_price = $price_exact ? $featured->regular_price : $featured->extended_price;

  $trendBuyHref = null;
  $trendBuyLabel = __('Buy now');
  if ((int) $addition_settings->guest_checkout === 1) {
      if ($checkif_purchased == 0) {
          if ($featured->free_download == 0) {
              if (Auth::check()) {
                  if (Auth::user()->id != 1 && $featured->user_id != Auth::user()->id) {
                      $trendBuyHref = URL::to('/add-to-cart').'/'.$featured->item_slug;
                  }
              } else {
                  $trendBuyHref = URL::to('/add-to-cart').'/'.$featured->item_slug;
              }
          } elseif (Auth::guest()) {
              $trendBuyHref = URL::to('/login');
              $trendBuyLabel = __('Download');
          } else {
              $trendBuyHref = URL::to('/item').'/download/'.base64_encode($featured->item_token);
              $trendBuyLabel = __('Download');
          }
      }
  } elseif ($checkif_purchased == 0 && $featured->free_download != 0) {
      if (Auth::guest()) {
          $trendBuyHref = URL::to('/login');
          $trendBuyLabel = __('Download');
      } else {
          $trendBuyHref = URL::to('/item').'/download/'.base64_encode($featured->item_token);
          $trendBuyLabel = __('Download');
      }
  } else {
      $trendBuyHref = URL::to('/item').'/'.$featured->item_slug;
  }

  $trendWishHref = null;
  if (Auth::guest()) {
      $trendWishHref = url('/').'/login';
  } elseif ($featured->user_id != Auth::user()->id) {
      $trendWishHref = url('/item').'/'.base64_encode($featured->item_id).'/favorite/'.base64_encode($featured->item_liked);
  }

  $trendGroup = \Fickrr\Support\PopularMix::group($featured);
  $trendChip = ['ai' => __('AI'), 'games' => __('Games'), 'office' => __('Office')][$trendGroup] ?? null;
  $cardImage = Helper::card_image($featured);
  $trendRank = (int) ($trendRank ?? 1);
@endphp
<article class="hv-trend-card">
  <span class="hv-trend-rank{{ $trendRank === 1 ? ' is-hot' : '' }}">{{ $trendRank === 1 ? 'HOT' : '#'.$trendRank }}</span>
  @if($trendWishHref)
  <a class="hv-trend-wish" href="{{ $trendWishHref }}" aria-label="{{ __('Wishlist') }}"><i class="dwg-heart"></i></a>
  @endif
  <a class="hv-trend-photo" href="{{ URL::to('/item') }}/{{ $featured->item_slug }}">
    @if($cardImage)
    <img class="hv-trend-img" loading="lazy" decoding="async" src="{{ Helper::Image_Path($cardImage,'no-image.png') }}" alt="{{ $featured->item_name }}">
    @else
    <img class="hv-trend-img" loading="lazy" decoding="async" src="{{ url('/') }}/public/img/no-image.png" alt="{{ $featured->item_name }}" width="300" height="200">
    @endif
  </a>
  <div class="hv-trend-body">
    @if($trendChip)
    <span class="hv-trend-chip hv-trend-chip--{{ $trendGroup }}">{{ $trendChip }}</span>
    @endif
    <h3 class="hv-trend-title"><a href="{{ URL::to('/item') }}/{{ $featured->item_slug }}">{{ $featured->item_name }}</a></h3>
    <div class="hv-trend-price">
      @if($featured->free_download == 0)
        @if($featured->item_flash == 1)
        <del class="hv-trend-old">{{ Helper::price_format($allsettings->site_currency_position,$compare_price,$currency_symbol,$multicurrency,$price_exact) }}</del>
        @endif
        <span>{{ Helper::price_format($allsettings->site_currency_position,$price,$currency_symbol,$multicurrency,$price_exact) }}</span>
      @else
        <span>{{ __('Free') }}</span>
      @endif
    </div>
    @if($trendBuyHref)
    <a class="hv-trend-buy" href="{{ $trendBuyHref }}">{{ $trendBuyLabel }}</a>
    @endif
  </div>
</article>
