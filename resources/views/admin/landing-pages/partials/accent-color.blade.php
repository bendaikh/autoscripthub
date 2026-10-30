@php
    $accentValue = old('lp_accent_color', isset($landing_page) ? ($landing_page->lp_accent_color ?? '') : '');
    $pickerValue = $accentValue !== '' ? $accentValue : '#22c55e';
@endphp
<div class="form-group">
    <label>{{ __('Accent Colour') }}</label>
    <div class="d-flex align-items-center" style="gap:10px;flex-wrap:wrap;">
        <input type="color" id="lp_accent_color_picker" value="{{ $pickerValue }}"
               style="width:46px;height:38px;padding:0;border:1px solid #ced4da;border-radius:4px;cursor:pointer;"
               title="{{ __('Pick a colour') }}">
        <input type="text" name="lp_accent_color" id="lp_accent_color" class="form-control"
               value="{{ $accentValue }}" placeholder="#22c55e" maxlength="7"
               style="max-width:140px;font-family:monospace;">
        <span id="lp_accent_swatch" style="display:inline-block;width:22px;height:22px;border-radius:50%;border:1px solid #ddd;background:{{ $pickerValue }};"></span>
    </div>
    <small class="form-text text-muted">{{ __('AI picks this from the product type when you generate. Leave blank to auto-match from the title, or override it here.') }}</small>
</div>
<script>
(function () {
    var picker = document.getElementById('lp_accent_color_picker');
    var field = document.getElementById('lp_accent_color');
    var swatch = document.getElementById('lp_accent_swatch');
    if (!picker || !field) return;
    picker.addEventListener('input', function () {
        field.value = picker.value;
        if (swatch) swatch.style.background = picker.value;
    });
    field.addEventListener('input', function () {
        var v = field.value.trim();
        if (/^#[0-9a-fA-F]{6}$/.test(v)) {
            picker.value = v;
            if (swatch) swatch.style.background = v;
        }
    });
})();
</script>
