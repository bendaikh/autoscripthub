@php
    $physical = \Fickrr\Support\Store::isPhysical();
    $kind = old('lp_product_kind', isset($landing_page) ? ($landing_page->lp_product_kind ?? 'script') : 'script');
    if ($physical) {
        $kind = 'physical';
    } elseif (!in_array($kind, ['script', 'sheet', 'product'], true)) {
        $kind = 'script';
    }
@endphp
<div class="form-group">
    <label>{{ __('Product type') }} <span class="text-danger">*</span></label>
    @if($physical)
        <input type="hidden" name="lp_product_kind" value="physical">
        <input type="hidden" name="lp_delivery_method" value="shipping">
        <p class="mb-2"><i class="fa fa-truck"></i> {{ __('Physical product. Customers enter a delivery address. No file or download link is required.') }}</p>
        <div class="form-group">
            <label>{{ __('SKU') }}</label>
            <input type="text" name="lp_sku" class="form-control" value="{{ old('lp_sku', $landing_page->lp_sku ?? '') }}">
        </div>
        <div class="form-group">
            <label>{{ __('Stock quantity') }}</label>
            <input type="number" min="0" name="lp_stock_quantity" class="form-control" value="{{ old('lp_stock_quantity', $landing_page->lp_stock_quantity ?? 0) }}">
        </div>
        <div class="form-group">
            <label>{{ __('Weight (grams)') }}</label>
            <input type="number" min="0" name="lp_weight_grams" class="form-control" value="{{ old('lp_weight_grams', $landing_page->lp_weight_grams ?? '') }}">
        </div>
        <div class="form-group">
            <label>{{ __('Shipping fee per unit') }}</label>
            <input type="number" min="0" step="0.01" name="lp_shipping_fee" class="form-control" value="{{ old('lp_shipping_fee', $landing_page->lp_shipping_fee ?? 0) }}">
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="lp_track_inventory" name="lp_track_inventory" value="1" @if(old('lp_track_inventory', $landing_page->lp_track_inventory ?? 1)) checked @endif>
            <label class="form-check-label" for="lp_track_inventory">{{ __('Track stock') }}</label>
        </div>
    @else
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
    @endif
</div>
