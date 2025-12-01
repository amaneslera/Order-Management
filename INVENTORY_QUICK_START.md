# Quick Start: Activate Inventory System

## ⚡ 3-Step Activation

### Step 1: Backup Database (30 seconds)
```powershell
cd c:\xampp\mysql\bin
.\mysqldump.exe -u root employee_db > C:\xampp\htdocs\Order-Management\backup_before_inventory.sql
```

### Step 2: Run Migration (10 seconds)
```powershell
cd c:\xampp\htdocs\Order-Management
php spark migrate
```

**Expected Output:**
```
Running: 2025-12-02-000001_AddInventorySystem
Migrated: 2025-12-02-000001_AddInventorySystem
```

### Step 3: Set Initial Stock (5 minutes)
1. Go to: `http://localhost/Order-Management/admin/inventory`
2. Click "Update Stock" for each item
3. Set quantity to 100, threshold to 20
4. Save

---

## ✅ Verification Checklist

After activation, verify these work:

- [ ] Admin dashboard shows inventory alerts widget
- [ ] Can access `admin/inventory` page
- [ ] Can update stock quantities via modal
- [ ] Stock changes appear in "Recent Activity"
- [ ] Low stock items show yellow warning
- [ ] Out of stock items show red critical alert
- [ ] Kiosk prevents ordering out-of-stock items
- [ ] POS automatically deducts stock on payment
- [ ] Inventory report shows all changes
- [ ] Activity logs recorded in database

---

## 🔄 Test Transaction Flow

### Complete Order Test:
1. **Kiosk:** Add 2x Hot Coffee to cart (stock: 100)
2. **Kiosk:** Checkout order
3. **POS:** Process payment for the order
4. **Admin:** Check inventory page
   - **Expected:** Hot Coffee stock now 98
5. **Admin:** Check Recent Stock Changes
   - **Expected:** Deduction entry with order reference

---

## 🎯 Key URLs

- **Main Inventory:** `http://localhost/Order-Management/admin/inventory`
- **Low Stock Alerts:** `http://localhost/Order-Management/admin/inventory/low-stock`
- **Inventory Report:** `http://localhost/Order-Management/admin/inventory/report`
- **Admin Dashboard:** `http://localhost/Order-Management/admin`

---

## ⚠️ If Something Goes Wrong

### Rollback Migration:
```powershell
cd c:\xampp\htdocs\Order-Management
php spark migrate:rollback
```

### Restore Backup:
```powershell
cd c:\xampp\mysql\bin
.\mysql.exe -u root employee_db < C:\xampp\htdocs\Order-Management\backup_before_inventory.sql
```

---

## 📊 Default Stock Settings

| Item | Initial Stock | Threshold |
|------|--------------|-----------|
| Hot Coffee | 100 | 20 |
| Iced Coffee | 100 | 20 |
| Cappuccino | 80 | 15 |
| Latte | 80 | 15 |
| Espresso | 60 | 10 |
| Mocha | 70 | 15 |
| Americano | 90 | 20 |
| Macchiato | 60 | 10 |
| Frappe | 80 | 15 |
| Tea | 100 | 20 |

---

## 🎉 Success Indicators

After successful activation:
- ✅ Migration status shows completed
- ✅ `menu_items` table has `stock_quantity` column
- ✅ `inventory_logs` table exists
- ✅ Admin dashboard shows stock widget
- ✅ No PHP errors in logs
- ✅ Stock updates work via AJAX
- ✅ Alerts appear for low stock items

---

**Ready? Run the migration and activate your inventory system!**

For detailed information, see: `PHASE_1_IMPLEMENTATION_GUIDE.md`
