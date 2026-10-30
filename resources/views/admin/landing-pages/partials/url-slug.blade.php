@php
    $slugValue = old('lp_slug', $landing_page->lp_slug ?? '');
    $landingBase = url('/landing');
@endphp
<div class="form-group">
    <label for="lp_slug">{{ __('URL Slug') }} <span class="text-danger">*</span></label>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text text-muted" style="font-size:.85rem;">/landing/</span>
        </div>
        <input type="text" name="lp_slug" id="lp_slug" class="form-control"
               value="{{ $slugValue }}" required
               pattern="[a-z0-9]+(-[a-z0-9]+)*"
               maxlength="60"
               placeholder="kids-coloring-pages"
               autocomplete="off">
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-secondary" id="btn-slugify" title="{{ __('Format slug') }}">
                <i class="fa fa-text-width"></i>
            </button>
        </div>
    </div>
    <small class="form-text text-muted">
        {{ __('English only — 2 to 4 short words, lowercase, separated by hyphens. Example:') }}
        <code>coloring-pages-kids</code>.
        {{ __('AI will suggest one when you generate content.') }}
    </small>
    <div class="mt-1" style="font-size:.9rem;">
        <strong>{{ __('Public URL:') }}</strong>
        <a href="{{ $slugValue ? $landingBase . '/' . $slugValue : '#' }}" target="_blank" id="lp_slug_preview_link">
            <span id="lp_slug_preview">{{ $slugValue ? $landingBase . '/' . $slugValue : $landingBase . '/…' }}</span>
        </a>
    </div>
    @if ($errors->has('lp_slug'))
    <span class="text-danger d-block">{{ $errors->first('lp_slug') }}</span>
    @endif
</div>
<script>
(function () {
    var input = document.getElementById('lp_slug');
    var preview = document.getElementById('lp_slug_preview');
    var previewLink = document.getElementById('lp_slug_preview_link');
    var btn = document.getElementById('btn-slugify');
    if (!input || !preview) return;

    var base = @json($landingBase);

    function slugify(value) {
        return String(value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/[\s_]+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '')
            .slice(0, 60)
            .replace(/-$/, '');
    }

    function refreshPreview() {
        var slug = slugify(input.value);
        if (slug) {
            preview.textContent = base + '/' + slug;
            previewLink.href = base + '/' + slug;
        } else {
            preview.textContent = base + '/…';
            previewLink.href = '#';
        }
    }

    input.addEventListener('input', refreshPreview);
    btn && btn.addEventListener('click', function () {
        input.value = slugify(input.value);
        refreshPreview();
    });
    refreshPreview();
})();
</script>
