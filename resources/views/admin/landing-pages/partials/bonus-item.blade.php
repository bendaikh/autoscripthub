{{-- One bonus row in the Bonuses card. $it: key,row,title,description,delivery,link,value_label,badge_text,active; $pos: position --}}
@php
    $k = $it['key'];
    $row = $it['row'];
    $ek = 'bonuses.' . $k . '.';
    $cover = $row ? \Fickrr\Models\LandingBonus::imageUrl($row->image_path) : null;
    $hasFile = $row && $row->delivery_method === 'upload' && !empty($row->file_path);
@endphp
<div class="lpb-item @if(!$it['active']) is-inactive @endif" data-key="{{ $k }}">
    <input type="hidden" name="bonus_order[]" value="{{ $k }}">
    @if($row)<input type="hidden" class="lpb-id" name="bonuses[{{ $k }}][id]" value="{{ $row->id }}">@endif
    <input type="hidden" class="lpb-remove-flag" name="bonuses[{{ $k }}][remove]" value="0">
    <div class="lpb-head">
        <span class="lpb-handle" title="{{ __('Drag to reorder') }}"><i class="fa fa-bars"></i></span>
        <span class="lpb-num">BONUS #{{ $pos }}</span>
        <div class="lpb-tools">
            <label class="mb-0 small mr-2" style="cursor:pointer">
                <input type="hidden" name="bonuses[{{ $k }}][active]" value="0">
                <input type="checkbox" class="lpb-active" name="bonuses[{{ $k }}][active]" value="1" @if($it['active']) checked @endif> {{ __('Active') }}
            </label>
            <button type="button" class="btn btn-light btn-sm lpb-move" data-dir="up" title="{{ __('Move up') }}"><i class="fa fa-arrow-up"></i></button>
            <button type="button" class="btn btn-light btn-sm lpb-move" data-dir="down" title="{{ __('Move down') }}"><i class="fa fa-arrow-down"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm lpb-remove" title="{{ __('Remove') }}"><i class="fa fa-trash"></i></button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <div class="form-group">
                <label>{{ __('Title') }} <span class="text-danger">*</span></label>
                <input type="text" name="bonuses[{{ $k }}][title]" class="form-control" maxlength="80" value="{{ $it['title'] }}" placeholder="{{ __('e.g. 50 Scripture Memory Cards') }}">
                @if($errors->has($ek.'title'))<span class="lpb-err">{{ $errors->first($ek.'title') }}</span>@endif
            </div>
            <div class="form-group">
                <label>{{ __('Short description') }} <span class="text-danger">*</span></label>
                <textarea name="bonuses[{{ $k }}][description]" class="form-control" rows="3" maxlength="300" placeholder="{{ __('One or two sentences, plain text') }}">{{ $it['description'] }}</textarea>
                @if($errors->has($ek.'description'))<span class="lpb-err">{{ $errors->first($ek.'description') }}</span>@endif
            </div>
            <div class="row">
                <div class="col-sm-6 form-group">
                    <label>{{ __('Value label (optional)') }}</label>
                    <input type="text" name="bonuses[{{ $k }}][value_label]" class="form-control" maxlength="40" value="{{ $it['value_label'] }}" placeholder="$7 value">
                    <small class="form-text text-muted">{{ __('Use only real prices.') }}</small>
                    @if($errors->has($ek.'value_label'))<span class="lpb-err">{{ $errors->first($ek.'value_label') }}</span>@endif
                </div>
                <div class="col-sm-6 form-group">
                    <label>{{ __('Badge text (optional)') }}</label>
                    <input type="text" name="bonuses[{{ $k }}][badge_text]" class="form-control lpb-badge-input" maxlength="30" value="{{ $it['badge_text'] }}" placeholder="BONUS #{{ $pos }}">
                    @if($errors->has($ek.'badge_text'))<span class="lpb-err">{{ $errors->first($ek.'badge_text') }}</span>@endif
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label>{{ __('Cover image') }} <span class="text-danger">*</span></label>
                @if($cover)
                <div class="lpb-current"><img src="{{ $cover }}" alt="" class="lpb-cover"><span class="small text-muted">{{ __('Choose a file below to replace it') }}</span></div>
                @endif
                <input type="file" name="bonuses[{{ $k }}][cover]" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <small class="form-text text-muted">{{ __('JPG, PNG or WebP, max 5 MB, 4:5 (1080×1350) recommended.') }}</small>
                @if($errors->has($ek.'cover'))<span class="lpb-err">{{ $errors->first($ek.'cover') }}</span>@endif
            </div>
            <div class="form-group mb-2">
                <label class="d-block">{{ __('Delivery') }}</label>
                <label class="mr-3 mb-0" style="font-weight:normal"><input type="radio" class="lpb-delivery" name="bonuses[{{ $k }}][delivery]" value="upload" @if($it['delivery'] !== 'link') checked @endif> {{ __('Upload file') }}</label>
                <label class="mb-0" style="font-weight:normal"><input type="radio" class="lpb-delivery" name="bonuses[{{ $k }}][delivery]" value="link" @if($it['delivery'] === 'link') checked @endif> {{ __('External link') }}</label>
            </div>
            <div class="form-group lpb-file-wrap">
                @if($hasFile)
                <div class="lpb-filename mb-1"><i class="fa fa-file-o"></i> {{ $row->file_original_name ?: basename($row->file_path) }} <span class="text-muted">({{ \Fickrr\Models\LandingBonus::sizeLabel((int) $row->file_size) }})</span> · <span class="text-muted">{{ __('choose a file to replace it') }}</span></div>
                @endif
                <input type="file" name="bonuses[{{ $k }}][file]" class="form-control" accept=".pdf,.zip,.png,.jpg,.jpeg">
                <small class="form-text text-muted">{{ __('PDF, ZIP, PNG or JPG, max 100 MB. Kept private: buyers download it with a secure link.') }}</small>
                @if($errors->has($ek.'file'))<span class="lpb-err">{{ $errors->first($ek.'file') }}</span>@endif
            </div>
            <div class="form-group lpb-link-wrap">
                <input type="url" name="bonuses[{{ $k }}][link]" class="form-control" maxlength="500" value="{{ $it['link'] }}" placeholder="https://...">
                @if($errors->has($ek.'link'))<span class="lpb-err">{{ $errors->first($ek.'link') }}</span>@endif
            </div>
        </div>
    </div>
</div>
