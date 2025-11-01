# Installation Video Feature Implementation

## Overview
This feature allows administrators and vendors to add a YouTube installation video link for each item. Customers can then see a button on the item page that links to the installation/setup tutorial video.

## What Was Implemented

### 1. Database Changes
- **Migration**: Created migration `2025_11_01_222644_add_installation_video_url_to_items_table.php`
- **New Column**: `installation_video_url` (text, nullable) added to the `items` table

### 2. Model Updates
- **Import.php**: Added `installation_video_url` to the fillable array

### 3. Admin Panel Updates

#### Admin Edit Item Page (`resources/views/admin/edit-item.blade.php`)
- Added new form field: "Installation Video YouTube URL (Optional)"
- Located after the video preview fields
- Includes placeholder and helper text

#### Admin Upload Item Page (`resources/views/admin/upload-item.blade.php`)
- Added the same form field for new item creation
- Consistent styling and placement with edit page

#### Admin Item Controller (`app/Http/Controllers/Admin/ItemController.php`)
- Updated `update_items()` method to handle the new field
- Updated `save_items()` method to handle the new field
- Added field to data arrays for both insert and update operations

### 4. Vendor Panel Updates

#### Vendor Item Controller (`app/Http/Controllers/ItemController.php`)
- Updated `update_items()` method to handle the new field
- Updated `save_items()` method to handle the new field
- Added field to data arrays for both insert and update operations

### 5. Customer-Facing Updates

#### Item Detail Page (`resources/views/pages/item.blade.php`)
- Added "Installation Video" button displayed next to "Live Preview" button
- Button only shows when `installation_video_url` is set
- Opens YouTube link in a new tab
- Uses primary button styling with YouTube icon

## How to Use

### For Admins/Vendors (Quick Action):
**NEW! Quick Add via Action Button:**
1. Go to **Admin Panel > Items**
2. Find the item you want to add an installation video to
3. Click the **YouTube icon button** (🎬) in the Actions column
4. A modal will open where you can paste the YouTube URL
5. Click "Save" - Done! No need to open the full edit page

### For Admins/Vendors (Traditional Method):
1. Go to **Admin Panel > Items > Edit Item** (or Upload New Item)
2. Scroll to the "Installation Video YouTube URL (Optional)" field
3. Enter a YouTube URL (e.g., `https://www.youtube.com/watch?v=ABC123`)
4. Save the item

### For Customers:
1. Visit any item page that has an installation video URL set
2. You'll see an "Installation Video" button near the top of the page (next to Live Preview)
3. Click the button to watch the installation tutorial on YouTube in a new tab

## Features
- ✅ **Quick Action Button** - Add installation video without opening full edit page
- ✅ **Modal Interface** - Fast and easy to use popup form
- ✅ **AJAX Saving** - No page reload needed (when using quick action)
- ✅ Optional field - not required for item creation/editing
- ✅ Works for both new items and existing items
- ✅ Button only appears when URL is provided
- ✅ Opens in new tab to keep user on your site
- ✅ Supports all standard YouTube URL formats
- ✅ Available for both admin and vendor item management
- ✅ Clean, professional button styling

## Button Text
The button displays: **"Installation Video"** with a YouTube icon

You can customize the button text by modifying the translation in the item.blade.php file or through your language translation system.

## Database Migration
To apply the database changes, the migration was already run. If you need to run it again on another environment:

```bash
php artisan migrate
```

To rollback this specific change:
```bash
php artisan migrate:rollback --step=1
```

## Files Modified
1. `database/migrations/2025_11_01_222644_add_installation_video_url_to_items_table.php` (new)
2. `app/Models/Import.php`
3. `app/Http/Controllers/Admin/ItemController.php`
   - Added YouTube icon button to Actions column in DataTables
   - Added `saveInstallationVideo()` method for AJAX saving
4. `app/Http/Controllers/ItemController.php`
5. `resources/views/admin/edit-item.blade.php`
6. `resources/views/admin/upload-item.blade.php`
7. `resources/views/admin/items.blade.php`
   - Added modal dialog for quick installation video management
   - Added JavaScript functions for modal interaction and AJAX submission
   - Added YouTube icon button to static table rows
8. `resources/views/pages/item.blade.php`
9. `routes/web.php`
   - Added route for `/admin/save-installation-video`

## Notes
- The field accepts any YouTube URL format
- No validation is enforced on the URL format (you can add validation if needed)
- The button uses the existing icon classes from your theme
- The button styling matches your site's design system

