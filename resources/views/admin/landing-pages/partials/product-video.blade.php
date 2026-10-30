<div class="form-group">
    <label>{{ __('Product video') }}</label>
    @if(!empty($landing_page->lp_product_video ?? null))
        <div class="mb-2">
            <video src="{{ asset('storage/landing-pages/' . $landing_page->lp_product_video) }}" controls style="max-width:100%;max-height:220px;border-radius:8px;"></video>
            <div class="custom-control custom-checkbox mt-2">
                <input type="checkbox" class="custom-control-input" id="remove_product_video" name="remove_product_video" value="1">
                <label class="custom-control-label" for="remove_product_video">{{ __('Remove current video') }}</label>
            </div>
        </div>
    @endif
    <input type="file" name="lp_product_video" class="form-control" accept="video/mp4,video/webm,video/quicktime">
    <small class="form-text text-muted">{{ __('Shown in the screenshot gallery with a play button, like Etsy. MP4, WebM, or MOV. Max 50 MB.') }}</small>
    @if ($errors->has('lp_product_video'))
        <span class="text-danger">{{ $errors->first('lp_product_video') }}</span>
    @endif
</div>
