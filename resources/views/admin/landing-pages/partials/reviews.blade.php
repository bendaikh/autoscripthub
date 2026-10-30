@php
    $reviewsJson = old('lp_reviews', isset($landing_page) ? ($landing_page->lp_reviews ?? '') : '');
    if (is_array($reviewsJson)) {
        $reviewsJson = json_encode($reviewsJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } elseif (is_string($reviewsJson) && $reviewsJson !== '') {
        $decoded = json_decode($reviewsJson, true);
        if (is_array($decoded)) {
            $reviewsJson = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }
@endphp
<div class="form-group">
    <label>{{ __('Reviews') }}</label>
    <p class="text-muted mb-2" style="font-size:0.9rem;">
        {{ __('AI fills this when you generate the landing page. You can edit the JSON before saving.') }}
    </p>
    <textarea name="lp_reviews" id="lp_reviews" class="form-control" rows="8" placeholder='{"rating":4.8,"count":24,"items":[]}'>{{ $reviewsJson }}</textarea>
</div>
