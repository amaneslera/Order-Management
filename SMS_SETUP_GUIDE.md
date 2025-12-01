# 📱 SMS Feature Setup Guide - Staff to Admin Messaging

## ✅ Feature Overview

Your Coffee Kiosk POS system now has **SMS functionality** where staff (cashiers) can send urgent messages directly to the admin's phone via SMS.

---

## 🎯 What Was Implemented

### **1. Database**
- ✅ New table: `staff_sms_logs`
- ✅ Migration file created
- ✅ Tracks all SMS messages (SENT/FAILED status)

### **2. SMS Service Library**
- ✅ `app/Libraries/SMSService.php`
- ✅ Semaphore API integration
- ✅ Phone number formatting (09XX → +639XX)
- ✅ Error handling & logging

### **3. Models**
- ✅ `SMSLogModel.php` - Database operations
- ✅ Methods: getStaffLogs, getTodayLogs, getStatistics

### **4. Controllers**
- ✅ `StaffMessagingController.php` - Staff SMS sending
- ✅ `AdminController::smsLogs()` - Admin view logs

### **5. Views**
- ✅ `staff/send_sms.php` - Staff SMS form with templates
- ✅ `admin/sms_logs.php` - Admin SMS logs dashboard
- ✅ Updated POS dashboard with "Message Admin" link

### **6. Routes**
- ✅ `/staff/send-sms` - Send SMS form
- ✅ `/admin/sms-logs` - View all SMS logs

### **7. Security**
- ✅ Authentication check (logged-in staff only)
- ✅ CSRF protection
- ✅ Rate limiting (10 SMS per staff per day)
- ✅ Input validation (5-160 characters)

---

## 🚀 Setup Instructions

### **Step 1: Run Database Migration**

Open PowerShell/Terminal in your project directory:

