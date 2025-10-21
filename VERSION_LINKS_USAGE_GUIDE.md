# Version Links - User Guide

## 🎯 How to Use the Version Links Feature

### Step 1: Select "Link / URL" Type
When uploading or editing an item, select **"Link / URL"** from the "Upload Main File Type" dropdown.

```
Upload Main File Type: [Link / URL ▼]
```

### Step 2: The Version Links Section Appears
You'll see a new section with one version block by default:

```
┌─────────────────────────────────────────────────────────────┐
│ Main File Link/URL *  [➕ Add Version]                      │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  [Version name...     ]  [File link / URL...    ] 🗑️ │   │
│  └──────────────────────────────────────────────────────┘   │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### Step 3: Enter Version Information
Fill in the version details:

**Example:**
```
┌──────────────────────────────────────────────────────────┐
│  [v1.0               ]  [https://cdn.site.com/app-v1.zip] 🗑️ │
└──────────────────────────────────────────────────────────┘
```

### Step 4: Add More Versions
Click the **➕** button to add additional versions:

```
┌─────────────────────────────────────────────────────────────┐
│ Main File Link/URL *  [➕ Add Version]                      │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  [v1.0               ]  [https://cdn.site.com/app-v1.zip] 🗑️ │
│  └──────────────────────────────────────────────────────┘   │
│                                                               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  [v2.0               ]  [https://cdn.site.com/app-v2.zip] 🗑️ │
│  └──────────────────────────────────────────────────────┘   │
│                                                               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  [v2.1 (Beta)        ]  [https://cdn.site.com/app-v2.1.zip] 🗑️ │
│  └──────────────────────────────────────────────────────┘   │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### Step 5: Remove Unwanted Versions
Click the **🗑️** (trash) icon to remove any version block you don't need.

> **Note:** You must keep at least one version block. If you try to delete the last one, it will just clear the fields instead of removing the block.

## 📋 Real-World Examples

### Example 1: Software with Multiple Versions
```
Version: v3.0 (Latest)
URL: https://downloads.myapp.com/myapp-v3.0.zip

Version: v2.8 (Stable)
URL: https://downloads.myapp.com/myapp-v2.8.zip

Version: v2.5 (Legacy Support)
URL: https://downloads.myapp.com/myapp-v2.5.zip
```

### Example 2: Theme with Different Frameworks
```
Version: WordPress 6.x
URL: https://cdn.mytheme.com/mytheme-wp6.zip

Version: WordPress 5.x
URL: https://cdn.mytheme.com/mytheme-wp5.zip

Version: Classic PHP
URL: https://cdn.mytheme.com/mytheme-classic.zip
```

### Example 3: Plugin with Platform Variants
```
Version: Chrome Extension
URL: https://store.myplugin.com/chrome-latest.crx

Version: Firefox Add-on
URL: https://store.myplugin.com/firefox-latest.xpi

Version: Edge Extension
URL: https://store.myplugin.com/edge-latest.zip
```

## 🔄 Important Notes

### File Upload Section Compatibility
✅ **Works perfectly with file uploads!**

When you upload files in the "Files" section at the top of the form:
- Your version link data **will NOT be lost**
- All version names and URLs are **automatically preserved**
- You can continue adding/editing versions after uploading files

### Switching File Types
If you switch between different "Upload Main File Type" options:
- **File** → Shows file selector dropdown
- **Link / URL** → Shows version links section
- **License Keys / Serial Numbers** → Shows serial number input

The version link section only appears when "Link / URL" is selected.

### Form Validation
- ✅ Version name field is **optional** (can be left empty)
- ✅ File URL field is **required** and must be a valid URL
- ✅ At least one version link is required when using "Link / URL" type

## 💡 Tips & Best Practices

### Version Naming Suggestions:
- ✅ **Good**: `v1.0`, `v2.1.3`, `2023 Edition`, `Pro Version`
- ✅ **Good**: `Latest`, `Stable`, `Beta`, `LTS`
- ✅ **Good**: `WordPress 6.x`, `Laravel 10`, `PHP 8.2`
- ❌ **Avoid**: Too long names, special characters

### URL Best Practices:
- ✅ Use direct download links (not landing pages)
- ✅ Use HTTPS for security
- ✅ Test links before submitting
- ✅ Use CDN or reliable hosting for files

### Organization Tips:
1. **List newest version first** for better user experience
2. **Keep version names consistent** (e.g., all use "v" prefix)
3. **Add descriptive notes in parentheses** if needed (e.g., "v2.0 (LTS)")
4. **Remove old/unsupported versions** to avoid confusion

## 🛠️ Troubleshooting

### Version links don't appear
→ Make sure "Link / URL" is selected in "Upload Main File Type"

### Data disappears after uploading files
→ This shouldn't happen! The feature is designed to preserve data. If it does, please report it as a bug.

### Can't delete the last version block
→ This is by design. At least one version must remain when using "Link / URL" type.

### Form won't submit
→ Check that all URL fields have valid URLs (must start with http:// or https://)

## 📱 Where This Feature Works

✅ User Upload Page (`/upload-my-item`)  
✅ User Edit Page (`/edit-my-item`)  
✅ Admin Upload Page (`/admin/upload-item`)  
✅ Admin Edit Page (`/admin/edit-item`)  

---

**Happy uploading!** 🚀

