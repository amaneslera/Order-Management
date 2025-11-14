# Email Setup Guide for Daily Sales Reports

## Overview
Your Coffee Kiosk POS system now has integrated email functionality to send automated daily sales reports via Gmail SMTP using PHPMailer.

## Features
- ✉️ Send daily sales reports via email
- 📊 Professional HTML email templates with sales statistics
- 📈 Includes: Total revenue, order counts, top selling items, payment methods breakdown
- 🔒 Secure Gmail SMTP integration with App Password
- 🎨 Beautiful responsive email design

---

## Step 1: Enable Gmail App Password

**⚠️ IMPORTANT:** You cannot use your regular Gmail password. You MUST create an App Password.

### How to Generate Gmail App Password:

1. **Enable 2-Step Verification** (if not already enabled):
   - Go to: https://myaccount.google.com/security
   - Click on "2-Step Verification"
   - Follow the steps to enable it

2. **Generate App Password**:
   - Go to: https://myaccount.google.com/apppasswords
   - OR Search "App Passwords" in your Google Account settings
   - Sign in if prompted
   - In the "Select app" dropdown, choose **"Mail"**
   - In the "Select device" dropdown, choose **"Windows Computer"** or **"Other (Custom name)"**
   - If using "Other", enter: `Coffee Kiosk POS`
   - Click **"Generate"**

3. **Copy the 16-Character Password**:
   - You'll see a 16-character password like: `abcd efgh ijkl mnop`
   - **COPY THIS PASSWORD** - you'll need it for the .env file
   - ⚠️ You can only see this password ONCE, so save it immediately

---

## Step 2: Configure .env File

Open your `.env` file located in the root directory (`c:\xampp\htdocs\Order-Management\.env`)

### Find the Email Configuration Section:

```ini
#--------------------------------------------------------------------
# EMAIL CONFIGURATION (for PHPMailer)
#--------------------------------------------------------------------

email.fromEmail = your-email@gmail.com
email.fromName = Coffee Kiosk POS
email.SMTPHost = smtp.gmail.com
email.SMTPPort = 587
email.SMTPUser = your-email@gmail.com
email.SMTPPass = your-app-password-here
```

### Update with Your Details:

```ini
#--------------------------------------------------------------------
# EMAIL CONFIGURATION (for PHPMailer)
#--------------------------------------------------------------------

email.fromEmail = yourname@gmail.com
email.fromName = Coffee Kiosk POS
email.SMTPHost = smtp.gmail.com
email.SMTPPort = 587
email.SMTPUser = yourname@gmail.com
email.SMTPPass = abcd efgh ijkl mnop
```

**Replace:**
- `yourname@gmail.com` - Your actual Gmail address
- `abcd efgh ijkl mnop` - The 16-character App Password from Step 1

**Notes:**
- You can include or remove spaces in the App Password - both work
- `email.fromName` is what appears as the sender name in emails
- Port 587 with TLS is the standard for Gmail SMTP

---

## Step 3: Test Email Sending

### From Admin Dashboard:

1. **Login as Admin**:
   - Go to: http://localhost/Order-Management/login
   - Username: `admin`
   - Password: `admin`

2. **Open Admin Dashboard**:
   - You'll be redirected to: http://localhost/Order-Management/admin

3. **Click "Send Daily Report" Button**:
   - Look for the red "Send Daily Report" button in the Quick Actions section
   - Click it to open the email modal

4. **Enter Recipient Email**:
   - Enter the email address where you want to receive the report
   - This can be the same Gmail you configured, or any other email
   - Click "Send Report"

5. **Check Results**:
   - If successful: You'll see a success message and receive the email within 1-2 minutes
   - If failed: Check the error message and verify your .env configuration

### Check Email Inbox:

- **Subject:** "Daily Sales Report - [Today's Date]"
- **Contains:**
  - 📊 Total Revenue (large, prominent)
  - 📦 Total Orders Count
  - ✅ Completed Orders
  - ⏳ Pending Orders
  - 💰 Average Order Value
  - 🏆 Top 5 Selling Items (table with quantities and revenue)
  - 💳 Payment Methods Summary (Cash, GCash, Card)

