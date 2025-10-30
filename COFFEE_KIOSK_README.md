# ☕ Coffee Kiosk Ordering System

A complete coffee shop ordering and management system built with **CodeIgniter 4**, featuring a customer kiosk interface, cashier POS system, and admin dashboard.

---

## 🎯 Features

### 1️⃣ Customer Kiosk Interface
- Browse menu items by category
- Add items to cart with customization options
- Edit cart before checkout
- Generate order with barcode
- Mobile-responsive design

### 2️⃣ Cashier POS System
- Search orders by order number or barcode
- View and edit order details
- Process payments (Cash, GCash, Card)
- Mark orders as paid/completed
- View pending and completed orders
- Print receipts

### 3️⃣ Admin Dashboard
- Sales analytics and reports (daily, weekly, monthly)
- Revenue tracking
- Top-selling items analysis
- Menu item management (CRUD)
- User management
- Activity logs

---

## 📋 Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Apache/Nginx (XAMPP recommended)
- CodeIgniter 4

---

## 🚀 Installation & Setup

### Step 1: Clone or Extract the Project

Place the project in your web server directory:
```
C:\xampp\htdocs\Order-Management
```

### Step 2: Install Dependencies

Open terminal in the project directory and run:
```bash
composer install
```

### Step 3: Configure Database

1. Create a new MySQL database:
```sql
CREATE DATABASE coffee_kiosk;
```

2. Update database configuration in `app/Config/Database.php`:
```php
public array $default = [
    'DSN'      => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'coffee_kiosk',
    'DBDriver' => 'MySQLi',
    'DBPrefix' => '',
    'pConnect' => false,
    'DBDebug'  => true,
    'charset'  => 'utf8mb4',
    'DBCollat' => 'utf8mb4_general_ci',
    'swapPre'  => '',
    'encrypt'  => false,
    'compress' => false,
    'strictOn' => false,
    'failover' => [],
    'port'     => 3306,
];
```

### Step 4: Run Migrations

Run the database migrations to create all tables:
```bash
php spark migrate
```

This will create the following tables:
- `users` - Admin and cashier accounts
- `menu_items` - Coffee and snack items
- `orders` - Customer orders
- `order_items` - Items in each order
- `payments` - Payment records
- `activity_logs` - System activity tracking

### Step 5: Seed Sample Data

Run the seeder to populate the database with sample data:
```bash
php spark db:seed InitialDataSeeder
```

This will create:
- **Admin user**: admin@coffeekiosk.com / admin123
- **Cashier user**: cashier@coffeekiosk.com / cashier123
- Sample menu items (coffee and snacks)

### Step 6: Configure Environment

1. Copy `env` to `.env`:
```bash
cp env .env
```

2. Edit `.env` file:
```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost/Order-Management/'

database.default.hostname = localhost
database.default.database = coffee_kiosk
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

### Step 7: Set Permissions

Ensure the `writable` folder and `public/uploads` folder have write permissions:
```bash
chmod -R 777 writable/
chmod -R 777 public/uploads/
```

On Windows, right-click the folders → Properties → Security → Give full control.

### Step 8: Start the Server

**Option A: Using PHP Built-in Server**
```bash
php spark serve
```
Access at: `http://localhost:8080`

**Option B: Using XAMPP**
1. Start Apache and MySQL from XAMPP Control Panel
2. Access at: `http://localhost/Order-Management/`

---

## 🔐 Default Login Credentials

### Admin Account
- **Email**: admin@coffeekiosk.com
- **Password**: admin123
- **Access**: Full system control, reports, menu management, user management

### Cashier Account
- **Email**: cashier@coffeekiosk.com
- **Password**: cashier123
- **Access**: POS system, order processing, payment handling

---

## 📁 Project Structure

```
Order-Management/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php       # Authentication
│   │   ├── KioskController.php      # Customer kiosk
│   │   ├── POSController.php        # Cashier POS
│   │   ├── AdminController.php      # Admin dashboard
│   │   └── MenuController.php       # Menu management
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── MenuItemModel.php
│   │   ├── OrderModel.php
│   │   ├── OrderItemModel.php
│   │   ├── PaymentModel.php
│   │   └── ActivityLogModel.php
│   ├── Views/
│   │   ├── auth/
│   │   │   └── login.php
│   │   ├── kiosk/
│   │   │   ├── menu.php
│   │   │   ├── cart.php
│   │   │   └── order_confirmation.php
│   │   ├── pos/
│   │   │   ├── dashboard.php
│   │   │   ├── order_details.php
│   │   │   └── receipt.php
│   │   └── admin/
│   │       ├── dashboard.php
│   │       ├── reports.php
│   │       └── menu/
│   ├── Database/
│   │   ├── Migrations/          # Database table structures
│   │   └── Seeds/               # Sample data
│   └── Config/
│       ├── Routes.php           # URL routing
│       └── Database.php         # DB configuration
├── public/
│   ├── uploads/
│   │   └── menu/               # Menu item images
│   └── barcode-master/         # Existing barcode system
├── writable/                   # Cache, logs, sessions
└── composer.json
```

