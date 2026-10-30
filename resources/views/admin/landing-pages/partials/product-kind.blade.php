@php
    $kind = old('lp_product_kind', isset($landing_page) ? ($landing_page->lp_product_kind ?? 'script') : 'script');
    if (!in_array($kind, ['script', 'sheet', 'product'], true)) {
        $kind = 'script';
    }
@endphp
<div class="form-group">
    <label>{{ __('Product type') }} <span class="text-danger">*</span></label>
    <div class="mt-2">
        <div class="custom-control custom-radio">
            <input type="radio" class="custom-control-input" id="kind_script" name="lp_product_kind" value="script" {{ $kind === 'script' ? 'checked' : '' }}>
            <label class="custom-control-label" for="kind_script">
                <i class="fa fa-code"></i> {{ __('Script / web app') }}
            </label>
        </div>
        <small class="form-text text-muted d-block mb-2">{{ __('Needs hosting. Buyers can self-host or you can host it for them.') }}</small>
        <div class="custom-control custom-radio">
            <input type="radio" class="custom-control-input" id="kind_sheet" name="lp_product_kind" value="sheet" {{ $kind === 'sheet' ? 'checked' : '' }}>
            <label class="custom-control-label" for="kind_sheet">
                <i class="fa fa-table"></i> {{ __('Spreadsheet (Excel / Google Sheets)') }}
            </label>
        </div>
        <small class="form-text text-muted d-block mb-2">{{ __('Instant file download. No hosting required.') }}</small>
        <div class="custom-control custom-radio">
            <input type="radio" class="custom-control-input" id="kind_product" name="lp_product_kind" value="product" {{ $kind === 'product' ? 'checked' : '' }}>
            <label class="custom-control-label" for="kind_product">
                <i class="fa fa-box"></i> {{ __('Digital product (PDF, templates, assets, etc.)') }}
            </label>
        </div>
        <small class="form-text text-muted">{{ __('Instant file download for ebooks, templates, graphics, or other digital files.') }}</small>
    </div>
</div>