\`\`\`powershell
cd C:\xampp\htdocs\Order-Management
php spark migrate
\`\`\`

This creates the `staff_sms_logs` table.

---

### **Step 2: Sign Up for Semaphore**

1. **Go to:** https://semaphore.co/
2. **Click "Sign Up"**
3. **Fill in details:**
   - Email: your-email@gmail.com
   - Company: Coffee Kiosk
   - Country: Philippines
4. **Verify email**
5. **Login to dashboard**

---

### **Step 3: Get API Key**

1. **Login to Semaphore dashboard**
2. **Go to:** Account → API Keys
3. **Copy your API Key** (looks like: `abcd1234efgh5678...`)
4. **Save it** - you'll need it for .env

---

### **Step 4: Load SMS Credits**

1. **In Semaphore dashboard:**
   - Click "Buy Credits"
   - Choose amount:
     - ₱500 = ~500-1000 SMS
     - ₱1000 = ~1000-2000 SMS
2. **Payment methods:**
   - GCash
   - Credit/Debit Card
   - Bank Transfer

**Pricing:** ~₱0.50-₱1.00 per SMS (cheaper in bulk)

---

### **Step 5: Configure .env File**

Open: `C:\xampp\htdocs\Order-Management\.env`

Find the SMS configuration section (already added):

\`\`\`ini
#--------------------------------------------------------------------
# SMS CONFIGURATION (Semaphore API)
#--------------------------------------------------------------------

sms.apiKey = "your-semaphore-api-key-here"
sms.adminPhone = "+639686186310"
sms.senderName = "CoffeeKiosk"
\`\`\`

**Update:**
- `sms.apiKey` = Your actual Semaphore API key (from Step 3)
- `sms.adminPhone` = Already set to your number: +639686186310
- `sms.senderName` = Name shown on SMS (max 11 chars)

**Example:**
\`\`\`ini
sms.apiKey = "abcd1234efgh5678ijkl9012mnop3456"
sms.adminPhone = "+639686186310"
sms.senderName = "CoffeeKiosk"
\`\`\`

**Save the file!**

---

### **Step 6: Test SMS Feature**

#### **A. Login as Cashier**
1. Go to: http://localhost/Order-Management/login
2. Username: `cashier`
3. Password: `cashier`

#### **B. Navigate to SMS Page**
- Click **"Message Admin"** in the sidebar
- Or go to: http://localhost/Order-Management/staff/send-sms

#### **C. Send Test SMS**
1. Type message: "Test message from coffee kiosk system"
2. Click **"Send to Admin"**
3. Wait 2-5 seconds
4. Check your phone (+639686186310) for SMS

#### **D. Expected Result**
- ✅ Success message: "SMS sent successfully to admin!"
- ✅ SMS received on your phone within 1 minute
- ✅ Message appears in "Recent Messages" list

---

## 📊 Features & Functionality

### **Staff Features:**

1. **Send SMS Form**
   - 160 character limit
   - Real-time character counter
   - Quick message templates
   - SMS history view

2. **Quick Templates**
   - 📦 Need Supplies
   - ⚙️ Equipment Issue
   - 👤 Customer Issue
   - 🚨 Urgent Message

3. **Rate Limiting**
   - Max 10 SMS per staff per day
   - Prevents abuse
   - Counter shows remaining SMS

4. **SMS History**
   - View past messages
   - See SENT/FAILED status
   - Error messages for failed SMS

---

### **Admin Features:**

1. **SMS Logs Dashboard**
   - View all staff messages
   - Filter by status (SENT/FAILED)
   - Search by staff name
   - Date/time stamps

2. **Statistics Cards**
   - Total Sent
   - Total Failed
   - Today's Count
   - Success Rate %

3. **Message Details**
   - Click "Details" button
   - View full message
   - See error messages
   - SMS ID tracking

---

## 🔧 Technical Details

### **Routes:**

\`\`\`php
/staff/send-sms (GET)    - Show SMS form
/staff/send-sms (POST)   - Send SMS (AJAX)
/staff/sms-logs (GET)    - View staff's SMS history
/admin/sms-logs (GET)    - Admin view all SMS
\`\`\`

### **Database Table:**

\`\`\`sql
staff_sms_logs
├── id (PK)
├── staff_id (FK → users.id)
├── staff_name
├── message
├── admin_phone
├── status (SENT/FAILED)
├── error_message
├── sms_id (Semaphore message ID)
├── sent_at
└── created_at
\`\`\`

### **SMS API:**

- **Provider:** Semaphore
- **Endpoint:** https://api.semaphore.co/api/v4/messages
- **Method:** POST
- **Format:** application/x-www-form-urlencoded
- **Response:** JSON

---

## ⚠️ Troubleshooting

### **Error: "SMS API key not configured"**

**Cause:** API key not in .env file

**Fix:**
1. Check `.env` file
2. Make sure `sms.apiKey` is set
3. Value must be in quotes: `sms.apiKey = "your-key"`
4. Save and refresh page

---

### **Error: "Admin phone number not configured"**

**Cause:** Phone number missing from .env

**Fix:**
1. Check `.env` file
2. Make sure `sms.adminPhone = "+639686186310"`
3. Save and refresh

---

### **Error: "Invalid API key"**

**Cause:** Wrong API key

**Fix:**
1. Login to Semaphore dashboard
2. Go to Account → API Keys
3. Copy the correct API key
4. Update `.env` file
5. Save and test again

---

### **Error: "Insufficient credits"**

**Cause:** No SMS credits in Semaphore account

**Fix:**
1. Login to Semaphore
2. Buy credits (₱500 recommended)
3. Wait for payment confirmation
4. Try sending SMS again

---

### **SMS not received but status shows SENT**

**Possible causes:**
- Network delay (wait 5 minutes)
- Phone turned off
- No signal
- Number blocked SMS
- Telco issue

**Fix:**
- Check Semaphore dashboard → Messages
- Verify delivery status
- Try sending to another number

---

### **Error: "Daily SMS limit reached"**

**Cause:** Staff sent 10 SMS today

**Fix:**
- This is normal rate limiting
- Limit resets at midnight
- Admin can adjust limit in `StaffMessagingController.php`:
  \`\`\`php
  private $maxSmsPerDay = 10; // Change this number
  \`\`\`

---

## 💡 Usage Tips

### **For Staff:**
1. ✅ Use for **urgent matters only**
2. ✅ Keep messages **clear and brief**
3. ✅ Use **quick templates** to save time
4. ✅ Check **SMS history** for sent messages
5. ❌ Don't spam admin with unnecessary SMS

### **For Admin:**
1. ✅ Check **SMS Logs** regularly
2. ✅ Monitor **success rate** in statistics
3. ✅ Filter messages by **status**
4. ✅ Keep **SMS credits loaded** in Semaphore
5. ✅ Update phone number in .env if changed

---

## 📈 Cost Estimation

### **Monthly Usage:**
- **Low usage:** 50 SMS/month = ₱50
- **Medium usage:** 200 SMS/month = ₱200
- **High usage:** 500 SMS/month = ₱500

### **Recommended:**
- Start with ₱500 credit
- Monitor usage in Semaphore dashboard
- Reload when credits low

---

## 🔐 Security Features

1. ✅ **Authentication Required** - Must be logged in
2. ✅ **CSRF Protection** - Prevents fake requests
3. ✅ **Rate Limiting** - Max 10 SMS per day per staff
4. ✅ **Input Validation** - 5-160 characters
5. ✅ **Error Logging** - All errors logged
6. ✅ **API Key Security** - Stored in .env (not in Git)

---

## 📝 Testing Checklist

- [ ] Run migration: `php spark migrate`
- [ ] Signed up for Semaphore account
- [ ] Got API key from Semaphore
- [ ] Loaded SMS credits (₱500+)
- [ ] Updated .env with API key
- [ ] Verified admin phone number in .env
- [ ] Logged in as cashier
- [ ] Sent test SMS
- [ ] Received SMS on phone
- [ ] Checked SMS logs as admin
- [ ] Verified statistics working

---

## 🎉 Success Indicators

✅ **SMS Feature Working If:**
1. Staff can access "Message Admin" page
2. Character counter works
3. Quick templates load message
4. Submit button sends SMS
5. Success message appears
6. SMS received on admin phone
7. Message appears in history
8. Admin can view SMS in logs
9. Statistics cards show data
10. Filter buttons work

---

## 📞 Quick Reference

### **URLs:**
- Staff SMS: `http://localhost/Order-Management/staff/send-sms`
- Admin Logs: `http://localhost/Order-Management/admin/sms-logs`

### **Credentials:**
- Admin: `admin` / `admin`
- Cashier: `cashier` / `cashier`

### **Admin Phone:**
- +639686186310 (configured in .env)

### **API Provider:**
- Semaphore: https://semaphore.co/

### **Support:**
- Semaphore Docs: https://semaphore.co/docs
- Contact: support@semaphore.co

---

## 🔄 Optional Enhancements

### **Future Features (Not Yet Implemented):**
1. **Inbound SMS** - Admin replies via SMS
2. **Multiple Recipients** - Send to multiple admins
3. **SMS Scheduling** - Schedule messages
4. **SMS Templates** - Admin can customize templates
5. **Email Notifications** - Email copy of SMS
6. **SMS Reports** - Weekly/monthly SMS reports

---

**🎊 SMS Feature is Ready! Just configure Semaphore API key and start sending messages!**

For questions, check the troubleshooting section or Semaphore documentation.
