# Currency Conversion Troubleshooting Guide

## Issue: Still Seeing USD ($) Instead of Local Currency

If you're seeing USD instead of your local currency (e.g., MAD for Morocco), follow these steps:

### Step 1: Test Currency Detection

Visit this URL to test what country is being detected:
```
http://your-domain.com/test-currency
```

Or test with a specific country:
```
http://your-domain.com/test-currency/MA
```

This will show you:
- Your detected IP address
- Detected country code
- Currency code
- Converted price

### Step 2: Force Country Detection (For Testing)

If you want to test with Morocco specifically, add this to your landing page URL:
```
http://your-domain.com/landing/your-slug?force_country=MA
```

This will force the system to detect Morocco and show MAD currency.

### Step 3: Clear Session Cache

The country detection is cached in your session. To clear it:

1. Clear your browser cookies/session for the site
2. Or use an incognito/private window
3. Or add `?clear_currency_cache=1` to the URL (if we add this feature)

### Step 4: Check if MAD Currency Exists in Database

The system now has a default exchange rate for MAD (10 MAD = 1 USD), but for best results, add MAD to your currencies table:

1. Go to Admin Panel → Currency Settings
2. Add a new currency:
   - Currency Name: Moroccan Dirham
   - Currency Code: MAD
   - Currency Symbol: د.م. (or MAD)
   - Currency Rate: 10.0 (or current exchange rate)
   - Status: Active

### Step 5: Verify IP Detection

If you're testing locally (localhost), the system might default to US. To test properly:
- Use your actual public IP (not localhost)
- Or use a VPN to simulate different countries
- Or use the `?force_country=MA` parameter

### Step 6: Check Debug Info

If `APP_DEBUG=true` in your `.env` file, you'll see debug information on the landing page showing:
- Your IP address
- Detected country
- Currency code
- Original USD price
- Converted price

### Common Issues:

1. **Localhost Testing**: If testing on localhost (127.0.0.1), it defaults to US. Use `?force_country=MA` to test.

2. **Session Cache**: Your country is cached for 24 hours. Clear cookies or use incognito mode.

3. **Currency Not in Database**: The system now uses default rates, but adding the currency to the database is recommended for accurate rates.

4. **API Rate Limits**: The free IP geolocation API has rate limits. If you hit the limit, it will default to USD.

### Quick Fix for Morocco:

1. Visit your landing page with: `?force_country=MA`
2. You should now see MAD currency
3. The session will remember this for 24 hours

### For Production:

1. Make sure your server has a public IP (not behind a proxy that hides the real IP)
2. Add MAD currency to your database with current exchange rate
3. Remove debug info from the view (it only shows if APP_DEBUG=true)

## Testing Checklist:

- [ ] Visit `/test-currency` to see detected country
- [ ] Visit landing page with `?force_country=MA` to test Morocco
- [ ] Check if MAD currency exists in database
- [ ] Clear browser session/cookies
- [ ] Verify you're not on localhost (use real domain)
- [ ] Check debug info (if enabled) to see what's detected

