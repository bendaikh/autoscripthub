# Backend Integration Guide for Version Links

## 📌 Overview

This guide shows you exactly how to integrate the version links feature into your backend controllers and database.

## 🗄️ Database Changes

### Option 1: Add Column to Existing Table (Recommended)

Add a new column to your `items` table to store version link data:

```sql
ALTER TABLE `items` 
ADD COLUMN `version_links` TEXT NULL AFTER `item_file_link`;
```

### Option 2: Create Separate Table (Advanced)

For more complex version management:

```sql
CREATE TABLE `item_version_links` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `version_name` varchar(100) DEFAULT NULL,
  `version_url` text NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `fk_item_version_links` 
    FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) 
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 🔧 Controller Changes

### Step 1: Locate Your Item Controllers

Find these files:
- `app/Http/Controllers/ItemController.php` (User-facing)
- `app/Http/Controllers/Admin/ItemController.php` (Admin panel, if exists)

### Step 2: Update the Store/Upload Method

**Find the method that handles item creation** (usually `store()` or similar):

```php
// BEFORE (old code)
public function store(Request $request)
{
    // ... existing validation ...
    
    $item = new Item();
    $item->item_file_link = $request->input('item_file_link1');
    
    // ... rest of the code ...
}
```

**Update to handle version links:**

```php
// AFTER (new code with version links)
public function store(Request $request)
{
    // ... existing validation ...
    
    $item = new Item();
    
    // Handle version links
    if ($request->input('file_type1') === 'link') {
        $versionLinks = $this->processVersionLinks($request);
        
        // Store as JSON in database
        $item->version_links = json_encode($versionLinks);
        
        // Keep backward compatibility - store first link in old field
        $item->item_file_link = !empty($versionLinks) ? $versionLinks[0]['url'] : '';
    } else {
        // For file or serial types, keep existing behavior
        $item->item_file_link = $request->input('item_file_link1', '');
        $item->version_links = null;
    }
    
    // ... rest of the code ...
}
```

### Step 3: Add Helper Method

Add this method to your controller:

```php
/**
 * Process version links from request
 *
 * @param Request $request
 * @return array
 */
private function processVersionLinks(Request $request)
{
    $versionNames = $request->input('version_names', []);
    $versionLinks = $request->input('version_links', []);
    
    $processedLinks = [];
    
    foreach ($versionLinks as $index => $url) {
        // Skip empty URLs
        if (empty($url)) {
            continue;
        }
        
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            continue;
        }
        
        $processedLinks[] = [
            'version' => isset($versionNames[$index]) ? trim($versionNames[$index]) : '',
            'url' => trim($url),
            'created_at' => now()->toDateTimeString()
        ];
    }
    
    return $processedLinks;
}
```

### Step 4: Update the Edit/Update Method

**Find the method that handles item updates** (usually `update()` or similar):

```php
// BEFORE (old code)
public function update(Request $request, $id)
{
    // ... existing validation ...
    
    $item = Item::findOrFail($id);
    $item->item_file_link = $request->input('item_file_link1');
    
    // ... rest of the code ...
}
```

**Update to handle version links:**

```php
// AFTER (new code with version links)
public function update(Request $request, $id)
{
    // ... existing validation ...
    
    $item = Item::findOrFail($id);
    
    // Handle version links
    if ($request->input('file_type1') === 'link') {
        $versionLinks = $this->processVersionLinks($request);
        
        // Store as JSON in database
        $item->version_links = json_encode($versionLinks);
        
        // Keep backward compatibility
        $item->item_file_link = !empty($versionLinks) ? $versionLinks[0]['url'] : '';
    } else {
        // For file or serial types
        $item->item_file_link = $request->input('item_file_link1', '');
        $item->version_links = null;
    }
    
    // ... rest of the code ...
}
```

### Step 5: Update Edit Form Population

**When loading the edit form**, populate version links:

```php
// In your edit method (shows the edit form)
public function edit($id)
{
    $item = Item::findOrFail($id);
    
    // Decode version links for editing
    $versionLinks = [];
    if (!empty($item->version_links)) {
        $versionLinks = json_decode($item->version_links, true);
    }
    
    return view('edit-my-item', [
        'edit' => ['item' => $item],
        'versionLinks' => $versionLinks,
        // ... other data ...
    ]);
}
```

**Update the edit view** to show existing version links:

In `edit-my-item.blade.php`, update the version links container:

```blade
<div id="version-links-container">
    @if(!empty($versionLinks) && count($versionLinks) > 0)
        @foreach($versionLinks as $index => $versionLink)
        <div class="version-link-block" style="margin-bottom: 15px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 4px; background-color: #f9f9f9;">
            <div class="row">
                <div class="col-sm-5">
                    <div class="form-group">
                        <input type="text" name="version_names[]" class="form-control" 
                               placeholder="Version name" 
                               value="{{ $versionLink['version'] ?? '' }}" 
                               style="font-size: 14px;">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" name="version_links[]" class="form-control" 
                               placeholder="File link / URL" 
                               data-bvalidator="required,url" 
                               value="{{ $versionLink['url'] ?? '' }}" 
                               style="font-size: 14px;">
                    </div>
                </div>
                <div class="col-sm-1" style="text-align: center;">
                    <button type="button" class="btn btn-danger btn-sm remove-version-btn" 
                            style="margin-top: 0px; padding: 6px 10px; font-size: 18px;" 
                            title="Remove this version">
                        🗑️
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <!-- Default single block for new items or items without version links -->
        <div class="version-link-block" style="margin-bottom: 15px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 4px; background-color: #f9f9f9;">
            <div class="row">
                <div class="col-sm-5">
                    <div class="form-group">
                        <input type="text" name="version_names[]" class="form-control" 
                               placeholder="Version name" style="font-size: 14px;">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <input type="text" name="version_links[]" class="form-control" 
                               placeholder="File link / URL" 
                               data-bvalidator="required,url" 
                               value="{{ $edit['item']->item_file_link }}" 
                               style="font-size: 14px;">
                    </div>
                </div>
                <div class="col-sm-1" style="text-align: center;">
                    <button type="button" class="btn btn-danger btn-sm remove-version-btn" 
                            style="margin-top: 0px; padding: 6px 10px; font-size: 18px;" 
                            title="Remove this version">
                        🗑️
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
```

## 📦 Model Changes (Optional)

Add accessor and mutator to your `Item` model for easier handling:

```php
// app/Models/Items.php

