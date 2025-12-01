<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route - Kiosk
$routes->get('/', 'KioskController::index');

// Authentication Routes (using your existing Auth controller)
$routes->match(['get', 'post'], 'login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Main Dashboard Routes - Using NEW Coffee Kiosk Dashboards
$routes->get('admin', 'AdminController::dashboard');
$routes->get('cashier', 'POSController::index');

// Kiosk Routes (Customer Interface)
$routes->group('kiosk', function($routes) {
    $routes->get('/', 'KioskController::index');
    $routes->get('menu/category/(:segment)', 'KioskController::getMenuByCategory/$1');
    $routes->get('cart', 'KioskController::cart');
    $routes->post('cart/add', 'KioskController::addToCart');
    $routes->post('cart/update', 'KioskController::updateCart');
    $routes->post('cart/remove', 'KioskController::removeFromCart');
    $routes->get('cart/clear', 'KioskController::clearCart');
    $routes->post('checkout', 'KioskController::checkout');
    $routes->get('order-confirmation/(:num)', 'KioskController::orderConfirmation/$1');
});

// POS Routes (Cashier Interface)
$routes->group('pos', function($routes) {
    $routes->get('/', 'POSController::index');
    $routes->get('search', 'POSController::searchOrder');
    $routes->get('order/(:num)', 'POSController::viewOrder/$1');
    $routes->post('order/status/update', 'POSController::updateOrderStatus');
    $routes->post('order/item/add', 'POSController::addOrderItem');
    $routes->post('order/item/remove', 'POSController::removeOrderItem');
    $routes->post('payment/process', 'POSController::processPayment');
    $routes->get('receipt/(:num)', 'POSController::viewReceipt/$1');
    $routes->get('orders', 'POSController::listOrders');
});

// Staff SMS Routes (for cashiers/staff to message admin)
$routes->group('staff', function($routes) {
    $routes->get('send-sms', 'StaffMessagingController::index');
    $routes->post('send-sms', 'StaffMessagingController::sendToAdmin');
    $routes->get('sms-logs', 'StaffMessagingController::logs');
    $routes->get('sms-balance', 'StaffMessagingController::checkBalance');
});

// Admin Routes (Coffee Kiosk Management)
$routes->group('admin', function($routes) {
    $routes->get('coffee-dashboard', 'AdminController::dashboard');
    $routes->get('reports', 'AdminController::reports');
    $routes->get('activity-logs', 'AdminController::activityLogs');
    
    // Admin SMS Logs
    $routes->get('sms-logs', 'AdminController::smsLogs');
    
    // User Management
    $routes->get('users', 'AdminController::users');
    $routes->get('users/add', 'AdminController::addUser');
    $routes->post('users/add', 'AdminController::addUser');
    $routes->get('users/edit/(:num)', 'AdminController::editUser/$1');
    $routes->post('users/edit/(:num)', 'AdminController::editUser/$1');
    $routes->get('users/delete/(:num)', 'AdminController::deleteUser/$1');
    
    // Email Reports
    $routes->post('send-daily-report', 'AdminController::sendDailySalesReport');
    
    // Inventory Management
    $routes->get('inventory', 'AdminController::inventory');
    $routes->post('inventory/update-stock', 'AdminController::updateStock');
    $routes->get('inventory/low-stock', 'AdminController::lowStockAlerts');
    $routes->get('inventory/report', 'AdminController::inventoryReport');
    
    // Menu Management
    $routes->get('menu', 'MenuController::index');
    $routes->get('menu/add', 'MenuController::add');
    $routes->post('menu/add', 'MenuController::add');
    $routes->get('menu/edit/(:num)', 'MenuController::edit/$1');
    $routes->post('menu/edit/(:num)', 'MenuController::edit/$1');
    $routes->get('menu/delete/(:num)', 'MenuController::delete/$1');
    $routes->post('menu/toggle-status/(:num)', 'MenuController::toggleStatus/$1');
});

