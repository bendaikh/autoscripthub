<div class="form-group">
    <label class="control-label mb-1" for="single_price">
        <input type="checkbox" id="single_price" name="single_price" value="1" @if(!empty($singlePriceOn)) checked @endif>
        {{ __('Single price (license) category') }}
    </label>
    <small class="form-text text-muted">{{ $singlePriceHint }}</small>
</div>
