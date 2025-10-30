@echo off
REM Coffee Kiosk System - Windows Setup Script
REM Run this script to automatically set up the system

echo.
echo ========================================
echo   COFFEE KIOSK SYSTEM - SETUP WIZARD
echo ========================================
echo.

REM Check if we're in the right directory
if not exist "app\Config\Database.php" (
    echo ERROR: Please run this script from the project root directory!
    echo Current directory: %cd%
    pause
    exit /b 1
)

echo [1/6] Checking PHP installation...
php --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: PHP is not installed or not in PATH!
    echo Please install PHP or add it to your system PATH.
    pause
    exit /b 1
)
echo OK - PHP is installed
echo.

echo [2/6] Checking Composer...
composer --version >nul 2>&1
if errorlevel 1 (
    echo WARNING: Composer not found. Skipping dependency installation.
) else (
    echo OK - Composer is installed
    echo Installing dependencies...
    call composer install
)
echo.

echo [3/6] Checking database configuration...
echo Please make sure you have:
echo   1. Created a database named 'coffee_kiosk'
echo   2. Updated app/Config/Database.php with your credentials
echo.
echo Press any key to continue or Ctrl+C to exit...
pause >nul
echo.

echo [4/6] Running database migrations...
php spark migrate
if errorlevel 1 (
    echo ERROR: Migration failed! Please check your database configuration.
    pause
    exit /b 1
)
echo OK - Database tables created
echo.

echo [5/6] Seeding sample data...
php spark db:seed InitialDataSeeder
if errorlevel 1 (
    echo ERROR: Seeding failed!
    pause
    exit /b 1
)
echo OK - Sample data inserted
echo.

echo [6/6] Creating required directories...
if not exist "public\uploads\menu" mkdir "public\uploads\menu"
if not exist "writable\cache" mkdir "writable\cache"
if not exist "writable\logs" mkdir "writable\logs"
if not exist "writable\session" mkdir "writable\session"
echo OK - Directories created
echo.

echo ========================================
echo   SETUP COMPLETE!
echo ========================================
echo.
echo Your Coffee Kiosk System is ready to use!
echo.
echo Default Login Credentials:
echo.
echo ADMIN:
echo   Email: admin@coffeekiosk.com
echo   Password: admin123
echo.
echo CASHIER:
echo   Email: cashier@coffeekiosk.com
echo   Password: cashier123
echo.
echo ----------------------------------------
echo.
echo To start the development server, run:
echo   php spark serve
echo.
echo Then visit:
echo   http://localhost:8080/kiosk     (Customer Kiosk)
echo   http://localhost:8080/login     (Staff Login)
echo.
echo For more information, see:
echo   - QUICK_START.md
echo   - COFFEE_KIOSK_README.md
echo.
pause
