{{-- Shared AI landing page generator UI + script --}}
@php
    $openaiReady = $openai_ready ?? false;
    $aiLanguages = \Fickrr\Services\OpenAIService::contentLanguages();
    $selectedAiLanguage = old('lp_content_language', isset($landing_page) ? ($landing_page->lp_content_language ?? 'en') : 'en');
    if ($selectedAiLanguage === 'en' && isset($landing_page)) {
        $inferredAiLanguage = \Fickrr\Helpers\LandingPageI18n::inferLocaleFromCopy($landing_page);
        if ($inferredAiLanguage !== null) {
            $selectedAiLanguage = $inferredAiLanguage;
        }
    }
    $selectedAiLanguage = \Fickrr\Helpers\LandingPageI18n::normalizeLocale($selectedAiLanguage);
@endphp

<div class="card mb-3" style="border: 1px solid #0d9488; background: linear-gradient(135deg, #f0fdfa 0%, #fff 60%);">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div class="mb-2">
                <h5 class="mb-1" style="color:#0f766e;">
                    <i class="fa fa-magic"></i> {{ __('Generate with AI') }}
                </h5>
                <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                    {{ __('Enter a title and fill in About This Product with real product details, choose the language, then let OpenAI translate the title into that language and build conversion copy (including a short hero description), pick an accent colour, features, and SEO. Upload a banner yourself if you want one.') }}
                </p>
            </div>
            <div>
                @if($openaiReady)
                <button type="button" id="btn-generate-ai" class="btn btn-success">
                    <i class="fa fa-magic"></i> {{ __('Generate with AI') }}
                </button>
                @else
                <a href="{{ url('/admin/ai-api-connection') }}" class="btn btn-warning">
                    <i class="fa fa-plug"></i> {{ __('Connect OpenAI First') }}
                </a>
                @endif
            </div>
        </div>
        <div class="form-group mt-3 mb-0" style="max-width: 320px;">
            <label for="ai_content_language">{{ __('Landing page language') }}</label>
            <select id="ai_content_language" class="form-control">
                @foreach($aiLanguages as $code => $name)
                    <option value="{{ $code }}" @if($code === $selectedAiLanguage) selected @endif>{{ $name }}</option>
                @endforeach
            </select>
            <small class="form-text text-muted">{{ __('AI will write the generated copy in this language. This also updates the page language field below.') }}</small>
        </div>
        <div id="ai-generate-status" class="mt-2" style="display:none;"></div>
        <input type="hidden" name="ai_banner_image" id="ai_banner_image" value="">
    </div>
</div>

