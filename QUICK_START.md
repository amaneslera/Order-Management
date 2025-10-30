# ☕ QUICK SETUP GUIDE - Coffee Kiosk System

## 🚀 5-Minute Setup

### Step 1: Create Database
Open phpMyAdmin or MySQL command line:
```sql
CREATE DATABASE coffee_kiosk;
```

### Step 2: Run Migrations
Open terminal in project root:
```bash
php spark migrate
```

### Step 3: Seed Sample Data
```bash
php spark db:seed InitialDataSeeder
```

### Step 4: Start Server
```bash
php spark serve
```
Or use XAMPP: http://localhost/Order-Management/

### Step 5: Login

**Customer Kiosk** (No login required)
- URL: http://localhost:8080/kiosk
- Browse menu, add to cart, place orders

**Cashier POS**
- URL: http://localhost:8080/login
- Email: cashier@coffeekiosk.com
- Password: cashier123

**Admin Dashboard**
- URL: http://localhost:8080/login
- Email: admin@coffeekiosk.com
- Password: admin123

---

## 📁 What's Included

✅ **6 Database Tables** (auto-created via migrations)
- users (admin, cashier accounts)
- menu_items (products)
- orders (customer orders)
- order_items (order line items)
- payments (payment records)
- activity_logs (system logs)

✅ **10+ Sample Menu Items**
- Coffee (Espresso, Latte, Cappuccino, etc.)
- Snacks (Cookies, Muffins, Sandwiches, etc.)

✅ **3 User Interfaces**
- Customer Kiosk (self-service ordering)
- Cashier POS (order & payment processing)
- Admin Dashboard (reports & management)

✅ **Barcode Integration**
- Uses existing barcode system
- Generates scannable order numbers

---

## 🎯 Key Features

### Customer Features
- Browse menu by category
- Add items to cart
- Place orders
- Get order number with barcode

### Cashier Features
- Search orders by number/barcode
- View order details
- Process payments
- Update order status
- Print receipts

### Admin Features
- Sales dashboard
- Revenue reports
- Top-selling items
- Menu management (CRUD)
- User management
- Activity logs

---

## 🔧 Troubleshooting

**Can't access database?**
- Check XAMPP MySQL is running
- Verify database name is `coffee_kiosk`
- Check credentials in `app/Config/Database.php`

**404 Error?**
- Enable Apache mod_rewrite
- Check .htaccess files exist
- Verify app.baseURL in .env

**Can't upload images?**
- Check folder: `public/uploads/menu/` exists
- Set permissions to 777 (Linux/Mac)
- Check PHP upload_max_filesize setting

**Sessions not working?**
- Clear writable/session/ folder
- Check session settings in app/Config/App.php

---

## 📱 Mobile Responsive

All interfaces are fully responsive:
- Kiosk: Perfect for tablets & touch screens
- POS: Works on desktop & tablets
- Admin: Optimized for desktop

---

## 🎨 Customization

**Change Colors:**
Edit CSS variables in view files:
```css
:root {
    --primary-color: #6B4423; /* Brown coffee color */
}
```

**Add Menu Categories:**
Just add items with new category names in Menu Management

**Modify Email/SMS Notifications:**
Extend controllers to add notification features

---

## 📊 Sample Data Included

**Users:**
- 1 Admin account
- 1 Cashier account

**Menu Items:**
- 6 Coffee items (₱80 - ₱150)
- 4 Snack items (₱45 - ₱95)

---

## 🛠️ Commands Reference

```bash
# Run migrations
php spark migrate

# Rollback migrations
php spark migrate:rollback

# Seed database
php spark db:seed InitialDataSeeder

# Clear cache
php spark cache:clear

# Start development server
php spark serve

# Create new migration
php spark make:migration MigrationName

# Create new controller
php spark make:controller ControllerName

# Create new model
php spark make:model ModelName
```

---

## 🌐 Routes Overview

### Public Routes
- `/` → Kiosk Menu (default)
- `/kiosk` → Kiosk Menu
- `/kiosk/cart` → Shopping Cart
- `/login` → Staff Login

### Authenticated Routes (Cashier/Admin)
- `/pos` → POS Dashboard
- `/pos/search` → Search Orders
- `/pos/order/{id}` → Order Details

### Admin-Only Routes
- `/admin/dashboard` → Analytics
- `/admin/reports` → Sales Reports
- `/admin/menu` → Menu Management
- `/admin/users` → User Management

---

## ✅ Testing Checklist

1. ✅ Can customers browse menu
2. ✅ Can add items to cart
3. ✅ Can place orders
4. ✅ Order generates barcode
5. ✅ Cashier can search orders
6. ✅ Cashier can process payment
7. ✅ Admin can view dashboard
8. ✅ Admin can add menu items
9. ✅ Admin can view reports
10. ✅ Activity logs are working

---

## 📞 Support

For detailed documentation, see:
**COFFEE_KIOSK_README.md**

---

**System Ready! Start Serving Coffee! ☕**
