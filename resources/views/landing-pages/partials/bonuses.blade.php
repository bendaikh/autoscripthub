{{-- Public bonus section of a landing page (rendered by Blade; v-pre keeps Vue from compiling admin text). --}}
@if(isset($lpBonuses) && $lpBonuses->count())
@php
    $bnTitle = trim((string) ($landing_page->bonus_section_title ?? '')) !== '' ? $landing_page->bonus_section_title : $lpT('FREE Bonuses Included');
    $bnBanner = \Fickrr\Models\LandingBonus::imageUrl($landing_page->bonus_banner_image ?? null);
    $bnBannerWebp = $bnBanner ? \Fickrr\Models\LandingBonus::webpUrl($landing_page->bonus_banner_image) : null;
    $bnPrice = (float) ($price_data['price'] ?? $landing_page->lp_price);
    $bnPriceText = ($lpPricePrefix ?? '') . number_format($bnPrice, abs($bnPrice - round($bnPrice)) < 0.00001 ? 0 : 2) . ($lpPriceSuffix ?? '');
@endphp
<section class="lp-section lp-bonus-section" id="bonuses" v-pre>
    <div class="lp-container">
        <header class="lp-section-head lp-bonus-head">
            <span class="lp-bonus-kicker"><i class="fas fa-gift"></i> {{ $lpBonuses->count() === 1 ? $lpT('+ 1 FREE bonus included') : $lpT('+ :count FREE bonuses included', ['count' => $lpBonuses->count()]) }}</span>
            <h2 class="lp-section-title lp-bonus-title">{{ $bnTitle }}</h2>
        </header>

        @if($bnBanner)
        <picture class="lp-bonus-banner">
            @if($bnBannerWebp)<source srcset="{{ $bnBannerWebp }}" type="image/webp">@endif
            <img src="{{ $bnBanner }}" alt="{{ $bnTitle }}" loading="lazy" decoding="async">
        </picture>
        @endif

        <div class="lp-bonus-grid">
            @foreach($lpBonuses as $bnIndex => $bonus)
            @php
                $bnImg = \Fickrr\Models\LandingBonus::imageUrl($bonus->image_path);
                $bnWebp = \Fickrr\Models\LandingBonus::webpUrl($bonus->image_path);
            @endphp
            <article class="lp-bonus-card">
                <div class="lp-bonus-cover">
                    <picture>
                        @if($bnWebp)<source srcset="{{ $bnWebp }}" type="image/webp">@endif
                        <img src="{{ $bnImg }}" alt="{{ $bonus->title }}" loading="lazy" decoding="async">
                    </picture>
                    <span class="lp-bonus-badge">{{ $bonus->badge_text ?: 'BONUS #' . ($bnIndex + 1) }}</span>
                </div>
                <div class="lp-bonus-body">
                    <h3>{{ $bonus->title }}</h3>
                    <p>{{ $bonus->description }}</p>
                    @if(!empty($bonus->value_label))<span class="lp-bonus-value">{{ $bonus->value_label }}</span>@endif
                </div>
            </article>
            @endforeach
        </div>

        <p class="lp-bonus-closing"><i class="fas fa-check-circle"></i> {{ $lpT('All included for :price – instant download', ['price' => $bnPriceText]) }}</p>
    </div>
</section>
@endif
