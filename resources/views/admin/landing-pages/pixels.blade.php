<!doctype html>
<html class="no-js" lang="en">
<head>
    @include('admin.stylesheet')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/landing-admin.css') }}?v=20260824-pixel">
</head>
<body>
    @include('admin.navigation')

    <div id="right-panel" class="right-panel">
        @include('admin.header')
        @include('admin.warning')

        <div class="content mt-3">
            <div class="landing-admin">
                <div class="landing-hero">
                    <div>
                        <div class="landing-eyebrow">{{ __('Landing Pages') }}</div>
                        <h1>{{ __('Pixel Connect') }}</h1>
                        <p>{{ __('Landing pages send Meta standard events from code: PageView, InitiateCheckout, Contact, Lead, AddPaymentInfo, and Purchase. Automatic Meta button tracking is disabled so Test Events shows your configured events—not defaults like subscribedButton. In Meta Events Manager → Settings, also turn off “Track events automatically without code”.') }}</p>
                    </div>
                </div>

                <div class="landing-stats">
                    <div class="landing-stat">
                        <span class="landing-stat-label">{{ __('Facebook Pixels') }}</span>
                        <span class="landing-stat-value">{{ $facebook_count }}</span>
                    </div>
                    <div class="landing-stat">
                        <span class="landing-stat-label">{{ __('TikTok Pixels') }}</span>
                        <span class="landing-stat-value">{{ $tiktok_count }}</span>
                    </div>
                    <div class="landing-stat">
                        <span class="landing-stat-label">{{ __('Active') }}</span>
                        <span class="landing-stat-value">{{ $pixels->where('is_active', 1)->count() }}</span>
                    </div>
                    <div class="landing-stat">
                        <span class="landing-stat-label">{{ __('Paused') }}</span>
                        <span class="landing-stat-value">{{ $pixels->where('is_active', 0)->count() }}</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="landing-panel" style="margin-bottom: 20px;">
                            <div class="landing-toolbar">
                                <h2>{{ __('Connect a Pixel') }}</h2>
                            </div>
                            <form action="{{ route('admin.landing-pages.pixels.store') }}" method="post" style="padding: 20px;">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label>{{ __('Platform') }}</label>
                                    <select name="platform" class="form-control" required>
                                        <option value="facebook" {{ old('platform') === 'tiktok' ? '' : 'selected' }}>{{ __('Facebook / Meta') }}</option>
                                        <option value="tiktok" {{ old('platform') === 'tiktok' ? 'selected' : '' }}>{{ __('TikTok') }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Pixel ID') }}</label>
                                    <input type="text" name="pixel_id" class="form-control" value="{{ old('pixel_id') }}" required placeholder="{{ __('e.g. 1234567890 or CXXXX...') }}">
                                </div>
                                <div class="form-group">
                                    <label>{{ __('Label') }} <span class="text-muted">({{ __('optional') }})</span></label>
                                    <input type="text" name="label" class="form-control" value="{{ old('label') }}" placeholder="{{ __('Ads account, campaign, etc.') }}">
                                </div>
                                <button type="submit" class="landing-button landing-button-connect">
                                    <i class="fa fa-plus"></i> {{ __('Connect Pixel') }}
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="landing-panel">
                            <div class="landing-toolbar">
                                <h2>{{ __('Connected Pixels') }}</h2>
                            </div>
                            <div class="table-responsive">
                                <table class="table landing-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Platform') }}</th>
                                            <th>{{ __('Pixel ID') }}</th>
                                            <th>{{ __('Label') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pixels as $pixel)
                                            <tr>
                                                <td>
                                                    @if($pixel->platform === 'facebook')
                                                        <span class="badge badge-primary">{{ __('Facebook') }}</span>
                                                    @else
                                                        <span class="badge badge-dark">{{ __('TikTok') }}</span>
                                                    @endif
                                                </td>
                                                <td><code>{{ $pixel->pixel_id }}</code></td>
                                                <td>{{ $pixel->label ?: '—' }}</td>
                                                <td>
                                                    @if($pixel->is_active)
                                                        <span class="badge badge-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ __('Paused') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.landing-pages.pixels.toggle', $pixel->id) }}" class="btn btn-sm btn-outline-secondary">
                                                        {{ $pixel->is_active ? __('Pause') : __('Activate') }}
                                                    </a>
                                                    <a href="{{ route('admin.landing-pages.pixels.delete', $pixel->id) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm(@json(__('Disconnect this pixel?')));">
                                                        {{ __('Disconnect') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">{{ __('No pixels connected yet. Add a Facebook or TikTok pixel to start tracking landing pages.') }}</td>
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
