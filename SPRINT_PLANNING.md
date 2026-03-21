# Coffee Kiosk Order Management System - Sprint Planning

## Project Configuration Guide (Local Setup)

Use this checklist first before developing Sprint features.

### 1. Prerequisites
- PHP 8.1+
- Composer
- MySQL/MariaDB (XAMPP is supported)
- Apache (if running via XAMPP)

### 2. Clone and Install
```bash
git clone https://github.com/amaneslera/Order-Management.git
cd Order-Management
composer install
```

### 3. Database Setup
Create database:
```sql
CREATE DATABASE coffee_kiosk;
```

Update DB connection in `.env` (copy from `.env.example` if needed):
```env
database.default.hostname = localhost
database.default.database = coffee_kiosk
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### 4. App URL Setup
If using `php spark serve`:
```env
app.baseURL = 'http://localhost:8080/'
```

If using XAMPP Apache project path:
```env
app.baseURL = 'http://localhost/Order-Management/public/'
```

### 5. Run Schema and Seeders
Run CodeIgniter migrations and seed data:
```bash
php spark migrate
php spark db:seed UsersSeeder
php spark db:seed InitialDataSeeder
```

### 6. Stock Alerts Table (Important)
`stock_alerts` is provided as SQL script, so apply it after migrations:
```bash
mysql -u root coffee_kiosk < script/create_stock_alerts_table.sql
```

Alternative: run the SQL file in phpMyAdmin.

### 7. Run the Application
Option A - CI dev server:
```bash
php spark serve
```

Option B - XAMPP Apache:
- Open: `http://localhost/Order-Management/public/`

### 8. Login Accounts
Default seeded accounts:
- Admin: `admin` / `admin123`
- Cashier: `cashier` / `cashier123`

### 9. Optional SMS/Alert Configuration
For SMS low-stock alerts and staff messaging, set in `.env`:
```env
TWILIO_ACCOUNT_SID=
TWILIO_AUTH_TOKEN=
TWILIO_PHONE_NUMBER=
ADMIN_PHONE_NUMBER=
```

Without these, SMS-related features load but cannot send messages.

### 10. Quick Verification Checklist
- Can open `/login`
- Can login as Admin and Cashier
- Can open `/admin/menu/inventory`
- Can open `/admin/menu/alerts`
- Can open `/admin/users`

---

## Sprint 1: Core POS & Inventory (COMPLETED ✅)

| Story ID | User Story Title | Complexity (Points) | Status |
|----------|------------------|---------------------|--------|
| US01 | Owner Login | 3 | Done ✅ |
| US09 | Search Products in POS | 5 | Done ✅ |
| US10 | Add/Modify/Remove Items in Order | 5 | Done ✅ |
| **TOTALS** | | **13 Points** | **All Complete** |

### Sprint 1 Deliverables Completed:
- ✅ User authentication system (Admin/Cashier login)
- ✅ Menu items database with images and stock management
- ✅ Kiosk customer interface with category filtering
- ✅ Shopping cart with add/remove/update functionality
- ✅ Inventory system with stock validation
- ✅ POS dashboard for cashiers
- ✅ Order management (view, create, update status)
- ✅ Receipt generation and printing
- ✅ Payment processing with stock deduction
- ✅ Orders list with status filtering
- ✅ Notification system (non-blocking alerts)

---

## Sprint 2: Enhanced Features & Admin Panel

| Story ID | User Story Title | Complexity (Points) | Status |
|----------|------------------|---------------------|--------|
| US02 | Admin Inventory Management | 8 | To Do |
| US03 | Generate Sales Reports | 8 | To Do |
| US04 | Low Stock Alerts & Notifications | 5 | To Do |
| US05 | Customer Transaction History | 5 | To Do |
| US11 | Refund/Cancel Order Processing | 5 | To Do |
| US12 | Daily Sales Summary & Email Reports | 5 | To Do |
| **TOTALS** | | **36 Points** | **Target: 30-40 Point Sprint** |

### Sprint 2 Proposed Features:

#### US02: Admin Inventory Management (8 points)
- Edit menu item details (name, price, description)
- Upload/replace menu item images
- Adjust stock quantities manually
- Set low stock thresholds
- Disable/enable menu items
- Bulk inventory import from CSV

#### US03: Generate Sales Reports (8 points)
- Daily sales summary by payment method
- Hourly revenue breakdown
- Top selling items report
- Sales trend charts (daily/weekly/monthly)
- Filter reports by date range
- Export reports to PDF/Excel

#### US04: Low Stock Alerts & Notifications (5 points)
- Real-time low stock alerts on admin dashboard
- SMS notifications to staff when items low (using Twilio)
- Auto-generate purchase orders for critical items
- Color-coded stock level indicators (Green/Yellow/Red)
- Configurable alert thresholds per item

#### US05: Customer Transaction History (5 points)
- View past orders on kiosk/receipt
- Repeat order functionality
- Order details with timestamps
- Payment method used display
- Print previous receipt option

#### US11: Refund/Cancel Order Processing (5 points)
- Cancel unpaid orders with stock reversal
- Process refunds for paid orders
- Automatic inventory return on cancellation
- Refund history tracking
- Reason code selection (customer request, damaged, etc.)

#### US12: Daily Sales Summary & Email Reports (5 points)
- Automated daily sales email to admin at 6 PM
- Weekly sales digest every Monday
- Staff performance reports (by cashier)
- Summary includes: total revenue, orders count, top items
- Configurable email recipients and schedule

---

## Sprint 3 (Future): Advanced Features

| Story ID | User Story Title | Complexity (Points) | Status |
|----------|------------------|---------------------|--------|
| US06 | Loyalty Program / Points System | 8 | Backlog |
| US07 | Multi-Location Support | 13 | Backlog |
| US08 | Mobile App Integration | 13 | Backlog |
| US13 | Customer Feedback & Ratings | 5 | Backlog |
| US14 | Promotional Discounts & Coupons | 5 | Backlog |

---

## Technical Debt & Improvements

- [ ] Add unit tests for core business logic
- [ ] Implement API documentation (Swagger/OpenAPI)
- [ ] Add database backup automation
- [ ] Implement request rate limiting
- [ ] Add activity logging audit trail
- [ ] Improve error handling and user feedback
- [ ] Optimize database queries for large datasets
- [ ] Add caching for menu items and reports

---

## Development Progress Timeline

```
Sprint 1: Weeks 1-2 (COMPLETED)
  ↓
Sprint 2: Weeks 3-4 (CURRENT)
  ↓
Sprint 3: Weeks 5-6 (PLANNED)
  ↓
Production Release: Week 7
```

---

## Notes:
- Story points use Fibonacci scale (1, 2, 3, 5, 8, 13)
- Each sprint targets 2-week delivery
- Buffer time allocated for bug fixes and integration testing
- Admin panel largely built; Sprint 2 focuses on completing it
