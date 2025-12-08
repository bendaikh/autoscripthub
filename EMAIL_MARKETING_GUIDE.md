# Email Marketing System - User Guide

## Overview

The Email Marketing system allows you to create email templates and send marketing campaigns to your customers from both:
- **Regular Script Customers** (from `/admin/customer`)
- **Landing Page Customers** (from `/admin/landing-customers`)

## Features

✅ Create and manage email templates with rich HTML content
✅ Send emails to specific customer groups or all customers
✅ Track campaign statistics (sent, failed, success rate)
✅ Use dynamic variables in templates
✅ Beautiful admin interface with Summernote editor

---

## Getting Started

### 1. Access Email Marketing

Navigate to the admin panel and find the new **Email Marketing** menu in the sidebar:
- **Email Marketing** → **Templates** - Manage your email templates
- **Email Marketing** → **Campaigns** - View and send campaigns

---

## Creating Email Templates

### Step 1: Create a New Template

1. Go to **Email Marketing** → **Templates**
2. Click **"Create New Template"**
3. Fill in the template details:
   - **Template Name**: Internal name (e.g., "Welcome Email", "Newsletter")
   - **Email Subject**: The subject line for the email
   - **Email Content**: Rich HTML content using the editor
   - **Status**: Active or Inactive
   - **Notes**: Optional internal notes

### Available Variables

You can use these dynamic variables in your templates:

- `{{name}}` - Customer's name
- `{{email}}` - Customer's email address
- `{{site_name}}` - Your site name

**Example Template:**

```html
<h1>Hello {{name}}!</h1>

<p>Thank you for being a valued customer at {{site_name}}.</p>

<p>We're excited to announce our latest updates...</p>

<p>Best regards,<br>
The {{site_name}} Team</p>
```

### Step 2: Edit or Delete Templates

- Click the **Edit** button to modify a template
- Click the **Delete** button to remove a template (only if not used in campaigns)

---

## Sending Email Campaigns

### Step 1: Create a Campaign

1. Go to **Email Marketing** → **Campaigns**
2. Click **"Send New Campaign"**
3. Fill in the campaign details:
   - **Campaign Name**: Internal name for tracking
   - **Select Email Template**: Choose from your active templates
   - **Send To**: Select recipient group:
     - **All Customers** - Both regular and landing page customers
     - **Regular Script Customers** - Only customers who bought scripts
     - **Landing Page Customers** - Only customers from landing pages
     - **Specific Emails** - Enter comma-separated email addresses

### Step 2: Send Campaign

1. Review the campaign details and customer count
2. Click **"Send Campaign Now"**
3. Confirm the sending action
4. Wait for the emails to be sent (processed immediately)

### Step 3: View Campaign Results

1. Go to **Email Marketing** → **Campaigns**
2. Click the **View** button on any campaign to see:
   - Total recipients
   - Emails sent successfully
   - Failed emails
   - Success rate
   - Email preview

---

## Database Structure

### Tables Created

1. **email_marketing_templates**
   - Stores email templates
   - Fields: id, name, subject, content, status, notes, timestamps

2. **email_marketing_campaigns**
   - Stores campaign records
   - Fields: id, campaign_name, template_id, recipient_type, recipient_emails, total_recipients, emails_sent, emails_failed, status, scheduled_at, sent_at, timestamps

---

## Files Created

### Migrations
- `database/migrations/2025_11_06_000001_create_email_marketing_templates_table.php`
- `database/migrations/2025_11_06_000002_create_email_marketing_campaigns_table.php`

### Models
- `app/Models/EmailMarketingTemplate.php`
- `app/Models/EmailMarketingCampaign.php`

### Controller
- `app/Http/Controllers/Admin/EmailMarketingController.php`

### Views
- `resources/views/admin/email-marketing/templates.blade.php`
- `resources/views/admin/email-marketing/create-template.blade.php`
- `resources/views/admin/email-marketing/edit-template.blade.php`
- `resources/views/admin/email-marketing/campaigns.blade.php`
- `resources/views/admin/email-marketing/create-campaign.blade.php`
- `resources/views/admin/email-marketing/view-campaign.blade.php`

### Routes Added to `routes/web.php`

```php
// Email Marketing Templates
Route::get('/admin/email-marketing/templates', 'Admin\EmailMarketingController@templates');
Route::get('/admin/email-marketing/templates/create', 'Admin\EmailMarketingController@createTemplate');
Route::post('/admin/email-marketing/templates/store', 'Admin\EmailMarketingController@storeTemplate');
Route::get('/admin/email-marketing/templates/edit/{id}', 'Admin\EmailMarketingController@editTemplate');
Route::post('/admin/email-marketing/templates/update/{id}', 'Admin\EmailMarketingController@updateTemplate');
Route::get('/admin/email-marketing/templates/delete/{id}', 'Admin\EmailMarketingController@deleteTemplate');

// Email Marketing Campaigns
Route::get('/admin/email-marketing/campaigns', 'Admin\EmailMarketingController@campaigns');
Route::get('/admin/email-marketing/campaigns/create', 'Admin\EmailMarketingController@createCampaign');
Route::post('/admin/email-marketing/campaigns/store', 'Admin\EmailMarketingController@storeCampaign');
Route::get('/admin/email-marketing/campaigns/view/{id}', 'Admin\EmailMarketingController@viewCampaign');
Route::get('/admin/email-marketing/campaigns/delete/{id}', 'Admin\EmailMarketingController@deleteCampaign');
```

---

## Important Notes

### Email Configuration

Make sure your Laravel email configuration is properly set up in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Customer Data Sources

The system automatically fetches customers from:

1. **users table** - Regular customers (`user_type = 'customer'`)
2. **landing_customers table** - Landing page customers

### Best Practices

✅ Test your email templates before sending to all customers
✅ Keep your email content concise and engaging
✅ Use variables to personalize emails
✅ Monitor campaign statistics to improve future campaigns
✅ Ensure your SMTP settings are configured correctly

---

## Troubleshooting

### Emails Not Sending

1. Check your `.env` email configuration
2. Verify SMTP credentials are correct
3. Check Laravel logs in `storage/logs/laravel.log`
4. Test email sending with `php artisan tinker`:

```php
Mail::raw('Test email', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});
```

### Template Not Showing

- Ensure template status is set to "Active"
- Clear Laravel cache: `php artisan cache:clear`

### Campaign Shows Failed Status

- Check the campaign details for error information
- Review the failed email count
- Check logs for specific error messages

---

## Future Enhancements

Potential features to add:
- Email scheduling for future dates
- Email open tracking
- Click tracking
- Unsubscribe functionality
- Email list segmentation
- A/B testing
- Automated drip campaigns

---

## Support

For issues or questions:
1. Check Laravel logs
2. Review email configuration
3. Test with a small recipient group first
4. Monitor campaign statistics

---

**Created:** November 6, 2025
**Version:** 1.0

Happy Email Marketing! 📧