---

## 🎨 User Interface Routes

### Customer (Kiosk)
- `/kiosk` - Browse menu
- `/kiosk/cart` - View cart
- `/kiosk/order-confirmation/{id}` - Order confirmation with barcode

### Cashier (POS)
- `/login` - Staff login
- `/pos` - POS dashboard
- `/pos/search` - Search orders
- `/pos/order/{id}` - Order details
- `/pos/receipt/{id}` - Print receipt

### Admin (Owner)
- `/admin/dashboard` - Analytics dashboard
- `/admin/reports` - Sales reports
- `/admin/menu` - Menu management
- `/admin/users` - User management
- `/admin/activity-logs` - Activity logs

---

## 🔧 Key Features Explained

### Barcode Integration
The system uses the existing barcode generator at `/public/barcode-master/` to generate order number barcodes. Order numbers are displayed as scannable barcodes on the order confirmation page.

### Order Flow
1. **Customer** selects items on kiosk → adds to cart → checkout
2. System generates unique **order number** and **barcode**
3. Order status: `pending`
4. **Cashier** scans/enters order number → views order details
5. Cashier processes **payment** → status: `paid`
6. Order prepared → status: `completed`

### Payment Methods
- Cash
- GCash
- Credit/Debit Card
- Other digital wallets

### Reports & Analytics
- Daily, weekly, monthly sales
- Top-selling items
- Revenue tracking
- Payment method distribution
- Order status summary

---

## 🛠️ Customization

### Adding Menu Items
1. Login as Admin
2. Go to Menu Management
3. Click "Add Menu Item"
4. Fill in details and upload image
5. Set availability status

### Adding Users
1. Login as Admin
2. Go to User Management
3. Click "Add User"
4. Select role (Admin/Cashier)
5. Set credentials

### Modifying Categories
Edit categories in the seeder or directly add them through the menu management interface.

---

## 📊 Database Tables

| Table | Purpose |
|-------|---------|
| `users` | Staff accounts (admin, cashier) |
| `menu_items` | Products (coffee, snacks) |
| `orders` | Customer orders |
| `order_items` | Line items in orders |
| `payments` | Payment transactions |
| `activity_logs` | System activity tracking |

---

## 🐛 Troubleshooting

### Database Connection Error
- Check MySQL is running
- Verify database credentials in `app/Config/Database.php`
- Ensure database exists

### 404 Errors
- Check `.htaccess` file exists in root and `public/` folders
- Verify `mod_rewrite` is enabled in Apache
- Check `app.baseURL` in `.env`

### File Upload Errors
- Ensure `public/uploads/menu/` folder exists
- Check folder permissions (777)
- Verify `php.ini` upload settings

### Session Errors
- Clear `writable/session/` folder
- Check session configuration in `app/Config/App.php`

---

## 📝 Additional Commands

### Clear Cache
```bash
php spark cache:clear
```

### Create New Migration
```bash
php spark make:migration CreateTableName
```

### Rollback Migration
```bash
php spark migrate:rollback
```

### Create New Controller
```bash
php spark make:controller ControllerName
```

### Create New Model
```bash
php spark make:model ModelName
```

---

## 🎓 Technologies Used

- **Backend**: CodeIgniter 4 (PHP Framework)
- **Database**: MySQL
- **Frontend**: Bootstrap 5, Bootstrap Icons
- **JavaScript**: Vanilla JS, Chart.js
- **Barcode**: Existing barcode generation system

---

## 📄 License

This project is licensed under the MIT License.

---

## 👨‍💻 Support

For issues or questions:
1. Check the troubleshooting section
2. Review CodeIgniter 4 documentation: https://codeigniter.com/user_guide/
3. Check database migration files for schema reference

---

## ✅ System Checklist

- ✅ Database migrations created
- ✅ Sample data seeded
- ✅ Role-based authentication
- ✅ Customer kiosk interface
- ✅ Cashier POS system
- ✅ Admin dashboard
- ✅ Barcode integration
- ✅ Order management
- ✅ Payment processing
- ✅ Sales reports
- ✅ Activity logging
- ✅ Menu CRUD operations
- ✅ User management
- ✅ Responsive design

---

**Happy Coding! ☕**
