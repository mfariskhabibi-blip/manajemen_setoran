<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Authentication Routes
$routes->get('login', 'AuthController::login');
$routes->post('auth/process-login', 'AuthController::processLogin');
$routes->get('register', 'AuthController::register');
$routes->post('auth/process-register', 'AuthController::processRegister');
$routes->get('logout', 'AuthController::logout');
$routes->get('forgot-password', 'AuthController::forgotPassword');
$routes->post('auth/process-forgot-password', 'AuthController::processForgotPassword');
$routes->get('reset-password/(:any)', 'AuthController::resetPassword/$1');
$routes->post('auth/process-reset-password/(:any)', 'AuthController::processResetPassword/$1');

// Dashboard Routes
$routes->get('dashboard', 'DashboardController::index');
$routes->get('admin/dashboard', 'DashboardController::adminDashboard');
$routes->get('api/dashboard/stats', 'DashboardController::getStats');
$routes->get('api/dashboard/chart-data', 'DashboardController::getChartData');

// Setoran Routes (User)
$routes->get('setoran', 'SetoranController::index');
$routes->get('setoran/(:num)', 'SetoranController::show/$1');
$routes->get('api/setoran/chart-data', 'SetoranController::getChartData');

// Riwayat Routes
$routes->get('riwayat', 'RiwayatController::index');
$routes->get('riwayat/(:num)', 'RiwayatController::show/$1');
$routes->get('riwayat/export', 'RiwayatController::export');
$routes->get('riwayat/print/(:num)', 'RiwayatController::printReceipt/$1');
$routes->get('api/riwayat/chart-data', 'RiwayatController::getChartData');

// Setoran Admin Routes (Protected)
$routes->group('admin', ['filter' => 'role:admin'], function($routes) {
    // Setoran Management
    $routes->get('setoran', 'SetoranController::index');
    $routes->get('setoran/create', 'SetoranController::create');
    $routes->post('setoran/store', 'SetoranController::store');
    $routes->get('setoran/(:num)/edit', 'SetoranController::edit/$1');
    $routes->post('setoran/(:num)/update', 'SetoranController::update/$1');
    $routes->post('setoran/(:num)/delete', 'SetoranController::delete/$1');
    $routes->post('setoran/(:num)/verify', 'SetoranController::verify/$1');
    $routes->get('setoran/export', 'SetoranController::export');
    $routes->post('api/setoran/generate-report', 'SetoranController::generateReport');
    
    // Reports
    $routes->get('reports', 'SetoranController::export');
    $routes->get('reports/monthly', 'DashboardController::getChartData');
});

// Default route (redirect to login)
$routes->get('/', function() {
    return redirect()->to('login');
});
