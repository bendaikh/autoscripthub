<!doctype html>
<html class="no-js" lang="en">
<head>
    @include('admin.stylesheet')
</head>
<body>
    @include('admin.navigation')

    <div id="right-panel" class="right-panel">
        @include('admin.header')

        <div class="breadcrumbs">
            <div class="col-sm-8">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Ads Costs') }}</h1>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.warning')

        <div class="content mt-3">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header"><strong>{{ __('Add Ad Cost') }}</strong></div>
                        <div class="card-body">
                            <form action="{{ route('admin.landing-pages.ad-costs.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>{{ __('Product') }} *</label>
                                    <select name="lp_id" class="form-control" required>
                                        <option value="">{{ __('Select product') }}</option>
                                        @foreach($landing_pages as $page)
                                            <option value="{{ $page->lp_id }}" @selected(old('lp_id') == $page->lp_id)>{{ $page->lp_title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Country') }} *</label>
                                    <select name="lpac_country_code" class="form-control" required>
                                        <option value="">{{ __('Select country') }}</option>
                                        @foreach($countries as $code => $name)
                                            <option value="{{ $code }}" @selected(old('lpac_country_code') === $code)>{{ $name }} ({{ $code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Date') }} *</label>
                                    <input type="date" name="lpac_date" class="form-control" value="{{ old('lpac_date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Ads cost') }} *</label>
                                    <input type="number" name="lpac_amount" class="form-control" step="0.01" min="0" value="{{ old('lpac_amount') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Currency') }}</label>
                                    <select name="lpac_currency" class="form-control">
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                        <option value="GBP">GBP</option>
                                        <option value="THB">THB</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Notes') }}</label>
                                    <textarea name="lpac_notes" class="form-control" rows="2">{{ old('lpac_notes') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><strong>{{ __('Ad cost entries') }}</strong></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Product') }}</th>
                                            <th>{{ __('Country') }}</th>
                                            <th>{{ __('Ads cost') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($entries as $entry)
                                        <tr>
                                            <td>{{ $entry->lpac_date }}</td>
                                            <td>{{ $entry->lp_title }}</td>
                                            <td>{{ $entry->lpac_country_code }}</td>
                                            <td>{{ $entry->lpac_currency }} {{ number_format((float) $entry->lpac_amount, 2) }}</td>
                                            <td class="text-right">
                                                <a href="{{ url('/admin/landing-pages/ad-costs/delete/' . $entry->lpac_id) }}"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('{{ __('Delete this entry?') }}')">{{ __('Delete') }}</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted p-4">{{ __('No ad cost entries yet.') }}</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.javascript')
</body>
</html>
