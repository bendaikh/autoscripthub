<!DOCTYPE HTML>
<html lang="en">
<head>
<title>{{ $allsettings->site_title }} - {{ $page['page']->page_title }}</title>
@if($page['page']->page_allow_seo == 1)
<meta name="Keywords" content="{{ $page['page']->page_seo_keyword }}">
<meta name="Description" content="{{ $page['page']->page_seo_desc }}">
@else
@include('meta')
@endif
@include('style')
@php
  $infoPages = [
    'privacy-policy' => [
      'icon' => 'fa-shield',
      'badge' => __('Your Privacy Matters'),
      'lead' => \Fickrr\Support\Store::isPhysical()
        ? __('ESIPRES collects the delivery details you submit so orders can be shipped. This page explains what is stored and how to ask about it.')
        : __('We are committed to protecting your personal data and being transparent about how information is collected, used, and safeguarded across AutoScriptHub.'),
      'cta_title' => __('Questions about this page?'),
      'cta_text' => \Fickrr\Support\Store::isPhysical()
        ? __('Write to the store if you have a question about an order, a delivery, or this page.')
        : __('Our support team is here to help with privacy, billing, licensing, or account questions.'),
      'related' => [
        ['slug' => 'terms-and-conditions', 'title' => __('Terms & Conditions')],
        ['slug' => 'refund-policy', 'title' => __('Refund Policy')],
      ],
      'layout' => 'prose',
    ],
    'terms-and-conditions' => [
      'icon' => 'fa-file-text-o',
      'badge' => __('Legal Agreement'),
      'lead' => \Fickrr\Support\Store::isPhysical()
        ? __('These terms explain how orders for physical products work at ESIPRES, including delivery details and online payment.')
        : __('These terms explain how AutoScriptHub works, what you can expect from our digital marketplace, and the responsibilities that apply when you browse, purchase, or sell on our platform.'),
      'cta_title' => __('Questions about this page?'),
      'cta_text' => \Fickrr\Support\Store::isPhysical()
        ? __('Write to the store if you have a question about an order, a delivery, or this page.')
        : __('Our support team is here to help with privacy, billing, licensing, or account questions.'),
      'related' => [
        ['slug' => 'privacy-policy', 'title' => __('Privacy Policy')],
        ['slug' => 'refund-policy', 'title' => __('Refund Policy')],
      ],
      'layout' => 'prose',
    ],
    'refund-policy' => [
      'icon' => 'fa-undo',
      'badge' => __('Fair & Transparent'),
      'lead' => \Fickrr\Support\Store::isPhysical()
        ? __('ESIPRES ships physical products. This page explains how to ask about a return after an order has been delivered.')
        : __('Because AutoScriptHub sells digital products, purchases are generally final. This page explains when refunds may be considered and how our support team can help before a refund is requested.'),
      'cta_title' => __('Need help with an order?'),
      'cta_text' => __('Contact support first — most issues can be resolved quickly without needing a refund request.'),
      'related' => [
        ['slug' => 'faq', 'title' => __('FAQ')],
        ['slug' => 'terms-and-conditions', 'title' => __('Terms & Conditions')],
      ],
      'layout' => 'prose',
    ],
    'faq' => [
      'icon' => 'fa-question-circle',
      'badge' => __('Help Center'),
      'lead' => \Fickrr\Support\Store::isPhysical()
        ? __('Answers about ordering, shipping, card and PayPal payment, and returns. This store does not sell downloads or licences.')
        : __('Quick answers about purchasing, downloads, licensing, payments, installation support, and how to get help from the AutoScriptHub team.'),
      'cta_title' => __('Still have a question?'),
      'cta_text' => __('If you cannot find what you need here, our support team is ready to help.'),
      'related' => [
        ['slug' => 'refund-policy', 'title' => __('Refund Policy')],
        ['slug' => 'contact', 'title' => __('Contact Support')],
      ],
      'layout' => 'faq',
    ],
  ];
  $pageSlug = $page['page']->page_slug;
  $isInfoPage = array_key_exists($pageSlug, $infoPages);
  $info = $isInfoPage ? $infoPages[$pageSlug] : null;
