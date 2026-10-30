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
                        <h1>{{ __('Revenue') }}</h1>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.warning')

        <div class="content mt-3">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header"><strong>{{ __('Add Revenue') }}</strong></div>
                        <div class="card-body">
                            <form action="{{ route('admin.landing-pages.revenue.store') }}" method="POST">
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
                                    <select name="lpr_country_code" class="form-control" required>
                                        <option value="">{{ __('Select country') }}</option>
                                        @foreach($countries as $code => $name)
                                            <option value="{{ $code }}" @selected(old('lpr_country_code') === $code)>{{ $name }} ({{ $code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Date') }} *</label>
                                    <input type="date" name="lpr_date" class="form-control" value="{{ old('lpr_date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Revenue') }} *</label>
                                    <input type="number" name="lpr_amount" class="form-control" step="0.01" min="0" value="{{ old('lpr_amount') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Quantity') }} *</label>
                                    <input type="number" name="lpr_quantity" class="form-control" min="1" value="{{ old('lpr_quantity', 1) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Currency') }}</label>
                                    <select name="lpr_currency" class="form-control">
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                        <option value="GBP">GBP</option>
                                        <option value="THB">THB</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Notes') }}</label>
                                    <textarea name="lpr_notes" class="form-control" rows="2">{{ old('lpr_notes') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">{{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header"><strong>{{ __('Revenue entries') }}</strong></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Product') }}</th>
                                            <th>{{ __('Country') }}</th>
                                            <th>{{ __('Revenue') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($entries as $entry)
                                        <tr>
                                            <td>{{ $entry->lpr_date }}</td>
                                            <td>{{ $entry->lp_title }}</td>
                                            <td>{{ $entry->lpr_country_code }}</td>
                                            <td>{{ $entry->lpr_currency }} {{ number_format((float) $entry->lpr_amount, 2) }}</td>
                                            <td>{{ (int) $entry->lpr_quantity }}</td>
                                            <td class="text-right">
                                                <a href="{{ url('/admin/landing-pages/revenue/delete/' . $entry->lpr_id) }}"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('{{ __('Delete this entry?') }}')">{{ __('Delete') }}</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted p-4">{{ __('No revenue entries yet.') }}</td>
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
