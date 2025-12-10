# Landing Page Currency Conversion Feature

## Overview
This feature automatically detects the visitor's country based on their IP address and displays the landing page price in their local currency, while keeping the actual payment processing in USD (as required by DodoPayments).

## How It Works

1. **Country Detection**: When a visitor accesses a landing page, the system detects their country using their IP address via the free ip-api.com service.

2. **Currency Mapping**: The detected country is mapped to its local currency code (e.g., Morocco → MAD, France → EUR).

3. **Price Conversion**: The USD price stored in the database is converted to the visitor's local currency using exchange rates from the `currencies` table.

4. **Display**: The converted price is displayed throughout the landing page, while payment processing still uses the original USD amount.

## Implementation Details

### Files Modified

1. **app/Helpers/Helper.php**
   - Added `getVisitorCountry()` - Detects country from IP address
   - Added `countryToCurrency()` - Maps country codes to currency codes
   - Added `convertPriceToLocalCurrency()` - Converts USD price to local currency
   - Added `formatLocalPrice()` - Formats price with currency symbol

2. **app/Http/Controllers/LandingPagePublicController.php**
   - Updated `show()` method to detect country and convert prices
   - Passes converted price data to the view

3. **resources/views/landing-pages/show.blade.php**
   - Updated all price displays to use converted prices
   - Shows local currency symbol and converted amount

### Supported Countries

The system supports currency conversion for over 100 countries, including:
- **Morocco (MA)** → MAD (Moroccan Dirham)
- **United States (US)** → USD
- **European Union countries** → EUR
- **United Kingdom (GB)** → GBP
- And many more...

### Currency Exchange Rates

Exchange rates are stored in the `currencies` table. Make sure to:
1. Add currencies to the database via the admin panel
2. Keep exchange rates updated regularly
3. Set `currency_status` to 1 (active) for currencies you want to support

### Payment Processing

**Important**: Payment processing still uses USD amounts from the database. This ensures:
- DodoPayments receives the correct USD amount
- PayPal processes in USD
- Order records maintain USD values

Only the **display** is converted to local currency for better user experience.

## Caching

Country detection results are cached in the session for 24 hours to:
- Reduce API calls
- Improve performance
- Provide consistent pricing during a session

## Fallback Behavior

If country detection fails or a currency is not found:
- Defaults to USD
- Shows original USD price
- Payment processing continues normally

## Testing

To test the feature:
1. Use a VPN or proxy to simulate different countries
2. Visit a landing page from different locations
3. Verify prices are displayed in local currency
4. Confirm payment processing still works with USD

## API Usage

The feature uses ip-api.com's free tier:
- 45 requests per minute
- No API key required
- Free for non-commercial use

For higher volume, consider:
- Upgrading to a paid plan
- Using a different geolocation service
- Implementing server-side caching

## Notes

- The system assumes all landing page prices are stored in USD
- Currency conversion is for display purposes only
- Actual payment amounts remain in USD
- Exchange rates should be updated regularly for accuracy

