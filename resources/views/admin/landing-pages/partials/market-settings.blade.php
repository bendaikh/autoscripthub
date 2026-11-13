@php
    $countries = \Fickrr\Helpers\Helper::countryList();
    $selectedCountry = old('lp_target_country', isset($landing_page) ? ($landing_page->lp_target_country ?? '') : '');
    $aiLanguages = \Fickrr\Services\OpenAIService::contentLanguages();
    $selectedLanguage = old('lp_content_language', isset($landing_page) ? ($landing_page->lp_content_language ?? 'en') : 'en');
    if ($selectedLanguage === 'en' && isset($landing_page)) {
        $inferred = \Fickrr\Helpers\LandingPageI18n::inferLocaleFromCopy($landing_page);
        if ($inferred !== null) {
            $selectedLanguage = $inferred;
        }
    }
    $selectedLanguage = \Fickrr\Helpers\LandingPageI18n::normalizeLocale($selectedLanguage);
@endphp
<div class="form-group">
    <label for="lp_target_country">{{ __('Target country') }} <span class="text-danger">*</span></label>
    <select name="lp_target_country" id="lp_target_country" class="form-control" required>
        <option value="">{{ __('Select country') }}</option>
        @foreach($countries as $code => $name)
            <option value="{{ $code }}" @if($selectedCountry === $code) selected @endif>{{ $name }} ({{ $code }})</option>
        @endforeach
    </select>
    <small class="form-text text-muted">{{ __('The primary market for this landing page (used for reporting and ads/revenue tracking).') }}</small>
</div>

<div class="form-group">
    <label for="lp_content_language">{{ __('Landing page language') }} <span class="text-danger">*</span></label>
    <select name="lp_content_language" id="lp_content_language" class="form-control" required>
        @foreach($aiLanguages as $code => $name)
            <option value="{{ $code }}" @if($selectedLanguage === $code) selected @endif>{{ $code === 'ar' ? 'العربية' : $name }}</option>
        @endforeach
    </select>
    <small class="form-text text-muted">{{ __('All visitor-facing page labels and auto-generated sections will appear in this language.') }}</small>
</div>

<div class="form-group">
    <label for="lp_source_url">{{ __('Product source link') }}</label>
    <input
        type="url"
        name="lp_source_url"
        id="lp_source_url"
        class="form-control"
        value="{{ old('lp_source_url', isset($landing_page) ? ($landing_page->lp_source_url ?? '') : '') }}"
        placeholder="https://www.etsy.com/listing/..."
    >
    <small class="form-text text-muted">{{ __('Private admin-only link to the original product (e.g. Etsy). Never shown on the public landing page.') }}</small>
    @if($errors->has('lp_source_url'))
        <span class="text-danger">{{ $errors->first('lp_source_url') }}</span>
    @endif
</div>