---

## Troubleshooting

### Error: "SMTP connect() failed"

**Causes:**
- Wrong Gmail App Password
- Regular Gmail password used instead of App Password
- 2-Step Verification not enabled

**Solutions:**
1. Verify you generated an App Password (not using regular password)
2. Check 2-Step Verification is enabled
3. Generate a new App Password and update .env
4. Make sure there are no extra spaces in email.SMTPPass

---

### Error: "Could not authenticate"

**Causes:**
- Incorrect username or password
- Copy-paste error in .env file

**Solutions:**
1. Double-check email.SMTPUser matches your Gmail
2. Verify email.SMTPPass has the correct 16-character App Password
3. Try removing spaces from the App Password
4. Generate a new App Password

---

### Error: "Could not instantiate mail function"

**Causes:**
- PHP mail extension disabled
- Windows firewall blocking SMTP

**Solutions:**
1. Check if XAMPP allows outbound SMTP connections
2. Try temporarily disabling Windows Firewall
3. Verify port 587 is not blocked

---

### No Email Received (No Error)

**Possible Causes:**
- Email sent to spam/junk folder
- Delay in email delivery
- Wrong recipient email

**Solutions:**
1. **Check Spam/Junk folder** - Gmail might filter automated emails
2. Wait 2-5 minutes - sometimes there's a delay
3. Verify recipient email address is correct
4. Check sent items in your Gmail account (from web interface)

---

### Error: "Please provide recipient email address"

**Cause:**
- Modal form submitted without entering email

**Solution:**
- Enter a valid email address in the modal before clicking "Send Report"

---

## Email Report Content

The daily sales report includes:

### 1. **Sales Overview Section**
- Large total revenue display
- Total number of orders
- Completed vs Pending orders breakdown
- Average order value calculation

### 2. **Top Selling Items Table**
```
Rank | Item Name | Quantity | Revenue
-----|-----------|----------|--------
1    | Cappuccino| 45       | ₱2,250
2    | Latte     | 38       | ₱1,900
... (up to 5 items)
```

### 3. **Payment Methods Summary**
- Cash: ₱X,XXX.XX
- GCash: ₱X,XXX.XX
- Card: ₱X,XXX.XX

### 4. **Email Footer**
- Timestamp of when report was generated
- Company branding

---

## Advanced: Schedule Automatic Daily Reports

You can set up Windows Task Scheduler to automatically send reports daily.

### Create PHP Script for Scheduled Sending:

Create: `c:\xampp\htdocs\Order-Management\send_daily_report.php`

```php
<?php
// Load CodeIgniter
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Config/Paths.php';

$paths = new Config\Paths();
require_once SYSTEMPATH . 'bootstrap.php';

use App\Libraries\EmailService;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\PaymentModel;

$emailService = new EmailService();
$orderModel = new OrderModel();
$orderItemModel = new OrderItemModel();
$paymentModel = new PaymentModel();

// Gather today's sales data
$today = date('Y-m-d');
$todayOrders = $orderModel->where('DATE(created_at)', $today)->findAll();

$totalOrders = count($todayOrders);
$totalRevenue = array_sum(array_column($todayOrders, 'total_amount'));

// Get top selling items
$topItems = $orderItemModel
    ->select('menu_items.name, SUM(order_items.quantity) as quantity, SUM(order_items.quantity * order_items.price) as revenue')
    ->join('menu_items', 'menu_items.id = order_items.menu_item_id')
    ->join('orders', 'orders.id = order_items.order_id')
    ->where('DATE(orders.created_at)', $today)
    ->groupBy('order_items.menu_item_id')
    ->orderBy('quantity', 'DESC')
    ->limit(5)
    ->findAll();

// Get payment methods
$payments = $paymentModel
    ->select('payment_method, SUM(amount) as total')
    ->join('orders', 'orders.id = payments.order_id')
    ->where('DATE(payments.payment_date)', $today)
    ->groupBy('payment_method')
    ->findAll();

$paymentMethods = [];
foreach ($payments as $payment) {
    $paymentMethods[$payment['payment_method']] = $payment['total'];
}

$salesData = [
    'total_orders' => $totalOrders,
    'total_revenue' => $totalRevenue,
    'top_items' => $topItems,
    'payment_methods' => $paymentMethods,
];

// Send email to admin
$recipientEmail = 'admin@yourdomain.com'; // Change this
$result = $emailService->sendDailySalesReport($recipientEmail, $salesData);

if ($result['success']) {
    echo "Report sent successfully!\n";
} else {
    echo "Error: " . $result['message'] . "\n";
}
```

