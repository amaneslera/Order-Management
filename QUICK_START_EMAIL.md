# 🚀 Quick Start: Send Your First Email Report

## ⏱️ 5-Minute Setup

### Step 1: Get Gmail App Password (2 minutes)

1. Go to: **https://myaccount.google.com/apppasswords**
2. Sign in to your Gmail account
3. Select app: **Mail**
4. Select device: **Windows Computer**
5. Click **Generate**
6. **Copy the 16-character password** (looks like: `abcd efgh ijkl mnop`)

> ⚠️ **IMPORTANT:** You can only see this password ONCE! Save it now.

---

### Step 2: Update .env File (1 minute)

Open: `c:\xampp\htdocs\Order-Management\.env`

Find this section:
```ini
email.fromEmail = your-email@gmail.com
email.fromName = Coffee Kiosk POS
email.SMTPHost = smtp.gmail.com
email.SMTPPort = 587
email.SMTPUser = your-email@gmail.com
email.SMTPPass = your-app-password-here
```

Replace with YOUR info:
```ini
email.fromEmail = yourname@gmail.com
email.fromName = Coffee Kiosk POS
email.SMTPHost = smtp.gmail.com
email.SMTPPort = 587
email.SMTPUser = yourname@gmail.com
email.SMTPPass = abcd efgh ijkl mnop
```

**Save the file!**

---

### Step 3: Send Test Email (2 minutes)

1. **Open Browser:**
   - Go to: http://localhost/Order-Management/login

2. **Login as Admin:**
   - Username: `admin`
   - Password: `admin`

3. **Click "Send Daily Report" Button:**
   - Look for the RED button in Quick Actions section

4. **Enter Email:**
   - Type your email address (can be the same Gmail)
   - Click "Send Report"

5. **Check Email:**
   - Wait 1-2 minutes
   - Check your inbox (and spam folder)
   - Subject: "Daily Sales Report - [Today's Date]"

---

## ✅ Success Checklist

- [ ] Generated Gmail App Password
- [ ] Updated .env file
- [ ] Saved .env file
- [ ] Logged in as admin
- [ ] Clicked "Send Daily Report"
- [ ] Entered email address
- [ ] Saw success message
- [ ] Received email

---

## ❌ Common Issues

### "SMTP connect() failed"
**Fix:** You used your regular Gmail password instead of App Password
- Go back to Step 1 and generate an App Password

### "Authentication failed"
**Fix:** Wrong email or password in .env
- Double-check email.SMTPUser and email.SMTPPass
- No spaces or quotes around values

### "No email received"
**Fix:** Check spam folder
- Gmail might filter automated emails
- Wait 5 minutes
- Check Gmail sent items

---

## 📧 What's in the Email?

Your daily sales report includes:

- 💰 **Total Revenue** (₱X,XXX.XX)
- 📦 **Total Orders** (XX orders)
- ✅ **Completed Orders** (XX orders)
- ⏳ **Pending Orders** (XX orders)
- 💵 **Average Order Value** (₱XXX.XX)
- 🏆 **Top 5 Selling Items** (with quantities and revenue)
- 💳 **Payment Methods Breakdown** (Cash, GCash, Card)

---

## 🔧 .env Example (Copy-Paste Ready)

```ini
#--------------------------------------------------------------------
# EMAIL CONFIGURATION
#--------------------------------------------------------------------

email.fromEmail = yourname@gmail.com
email.fromName = Coffee Kiosk POS
email.SMTPHost = smtp.gmail.com
email.SMTPPort = 587
email.SMTPUser = yourname@gmail.com
email.SMTPPass = abcd efgh ijkl mnop
```

**Replace:**
- `yourname@gmail.com` → Your Gmail address
- `abcd efgh ijkl mnop` → Your App Password from Step 1

---

## 🎯 Test Data

If you want to test with sample data, create some test orders:

1. Go to: http://localhost/Order-Management/
2. Add items to cart as a customer
3. Checkout to create an order
4. Login as cashier and mark order as paid
5. Then send the email report to see real data!

---

## 📱 Next Steps

### Daily Use:
- Send report to managers/owners
- Track daily performance
- Share with team members

### Automation (Optional):
- Set up Windows Task Scheduler
- Auto-send every day at 11:59 PM
- See: EMAIL_SETUP_GUIDE.md → "Advanced: Schedule Automatic Daily Reports"

---

## 🆘 Need Help?

1. **Read full guide:** EMAIL_SETUP_GUIDE.md
2. **Check implementation:** IMPLEMENTATION_SUMMARY.md
3. **View logs:** `writable/logs/log-[DATE].log`
4. **Enable debug:** `.env` → `CI_ENVIRONMENT = development`

---

## 🎉 That's It!

You're now ready to send daily sales reports via email!

**Go ahead and send your first report! 📧**
