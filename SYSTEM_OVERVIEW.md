# Coffee Kiosk Order Management System - Overview

## 🎯 System Integration Complete!

Your new Coffee Kiosk system is now fully integrated with your existing integrations (Chat & Barcode).

---

## 🔐 Login Credentials

### Admin Account
- **Username:** `admin`
- **Password:** `admin`
- **Access:** Full system access

### Cashier Account
- **Username:** `cashier`
- **Password:** `cashier`
- **Access:** POS System & Order Management

---

## 🌐 Access Points

### 1. Customer Kiosk (No Login Required)
**URL:** `http://localhost/Order-Management/`

**Features:**
- Browse coffee menu by category
- Add items to cart
- Customize orders (notes, add-ons)
- Place orders
- Get order confirmation with barcode

### 2. Admin Dashboard
**URL:** `http://localhost/Order-Management/admin`

**Login:** admin/admin

**Features:**
- Overview dashboard with quick links
- Coffee Kiosk Management (`/admin/coffee-dashboard`)
  - Today's orders & revenue statistics
  - Top selling items
  - Recent orders
  - Sales charts
- POS System (`/pos`)
- Menu Management (`/admin/menu`)
- User Management (`/admin/users`)
- Reports (`/admin/reports`)
- Activity Logs (`/admin/activity-logs`)
- **Integrated Chat** (floating button bottom-right)
- **Barcode System** link in sidebar

### 3. Cashier Dashboard
**URL:** `http://localhost/Order-Management/cashier`

**Login:** cashier/cashier

**Features:**
- Cashier dashboard overview
- POS System access (`/pos`)
- Order management
- Product management
- **Scan Barcode** link in navbar
- **Integrated Chat** (can message admin)

### 4. POS System
**URL:** `http://localhost/Order-Management/pos`

**Access:** Both Admin & Cashier

**Features:**
- Quick order search (by order number or barcode scan)
- View pending orders
- Process payments
- Update order status
- Print receipts
- View order details
- **Integrated Chat** for staff communication
- **Barcode Scanner** link in sidebar

---

## 🔗 Integrated Features

### 💬 Chat System
**Location:** Floating button (bottom-right corner) on all dashboards

**How it works:**
- Click the chat icon to open chat panel
- Embedded from `public/Realtime-chat-application-main/`
- Real-time messaging between staff
- Passes username and role from session

**Access:**
- Admin Dashboard: Yes
- Cashier Dashboard: Yes (via POS)
- POS System: Yes
- Kiosk: No (customer-facing)

### 📊 Barcode System
**Location:** Links in navigation menus

**Integration Points:**
1. **Order Confirmation Page**
   - Automatically generates barcode for each order
   - Uses `/barcode-master/generate-barcode.php`
   - Shows order number in barcode format
   - Scannable at POS

2. **POS Quick Search**
   - Scan barcode to find order
   - Linked to `/barcode-master/scan.php`
   - Fast order lookup

3. **Admin Access**
   - Full barcode dashboard
   - Link: `/barcode-master/dashboard.php`

---

## 📁 Database Tables

### Existing (Preserved)
- `users` - User accounts (admin, cashier)
- `barcode` - Barcode records
- `messages` - Chat messages

### New (Created)
- `menu_items` - Coffee shop products (10 items seeded)
- `orders` - Customer orders
- `order_items` - Order line items
- `payments` - Payment records
- `activity_logs` - System activity tracking

---

## 🎨 User Flow

### Customer Journey
1. Visit kiosk → Browse menu
2. Add items to cart
3. Checkout → Get order number + barcode
4. Go to cashier → Show barcode
5. Pay → Receive order

### Cashier Journey
1. Login as cashier → `/cashier` dashboard
2. Access POS → `/pos`
3. Scan customer's barcode (or search order number)
4. Process payment
5. Mark order complete
6. Use chat to communicate with admin