@endphp
@if($isInfoPage)
<link rel="stylesheet" href="{{ asset('assets/legal-page.css') }}?v=4">
@endif
</head>
<body>
@include('header')

@if($isInfoPage)
<section class="legal-hero">
  <div class="container">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-start mb-0" style="background:transparent;">
      <li class="breadcrumb-item"><a class="text-white-50" href="{{ URL::to('/') }}">{{ __('Home') }}</a></li>
      <li class="breadcrumb-item text-white active" aria-current="page">{{ $page['page']->page_title }}</li>
    </ol>
  </nav>
  <span class="legal-hero-badge">{{ $info['badge'] }}</span>
  <h1 class="legal-hero-title">{{ $page['page']->page_title }}</h1>
  <p class="legal-hero-lead">{{ $info['lead'] }}</p>
  <div class="legal-meta">
    <span class="legal-meta-pill">{{ __('Effective') }}: {{ __('January 24, 2025') }}</span>
    <span class="legal-meta-pill">{{ __('Last updated') }}: {{ __('August 29, 2026') }}</span>
  </div>
  </div>
</section>

<div class="container legal-wrap">
  @if(in_array('pages', $top_ads ?? []))
  <div class="row">
    <div class="col-lg-12 mb-4 text-center">
      @php echo html_entity_decode($addition_settings->top_ads); @endphp
    </div>
  </div>
  @endif

  <div class="legal-grid @if($info['layout'] === 'faq') legal-grid-faq @endif">
    <aside class="legal-sidebar">
      @if($info['layout'] !== 'faq')
      <div class="legal-sidebar-card">
        <div class="legal-sidebar-title">{{ __('On this page') }}</div>
        <ul class="legal-toc" id="legal-toc"></ul>
      </div>
      @else
      <div class="legal-sidebar-card">
        <div class="legal-sidebar-title">{{ __('Quick topics') }}</div>
        <ul class="legal-toc" id="legal-toc"></ul>
      </div>
      @endif
      <div class="legal-sidebar-card">
        <div class="legal-sidebar-title">{{ __('Related') }}</div>
        <div class="legal-quick-links">
          @foreach($info['related'] as $related)
          <a class="legal-quick-link" href="{{ URL::to('/') }}/{{ $related['slug'] }}">
            <span>{{ $related['title'] }}</span>
          </a>
          @endforeach
        </div>
      </div>
    </aside>

    <main>
      <article class="legal-content-card">
        <div class="legal-prose @if($info['layout'] === 'faq') legal-prose-faq @endif" id="legal-content">
          @php echo html_entity_decode($page['page']->page_desc); @endphp
        </div>

        <div class="legal-cta">
          <div>
            <h3>{{ $info['cta_title'] }}</h3>
            <p>{{ $info['cta_text'] }}</p>
          </div>
          <div class="legal-cta-actions">
            <a class="legal-btn legal-btn-primary" href="mailto:{{ $allsettings->office_email ?? 'support@auto-script-hub.com' }}">
              {{ __('Email Support') }}
            </a>
            <a class="legal-btn legal-btn-ghost" href="{{ URL::to('/contact') }}">
              {{ __('Contact Us') }}
            </a>
          </div>
        </div>
      </article>
    </main>
  </div>

  @if(in_array('pages', $bottom_ads ?? []))
  <div class="row mt-4">
    <div class="col-lg-12 mb-4 text-center">
      @php echo html_entity_decode($addition_settings->bottom_ads); @endphp
    </div>
  </div>
  @endif