class Items extends Model
{
    // ... existing code ...
    
    /**
     * Get version links as array
     *
     * @return array
     */
    public function getVersionLinksArrayAttribute()
    {
        if (empty($this->version_links)) {
            return [];
        }
        
        $decoded = json_decode($this->version_links, true);
        return is_array($decoded) ? $decoded : [];
    }
    
    /**
     * Check if item has multiple versions
     *
     * @return bool
     */
    public function hasMultipleVersions()
    {
        return count($this->version_links_array) > 1;
    }
    
    /**
     * Get the latest/first version link
     *
     * @return string|null
     */
    public function getLatestVersionLink()
    {
        $versions = $this->version_links_array;
        return !empty($versions[0]['url']) ? $versions[0]['url'] : $this->item_file_link;
    }
}
```

## 🎨 Display Version Links on Item Detail Page

Update your item detail view to show version selection:

```blade
@if($item->file_type == 'link')
    @php
        $versionLinks = $item->version_links_array;
    @endphp
    
    @if(count($versionLinks) > 1)
        <div class="version-selector">
            <label>{{ __('Select Version:') }}</label>
            <select id="version-selector" class="form-control" onchange="updateDownloadLink(this.value)">
                @foreach($versionLinks as $index => $versionLink)
                    <option value="{{ $versionLink['url'] }}">
                        {{ !empty($versionLink['version']) ? $versionLink['version'] : __('Version') . ' ' . ($index + 1) }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="download-button-container mt-3">
            <a href="{{ $versionLinks[0]['url'] }}" id="download-link" class="btn btn-primary">
                <i class="fa fa-download"></i> {{ __('Download') }}
            </a>
        </div>
        
        <script>
        function updateDownloadLink(url) {
            document.getElementById('download-link').href = url;
        }
        </script>
    @else
        <!-- Single version - direct download -->
        <a href="{{ $item->getLatestVersionLink() }}" class="btn btn-primary">
            <i class="fa fa-download"></i> {{ __('Download') }}
        </a>
    @endif
@endif
```

## ✅ Validation Rules

Add validation rules to your form request or controller:

```php
$rules = [
    'file_type1' => 'required|in:file,link,serial',
    'version_names' => 'nullable|array',
    'version_names.*' => 'nullable|string|max:100',
    'version_links' => 'required_if:file_type1,link|array',
    'version_links.*' => 'required|url|max:500',
];

$messages = [
    'version_links.required_if' => 'At least one version link is required when using Link/URL type.',
    'version_links.*.required' => 'Version URL cannot be empty.',
    'version_links.*.url' => 'Please enter a valid URL.',
];

$validator = Validator::make($request->all(), $rules, $messages);
```

## 🔄 Migration for Existing Data

If you have existing items with single links, create a migration to convert them:

```php
// database/migrations/2025_10_21_convert_single_links_to_version_links.php

public function up()
{
    $items = DB::table('items')
        ->where('file_type', 'link')
        ->whereNotNull('item_file_link')
        ->whereNull('version_links')
        ->get();
    
    foreach ($items as $item) {
        $versionData = [
            [
                'version' => '',
                'url' => $item->item_file_link,
                'created_at' => $item->created_at ?? now()->toDateTimeString()
            ]
        ];
        
        DB::table('items')
            ->where('id', $item->id)
            ->update([
                'version_links' => json_encode($versionData),
                'updated_at' => now()
            ]);
    }
}
```

## 🧪 Testing Your Implementation

```php
// Test cases to verify

// 1. Create new item with version links
$response = $this->post('/upload-item', [
    'file_type1' => 'link',
    'version_names' => ['v1.0', 'v2.0'],
    'version_links' => [
        'https://example.com/v1.zip',
        'https://example.com/v2.zip'
    ],
    // ... other required fields ...
]);

// 2. Update existing item
$response = $this->put('/edit-item/' . $itemId, [
    'file_type1' => 'link',
    'version_names' => ['v3.0'],
    'version_links' => ['https://example.com/v3.zip'],
    // ... other fields ...
]);

// 3. Verify JSON storage
$item = Item::find($itemId);
$this->assertJson($item->version_links);
$versionLinks = json_decode($item->version_links, true);
$this->assertCount(1, $versionLinks);
$this->assertEquals('v3.0', $versionLinks[0]['version']);
```

---

**Need help?** Check the implementation in `public/theme/js/version-links.js` and the view files for reference!

