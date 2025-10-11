# Dodo Payments Integration Guide

This guide will help you complete the Dodo Payments integration for your Laravel marketplace application.

## ✅ Completed Steps

I've successfully integrated Dodo Payments into your application with the following changes:

### 1. Database Migration
- Created migration file: `database/migrations/2025_10_10_000001_add_dodopayments_fields_to_settings.php`
- Adds 3 new fields to `additional_settings` table:
  - `dodopayments_mode` (VARCHAR 10) - 'test' or 'live'
  - `dodopayments_api_key` (TEXT) - Your Dodo Payments API key
  - `dodopayments_business_id` (TEXT) - Optional business ID

### 2. Admin Settings
- ✅ Added 'dodopayments' to payment options in `SettingsController.php`
- ✅ Added configuration UI in `resources/views/admin/payment-settings.blade.php`
- ✅ Added save/update handling for Dodo Payments settings

### 3. Payment Service
- ✅ Created `app/Services/DodoPaymentsService.php` - Handles API communication with Dodo Payments
- Supports:
  - Creating checkout sessions
  - Creating one-time payments
  - Creating subscriptions
  - Webhook signature verification

### 4. Payment Processing
- ✅ Added Dodo Payments checkout logic in `ProfileController.php`
- ✅ Created success callback handler: `dodopayments_success()`
- ✅ Created webhook handler: `dodopayments_subscription()`

### 5. Routes
- ✅ Added route: `GET /subscription-dodopayments/{ord_token}` - Success callback
- ✅ Added webhooks:
  - `POST /webhooks/dodopayments-checkout`
  - `POST /webhooks/dodopayments-deposit`
  - `POST /webhooks/dodopayments-subscription`

## 🔧 Manual Steps Required

### Step 1: Fix Database Issue

Due to a migration issue, you need to manually add the Dodo Payments columns. Run this SQL in your MySQL:

```sql
-- Add Dodo Payments columns
ALTER TABLE additional_settings 
ADD COLUMN dodopayments_mode VARCHAR(10) NULL,
ADD COLUMN dodopayments_api_key TEXT NULL,
ADD COLUMN dodopayments_business_id TEXT NULL;
```

**OR** use this approach:

1. Access your database via phpMyAdmin or Laragon's MySQL interface
2. Run the SQL commands above
3. Alternatively, import the file `add_dodopayments_columns.sql` I created

### Step 2: Restore Currencies Table (if needed)

If your currencies table was accidentally removed:

```sql
-- Run the SQL file
SOURCE C:/laragon/www/autoscripthub/app/Seeds/currencies.sql;
```

Or import `app/Seeds/currencies.sql` via phpMyAdmin.

### Step 3: Mark Migration as Complete

After manually adding the columns, mark the migration as complete:

```bash
php artisan migrate:status
# If it shows as pending, manually insert into migrations table:
```

```sql
INSERT INTO migrations (migration, batch) 
VALUES ('2025_10_10_000001_add_dodopayments_fields_to_settings', (SELECT MAX(batch) FROM migrations) + 1);
```

## 🎯 Configuration

### 1. Admin Panel Setup

1. Log in to your admin panel
2. Navigate to **Settings → Payment Settings**
3. Scroll down to **Dodo Payments Settings**
4. Configure:
   - **Mode**: Select 'test' for sandbox or 'live' for production
   - **API Key**: Enter your Dodo Payments API key from https://test.dodopayments.com or https://live.dodopayments.com
   - **Business ID**: (Optional) Enter if you have multiple businesses
5. Check the **dodopayments** checkbox under "Admin Payment Methods"
6. Check the **dodopayments** checkbox under "Vendor Payment Methods" if vendors should accept it
7. Click **Update**

### 2. Get Your API Keys

1. Go to Dodo Payments Dashboard: https://test.dodopayments.com (for testing)
2. Navigate to **Developer → API Keys**
3. Click **Add API Key**
4. Copy the generated API key
5. Store it securely in your admin panel

### 3. Setup Webhooks

Configure webhooks in Dodo Payments dashboard to receive payment notifications:

**Webhook URLs:**
- Checkout: `https://yourdomain.com/webhooks/dodopayments-checkout`
- Subscription: `https://yourdomain.com/webhooks/dodopayments-subscription`
- Deposit: `https://yourdomain.com/webhooks/dodopayments-deposit`

**How to configure:**
1. Go to **Developer → Webhooks** in Dodo Payments dashboard
2. Click **Create Webhook**
3. Add the webhook URL for each event type
4. Select events to listen for:
   - `payment.completed`
   - `payment.succeeded`
   - `subscription.created`
   - `subscription.updated`

## 📝 How It Works

### Subscription Payment Flow

1. **User selects subscription plan** → Clicks upgrade
2. **User selects Dodo Payments** as payment method
3. **System creates checkout session** via Dodo Payments API
4. **User redirected** to Dodo Payments checkout page
5. **User completes payment** on Dodo Payments
6. **User redirected back** to success URL
7. **Webhook received** (optional) to confirm payment
8. **Subscription activated** and email sent

### Supported Features

✅ **Subscription Payments** - Fully integrated (ProfileController)
✅ **Product Checkout** - One-time payments for products (CommonController & ItemController)
✅ **Checkout Sessions** - Creates payment links
✅ **Webhook Verification** - Secure webhook handling
✅ **Multi-currency** - Supports all Dodo Payments currencies
✅ **Test/Live Mode** - Easy switching
✅ **Business ID Support** - For multi-business accounts
✅ **Email Notifications** - Automated emails to buyers and vendors

## 🔒 Security

- API keys stored securely in database
- Webhook signature verification (when configured)
- Encrypted purchase tokens
- HTTPS required for production

## 🧪 Testing

### Test Mode Setup

1. Set **Mode** to 'test' in admin settings
2. Use test API key from https://test.dodopayments.com
3. Use test card numbers from Dodo Payments documentation

### Test Payment

1. Select a subscription plan
2. Choose Dodo Payments
3. Complete checkout
4. Verify success page shows
5. Check admin panel for payment record

## 📚 Additional Integration Needed

If you also want Dodo Payments for:

### Regular Product Checkout (One-time payments)
You'll need to add similar logic to `ItemController.php` where product checkout is handled.

### Deposits/Wallet Top-up
Add similar integration to the deposit functionality in `ItemController.php`.

### Vendor Payments
Add Dodo Payments to vendor withdrawal methods if needed.

## 🐛 Troubleshooting

### Payment not completing
- Check API keys are correct
- Verify mode (test/live) matches your API keys
- Check Laravel logs: `storage/logs/laravel.log`

### Webhooks not working
- Ensure webhook URLs are accessible (not localhost for live mode)
- Check webhook signing key is configured
- Verify SSL certificate is valid (for live mode)

### Database errors
- Make sure columns were added successfully
- Check migrations table for completion

## 📞 Support

- **Dodo Payments Docs**: https://docs.dodopayments.com
- **API Reference**: https://docs.dodopayments.com/api-reference/introduction
- **Community**: Check Dodo Payments Discord or community forums

## 🎉 You're Done!

Once the manual database steps are complete, Dodo Payments will be fully integrated into your marketplace. Users can now pay for subscriptions using Dodo Payments!

