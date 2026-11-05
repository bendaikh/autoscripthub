<!DOCTYPE html>
@php
    \Fickrr\Helpers\LandingPageI18n::usePage($landing_page);
    $lpT = [\Fickrr\Helpers\LandingPageI18n::class, 't'];
    $lpLang = \Fickrr\Helpers\LandingPageI18n::locale();
    $lpProductKind = $content['productKind'] ?? 'script';
@endphp
<html lang="{{ $lpLang }}" dir="{{ $lpLang === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $landing_page->lp_meta_title ?? $landing_page->lp_title }}</title>
    <meta name="description" content="{{ $landing_page->lp_meta_description ?? strip_tags(substr($landing_page->lp_description, 0, 160)) }}">
    <meta name="keywords" content="{{ $landing_page->lp_meta_keywords ?? $landing_page->lp_title }}">
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $landing_page->lp_title }}">
    <meta property="og:description" content="{{ $landing_page->lp_tagline ?? strip_tags(substr($landing_page->lp_description, 0, 200)) }}">
    @if($landing_page->lp_banner_image)
    <meta property="og:image" content="{{ asset('storage/landing-pages/' . $landing_page->lp_banner_image) }}">
    @endif
    @include('landing-pages.partials.pixels')
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    @if($lpLang === 'ar')
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
    @endif
    @php $lpCssFile = base_path('public/css/landing-vue.css'); @endphp
    <link rel="stylesheet" href="{{ url('/public/css/landing-vue.css') }}?v={{ file_exists($lpCssFile) ? filemtime($lpCssFile) : 1 }}">
</head>
@php
    $youtubeId = '';
    if (!empty($landing_page->lp_youtube_url)) {
        $u = $landing_page->lp_youtube_url;
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([^\&\?\/]+)/', $u, $m)) {
            $youtubeId = $m[1];
        }
    }

    $galleryUrls = collect($gallery_images ?? [])
        ->map(function ($img) { return asset('storage/landing-pages/' . $img->lpg_image); })
        ->values()
        ->all();

    $videoUrl = !empty($landing_page->lp_product_video)
        ? asset('storage/landing-pages/' . $landing_page->lp_product_video)
        : null;

    $bannerUrl = $landing_page->lp_banner_image
        ? asset('storage/landing-pages/' . $landing_page->lp_banner_image)
        : null;

    // Screenshots belong in the product frame; the banner works better as hero atmosphere.
    $heroShot = $galleryUrls[0] ?? $bannerUrl;
    $heroSide = $galleryUrls[1] ?? null;
    $heroBg = $bannerUrl && $heroShot !== $bannerUrl ? $bannerUrl : null;

    // Product name (before a dash) can be accented; the rest of the title stays intact.
    $fullTitle = trim($landing_page->lp_title);
    $titleName = $fullTitle;
    $titleRest = '';
    if (preg_match('/^(.+?)\s+[—–-]\s+(.+)$/u', $fullTitle, $titleParts)) {
        $titleName = trim($titleParts[1]);
        $titleRest = trim($titleParts[2]);
    }

    $included = $content['whatYouGetItems'];
    if (!$included) {
        $included = collect($content['featureCards'])->pluck('title')->take(6)->all();
    }

    $aboutHtml = $landing_page->lp_about_description ?? '';
    if ($aboutHtml === '' && mb_strlen(\Fickrr\Helpers\LandingPageContent::plainText($landing_page->lp_description ?? '')) > \Fickrr\Helpers\LandingPageContent::DESCRIPTION_MAX_CHARS) {
        $aboutHtml = $landing_page->lp_description;
    }

    // Arabic-script currency symbols (e.g. "د.م.") read badly on FR/EN pages:
    // there we show the ISO currency code after the amount instead (e.g. "290 MAD").
    $lpPriceParts = function ($symbol, $currency) use ($lpLang) {
        $symbol = (string) $symbol;
        if ($lpLang !== 'ar' && preg_match('/\p{Arabic}/u', $symbol)) {
            return ['', ' ' . ($currency ?: trim($symbol))];
        }
        return [$symbol, ''];
    };
    $lpMainCurrency = $price_data['currency'] ?? ($landing_page->lp_currency ?? 'USD');
    [$lpPricePrefix, $lpPriceSuffix] = $lpPriceParts($price_data['symbol'] ?? '$', $lpMainCurrency);
    $lpExtParts = ($extended_price_data && ($extended_price_data['price'] ?? 0) > 0)
        ? $lpPriceParts($extended_price_data['symbol'], $extended_price_data['currency'] ?? $lpMainCurrency)
        : ['', ''];

    $pagePayload = [
        'title' => $landing_page->lp_title,
        'titleName' => $titleName,
        'titleRest' => $titleRest,
        'tagline' => $landing_page->lp_tagline ?: $content['shortDescription'],
        'shortDescription' => $content['shortDescription'],
        'badge' => $content['badge'],
        'cta' => $landing_page->lp_cta_text ?: ($lpProductKind === 'physical'
            ? $lpT('Order for delivery')
            : ($lpProductKind === 'sheet' || $lpProductKind === 'product'
            ? $lpT('Get Instant Download')
            : $lpT('Get Instant Access'))),
        'descriptionHtml' => $aboutHtml,
        'whatYouGetHtml' => $landing_page->lp_what_you_get,
        'whatYouGetItems' => $content['whatYouGetItems'],
        'included' => array_values($included),
        'heroBenefits' => $content['heroBenefits'],
        'featuresHeading' => $content['featuresHeading'],
        'featureCards' => $content['featureCards'],
        'outcomes' => $content['outcomes'],
        'problem' => $content['problem'],
        'trust' => $content['trust'],
        'stats' => $content['stats'],
        'faq' => $content['faq'],
        'heroShot' => $heroShot,
        'heroSide' => $heroSide,
        'heroBg' => $heroBg,
        'gallery' => $galleryUrls,
        'videoUrl' => $videoUrl,
        'videoPoster' => $bannerUrl,
        'youtubeId' => $youtubeId,
        'price' => [
            'symbol' => $lpPricePrefix,
            'suffix' => $lpPriceSuffix,
            'amount' => (float) ($price_data['price'] ?? $landing_page->lp_price),
            'currency' => $price_data['currency'] ?? ($landing_page->lp_currency ?? 'USD'),
        ],
        'extended' => ($extended_price_data && ($extended_price_data['price'] ?? 0) > 0) ? [
            'symbol' => $lpExtParts[0],
            'suffix' => $lpExtParts[1],
            'amount' => (float) $extended_price_data['price'],
        ] : null,
        'reviews' => $content['reviews'] ?? [],
        'productKind' => $content['productKind'] ?? 'script',
        'i18n' => [
            'onlyPrice' => $lpT('Only'),
            'paymentNote' => $lpProductKind === 'physical'
                ? $lpT('Pay by card or PayPal • Then we ship it')
                : (($lpProductKind === 'sheet' || $lpProductKind === 'product')
                ? $lpT('One-time payment • Instant download')
                : $lpT('One-time payment • Instant access')),
            'instantAccess' => $lpProductKind === 'physical'
                ? $lpT('We ship this to the address you enter.')
                : ($lpProductKind === 'sheet'
                ? $lpT('Download the spreadsheet as soon as your payment is confirmed')
                : ($lpProductKind === 'product'
                    ? $lpT('Download your files as soon as your payment is confirmed')
                    : $lpT('Instant access as soon as your payment is confirmed'))),
            'hostingHeading' => $content['hosting']['heading'],
            'hostingText' => $content['hosting']['text'],
            'watchDemo' => $lpT('Watch the demo'),
            'seeScreens' => $lpT('See the screens'),
            'seePricing' => $lpT('See what you get'),
            'problemLead' => $lpT('Before'),
            'solutionLead' => $lpT('After'),
            'aboutHeading' => $lpT('About this product'),
            'showcaseHeading' => $lpT('See it up close'),
            'showcaseSub' => $lpT('Real screens from the product you are buying'),
            'demoHeading' => $lpT('See it in action'),
            'demoSub' => $lpT('Watch the product in a short video'),
            'outcomesHeading' => $lpT('What this changes for you'),
            'whatYouGetHeading' => $lpT('What you get after payment'),
            'pricingHeading' => $lpT('Simple, one-time pricing'),
            'pricingSub' => $lpProductKind === 'physical'
                ? $lpT('Pay by card or PayPal, then we ship it')
                : $lpT('Pay once and keep access to the product'),
            'lifetimeLabel' => $lpProductKind === 'physical' ? $lpT('Ships to you') : $lpT('Full access'),
            'extendedNote' => $lpProductKind === 'physical'
                ? ''
                : $lpT('An extended licence is also available — send us a message for details.'),
            'includedLabel' => $lpT("What's included"),
            'faqHeading' => $lpT('Questions before you buy'),
            'finalHeading' => $lpT('Ready to get started?'),
            'featuresIncluded' => $lpT('features included'),
            'secureNote' => $lpProductKind === 'physical'
                ? $lpT('Pay by card or PayPal, then we ship it.')
                : $lpT('Secure checkout with PayPal or card'),
            'prev' => $lpT('Previous'),
            'next' => $lpT('Next'),
            'close' => $lpT('Close'),
            'reviewsHeading' => $lpT('Reviews for this item'),
            'reviewsCountLabel' => $lpT('reviews'),
            'reviewsAiLead' => $lpT('What buyers say, summarized by AI:'),
            'itemQuality' => $lpT('Item quality'),
            'shippingLabel' => $lpProductKind === 'physical' ? $lpT('Shipping') : (($lpProductKind === 'sheet' || $lpProductKind === 'product') ? $lpT('Delivery') : $lpT('Shipping')),
            'serviceLabel' => $lpT('Customer service'),
            'recommendLabel' => $lpT('Buyers recommend'),
            'thisItem' => $lpT('This item'),
            'sellerResponse' => $lpT('Response from seller'),
            'playVideo' => $lpT('Play video'),
        ],
    ];
