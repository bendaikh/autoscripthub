<input type="hidden" name="physical_fields_present" value="1">
<div class="form-group">
    <label class="control-label mb-1">{{ __('SKU') }}</label>
    <input type="text" name="sku" class="form-control" value="{{ old('sku', $edit['item']->sku ?? '') }}" maxlength="80">
</div>
<div class="form-group">
    <label class="control-label mb-1">{{ __('Price') }}</label>
    <input type="text" name="regular_price" class="form-control" value="{{ old('regular_price', $edit['item']->regular_price ?? '') }}">
    <small>{{ __('Selling price for one unit. No licence or support period.') }}</small>
</div>
<div class="form-group">
    <label class="control-label mb-1">{{ __('Stock quantity') }}</label>
    <input type="number" name="stock_quantity" class="form-control" min="0" value="{{ old('stock_quantity', $edit['item']->stock_quantity ?? 0) }}">
</div>
<div class="form-group">
    <label class="control-label mb-1">{{ __('Weight (grams)') }}</label>
    <input type="number" name="weight_grams" class="form-control" min="0" value="{{ old('weight_grams', $edit['item']->weight_grams ?? '') }}">
</div>
<div class="form-group">
    <label class="control-label mb-1">{{ __('Shipping fee per unit') }}</label>
    <input type="number" step="0.01" min="0" name="shipping_fee" class="form-control" value="{{ old('shipping_fee', $edit['item']->shipping_fee ?? 0) }}">
    <small>{{ __('Added to the store flat shipping fee, unless the order qualifies for free shipping.') }}</small>
</div>
<div class="form-group">
    <label class="control-label mb-1">
        <input type="checkbox" name="track_inventory" value="1" @if(old('track_inventory', $edit['item']->track_inventory ?? 1)) checked @endif>
        {{ __('Track stock') }}
    </label>
</div>
<div class="form-group">
    <label class="control-label mb-1">{{ __('Options (size, colour, etc.)') }}</label>
    <p class="text-muted">{{ __('Leave the name blank to skip a row. Stock on an option is used instead of the main stock when that option is purchased.') }}</p>
    @for($i = 0; $i < 4; $i++)
    <div class="border rounded p-2 mb-2">
        <input type="text" name="variant_name[]" class="form-control mb-1" placeholder="{{ __('Option name') }}" value="{{ old('variant_name.'.$i) }}">
        <input type="text" name="variant_sku[]" class="form-control mb-1" placeholder="{{ __('SKU') }}" value="{{ old('variant_sku.'.$i) }}">
        <input type="number" step="0.01" name="variant_price[]" class="form-control mb-1" placeholder="{{ __('Price override') }}" value="{{ old('variant_price.'.$i) }}">
        <input type="number" name="variant_stock[]" class="form-control mb-1" placeholder="{{ __('Stock') }}" min="0" value="{{ old('variant_stock.'.$i) }}">
        <input type="number" name="variant_weight[]" class="form-control" placeholder="{{ __('Weight (grams)') }}" min="0" value="{{ old('variant_weight.'.$i) }}">
    </div>
    @endfor
</div>
