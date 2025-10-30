# Setup Instructions for Collaborators

## Prerequisites
- XAMPP (PHP 8.1+, MySQL)
- Composer
- Git

## Installation Steps

### 1. Clone the Repository
```bash
git clone https://github.com/amaneslera/Order-Management.git
cd Order-Management
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Setup Environment File
```bash
# Copy the example file
cp .env.example .env
```

Then edit `.env` and configure:
- `app.baseURL` - Set to your local URL (e.g., `http://localhost/Order-Management/`)
- `database.default.database` - Database name: `employee_db`
- `database.default.username` - Your MySQL username (default: `root`)
- `database.default.password` - Your MySQL password (default: empty for XAMPP)
- `session.savePath` - Update path to your installation directory

### 4. Create Database
```sql
CREATE DATABASE employee_db;
```

### 5. Import Database
```bash
# Using MySQL command line
mysql -u root -p employee_db < script/employee_db.sql
```

Or use phpMyAdmin to import `script/employee_db.sql`

### 6. Run Migrations
```bash
php spark migrate
```

### 7. Seed the Database
```bash
php spark db:seed InitialDataSeeder
```

This will create:
- 10 sample coffee menu items
- Default users are already in the SQL file:
  - Admin: `admin` / `admin`
  - Cashier: `cashier` / `cashier`

### 8. Set Permissions (Important!)
Make sure the `writable` folder has write permissions:
```bash
# Windows (PowerShell as Admin)
icacls "writable" /grant Everyone:F /t

# Linux/Mac
chmod -R 777 writable/
```

### 9. Configure XAMPP
- Place project in `C:\xampp\htdocs\Order-Management`
- Start Apache and MySQL in XAMPP Control Panel
- Access: http://localhost/Order-Management/

## Default Credentials

### Admin Login
- Username: `admin`
- Password: `admin`

### Cashier Login
- Username: `cashier`
- Password: `cashier`

## Features

### Customer Kiosk
- URL: http://localhost/Order-Management/
- Self-service ordering
- Shopping cart
- Order confirmation with barcode

### Staff Login
- URL: http://localhost/Order-Management/login

### Admin Dashboard
- Sales reports and analytics
- Menu management
- User management
- Activity logs
- Barcode system integration
- Real-time chat

### Cashier POS
- Order search by number
- Process payments
- View order details
- Barcode scanner integration
- Real-time chat

## Integrations

### Barcode System
Located in `public/barcode-master/`
- Generate barcodes for orders
- Scan barcodes at POS

### Chat Application
Located in `public/Realtime-chat-application-main/`
- Real-time messaging between staff
- Accessible from floating chat button

## Troubleshooting

### Database Connection Error
- Check MySQL is running in XAMPP
- Verify database credentials in `.env`
- Ensure `employee_db` database exists

### 404 Errors
- Check `app.baseURL` in `.env`
- Verify `.htaccess` files exist
- Enable `mod_rewrite` in Apache

### Permission Errors
- Ensure `writable/` folder has write permissions
- Check session save path in `.env`

## Need Help?
Contact the repository owner or create an issue on GitHub.