@endphp
<body class="lp-body @if($lpLang === 'ar') is-rtl @endif"
      style="--lp-accent: {{ $content['accent'] }}; --lp-accent-dark: {{ $content['accentDark'] }}; --lp-accent-rgb: {{ $content['accentRgb'] }};">

<header class="lp-topbar">
    <div class="lp-container lp-topbar-inner">
        <a href="{{ url('/') }}" class="lp-logo" aria-label="{{ \Fickrr\Support\Store::publicName($setting->site_title ?? null) }}">
            @include('partials.brand', ['brandCompact' => false, 'brandLight' => true])
        </a>
    </div>
</header>

<div id="lp-vue-app" v-cloak>
    @if(Session::has('success') || Session::has('error') || Session::has('info'))
    <div class="lp-container lp-alerts">
        @if(Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif
        @if(Session::has('info'))
        <div class="alert alert-info">{{ Session::get('info') }}</div>
        @endif
    </div>
    @endif

    {{-- 1. HERO --------------------------------------------------------------}}
    <section class="lp-hero">
        <div v-if="page.heroBg" class="lp-hero-bg" :style="{ backgroundImage: 'url(' + page.heroBg + ')' }"></div>
        <div class="lp-hero-glow"></div>
        <div class="lp-container lp-hero-grid">
            <div class="lp-hero-copy">
                <span class="lp-badge lp-mobile-hide">
                    <i class="fas fa-{{ $content['categoryIcon'] }}"></i> @{{ page.badge }}
                </span>
                <h1 class="lp-hero-title">
                    <span class="lp-accent-text">@{{ page.titleName }}</span><span v-if="page.titleRest"> — @{{ page.titleRest }}</span>
                </h1>
                <p class="lp-hero-tagline">@{{ page.tagline }}</p>
                <p v-if="page.shortDescription && page.shortDescription !== page.tagline" class="lp-hero-lead lp-mobile-hide">
                    @{{ page.shortDescription }}
                </p>

                <ul v-if="page.heroBenefits.length" class="lp-hero-benefits lp-mobile-hide">
                    <li v-for="(benefit, idx) in page.heroBenefits" :key="idx">
                        <i :class="'fas fa-' + benefit.icon"></i>
                        <span>
                            <strong>@{{ benefit.short }}</strong>
                            <em v-if="benefit.title && benefit.title !== benefit.short">@{{ benefit.title }}</em>
                        </span>
                    </li>
                </ul>
            </div>

            <div class="lp-hero-offer">
                <div class="lp-hero-price">
                    <span class="lp-hero-price-label">@{{ page.i18n.onlyPrice }}</span>
                    <span class="lp-hero-price-value">@{{ page.price.symbol }}@{{ formatMoney(page.price.amount) }}@{{ page.price.suffix }}</span>
                </div>
                <p class="lp-hero-payment">@{{ page.i18n.paymentNote }}</p>
                <p class="lp-hero-payment lp-mobile-hide">@{{ page.i18n.hostingText }}</p>

                <div class="lp-hero-actions">
                    <button type="button" class="lp-btn lp-btn-lg" @click="openBuy">
                        <i class="fas fa-bolt"></i> @{{ page.cta }}
                    </button>
                    <button type="button" class="lp-btn-ghost lp-mobile-hide" @click="scrollTo(secondaryTarget)">
                        <i :class="'fas fa-' + (page.youtubeId ? 'play' : 'images')"></i> @{{ secondaryLabel }}
                    </button>
                </div>

                <p class="lp-hero-assurance lp-mobile-hide">
                    <i class="fas fa-lock"></i> @{{ page.i18n.instantAccess }}
                </p>
            </div>

            <div class="lp-hero-media">
                <div v-if="heroShotUrl || (activeItem && activeItem.type === 'video')" class="lp-hero-stage"
                     @touchstart="onTouchStart" @touchend="onTouchEnd">
                    <button v-if="shotCount() > 1" type="button" class="lp-hero-nav lp-hero-prev"
                            :aria-label="page.i18n.prev" @click="prevShot">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="lp-shot lp-shot-lg" @click="openHeroLightbox">
                        <div class="lp-shot-bar">
                            <span></span><span></span><span></span>
                        </div>
                        <video v-if="activeItem && activeItem.type === 'video'" :src="activeItem.url"
                               :poster="activeItem.thumb || ''" controls playsinline></video>
                        <img v-else :src="heroShotUrl" :alt="page.title">
                    </div>
                    <button v-if="shotCount() > 1" type="button" class="lp-hero-nav lp-hero-next"
                            :aria-label="page.i18n.next" @click="nextShot">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div v-else class="lp-shot lp-shot-lg lp-shot-empty">
                    <i :class="'fas fa-{{ $content['categoryIcon'] }}'"></i>
                </div>
                <div v-if="shotCount() > 1" class="lp-thumbs lp-hero-thumbs">
                    <button v-for="(item, idx) in galleryMedia" :key="'hero-thumb-' + idx" type="button"
                            class="lp-thumb" :class="{ active: idx === activeShot, 'is-video': item.type === 'video' }"
                            @click="activeShot = idx">
                        <img :src="item.thumb || item.url" :alt="page.title + ' — ' + (idx + 1)">
                        <span v-if="item.type === 'video'" class="lp-thumb-play" :aria-label="page.i18n.playVideo">
                            <i class="fas fa-play"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. TRUST / QUICK BENEFITS -------------------------------------------}}
    <section class="lp-trust">
        <div class="lp-container">
            <div class="lp-trust-strip">
                <div v-for="(stat, idx) in page.stats" :key="'s' + idx" class="lp-trust-item">
                    <span class="lp-trust-stat">@{{ stat.value }}</span>
                    <span class="lp-trust-text"><strong>@{{ stat.label }}</strong></span>
                </div>
                <div v-for="(item, idx) in page.trust" :key="'t' + idx" class="lp-trust-item">
                    <span class="lp-trust-icon"><i :class="'fas fa-' + item.icon"></i></span>
                    <span class="lp-trust-text">
                        <strong>@{{ item.title }}</strong>
                        <em>@{{ item.text }}</em>
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- 2b. PRODUCT VIDEO (only when an uploaded video or YouTube URL exists) --}}
    <section v-if="page.videoUrl || page.youtubeId" class="lp-section lp-video-section" id="demo">
        <div class="lp-container">
            <header class="lp-section-head">
                <h2 class="lp-section-title">@{{ page.i18n.demoHeading }}</h2>
                <p class="lp-section-sub">@{{ page.i18n.demoSub }}</p>
            </header>
            <div class="lp-video-frame" id="product-video">
                <video v-if="page.videoUrl" class="lp-video-player" :src="page.videoUrl"
                       :poster="page.videoPoster || null" controls playsinline preload="metadata"></video>
                <div v-else class="lp-video">
                    <iframe :src="'https://www.youtube.com/embed/' + page.youtubeId + '?rel=0&modestbranding=1'"
                            :title="page.title" allowfullscreen loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. PROBLEM -> SOLUTION ----------------------------------------------}}
    <section class="lp-section" id="about">
        <div class="lp-container">
            <div class="lp-switch" data-reveal>
                <article class="lp-switch-card lp-switch-problem">
                    <span class="lp-switch-tag">@{{ page.i18n.problemLead }}</span>
                    <h2>@{{ page.problem.heading }}</h2>
                    <ul>
                        <li v-for="(item, idx) in page.problem.items" :key="idx">
                            <i class="fas fa-times"></i> @{{ item }}
                        </li>
                    </ul>
                </article>
                <div class="lp-switch-arrow"><i class="fas fa-arrow-right"></i></div>
                <article class="lp-switch-card lp-switch-solution">
                    <span class="lp-switch-tag">@{{ page.i18n.solutionLead }}</span>
                    <h2>@{{ page.problem.solutionHeading }}</h2>
                    <ul>
                        <li v-for="(item, idx) in solutionPoints" :key="idx">
                            <i class="fas fa-check"></i> @{{ item }}
                        </li>
                    </ul>
                </article>
            </div>

            <div v-if="page.descriptionHtml" class="lp-about" data-reveal>
                <h2 class="lp-section-title">@{{ page.i18n.aboutHeading }}</h2>
                <div class="lp-prose" v-html="page.descriptionHtml"></div>
            </div>
        </div>
    </section>

    {{-- 4. FEATURES ---------------------------------------------------------}}
    <section v-if="page.featureCards.length" class="lp-section lp-section-tint" id="features">
        <div class="lp-container">
            <header class="lp-section-head">
                <h2 class="lp-section-title">@{{ page.featuresHeading }}</h2>
                <p class="lp-section-sub">@{{ page.featureCards.length }} @{{ page.i18n.featuresIncluded }}</p>
            </header>
            <div class="lp-feature-grid">
                <article v-for="(card, idx) in page.featureCards" :key="idx" class="lp-feature-card" data-reveal>
                    <span class="lp-feature-icon"><i :class="'fas fa-' + card.icon"></i></span>
                    <h3>@{{ card.title }}</h3>
                    <p v-if="card.text">@{{ card.text }}</p>
                </article>
            </div>
            <div class="lp-section-cta">
                <button type="button" class="lp-btn" @click="openBuy">
                    <i class="fas fa-bolt"></i> @{{ page.cta }}
                </button>
            </div>
        </div>
    </section>

    {{-- 5. PRODUCT SHOWCASE ------------------------------------------------}}
    <section v-if="galleryMedia.length" class="lp-section" id="showcase">
        <div class="lp-container">
            <header class="lp-section-head">
                <h2 class="lp-section-title">@{{ page.i18n.showcaseHeading }}</h2>
                <p class="lp-section-sub">@{{ page.i18n.showcaseSub }}</p>
            </header>

            <div class="lp-stage" data-reveal>
                <button v-if="galleryMedia.length > 1" type="button" class="lp-stage-nav lp-stage-prev"
                        :aria-label="page.i18n.prev" @click="prevShot">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <div class="lp-stage-viewport"
                     @touchstart="onTouchStart" @touchend="onTouchEnd">
                    <div class="lp-shot lp-shot-stage" @click="openHeroLightbox">
                        <div class="lp-shot-bar"><span></span><span></span><span></span></div>
                        <video v-if="activeItem && activeItem.type === 'video'" :src="activeItem.url"
                               :poster="activeItem.thumb || ''" controls playsinline></video>
                        <img v-else-if="activeItem" :src="activeItem.url" :alt="page.title + ' — ' + (activeShot + 1)">
                    </div>
                </div>

                <button v-if="galleryMedia.length > 1" type="button" class="lp-stage-nav lp-stage-next"
                        :aria-label="page.i18n.next" @click="nextShot">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div v-if="galleryMedia.length > 1" class="lp-thumbs">
                <button v-for="(item, idx) in galleryMedia" :key="idx" type="button"
                        class="lp-thumb" :class="{ active: idx === activeShot, 'is-video': item.type === 'video' }"
                        @click="activeShot = idx">
                    <img :src="item.thumb || item.url" :alt="page.title + ' — ' + (idx + 1)">
                    <span v-if="item.type === 'video'" class="lp-thumb-play" :aria-label="page.i18n.playVideo">
                        <i class="fas fa-play"></i>
                    </span>
                </button>
            </div>
        </div>
    </section>

    {{-- 6. OUTCOMES --------------------------------------------------------}}
    <section v-if="page.outcomes.length" class="lp-section" id="outcomes">
        <div class="lp-container">
            <header class="lp-section-head">
                <h2 class="lp-section-title">@{{ page.i18n.outcomesHeading }}</h2>
            </header>
            <div class="lp-outcome-grid">
                <article v-for="(item, idx) in page.outcomes" :key="idx" class="lp-outcome" data-reveal>
                    <span class="lp-outcome-icon"><i :class="'fas fa-' + item.icon"></i></span>
                    <h3>@{{ item.title }}</h3>
                    <p>@{{ item.text }}</p>
                </article>
            </div>
        </div>
    </section>

    {{-- 7. WHAT YOU GET AFTER PURCHASE ------------------------------------}}
    <section v-if="page.whatYouGetItems.length || page.whatYouGetHtml" class="lp-section lp-section-dark" id="delivery">
        <div class="lp-container lp-delivery">
            <div class="lp-delivery-copy" data-reveal>
                <span class="lp-badge lp-badge-solid"><i class="fas fa-box-open"></i> @{{ page.i18n.whatYouGetHeading }}</span>
                <h2>@{{ page.title }}</h2>
                <p>@{{ page.i18n.hostingText }}</p>
                <p>@{{ page.i18n.instantAccess }}</p>
                <button type="button" class="lp-btn lp-btn-lg" @click="openBuy">
                    <i class="fas fa-bolt"></i> @{{ page.cta }}
                </button>
            </div>
            <div class="lp-delivery-card" data-reveal>
                <ul v-if="page.whatYouGetItems.length" class="lp-check-list">
                    <li v-for="(item, idx) in page.whatYouGetItems" :key="idx">
                        <i class="fas fa-check-circle"></i> @{{ item }}
                    </li>
                </ul>
                <div v-else class="lp-prose lp-prose-light" v-html="page.whatYouGetHtml"></div>
            </div>
        </div>
    </section>

    {{-- 9. PRICING / OFFER -------------------------------------------------}}
    <section class="lp-section lp-section-tint" id="pricing">
        <div class="lp-container">
            <header class="lp-section-head">
                <h2 class="lp-section-title">@{{ page.i18n.pricingHeading }}</h2>
                <p class="lp-section-sub">@{{ page.i18n.pricingSub }}</p>
            </header>

            <div class="lp-price-card" data-reveal>
                <span class="lp-price-tag">@{{ page.i18n.lifetimeLabel }}</span>
                <h3 class="lp-price-product">@{{ page.title }}</h3>
                <div class="lp-price-amount">
                    <span class="lp-price-value">@{{ page.price.symbol }}@{{ formatMoney(page.price.amount) }}@{{ page.price.suffix }}</span>
                    <span class="lp-price-currency">@{{ page.price.currency }}</span>
                </div>
                <p class="lp-price-note">@{{ page.i18n.paymentNote }}</p>

                <ul v-if="page.included.length" class="lp-check-list lp-check-list-center">
                    <li v-for="(item, idx) in page.included" :key="idx">
                        <i class="fas fa-check-circle"></i> @{{ item }}
                    </li>
                </ul>

                <button type="button" class="lp-btn lp-btn-lg lp-btn-block" @click="openBuy">
                    <i class="fas fa-bolt"></i> @{{ page.cta }}
                </button>

                <p class="lp-price-secure">
                    <i class="fas fa-lock"></i> @{{ page.i18n.secureNote }}
                </p>
                <p v-if="page.extended" class="lp-price-extended">
                    @{{ page.i18n.extendedNote }}
                    <strong>@{{ page.extended.symbol }}@{{ formatMoney(page.extended.amount) }}@{{ page.extended.suffix }}</strong>
                </p>
            </div>
        </div>
    </section>

    {{-- 9b. REVIEWS --------------------------------------------------------}}
    <section v-if="page.reviews && page.reviews.items && page.reviews.items.length" class="lp-section lp-section-tint lp-reviews-section" id="reviews">
        <div class="lp-container">
            <header class="lp-section-head">
                <span class="lp-reviews-badge"><i class="fas fa-star"></i> @{{ page.reviews.rating }} / 5 · @{{ page.reviews.count }} @{{ page.i18n.reviewsCountLabel }}</span>
                <h2 class="lp-section-title">@{{ page.i18n.reviewsHeading }}</h2>
                <p v-if="page.reviews.highlights && page.reviews.highlights.length" class="lp-reviews-ai-lead">@{{ page.i18n.reviewsAiLead }}</p>
            </header>

            <div v-if="page.reviews.highlights && page.reviews.highlights.length" class="lp-reviews-highlights">
                <span v-for="(tag, idx) in page.reviews.highlights" :key="'hl' + idx" class="lp-review-chip">
                    <i class="fas fa-check-circle"></i> @{{ tag }}
                </span>
            </div>

            <div class="lp-reviews-hero" data-reveal>
                <div class="lp-reviews-score-card">
                    <div class="lp-reviews-score-main">
                        <span class="lp-reviews-score-num">@{{ page.reviews.rating }}</span>
                        <div class="lp-reviews-score-meta">
                            <span class="lp-stars lp-stars-lg">@{{ starString(Math.round(page.reviews.rating)) }}</span>
                            <em>@{{ page.reviews.count }} @{{ page.i18n.reviewsCountLabel }}</em>
                        </div>
                    </div>
                    <div class="lp-reviews-recommend">
                        <i class="fas fa-thumbs-up"></i>
                        <strong>@{{ page.reviews.recommend }}%</strong>
                        <span>@{{ page.i18n.recommendLabel }}</span>
                    </div>
                </div>
                <div class="lp-reviews-metrics">
                    <div class="lp-metric">
                        <div class="lp-metric-head">
                            <span>@{{ page.i18n.itemQuality }}</span>
                            <b>@{{ page.reviews.quality }}</b>
                        </div>
                        <div class="lp-metric-bar"><span class="lp-metric-fill" :style="{ width: (page.reviews.quality / 5 * 100) + '%' }"></span></div>
                    </div>
                    <div class="lp-metric">
                        <div class="lp-metric-head">
                            <span>@{{ page.i18n.shippingLabel }}</span>
                            <b>@{{ page.reviews.shipping }}</b>
                        </div>
                        <div class="lp-metric-bar"><span class="lp-metric-fill" :style="{ width: (page.reviews.shipping / 5 * 100) + '%' }"></span></div>
                    </div>
                    <div class="lp-metric">
                        <div class="lp-metric-head">
                            <span>@{{ page.i18n.serviceLabel }}</span>
                            <b>@{{ page.reviews.service }}</b>
                        </div>
                        <div class="lp-metric-bar"><span class="lp-metric-fill" :style="{ width: (page.reviews.service / 5 * 100) + '%' }"></span></div>
                    </div>
                </div>
            </div>

            <div v-if="reviewThemes.length" class="lp-review-filters" data-reveal>
                <button type="button" class="lp-filter-pill" :class="{ active: !reviewFilter }" @click="reviewFilter = ''">{{ $lpT('All') }}</button>
                <button v-for="theme in reviewThemes" :key="theme" type="button" class="lp-filter-pill"
                        :class="{ active: reviewFilter === theme }" @click="reviewFilter = (reviewFilter === theme ? '' : theme)">
                    @{{ theme }}
                </button>
            </div>

            <div class="lp-review-grid">
                <article v-for="(review, idx) in filteredReviews" :key="'rv' + idx" class="lp-review-card" data-reveal>
                    <div class="lp-review-quote" aria-hidden="true"><i class="fas fa-quote-left"></i></div>
                    <header class="lp-review-head">
                        <span class="lp-stars lp-stars-sm">@{{ starString(review.stars) }}</span>
                        <span v-if="review.theme" class="lp-review-theme-tag">@{{ review.theme }}</span>
                    </header>
                    <p class="lp-review-text">@{{ review.text }}</p>
                    <footer class="lp-review-foot">
                        <span class="lp-review-avatar">@{{ (review.name || 'B').charAt(0).toUpperCase() }}</span>
                        <div class="lp-review-author">
                            <strong>@{{ review.name }}</strong>
                            <span v-if="review.date">@{{ review.date }}</span>
                        </div>
                        <span class="lp-review-verified"><i class="fas fa-check-circle"></i> @{{ page.i18n.thisItem }}</span>
                    </footer>
                    <div v-if="review.response" class="lp-review-response">
                        <span class="lp-review-avatar lp-review-avatar-seller"><i class="fas fa-store"></i></span>
                        <div>
                            <strong>@{{ page.i18n.sellerResponse }}</strong>
                            <p>@{{ review.response }}</p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- 10. FAQ ------------------------------------------------------------}}
    <section v-if="page.faq.length" class="lp-section" id="faq">
        <div class="lp-container lp-faq-wrap">
            <header class="lp-section-head">
                <h2 class="lp-section-title">@{{ page.i18n.faqHeading }}</h2>
            </header>
            <div class="lp-faq">
                <article v-for="(item, idx) in page.faq" :key="idx" class="lp-faq-item" :class="{ open: openFaq === idx }">
                    <button type="button" class="lp-faq-q" @click="toggleFaq(idx)" :aria-expanded="openFaq === idx ? 'true' : 'false'">
                        <span>@{{ item.q }}</span>
                        <i class="fas" :class="openFaq === idx ? 'fa-minus' : 'fa-plus'"></i>
                    </button>
                    <div class="lp-faq-a" v-show="openFaq === idx"><p>@{{ item.a }}</p></div>
                </article>
            </div>
        </div>
    </section>

    {{-- 11. CONTACT --------------------------------------------------------}}
    <section class="lp-section lp-section-tint" id="contact">
        <div class="lp-container">
            <div class="lp-contact-grid">
                <div class="lp-contact-copy">
                    <span class="lp-badge"><i class="fas fa-envelope"></i> {{ $lpT('Get in touch') }}</span>
                    <h2 class="lp-section-title">{{ $lpT('Have a question before you buy?') }}</h2>
                    <p>{{ $lpT('Send us a note and we will reply by email, usually within a few hours.') }}</p>
                </div>
                <div class="lp-contact-card">
                    <div class="lp-contact-card-head">
                        <span class="lp-contact-card-icon"><i class="fas fa-paper-plane"></i></span>
                        <div>
                            <h3>{{ $lpT('Send us a message') }}</h3>
                            <p>{{ $lpT('We usually reply within a few hours.') }}</p>
                        </div>
                    </div>
                    <div id="lpContactAlert" class="lp-contact-alert" style="display:none;"></div>
                    <form id="lpContactForm" action="{{ url('/landing/' . $landing_page->lp_slug . '/send-message') }}" method="POST">
                        @csrf
                        <input type="hidden" name="lp_id" value="{{ $landing_page->lp_id }}">
                        <div class="lp-contact-row">
                            <div class="lp-contact-field">
                                <label for="lpm_name">{{ $lpT('Your Name') }} *</label>
                                <span class="lp-contact-input">
                                    <i class="fas fa-user"></i>
                                    <input type="text" name="lpm_name" id="lpm_name" required placeholder="{{ $lpT('Jane Smith') }}">
                                </span>
                            </div>
                            <div class="lp-contact-field">
                                <label for="lpm_email">{{ $lpT('Your Email') }} *</label>
                                <span class="lp-contact-input">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" name="lpm_email" id="lpm_email" required placeholder="you@email.com">
                                </span>
                            </div>
                        </div>
                        <div class="lp-contact-row">
                            <div class="lp-contact-field">
                                <label for="lpm_phone">{{ $lpT('Phone (optional)') }}</label>
                                <span class="lp-contact-input">
                                    <i class="fas fa-phone"></i>
                                    <input type="text" name="lpm_phone" id="lpm_phone" placeholder="{{ $lpT('WhatsApp or mobile') }}">
                                </span>
                            </div>
                            <div class="lp-contact-field">
                                <label for="lpm_subject">{{ $lpT('Subject (optional)') }}</label>
                                <span class="lp-contact-input">
                                    <i class="fas fa-tag"></i>
                                    <input type="text" name="lpm_subject" id="lpm_subject" placeholder="{{ $lpT('Hosting, licence, features…') }}">
                                </span>
                            </div>
                        </div>
                        <div class="lp-contact-field">
                            <label for="lpm_message">{{ $lpT('Your message') }} *</label>
                            <span class="lp-contact-input lp-contact-input-area">
                                <i class="fas fa-comment-dots"></i>
                                <textarea name="lpm_message" id="lpm_message" rows="5" required placeholder="{{ $lpT('Ask about hosting, features, licence, or anything else…') }}"></textarea>
                            </span>
                        </div>
                        <button type="submit" class="lp-btn lp-btn-block lp-contact-submit" id="lpContactSubmitBtn">
                            <i class="fas fa-paper-plane"></i> {{ $lpT('Send Message') }}
                        </button>
                        <p class="lp-contact-fineprint">
                            <i class="fas fa-shield-alt"></i> {{ $lpT('Your details stay private. We only use them to reply to you.') }}
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- 12. FINAL CTA ------------------------------------------------------}}
    <section class="lp-final">
        <div class="lp-container lp-final-inner">
            <h2>@{{ page.i18n.finalHeading }}</h2>
            <p>@{{ page.tagline }}</p>
            <button type="button" class="lp-btn lp-btn-lg lp-btn-light" @click="openBuy">
                <i class="fas fa-bolt"></i> @{{ page.cta }} — @{{ page.price.symbol }}@{{ formatMoney(page.price.amount) }}@{{ page.price.suffix }}
            </button>
            <p class="lp-final-note">
                <i class="fas fa-lock"></i> @{{ page.i18n.paymentNote }}
            </p>
        </div>
    </section>

    <div v-if="lightbox !== null && galleryMedia[lightbox]" class="lp-lightbox" @click.self="lightbox = null">
        <button type="button" class="lp-lightbox-close" :aria-label="page.i18n.close" @click="lightbox = null">&times;</button>
        <video v-if="galleryMedia[lightbox].type === 'video'" :src="galleryMedia[lightbox].url" controls autoplay playsinline></video>
        <img v-else :src="galleryMedia[lightbox].url" :alt="page.title">
    </div>