<script>
(function () {
    function bindLanguageSelects() {
        var aiLangSelect = document.getElementById('ai_content_language');
        var pageLangSelect = document.getElementById('lp_content_language');
        if (!aiLangSelect || !pageLangSelect || aiLangSelect.dataset.lpLangBound === '1') {
            return;
        }
        aiLangSelect.dataset.lpLangBound = '1';

        if (pageLangSelect.value) {
            aiLangSelect.value = pageLangSelect.value;
        } else if (aiLangSelect.value) {
            pageLangSelect.value = aiLangSelect.value;
        }

        aiLangSelect.addEventListener('change', function () {
            pageLangSelect.value = aiLangSelect.value;
        });
        pageLangSelect.addEventListener('change', function () {
            aiLangSelect.value = pageLangSelect.value;
        });

        var form = pageLangSelect.closest('form');
        if (form && form.dataset.lpLangSubmitBound !== '1') {
            form.dataset.lpLangSubmitBound = '1';
            form.addEventListener('submit', function () {
                if (aiLangSelect.value) {
                    pageLangSelect.value = aiLangSelect.value;
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindLanguageSelects);
    } else {
        bindLanguageSelects();
    }
})();
</script>

@if($openaiReady)
<script>
(function () {
    function setSummernote(selector, html) {
        if (window.jQuery && jQuery(selector).length && jQuery(selector).next('.note-editor').length) {
            jQuery(selector).summernote('code', html || '');
        } else {
            var el = document.querySelector(selector);
            if (el) el.value = html || '';
        }
    }

    function fillFeatures(features) {
        var container = document.getElementById('features-container');
        if (!container) return;
        container.innerHTML = '';
        (features && features.length ? features : ['']).forEach(function (feature) {
            var wrap = document.createElement('div');
            wrap.className = 'feature-item';
            wrap.innerHTML = '<div class="input-group">' +
                '<input type="text" name="features[]" class="form-control" value="' + String(feature).replace(/"/g, '&quot;') + '" placeholder="Enter a feature">' +
                '<div class="input-group-append">' +
                '<button type="button" class="btn btn-danger btn-remove-feature" onclick="removeFeature(this)"><i class="fa fa-times"></i></button>' +
                '</div></div>';
            container.appendChild(wrap);
        });
    }

    function setField(name, value) {
        var el = document.querySelector('[name="' + name + '"]');
        if (el) el.value = value || '';
    }

    function applyBanner(d) {
        if (!d || !d.banner_image) return;
        document.getElementById('ai_banner_image').value = d.banner_image;
        var preview = document.getElementById('ai-banner-preview');
        if (!preview) {
            preview = document.createElement('div');
            preview.id = 'ai-banner-preview';
            preview.className = 'mt-2';
            var bannerInput = document.querySelector('[name="lp_banner_image"]');
            if (bannerInput && bannerInput.parentNode) {
                bannerInput.parentNode.appendChild(preview);
            }
        }
        preview.innerHTML = '<p class="mb-1"><strong>{{ __("AI Banner Preview") }}</strong></p>' +
            '<img src="' + (d.banner_url || '') + '" alt="AI banner" style="max-width:100%;border-radius:8px;">';
    }

    function parseJsonResponse(r) {
        return r.text().then(function (text) {
            var data = null;
            try {
                data = text ? JSON.parse(text) : null;
            } catch (e) {
                data = null;
            }
            return { ok: r.ok, status: r.status, data: data, raw: text };
        });
    }

    function requestErrorMessage(res) {
        if (res.data && res.data.error) return res.data.error;
        if (res.status === 419) return '{{ __("Session expired. Refresh the page and try again.") }}';
        if (res.status === 504 || res.status === 502 || res.status === 0) {
            return '{{ __("The server timed out. Try again — copy may still work if you retry.") }}';
        }
        if (!res.data) {
            return '{{ __("Request failed (HTTP") }} ' + res.status + '). {{ __("Please try again.") }}';
        }
        return '{{ __("Generation failed") }}';
    }

    var btn = document.getElementById('btn-generate-ai');
    if (!btn) return;

    function getAboutProductText() {
        var html = '';
        if (window.jQuery && jQuery('#about_description').length && jQuery('#about_description').next('.note-editor').length) {
            html = jQuery('#about_description').summernote('code') || '';
        } else {
            var el = document.getElementById('about_description') || document.querySelector('[name="lp_about_description"]');
            html = el ? (el.value || '') : '';
        }
        return String(html).replace(/<[^>]+>/g, ' ').replace(/&nbsp;/gi, ' ').replace(/\s+/g, ' ').trim();
    }

    btn.addEventListener('click', function () {
        var title = (document.querySelector('[name="lp_title"]') || {}).value || '';
        var aboutText = getAboutProductText();

        var status = document.getElementById('ai-generate-status');
        if (!title.trim() || aboutText.length < 10) {
            status.style.display = 'block';
            status.innerHTML = '<div class="alert alert-warning mb-0">{{ __("Please enter a title and fill in About This Product (10+ characters) before generating. The AI uses that section to learn about your product.") }}</div>';
            return;
        }

        btn.disabled = true;
        status.style.display = 'block';
        status.innerHTML = '<div class="alert alert-info mb-0"><i class="fa fa-spinner fa-spin"></i> {{ __("Generating landing page copy…") }}</div>';

        var headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        };

        fetch('{{ route('admin.landing-pages.generate-ai') }}', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                lp_title: title.trim(),
                lp_about_description: aboutText,
                language: (document.getElementById('ai_content_language') || {}).value || 'en',
                product_kind: (document.querySelector('input[name="lp_product_kind"]:checked') || {}).value || 'script'
            })
        }).then(parseJsonResponse)
          .then(function (res) {
              if (!res.data || !res.data.success) {
                  throw new Error(requestErrorMessage(res));
              }

              var d = res.data.data || {};
              if (d.title) {
                  setField('lp_title', d.title);
              }
              setField('lp_tagline', d.tagline);
              setField('lp_cta_text', d.cta_text);
              if (d.accent_color) {
                  setField('lp_accent_color', d.accent_color);
                  var picker = document.getElementById('lp_accent_color_picker');
                  if (picker) picker.value = d.accent_color;
                  var swatch = document.getElementById('lp_accent_swatch');
                  if (swatch) swatch.style.background = d.accent_color;
              }
              setSummernote('#about_description', d.about_html);
              setSummernote('#what_you_get', d.what_you_get_html);
              if (d.description_html) {
                  var heroText = String(d.description_html).replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
                  setField('lp_description', heroText);
              }
              fillFeatures(d.features || []);
              setField('lp_meta_title', d.meta_title);
              setField('lp_meta_description', d.meta_description);
              setField('lp_meta_keywords', d.meta_keywords);
              if (d.url_slug) {
                  setField('lp_slug', d.url_slug);
                  var slugInput = document.getElementById('lp_slug');
                  if (slugInput) slugInput.dispatchEvent(new Event('input'));
              }
              if (d.suggested_price != null && !isNaN(d.suggested_price)) {
                  setField('lp_price', d.suggested_price);
              }
              if (d.reviews) {
                  var reviewsEl = document.getElementById('lp_reviews');
                  if (reviewsEl) {
                      reviewsEl.value = JSON.stringify(d.reviews, null, 2);
                  }
              }

              var aiLang = document.getElementById('ai_content_language');
              var pageLang = document.getElementById('lp_content_language');
              if (aiLang && pageLang) {
                  pageLang.value = aiLang.value;
              }

              btn.disabled = false;
              status.innerHTML = '<div class="alert alert-success mb-0"><i class="fa fa-check"></i> {{ __("AI content applied. Review the fields, then save.") }}</div>';
          })
          .catch(function (err) {
              btn.disabled = false;
              status.innerHTML = '<div class="alert alert-danger mb-0">' + (err && err.message ? err.message : '{{ __("Request failed. Please try again.") }}') + '</div>';
          });
    });
})();
</script>
@endif
