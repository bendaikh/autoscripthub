# DodoPayments Config-Based Solution

## Problem
The production database's `additional_settings` table has reached MySQL's maximum row size limit (65,535 bytes), making it impossible to add new columns.

## Solution
Store DodoPayments settings in `.env` file and `config/services.php` instead of the database. This is actually a **better practice** for API credentials anyway!

## Benefits
✅ **No database changes required** - Avoids row size limit completely  
✅ **Better security** - API keys in .env (not tracked by Git)  
✅ **Faster performance** - No database queries needed  
✅ **Industry standard** - This is how Laravel handles most API credentials  
✅ **Admin panel still works** - Settings form updates .env automatically  

## Changes Made

### 1. Config File (`config/services.php`)
Added DodoPayments configuration:
```php
'dodopayments' => [
    'mode' => env('DODOPAYMENTS_MODE', 'test'),
    'api_key' => env('DODOPAYMENTS_API_KEY'),
    'product_id' => env('DODO_DEFAULT_PRODUCT_ID'),
],
```

### 2. Migration (`database/migrations/2025_10_10_000001_add_dodopayments_fields_to_settings.php`)
- Changed to a "dummy" migration that doesn't modify the database
- Kept for tracking purposes only

### 3. Controllers Updated

**SettingsController:**
- Added `updateEnvFile()` method to write settings to .env
- Removed dodopayments from database save operations
- Settings form now updates `.env` file directly

**CommonController & ProfileController:**
- Changed to read from `config('services.dodopayments.mode')` 
- Changed to read from `config('services.dodopayments.api_key')`
- Added validation to check if API key is configured

### 4. Admin View (`resources/views/admin/payment-settings.blade.php`)
- Changed form fields to read from config instead of database
- Added helpful text indicating settings are stored in .env

## Production Deployment Instructions

### Step 1: Upload Updated Files

Upload these files to production:
- `config/services.php`
- `database/migrations/2025_10_10_000001_add_dodopayments_fields_to_settings.php`
- `app/Http/Controllers/Admin/SettingsController.php`
- `app/Http/Controllers/CommonController.php`
- `app/Http/Controllers/ProfileController.php`
- `resources/views/admin/payment-settings.blade.php`
- `use_config_instead_of_db.php` (temporary fix script)

### Step 2: Run the Fix Script

```bash
php use_config_instead_of_db.php
```

This will:
- Remove any partial columns that were added
- Mark the migration as complete
- Show you what to add to .env

### Step 3: Add to .env File

Add these lines to your production `.env` file:

```bash
DODOPAYMENTS_MODE=test
DODOPAYMENTS_API_KEY=your_api_key_here
DODO_DEFAULT_PRODUCT_ID=your_product_id_here
```

### Step 4: Run Migration

```bash
php artisan migrate
```

This should now complete successfully (the migration does nothing, just marks itself as run).

### Step 5: Clear Config Cache

```bash
php artisan config:clear
php artisan config:cache
```

### Step 6: Test & Clean Up

1. Go to Admin Panel → Payment Settings
2. Verify DodoPayments settings appear correctly
3. Test saving settings (they'll update .env)
4. Remove the fix script:
   ```bash
   rm use_config_instead_of_db.php
   rm final_dodo_migration_fix.php
   ```

## How It Works Now

### Admin Updates Settings:
1. Admin fills in DodoPayments Mode and API Key in admin panel
2. Form submits to `SettingsController@payment_settings()`
3. Controller calls `updateEnvFile()` to write values to `.env`
4. Settings are immediately available via `config('services.dodopayments')`

### Checkout Process:
1. User clicks "Pay with Card"
2. `CommonController` reads `config('services.dodopayments.api_key')`
3. Creates DodoPayments checkout session
4. Redirects to DodoPayments hosted checkout page

## Alternative: Manual .env Update

If the admin panel doesn't work initially, you can manually edit `.env` on the production server:

```bash
nano .env
```

Add the three lines, then:
```bash
php artisan config:clear
php artisan config:cache
```

## Why This Is Better

1. **Security**: API keys in `.env` are never committed to Git
2. **Performance**: No database query needed to get settings
3. **Standard**: This is how Stripe, PayPal, and other payment gateways work in Laravel
4. **Scalable**: No database row size concerns
5. **Portable**: Easy to copy settings between environments

## Date
October 11, 2025