### Windows Task Scheduler Setup:

1. Open Task Scheduler (search in Start menu)
2. Click "Create Basic Task"
3. Name: `Coffee Kiosk Daily Report`
4. Trigger: Daily at 11:59 PM
5. Action: Start a program
   - Program: `C:\xampp\php\php.exe`
   - Arguments: `C:\xampp\htdocs\Order-Management\send_daily_report.php`
6. Finish

---

## Security Best Practices

### ✅ DO:
- Use Gmail App Passwords (16 characters)
- Keep .env file in .gitignore (already done)
- Never commit .env to GitHub
- Change default admin password
- Use different passwords for dev/production

### ❌ DON'T:
- Don't use your regular Gmail password
- Don't share your .env file
- Don't push .env to public repositories
- Don't hardcode passwords in PHP files

---

## File Structure

```
Order-Management/
├── .env                           # Email config here (NOT in Git)
├── .env.example                   # Template for collaborators
├── app/
│   ├── Libraries/
│   │   └── EmailService.php      # PHPMailer wrapper
│   ├── Controllers/
│   │   └── AdminController.php   # sendDailySalesReport()
│   └── Views/
│       └── admin/
│           └── dashboard.php     # "Send Daily Report" button
├── vendor/
│   └── phpmailer/                # Installed via composer
└── EMAIL_SETUP_GUIDE.md          # This file
```

---

## Testing Checklist

- [ ] Generated Gmail App Password
- [ ] Updated .env with email settings
- [ ] Logged in as admin
- [ ] Clicked "Send Daily Report" button
- [ ] Entered recipient email
- [ ] Received email successfully
- [ ] Email contains sales data
- [ ] Email is formatted correctly (HTML)
- [ ] Checked spam folder if not in inbox

---

## Support

If you encounter issues:

1. **Check XAMPP Error Logs:**
   - `C:\xampp\apache\logs\error.log`
   - `c:\xampp\htdocs\Order-Management\writable\logs\log-[DATE].log`

2. **Enable Debug Mode:**
   - Edit `.env`: `CI_ENVIRONMENT = development`
   - Reload page to see detailed errors

3. **Verify Email Config:**
   - Make sure all email.* settings in .env are filled
   - No quotes around values
   - No extra spaces

4. **Test Gmail Access:**
   - Try logging into Gmail web interface
   - Make sure account is not locked

---

## Quick Reference

### Gmail SMTP Settings:
```
Host: smtp.gmail.com
Port: 587
Encryption: TLS (STARTTLS)
Auth: Yes (App Password required)
```

### .env Email Section:
```ini
email.fromEmail = your-email@gmail.com
email.fromName = Coffee Kiosk POS
email.SMTPHost = smtp.gmail.com
email.SMTPPort = 587
email.SMTPUser = your-email@gmail.com
email.SMTPPass = your-app-password-here
```

### Send Report URL:
```
POST: http://localhost/Order-Management/admin/send-daily-report
Parameters: email=[recipient@email.com]
```

---

**✅ Email integration is now complete and ready to use!**

For any questions or issues, review this guide or check the CodeIgniter 4 documentation.