### Admin Journey
1. Login as admin → `/admin` dashboard
2. View coffee kiosk stats → `/admin/coffee-dashboard`
3. Manage menu items → `/admin/menu`
4. View reports → `/admin/reports`
5. Monitor activity → `/admin/activity-logs`
6. Access POS if needed → `/pos`
7. Use barcode system → `barcode-master/dashboard.php`

---

## 🎨 Visual Integration

### Chat Integration
```
All Dashboards (Admin, Cashier, POS)
└── Floating Chat Button (bottom-right)
    ├── Click to open chat panel
    ├── Embedded iframe to chat app
    ├── Auto-passes: username, role
    └── Click X to close
```

### Barcode Integration
```
Order Flow
└── Customer places order
    └── Order Confirmation Page
        ├── Shows order number
        ├── Generates barcode image
        └── Customer scans at POS

POS Flow
└── Cashier opens POS
    └── Quick Search Form
        ├── Manual entry OR
        └── Barcode scan
            └── Redirects to order details
```

---

## 📊 Menu Items (Seeded Data)

**Coffee (6 items):**
- Espresso - ₱45
- Americano - ₱55
- Cappuccino - ₱65
- Latte - ₱70
- Mocha - ₱75
- Caramel Macchiato - ₱80

**Snacks (4 items):**
- Croissant - ₱60
- Blueberry Muffin - ₱55
- Chocolate Cookie - ₱45
- Banana Bread - ₱50

---

## 🚀 Quick Start

1. **Start XAMPP**
   - Apache ✅
   - MySQL ✅

2. **Access System**
   ```
   Customer: http://localhost/Order-Management/
   Staff:    http://localhost/Order-Management/login
   ```

3. **Login**
   - Admin: admin/admin
   - Cashier: cashier/cashier

4. **Test Features**
   - Place order as customer
   - See barcode on confirmation
   - Login as cashier
   - Use POS to find order
   - Process payment
   - Check chat system

---

## 📝 Important Routes

```
/                           → Customer Kiosk Menu
/login                      → Staff Login
/admin                      → Admin Dashboard (Your Original)
/admin/coffee-dashboard     → Coffee Kiosk Dashboard (New)
/admin/menu                 → Menu Management
/admin/reports              → Sales Reports
/cashier                    → Cashier Dashboard (Your Original)
/pos                        → POS System (New)
/kiosk                      → Customer Menu (Same as /)
/logout                     → Logout
```

---

## 🎯 Next Steps

1. **Customize Menu**
   - Go to `/admin/menu`
   - Add/Edit/Delete items
   - Set prices & categories
   - Upload product images

2. **Test Order Flow**
   - Place order from kiosk
   - Check barcode generation
   - Process in POS
   - Verify payment recording

3. **Configure Chat**
   - Test messaging between admin/cashier
   - Check real-time updates

4. **Generate Reports**
   - Go to `/admin/reports`
   - View daily/weekly/monthly sales
   - Check top-selling items

---

## 🔧 Troubleshooting

**Can't login?**
- Check database users table
- Verify: admin/admin, cashier/cashier
- Check role: 'Admin' or 'cashier' (case-sensitive)

**Barcode not showing?**
- Check `/barcode-master/` folder exists
- Verify `generate-barcode.php` is working
- Test: `http://localhost/Order-Management/barcode-master/generate-barcode.php?text=TEST123`

**Chat not loading?**
- Check `/Realtime-chat-application-main/` folder
- Verify `users.php` exists
- Check chat database configuration

**404 Not Found?**
- Check `.htaccess` file exists in root
- Verify mod_rewrite enabled in Apache
- Check baseURL in `app/Config/App.php`

---

## 📞 Support

All integrations are preserved:
✅ Your existing chat app in `public/Realtime-chat-application-main/`
✅ Your barcode system in `public/barcode-master/`
✅ Your users table and authentication
✅ Your existing admin and cashier dashboards

New features added:
✨ Coffee kiosk ordering system
✨ POS system for cashiers
✨ Menu management
✨ Sales reporting
✨ Activity logging
