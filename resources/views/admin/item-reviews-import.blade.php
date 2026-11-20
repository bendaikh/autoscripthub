<!doctype html>
<html class="no-js" lang="en">
<head>
    @include('admin.stylesheet')
    <style>
    .irv-help code{background:#f3f4f6;padding:2px 6px;border-radius:4px}
    .irv-tabs .btn{min-width:130px}
    .irv-stars{color:#f59e0b;letter-spacing:1px;white-space:nowrap}
    </style>
</head>
<body>
@include('admin.navigation')
<div id="right-panel" class="right-panel">
    @include('admin.header')
    <div class="breadcrumbs">
        <div class="col-sm-12">
            <div class="page-header float-left">
                <div class="page-title"><h1>{{ __('Import reviews & comments') }}</h1></div>
            </div>
        </div>
    </div>
    @include('admin.warning')
    <div class="content mt-3">
        <div class="animated fadeIn">
            <p><a href="{{ url('/admin/items') }}"><i class="fa fa-arrow-left"></i> {{ __('Items') }}</a> · <strong>{{ $item->item_name }}</strong> · <a href="{{ url('/item/' . $item->item_slug) }}" target="_blank">{{ __('View item page') }} <i class="fa fa-external-link"></i></a></p>
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            @if(!$ready)
            <div class="alert alert-warning">{{ __('Run the database migration (php artisan migrate), then reload this page.') }}</div>
            @else
            <div class="card">
                <div class="card-header"><strong class="card-title">{{ __('Import') }}</strong></div>
                <form action="{{ url('/admin/item-reviews/' . $item->item_token . '/import') }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="card-body">
                        <div class="form-group irv-tabs">
                            <label class="d-block">{{ __('What are you importing?') }}</label>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-primary active"><input type="radio" name="type" value="reviews" checked onchange="irvFormat()"> <i class="fa fa-star"></i> {{ __('Reviews') }}</label>
                                <label class="btn btn-outline-primary"><input type="radio" name="type" value="comments" onchange="irvFormat()"> <i class="fa fa-comments"></i> {{ __('Comments') }}</label>
                            </div>
                        </div>
                        <div class="irv-help alert alert-light border small">
                            <strong>{{ __('One per line.') }}</strong> {{ __('Separate the fields with | (or paste rows from Excel / a CSV file):') }}<br>
                            <span id="irvFmtReviews"><code>{{ __('Name') }} | {{ __('Rating 1-5') }} | {{ __('Review text') }} | {{ __('Date (optional)') }}</code><br>
                            {{ __('Example') }}: <code>Sarah M. | 5 | Works perfectly, installed in 10 minutes. | 2026-10-02</code></span>
                            <span id="irvFmtComments" style="display:none"><code>{{ __('Name') }} | {{ __('Comment text') }} | {{ __('Date (optional)') }}</code><br>
                            {{ __('Example') }}: <code>Karim B. | Does it work on shared hosting? | 2026-10-05</code></span>
                        </div>
                        <div class="form-group">
                            <textarea name="lines" class="form-control" rows="8" placeholder="Sarah M. | 5 | Works perfectly, installed in 10 minutes. | 2026-10-02" style="font-family:monospace">{{ old('lines') }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>{{ __('Or upload a CSV file') }}</label>
                                <input type="file" name="csv" class="form-control" accept=".csv,.txt">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>{{ __('Source (optional)') }}</label>
                                <input type="text" name="source" class="form-control" maxlength="60" value="{{ old('source') }}" placeholder="{{ __('e.g. Etsy, Gumroad, Envato') }}">
                                <small class="form-text text-muted">{{ __('Where these reviews were written. Shown next to the name, e.g. "Sarah M. · via Etsy".') }}</small>
                            </div>
                        </div>
                        <p class="small text-muted mb-0"><i class="fa fa-info-circle"></i> {{ __('Import real reviews of this product (for example from another store where you sell it). Imported reviews count in the star rating on your site, but are not sent to Google as review data, because Google only allows reviews collected on your own site there.') }}</p>
                    </div>
                    <div class="card-footer"><button class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> {{ __('Import') }}</button></div>
                </form>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong class="card-title mb-0">{{ __('Imported reviews') }} ({{ $reviews->count() }})</strong>
                    @if($reviews->count())
                    <form action="{{ url('/admin/item-reviews/' . $item->item_token . '/delete-all') }}" method="post" onsubmit="return confirm('{{ __('Delete all imported reviews of this item?') }}')">{{ csrf_field() }}<input type="hidden" name="type" value="reviews"><button class="btn btn-outline-danger btn-sm">{{ __('Delete all') }}</button></form>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($reviews->isEmpty())<p class="text-muted p-3 mb-0">{{ __('None yet.') }}</p>@else
                    <table class="table table-sm mb-0">
                        <tbody>
                        @foreach($reviews as $r)
                        <tr>
                            <td style="width:170px"><strong>{{ $r->import_name }}</strong>@if($r->import_source)<br><small class="text-muted">{{ __('via') }} {{ $r->import_source }}</small>@endif</td>
                            <td class="irv-stars">{{ str_repeat('★', (int) $r->rating) }}{{ str_repeat('☆', 5 - (int) $r->rating) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($r->rating_comment, 160) }}</td>
                            <td style="white-space:nowrap"><small class="text-muted">{{ $r->rating_date ? date('M d, Y', strtotime($r->rating_date)) : '' }}</small></td>
                            <td class="text-right"><form action="{{ url('/admin/item-reviews/' . $item->item_token . '/delete/review/' . $r->rating_id) }}" method="post">{{ csrf_field() }}<button class="btn btn-link btn-sm text-danger p-0"><i class="fa fa-trash"></i></button></form></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong class="card-title mb-0">{{ __('Imported comments') }} ({{ $comments->count() }})</strong>
                    @if($comments->count())
                    <form action="{{ url('/admin/item-reviews/' . $item->item_token . '/delete-all') }}" method="post" onsubmit="return confirm('{{ __('Delete all imported comments of this item?') }}')">{{ csrf_field() }}<input type="hidden" name="type" value="comments"><button class="btn btn-outline-danger btn-sm">{{ __('Delete all') }}</button></form>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($comments->isEmpty())<p class="text-muted p-3 mb-0">{{ __('None yet.') }}</p>@else
                    <table class="table table-sm mb-0">
                        <tbody>
                        @foreach($comments as $c)
                        <tr>
                            <td style="width:170px"><strong>{{ $c->import_name }}</strong>@if($c->import_source)<br><small class="text-muted">{{ __('via') }} {{ $c->import_source }}</small>@endif</td>
                            <td>{{ \Illuminate\Support\Str::limit($c->comm_text, 200) }}</td>
                            <td style="white-space:nowrap"><small class="text-muted">{{ $c->comm_date ? date('M d, Y', strtotime($c->comm_date)) : '' }}</small></td>
                            <td class="text-right"><form action="{{ url('/admin/item-reviews/' . $item->item_token . '/delete/comment/' . $c->comm_id) }}" method="post">{{ csrf_field() }}<button class="btn btn-link btn-sm text-danger p-0"><i class="fa fa-trash"></i></button></form></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@include('admin.javascript')
<script>
function irvFormat() {
  var c = document.querySelector('input[name="type"][value="comments"]').checked;
  document.getElementById('irvFmtReviews').style.display = c ? 'none' : '';
  document.getElementById('irvFmtComments').style.display = c ? '' : 'none';
}
</script>
</body>
</html>