</div>
@else
<section class="bg-position-center-top" style="background-image: url('{{ url('/') }}/public/storage/settings/{{ $allsettings->site_banner }}');">
  <div class="py-4">
    <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
      <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb flex-lg-nowrap justify-content-center justify-content-lg-star">
            <li class="breadcrumb-item"><a class="text-nowrap" href="{{ URL::to('/') }}"><i class="dwg-home"></i>{{ __('Home') }}</a></li>
            <li class="breadcrumb-item text-nowrap active" aria-current="page">{{ $page['page']->page_title }}</li>
          </ol>
        </nav>
      </div>
      <div class="order-lg-1 pr-lg-4 text-center text-lg-left">
        <h1 class="h3 mb-0 text-white">{{ $page['page']->page_title }}</h1>
      </div>
    </div>
  </div>
</section>
<div class="container py-5 mt-md-2 mb-2">
  @if(in_array('pages', $top_ads ?? []))
  <div class="row">
    <div class="col-lg-12 mb-4" align="center">
      @php echo html_entity_decode($addition_settings->top_ads); @endphp
    </div>
  </div>
  @endif
  <div class="row">
    <div class="col-lg-12" data-aos="fade-up" data-aos-delay="200">
      <div class="font-size-md">@php echo html_entity_decode($page['page']->page_desc); @endphp</div>
    </div>
  </div>
  @if(in_array('pages', $bottom_ads ?? []))
  <div class="row">
    <div class="col-lg-12 mb-4" align="center">
      @php echo html_entity_decode($addition_settings->bottom_ads); @endphp
    </div>
  </div>
  @endif
</div>
@endif

@include('footer')
@include('script')
@if($isInfoPage)
<script>
(function () {
  var content = document.getElementById('legal-content');
  var toc = document.getElementById('legal-toc');
  if (!content || !toc) return;

  function cleanTitle(text) {
    return String(text || '').replace(/\*+/g, '').replace(/^\d+\.\s*/, '').trim();
  }

  content.querySelectorAll('h2, h3').forEach(function (heading) {
    heading.textContent = cleanTitle(heading.textContent);
  });

  var faqItems = content.querySelectorAll('.legal-faq-item');
  if (faqItems.length) {
    faqItems.forEach(function (item, index) {
      var button = item.querySelector('.legal-faq-question');
      var answer = item.querySelector('.legal-faq-answer');
      if (!button || !answer) return;

      var sectionId = button.getAttribute('data-target') || ('faq-section-' + (index + 1));
      item.id = sectionId;

      var li = document.createElement('li');
      var link = document.createElement('a');
      link.href = '#' + sectionId;
      link.textContent = cleanTitle(button.textContent);
      li.appendChild(link);
      toc.appendChild(li);

      button.addEventListener('click', function () {
        var isOpen = item.classList.contains('is-open');
        faqItems.forEach(function (other) {
          other.classList.remove('is-open');
          var otherBtn = other.querySelector('.legal-faq-question');
          if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
        });
        if (!isOpen) {
          item.classList.add('is-open');
          button.setAttribute('aria-expanded', 'true');
        }
      });
    });

    if (faqItems[0]) {
      faqItems[0].classList.add('is-open');
      var firstBtn = faqItems[0].querySelector('.legal-faq-question');
      if (firstBtn) firstBtn.setAttribute('aria-expanded', 'true');
    }
    return;
  }

  var headings = content.querySelectorAll('h2, h3');
  headings.forEach(function (heading, index) {
    if (!heading.id) {
      heading.id = 'legal-section-' + (index + 1);
    }

    var li = document.createElement('li');
    var link = document.createElement('a');
    link.href = '#' + heading.id;
    link.textContent = cleanTitle(heading.textContent);
    if (heading.tagName === 'H3') {
      link.style.paddingLeft = '1.15rem';
      link.style.fontSize = '.86rem';
    }
    li.appendChild(link);
    toc.appendChild(li);
  });

  var links = toc.querySelectorAll('a');
  function setActive() {
    var activeId = '';
    headings.forEach(function (heading) {
      var rect = heading.getBoundingClientRect();
      if (rect.top <= 140) activeId = heading.id;
    });
    links.forEach(function (link) {
      link.classList.toggle('is-active', link.getAttribute('href') === '#' + activeId);
    });
  }

  window.addEventListener('scroll', setActive, { passive: true });
  setActive();
})();
</script>
@endif
</body>
</html>
