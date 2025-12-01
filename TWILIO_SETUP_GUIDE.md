# Twilio SMS Setup Guide

## Why Twilio?
✅ **FREE $15 Trial Credits** (~500 SMS)  
✅ Works worldwide  
✅ More reliable than Semaphore  
✅ Easy to set up  
✅ Better error messages

---

## Step 1: Sign Up for Twilio (FREE)

1. Go to: **https://www.twilio.com/try-twilio**
2. Click **"Sign up and start building"**
3. Fill in your details:
   - Email
   - Password
   - First Name / Last Name
4. Click **"Start your free trial"**
5. **Verify your email** (check inbox)
6. **Verify your phone number** (they'll send you a code)

---

## Step 2: Get Your Credentials

After signing in, you'll see your **Twilio Console Dashboard**:

### A. Get Account SID and Auth Token
1. On the dashboard, you'll see:
   - **Account SID**: `ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`
   - **Auth Token**: Click "Show" to reveal it
2. **Copy both** - you'll need them for `.env`

### B. Get a Twilio Phone Number (FREE)
1. Click **"Get a Twilio phone number"** button
2. Twilio will assign you a free US number: `+1 (xxx) xxx-xxxx`
3. Click **"Choose this number"**
4. **Copy this number** - you'll need it for `.env`

---

## Step 3: Configure Your Project

Open your `.env` file and update these lines:

```ini
twilio.accountSid = "ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"  # From Step 2A
twilio.authToken = "your-auth-token-here"                  # From Step 2A
twilio.phoneNumber = "+1xxxxxxxxxx"                        # From Step 2B
sms.adminPhone = "+639169412943"                           # Your Philippine number
```

**Important:** 
- Don't remove the `+` from phone numbers
- Twilio number format: `+1xxxxxxxxxx` (US number)
- Admin phone format: `+639xxxxxxxxx` (PH number)

---

## Step 4: Test the Feature

1. **Login as Cashier**
   - Username: `cashier`
   - Password: (your cashier password)

2. **Send Test SMS**
   - Click **"Message Admin"** in sidebar
   - Type: "Testing Twilio SMS!"
   - Click **"Send SMS"**

3. **Check Your Phone**
   - You should receive the SMS on **+639169412943**
   - Sender will show as your Twilio number

---

## Free Trial Limitations

✅ **Allowed:**
- Send SMS to verified phone numbers
- $15 FREE credits (~500 SMS)
- Test all features

⚠️ **Limitations:**
- Can only send to phone numbers you verify in Twilio Console
- Messages include "[Sent from your Twilio trial account]" prefix
- Remove limitations by upgrading (add $20+ credits)

### How to Verify Admin Phone in Twilio:
1. Go to: https://console.twilio.com/us1/develop/phone-numbers/manage/verified
2. Click **"+ Add new number"**
3. Enter: `+639169412943`
4. Select: **SMS**
5. You'll receive a verification code via SMS
6. Enter the code to verify

---

## Pricing (After Free Trial)

When trial credits run out:

| Service | Cost |
|---------|------|
| SMS to Philippines | $0.0395/SMS (~₱2.25) |
| Top-up amount | $20 minimum (~₱1,140) |
| Messages per $20 | ~500 SMS |

**Note:** More expensive than Semaphore (₱0.55/SMS), but has free trial!

---

## Upgrade to Production (Optional)

To remove trial limitations:

1. Go to: https://console.twilio.com/us1/billing/manage-billing/billing-overview
2. Click **"Upgrade account"**
3. Add payment method (Credit Card)
4. Add credits: Minimum $20
5. Benefits:
   - Remove "trial account" message prefix
   - Send to any phone number (no verification needed)
   - Higher sending limits

---

## Troubleshooting

### Error: "To number is not verified"
**Solution:** Verify your admin phone number in Twilio Console (see above)

### Error: "Account SID or Auth Token invalid"
**Solution:** Double-check your credentials in `.env` file

### Error: "Insufficient funds"
**Solution:** Trial credits expired - add more credits in Twilio Console

### SMS not received
**Check:**
1. Admin phone number format: `+639169412943` (with country code)
2. Phone number is verified in Twilio (for trial accounts)
3. Check spam/blocked messages on your phone

---

## Support

- **Twilio Docs:** https://www.twilio.com/docs/sms
- **Twilio Console:** https://console.twilio.com/
- **Support:** https://support.twilio.com/

---

## Summary

✅ Sign up: https://www.twilio.com/try-twilio  
✅ Get: Account SID, Auth Token, Phone Number  
✅ Update: `.env` file  
✅ Verify: Admin phone number in Twilio Console  
✅ Test: Send SMS from staff interface  

**FREE $15 credits = Perfect for testing! 🎉**