</div>

{{-- Payment modal kept outside Vue for Bootstrap/jQuery compatibility --}}
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered lp-payment-dialog" role="document">
        <div class="modal-content lp-modal">
            <div class="modal-header lp-modal-header">
                <h5 class="modal-title"><i class="fas fa-lock mr-2"></i> {{ $lpT('Choose Payment Method') }}</h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;"><span>&times;</span></button>
            </div>
            <div class="modal-body lp-modal-body">
                <div class="text-center mb-4">
                    <h3 class="lp-modal-product">{{ $landing_page->lp_title }}</h3>
                    <div class="lp-modal-price">
                        @php
                            $lpModalPrice = (float) ($price_data['price'] ?? 0);
                            $lpModalDecimals = abs($lpModalPrice - round($lpModalPrice)) < 0.00001 ? 0 : 2;
                        @endphp
                        {{ $lpPricePrefix }}{{ $lpPricePrefix !== '' ? ' ' : '' }}{{ number_format($lpModalPrice, $lpModalDecimals) }}{{ $lpPriceSuffix }}
                    </div>
                </div>
                @php $landingPaymentMethods = $landing_payment_methods ?? []; @endphp
                <form id="paymentForm" action="{{ url('/landing/' . $landing_page->lp_slug . '/process-payment') }}" method="POST">
                    @csrf
                    @if(\Fickrr\Support\Store::isPhysical())
                    <div id="lpPaymentSelect">
                    <div id="lpPaymentError" class="alert alert-danger" style="display:none;"></div>
                    <div class="form-group">
                        <label for="customer_name">{{ $lpT('Full name') }} *</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control lp-modal-input" required value="{{ old('customer_name') }}">
                    </div>
                    <div class="form-group">
                        <label for="customer_email" style="font-weight:600;">{{ $lpT('Email Address') }} *</label>
                        <input type="email" name="customer_email" id="customer_email" class="form-control lp-modal-input" required value="{{ old('customer_email') }}">
                    </div>
                    <div class="form-group">
                        <label for="customer_phone">{{ $lpT('Phone') }} *</label>
                        <input type="text" name="customer_phone" id="customer_phone" class="form-control lp-modal-input" required value="{{ old('customer_phone') }}">
                    </div>
                    <div class="form-group">
                        <label for="address">{{ $lpT('Street address') }} *</label>
                        <textarea name="address" id="address" class="form-control lp-modal-input" required>{{ old('address') }}</textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="city">{{ $lpT('City') }} *</label>
                            <input type="text" name="city" id="city" class="form-control lp-modal-input" required value="{{ old('city') }}">
                        </div>
                        <div class="form-group col-6">
                            <label for="country">{{ $lpT('Country') }} *</label>
                            <input type="text" name="country" id="country" class="form-control lp-modal-input" required value="{{ old('country') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quantity">{{ $lpT('Quantity') }} *</label>
                        <input type="number" name="quantity" id="quantity" class="form-control lp-modal-input" min="1" max="99" value="{{ old('quantity', 1) }}" required>
                    </div>
                    @php
                        $lpShip = (float) ($landing_page->lp_shipping_fee ?? 0) + (float) config('store.shipping.flat_fee', 0);
                    @endphp
                    <p class="text-muted">{{ $lpT('Shipping') }}: {{ number_format($lpShip, 2) }} {{ $landing_page->lp_currency ?? 'USD' }} {{ $lpT('per unit, plus any store flat fee already included above.') }}</p>
                    <div class="payment-option selected" onclick="selectPayment('stripe')">
                        <div class="d-flex align-items-center">
                            <input type="radio" name="payment_method" id="stripe_radio" value="stripe" checked>
                            <div>
                                <h5><i class="fas fa-credit-card"></i> {{ $lpT('Card') }}</h5>
                                <p>{{ $lpT('Pay by card, then we ship the order. No charge is taken until card payments are turned on.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="payment-option" onclick="selectPayment('paypal')">
                        <div class="d-flex align-items-center">
                            <input type="radio" name="payment_method" id="paypal_radio" value="paypal">
                            <div>
                                <h5><i class="fab fa-paypal"></i> {{ $lpT('PayPal') }}</h5>
                                <p>{{ $lpT('Pay with PayPal, then we ship the order.') }}</p>
                            </div>
                        </div>
                    </div>
                    <button type="submit" id="paymentBtn" class="lp-modal-submit">
                        <i class="fas fa-box mr-2"></i> {{ $lpT('Place order') }}
                    </button>
                    </div>
                    @else
                    <div id="lpPaymentSelect">
                    <div id="lpPaymentError" class="alert alert-danger" style="display:none;"></div>
                    <div class="form-group">
                        <label for="customer_email" style="font-weight:600;">{{ $lpT('Email Address') }} *</label>
                        <input type="email" name="customer_email" id="customer_email" class="form-control lp-modal-input" required
                               placeholder="{{ $lpT('Enter your email address') }}">
                    </div>
                    @if(in_array('paypal', $landingPaymentMethods, true))
                    <div class="payment-option" onclick="selectPayment('paypal')">
                        <div class="d-flex align-items-center">
                            <input type="radio" name="payment_method" id="paypal_radio" value="paypal">
                            <div>
                                <h5><i class="fab fa-paypal" style="color:#0070ba;"></i> {{ $lpT('PayPal') }}</h5>
                                <p>{{ $lpT('the safer, easier way to pay') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(in_array('dodopayments', $landingPaymentMethods, true))
                    <div class="payment-option" onclick="selectPayment('dodopayments')">
                        <div class="d-flex align-items-center">
                            <input type="radio" name="payment_method" id="dodopayments_radio" value="dodopayments">
                            <div>
                                <h5><i class="fas fa-credit-card"></i> {{ $lpT('Credit/Debit Card') }}</h5>
                                <p>{{ $lpT('Pay securely with your card') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(in_array('whop', $landingPaymentMethods, true))
                    <div class="payment-option" onclick="selectPayment('whop')">
                        <div class="d-flex align-items-center">
                            <input type="radio" name="payment_method" id="whop_radio" value="whop">
                            <div>
                                <h5><i class="fas fa-credit-card"></i> {{ $lpT('Credit/Debit Card') }}</h5>
                                <p>{{ $lpT('Pay securely with your card') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(empty($landingPaymentMethods))
                    <p class="text-danger mb-3">{{ $lpT('No payment methods are currently available.') }}</p>
                    @endif
                    <button type="submit" id="paymentBtn" class="lp-modal-submit" disabled>
                        <i class="fas fa-lock mr-2"></i> {{ $lpT('Proceed to Payment') }}
                    </button>
                    </div>
                    @endif
                    <div id="lpCheckoutEmbedWrap" style="display:none;">
                        <div id="lpCheckoutEmbedError" class="alert alert-danger" style="display:none;"></div>
                        <iframe id="lpCheckoutEmbed" title="{{ $lpT('Secure Checkout') }}"></iframe>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="lp-page-data">{!! json_encode($pagePayload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ url('/public/admin/vendors/vue/vue.global.prod.js') }}"></script>
@php $lpJsFile = base_path('public/js/landing-vue.js'); @endphp
<script src="{{ url('/public/js/landing-vue.js') }}?v={{ file_exists($lpJsFile) ? filemtime($lpJsFile) : 1 }}"></script>
<script>
window.LP_ANALYTICS_CONFIG = {
    endpoint: "{{ url('/landing/track') }}",
    lpId: {{ (int) $landing_page->lp_id }},
    lpSlug: "{{ $landing_page->lp_slug }}",
    contentName: @json($landing_page->lp_title),
    pageType: "landing"
};
</script>
<script src="{{ url('/public/js/lp-analytics.js') }}"></script>
<script>
function resetPaymentModal() {
    $('#lpPaymentSelect').show();
    $('#lpCheckoutEmbedWrap').hide();
    $('#lpCheckoutEmbed').attr('src', 'about:blank');
    $('#lpCheckoutEmbedError').hide().text('');
    $('.lp-payment-dialog').removeClass('lp-payment-dialog-wide');
    $('#paymentModal .modal-title').html('<i class="fas fa-lock mr-2"></i> {{ $lpT("Choose Payment Method") }}');
    $('#paymentBtn').prop('disabled', false);
}
function showPaymentModal() {
    resetPaymentModal();
    $('#paymentModal').modal('show');
    var methods = $('.payment-option input[name="payment_method"]');
    if (methods.length === 1) {
        selectPayment(methods.val());
    }
    if (typeof window.lpTrack === 'function') window.lpTrack('payment_modal_opened');
}
function selectPayment(method) {
    $('.payment-option').removeClass('selected');
    $('input[value="' + method + '"]').closest('.payment-option').addClass('selected');
    $('input[value="' + method + '"]').prop('checked', true);
    checkFormValidity();
    if (typeof window.lpTrack === 'function') window.lpTrack('payment_method_selected', { method: method });
}
function checkFormValidity() {
    var email = $('#customer_email').val();
    var paymentMethod = $('input[name="payment_method"]:checked').val();
    var physicalOk = true;
    if ($('#customer_name').length) {
        physicalOk = $('#customer_name').val() && $('#customer_phone').val() && $('#address').val() && $('#city').val() && $('#country').val();
    }
    $('#paymentBtn').prop('disabled', !(email && email.includes('@') && paymentMethod && physicalOk));
}
function showEmbeddedCheckout(embedUrl) {
    $('#lpPaymentSelect').hide();
    $('.lp-payment-dialog').addClass('lp-payment-dialog-wide');
    $('#paymentModal .modal-title').html('<i class="fas fa-lock mr-2"></i> {{ $lpT("Secure Checkout") }}');
    $('#lpCheckoutEmbedWrap').show();
    $('#lpCheckoutEmbed').attr('src', embedUrl);
}
function trackBuyNowClick() {
    if (typeof window.lpTrack === 'function') window.lpTrack('buy_now_click');
}
$('#customer_email').on('input', checkFormValidity);
$('#paymentModal').on('hidden.bs.modal', resetPaymentModal);
$('#paymentForm').on('submit', function (e) {
    var method = $('input[name="payment_method"]:checked').val();
    if (typeof window.lpTrack === 'function') window.lpTrack('initiate_checkout', { method: method || null, has_email: !!$('#customer_email').val() });
    if (method === 'paypal' || method === 'stripe' || method === 'cod') {
        return true;
    }
    e.preventDefault();
    var submitBtn = $('#paymentBtn');
    var originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> {{ $lpT("Please wait...") }}');
    $('#lpPaymentError').hide().text('');
    $('#lpCheckoutEmbedError').hide().text('');
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function (response) {
            if (response && response.embed_url) {
                showEmbeddedCheckout(response.embed_url);
                submitBtn.prop('disabled', false).html(originalText);
                return;
            }
            submitBtn.prop('disabled', false).html(originalText);
            $('#lpPaymentError').text(response.message || '{{ $lpT('An error occurred. Please try again.') }}').show();
        },
        error: function (xhr) {
            submitBtn.prop('disabled', false).html(originalText);
            var errorMessage = '{{ $lpT('An error occurred. Please try again.') }}';
            if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
            $('#lpPaymentError').text(errorMessage).show();
        }
    });
});
$('#lpContactForm').on('submit', function (e) {
    e.preventDefault();
    if (typeof window.lpTrack === 'function') window.lpTrack('chat_message_submitted', { has_email: !!$('#lpm_email').val() });
    var submitBtn = $('#lpContactSubmitBtn');
    var alertBox = $('#lpContactAlert');
    var originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> {{ $lpT('Sending...') }}');
    alertBox.hide().removeClass('lp-contact-alert-error').text('');
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function (response) {
            submitBtn.prop('disabled', false).html(originalText);
            if (response.success) {
                if (typeof window.lpTrack === 'function') window.lpTrack('chat_message_sent');
                $('#lpContactForm')[0].reset();
                alertBox.addClass('lp-contact-alert-success').text(response.message).fadeIn();
                document.getElementById('contact').scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                alertBox.addClass('lp-contact-alert-error').text(response.message || '{{ $lpT('An error occurred. Please try again.') }}').fadeIn();
            }
        },
        error: function (xhr) {
            submitBtn.prop('disabled', false).html(originalText);
            var errorMessage = '{{ $lpT('An error occurred. Please try again.') }}';
            if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
            alertBox.addClass('lp-contact-alert-error').text(errorMessage).fadeIn();
        }
    });
});
setTimeout(function () { $('.lp-alerts .alert').fadeOut('slow'); }, 5000);
</script>
</body>
</html>
