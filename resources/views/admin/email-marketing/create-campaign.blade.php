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
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Send Email Campaign') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="{{ url('/admin') }}">{{ __('Dashboard') }}</a></li>
                            <li><a href="{{ route('admin.email-marketing.campaigns') }}">{{ __('Campaigns') }}</a></li>
                            <li class="active">{{ __('Send') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.warning')

        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">{{ __('Create & Send Email Campaign') }}</strong>
                                <a href="{{ route('admin.email-marketing.campaigns') }}" class="btn btn-secondary btn-sm float-right">
                                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                                </a>
                            </div>
                            <div class="card-body">
                                @if(count($templates) == 0)
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i> 
                                    {{ __('You need to create an email template first.') }}
                                    <a href="{{ route('admin.email-marketing.create-template') }}" class="alert-link">
                                        {{ __('Create Template') }}
                                    </a>
                                </div>
                                @else

                                <form action="{{ route('admin.email-marketing.store-campaign') }}" method="POST" id="campaignForm">
                                    @csrf

                                    <div class="form-group">
                                        <label for="campaign_name">{{ __('Campaign Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="campaign_name" id="campaign_name" 
                                               class="form-control @error('campaign_name') is-invalid @enderror" 
                                               value="{{ old('campaign_name') }}" required 
                                               placeholder="e.g., October Newsletter, Product Launch">
                                        @error('campaign_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">{{ __('For internal reference only') }}</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="template_id">{{ __('Select Email Template') }} <span class="text-danger">*</span></label>
                                        <select name="template_id" id="template_id" 
                                                class="form-control @error('template_id') is-invalid @enderror" required>
                                            <option value="">{{ __('-- Select Template --') }}</option>
                                            @foreach($templates as $template)
                                            <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                                {{ $template->name }} - {{ $template->subject }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('template_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="recipient_type">{{ __('Send To') }} <span class="text-danger">*</span></label>
                                        <select name="recipient_type" id="recipient_type" 
                                                class="form-control @error('recipient_type') is-invalid @enderror" required>
                                            <option value="all" {{ old('recipient_type') == 'all' ? 'selected' : '' }}>
                                                {{ __('All Customers') }} ({{ $total_customers_count }})
                                            </option>
                                            <option value="regular_customers" {{ old('recipient_type') == 'regular_customers' ? 'selected' : '' }}>
                                                {{ __('Regular Script Customers') }} ({{ $regular_customers_count }})
                                            </option>
                                            <option value="landing_customers" {{ old('recipient_type') == 'landing_customers' ? 'selected' : '' }}>
                                                {{ __('Landing Page Customers') }} ({{ $landing_customers_count }})
                                            </option>
                                            <option value="specific" {{ old('recipient_type') == 'specific' ? 'selected' : '' }}>
                                                {{ __('Specific Emails') }}
                                            </option>
                                        </select>
                                        @error('recipient_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group" id="specificEmailsDiv" style="display: none;">
                                        <label for="recipient_emails">{{ __('Recipient Emails') }}</label>
                                        <textarea name="recipient_emails" id="recipient_emails" 
                                                  class="form-control" rows="4" 
                                                  placeholder="Enter email addresses separated by commas: email1@example.com, email2@example.com">{{ old('recipient_emails') }}</textarea>
                                        <small class="form-text text-muted">{{ __('Separate multiple emails with commas') }}</small>
                                    </div>

                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i> 
                                        <strong>{{ __('Important:') }}</strong> 
                                        {{ __('Once you click "Send Campaign", emails will be sent immediately to all selected recipients.') }}
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success btn-lg" id="sendBtn">
                                            <i class="fa fa-paper-plane"></i> {{ __('Send Campaign Now') }}
                                        </button>
                                        <a href="{{ route('admin.email-marketing.campaigns') }}" class="btn btn-secondary btn-lg">
                                            {{ __('Cancel') }}
                                        </a>
                                    </div>
                                </form>

                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">{{ __('Customer Statistics') }}</strong>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h5 class="text-primary">{{ $total_customers_count }}</h5>
                                    <small class="text-muted">{{ __('Total Customers') }}</small>
                                </div>
                                <div class="mb-3">
                                    <h5 class="text-info">{{ $regular_customers_count }}</h5>
                                    <small class="text-muted">{{ __('Regular Script Customers') }}</small>
                                </div>
                                <div class="mb-3">
                                    <h5 class="text-success">{{ $landing_customers_count }}</h5>
                                    <small class="text-muted">{{ __('Landing Page Customers') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">{{ __('Tips') }}</strong>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fa fa-check text-success"></i> 
                                        {{ __('Test your template before sending') }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fa fa-check text-success"></i> 
                                        {{ __('Check your email configuration') }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fa fa-check text-success"></i> 
                                        {{ __('Personalize with variables') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.javascript')

    <script>
        $(document).ready(function() {
            // Show/hide specific emails field
            $('#recipient_type').on('change', function() {
                if ($(this).val() === 'specific') {
                    $('#specificEmailsDiv').show();
                    $('#recipient_emails').prop('required', true);
                } else {
                    $('#specificEmailsDiv').hide();
                    $('#recipient_emails').prop('required', false);
                }
            });

            // Trigger on page load
            $('#recipient_type').trigger('change');

            // Confirm before sending
            $('#campaignForm').on('submit', function(e) {
                var recipientType = $('#recipient_type').val();
                var recipientCount = 0;

                if (recipientType === 'all') {
                    recipientCount = {{ $total_customers_count }};
                } else if (recipientType === 'regular_customers') {
                    recipientCount = {{ $regular_customers_count }};
                } else if (recipientType === 'landing_customers') {
                    recipientCount = {{ $landing_customers_count }};
                } else if (recipientType === 'specific') {
                    var emails = $('#recipient_emails').val().split(',');
                    recipientCount = emails.length;
                }

                var confirmed = confirm('Are you sure you want to send this email to ' + recipientCount + ' recipient(s)?');
                
                if (confirmed) {
                    $('#sendBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
                }
                
                return confirmed;
            });
        });
    </script>
</body>
</html>

