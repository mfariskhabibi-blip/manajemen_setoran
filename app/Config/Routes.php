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

// Chat Routes
$routes->get('chat', 'ChatController::index');
$routes->get('chat/create-default-group', 'ChatController::createDefaultGroup');
$routes->get('api/chat/contacts', 'ChatController::getContacts');
$routes->post('api/chat/ping', 'ChatController::ping');
$routes->get('api/chat/messages/(:num)/(:any)', 'ChatController::getMessages/$1/$2');
$routes->get('api/chat/messages/(:num)', 'ChatController::getMessages/$1');
$routes->post('api/chat/send', 'ChatController::sendMessage');
$routes->get('api/chat/unread-count', 'ChatController::getUnreadCount');

// Profile Routes
$routes->get('profile', 'ProfileController::index');
$routes->get('profile/edit', 'ProfileController::edit');
$routes->post('profile/update', 'ProfileController::update');
$routes->get('profile/change-password', 'ProfileController::changePassword');
$routes->post('profile/update-password', 'ProfileController::updatePassword');

// Setoran Admin Routes (Protected)
$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    // Program Management
    $routes->get('program', 'ProgramController::index');
    $routes->get('program/create', 'ProgramController::create');
    $routes->post('program/store', 'ProgramController::store');
    $routes->get('program/(:num)', 'ProgramController::show/$1');
    $routes->get('program/(:num)/edit', 'ProgramController::edit/$1');
    $routes->post('program/(:num)/update', 'ProgramController::update/$1');
    $routes->post('program/(:num)/delete', 'ProgramController::delete/$1');
    $routes->get('program/(:num)/add-participant', 'ProgramController::addParticipant/$1');
    $routes->post('program/store-participant', 'ProgramController::storeParticipant');
    $routes->post('program/(:num)/remove-participant/(:num)', 'ProgramController::removeParticipant/$1/$2');
    // Periode Management
    $routes->get('program/(:num)/add-periode', 'ProgramController::addPeriode/$1');
    $routes->post('program/store-periode', 'ProgramController::storePeriode');
    $routes->post('program/(:num)/update-periode-status/(:num)', 'ProgramController::updatePeriodeStatus/$1/$2');
    $routes->post('program/(:num)/delete-periode/(:num)', 'ProgramController::deletePeriode/$1/$2');

    // Setoran Management
    $routes->get('setoran', 'SetoranController::index');
    $routes->get('setoran/(:num)', 'SetoranController::show/$1');
    $routes->get('setoran/belum-dicatat', 'SetoranController::belumDicatat');
    $routes->get('setoran/create', 'SetoranController::create');
    $routes->post('setoran/store', 'SetoranController::store');
    $routes->get('setoran/(:num)/edit', 'SetoranController::edit/$1');
    $routes->post('setoran/(:num)/update', 'SetoranController::update/$1');
    $routes->post('setoran/(:num)/delete', 'SetoranController::delete/$1');
    $routes->get('setoran/(:num)/delete', 'SetoranController::delete/$1');
    $routes->post('setoran/(:num)/verify', 'SetoranController::verify/$1');
    $routes->get('setoran/export', 'SetoranController::export');
    $routes->get('setoran/get-warga-balance/(:num)', 'SetoranController::getWargaBalance/$1');
    $routes->post('api/setoran/generate-report', 'SetoranController::generateReport');

    // Data Pengguna (User Management)
    $routes->get('users', 'UserController::index');
    $routes->get('users/create', 'UserController::create');
    $routes->post('users/store', 'UserController::store');
    $routes->get('users/(:num)/edit', 'UserController::edit/$1');
    $routes->post('users/(:num)/update', 'UserController::update/$1');
    $routes->post('users/(:num)/delete', 'UserController::delete/$1');
    $routes->post('users/(:num)/toggle-status', 'UserController::toggleStatus/$1');

    // Rekap Setoran
    $routes->get('rekap', 'RekapController::index');
    $routes->get('rekap/export', 'RekapController::export');
    $routes->get('rekap/print', 'RekapController::printRekap');

    // Log Aktivitas
    $routes->get('activity-log', 'ActivityLogController::index');

    // Pengaturan Acara (Event Settings)
    $routes->get('acara', 'AcaraController::index');
    $routes->post('acara/update-info', 'AcaraController::updateInfo');
    $routes->post('acara/update-skema', 'AcaraController::updateSkema');
    $routes->post('acara/toggle-wajib', 'AcaraController::toggleWajib');
    $routes->post('acara/update-warga-tarif', 'AcaraController::updateWargaTarif');
    $routes->get('acara/sync-warga/(:num)', 'AcaraController::syncWarga/$1');
    $routes->get('acara/get-regular-count/(:num)', 'AcaraController::getRegularCount/$1');

    // Pengaturan System & Admin
    $routes->get('settings', 'SettingsController::index');
    $routes->post('settings/update', 'SettingsController::update');

    // Riwayat / Laporan Setoran (Admin - alias untuk halaman setoran)
    $routes->get('riwayat', 'SetoranController::index');
    $routes->get('riwayat/(:num)', 'SetoranController::show/$1');

    // Kelola Pengeluaran Kas
    $routes->get('pengeluaran', 'PengeluaranController::index');
    $routes->get('pengeluaran/create', 'PengeluaranController::create');
    $routes->post('pengeluaran/store', 'PengeluaranController::store');
    $routes->get('pengeluaran/(:num)/edit', 'PengeluaranController::edit/$1');
    $routes->post('pengeluaran/(:num)/update', 'PengeluaranController::update/$1');
    $routes->post('pengeluaran/(:num)/delete', 'PengeluaranController::delete/$1');
    $routes->get('pengeluaran/(:num)/delete', 'PengeluaranController::delete/$1');

    // Reports
    $routes->get('reports', 'SetoranController::export');
    $routes->get('reports/monthly', 'DashboardController::getChartData');
});

// Transparansi Routes
$routes->get('transparansi', 'TransparansiController::index');

// Pengeluaran Kas Routes (User View & Download)
$routes->get('pengeluaran', 'PengeluaranController::index');
$routes->get('pengeluaran/download/(:num)', 'PengeluaranController::download/$1');

// User Program Routes
$routes->get('program', 'ProgramController::index');
$routes->get('program/show/(:num)', 'ProgramController::show/$1');
$routes->get('program/(:num)', 'ProgramController::show/$1');

// Default route (redirect to login)
$routes->get('/', function () {
    return redirect()->to('login');
});
