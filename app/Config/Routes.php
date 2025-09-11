<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Dashboard routes
$routes->get('dashboard', 'Dashboard::index');
$routes->get('admin', 'Dashboard::admin');
$routes->get('test', 'Test::index');
$routes->post('test/login', 'Test::login');
$routes->get('cashier', 'Dashboard::cashier');

