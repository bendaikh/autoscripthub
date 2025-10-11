# Dodo Payments - Quick Start Checklist ✅

## 🚀 Get Started in 5 Minutes

Follow this checklist to activate Dodo Payments on your site:

---

## Step 1: Database Setup ⚠️ (REQUIRED)

Run this SQL in your database (via phpMyAdmin, MySQL Workbench, or terminal):

```sql
ALTER TABLE additional_settings 
ADD COLUMN dodopayments_mode VARCHAR(10) NULL,
ADD COLUMN dodopayments_api_key TEXT NULL,
ADD COLUMN dodopayments_business_id TEXT NULL;

-- Mark migration as complete
INSERT INTO migrations (migration, batch) 
VALUES ('2025_10_10_000001_add_dodopayments_fields_to_settings', 
        (SELECT MAX(batch) + 1 FROM migrations m));
```

✅ **Done? Move to Step 2**

---

## Step 2: Get Dodo Payments API Key

### For Testing:
1. Go to: https://test.dodopayments.com
2. Create account / Login
3. Navigate to: **Developer → API Keys**
4. Click: **Add API Key**
5. Copy your **Test API Key**

### For Production:
1. Go to: https://live.dodopayments.com
2. Create account / Login
3. Navigate to: **Developer → API Keys**
4. Click: **Add API Key**
5. Copy your **Live API Key**

✅ **Got your API key? Move to Step 3**

---

## Step 3: Configure in Admin Panel

1. Login to your **Admin Panel**
2. Navigate to: **Settings → Payment Settings**
3. Scroll down to: **Dodo Payments Settings**
4. Fill in:
   ```
   ┌─────────────────────────────────┐
   │ Dodo Payments Mode:             │
   │ ○ Test  ○ Live                  │
   │                                 │
   │ Dodo Payments API Key:          │
   │ [paste your API key here]       │
   │                                 │
   │ Dodo Business ID (Optional):    │
   │ [leave empty unless multi-biz]  │
   └─────────────────────────────────┘
   ```
5. Check these boxes:
   - ☑ **dodopayments** (under Admin Payment Methods)
   - ☑ **dodopayments** (under Vendor Payment Methods)
6. Click: **Update** button

✅ **Settings saved? Move to Step 4**

---

## Step 4: Test Payment

1. **Frontend**: Go to your website
2. **Select** a product or subscription
3. **Add to cart** / Choose plan
4. **Go to checkout**
5. **Select**: Dodo Payments
6. **Complete** payment (use test card if in test mode)
7. **Verify**: Success page loads
8. **Check**: Email received

✅ **Test successful? Move to Step 5**

---

## Step 5: Setup Webhooks (Optional - Recommended)

In Dodo Payments Dashboard:

1. Go to: **Developer → Webhooks**
2. Click: **Create Webhook**
3. Add these URLs one by one:

```
Subscription: https://yourdomain.com/webhooks/dodopayments-subscription
Checkout:     https://yourdomain.com/webhooks/dodopayments-checkout
Deposit:      https://yourdomain.com/webhooks/dodopayments-deposit
```

4. For each webhook, select events:
   - ☑ payment.completed
   - ☑ payment.succeeded
   - ☑ subscription.created
   - ☑ subscription.updated

✅ **Webhooks configured? You're done! 🎉**

---

## 🎯 Go Live Checklist

When ready for production:

- [ ] Switch mode to **Live** in admin panel
- [ ] Replace with **Live API Key**
- [ ] Update webhooks to use production URLs
- [ ] Test with real payment (small amount)
- [ ] Monitor Laravel logs for errors
- [ ] Verify emails are sending correctly

---

## 📞 Quick Links

| Resource | URL |
|----------|-----|
| Test Dashboard | https://test.dodopayments.com |
| Live Dashboard | https://live.dodopayments.com |
| Documentation | https://docs.dodopayments.com |
| API Reference | https://docs.dodopayments.com/api-reference |
| Your Admin Panel | https://yourdomain.com/admin |

---

## ⚠️ Common Issues

**❌ Payment not working?**
- Check API key is correct
- Verify mode matches (test with test key, live with live key)
- Check `storage/logs/laravel.log`

**❌ Database error?**
- Run the SQL from Step 1
- Verify columns were added: `DESCRIBE additional_settings;`

**❌ Redirect not working?**
- Clear cache: `php artisan cache:clear`
- Check routes: `php artisan route:list | grep dodo`

---

## 🎉 You're All Set!

Dodo Payments is now active on your marketplace!

Users can now:
- ✅ Pay for products using Dodo Payments
- ✅ Subscribe to plans using Dodo Payments
- ✅ Get email confirmations
- ✅ Use multiple currencies

---

**Need detailed docs?** Check `DODOPAYMENTS_INTEGRATION_GUIDE.md`

**Need technical details?** Check `DODOPAYMENTS_INTEGRATION_SUMMARY.md`

