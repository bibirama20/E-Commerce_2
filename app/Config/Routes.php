<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================== AUTH ====================
$routes->get('/', 'AuthController::login');
$routes->post('/login', 'AuthController::loginProcess');
$routes->get('/logout', 'AuthController::logout');

// ==================== DASHBOARD ====================
$routes->get('/admin/dashboard', 'DashboardController::admin', ['filter' => 'role:admin']);
$routes->get('/user/dashboard', 'DashboardController::user', ['filter' => 'role:user']);

// ==================== PRODUK (User & Admin) ====================
$routes->get('/admin/produk', 'OrderController::produk', ['filter' => 'role:admin']);
$routes->get('/user/produk', 'OrderController::produk', ['filter' => 'role:user']);

// ==================== KERANJANG ====================
$routes->get('/admin/keranjang', 'OrderController::keranjang', ['filter' => 'role:admin']);
$routes->get('/user/keranjang', 'OrderController::keranjang', ['filter' => 'role:user']);

$routes->get('/admin/keranjang/tambah/(:num)', 'OrderController::tambah/$1', ['filter' => 'role:admin']);
$routes->get('/user/keranjang/tambah/(:num)', 'OrderController::tambah/$1', ['filter' => 'role:user']);

$routes->post('/admin/keranjang/update', 'OrderController::updateKeranjang', ['filter' => 'role:admin']);
$routes->post('/user/keranjang/update', 'OrderController::updateKeranjang', ['filter' => 'role:user']);

$routes->get('/admin/keranjang/hapus/(:num)', 'OrderController::hapus/$1', ['filter' => 'role:admin']);
$routes->get('/user/keranjang/hapus/(:num)', 'OrderController::hapus/$1', ['filter' => 'role:user']);

// ==================== CHECKOUT ====================
$routes->get('/admin/checkout', 'OrderController::checkout', ['filter' => 'role:admin']);
$routes->post('/admin/checkout/simpan', 'OrderController::checkoutSimpan', ['filter' => 'role:admin']);

$routes->get('/user/checkout', 'OrderController::checkout', ['filter' => 'role:user']);
$routes->post('/user/checkout/simpan', 'OrderController::checkoutSimpan', ['filter' => 'role:user']);

// ==================== BATAL CHECKOUT ====================
$routes->get('/admin/batalCheckout', 'OrderController::batalCheckout', ['filter' => 'role:admin']);
$routes->get('/user/batalCheckout', 'OrderController::batalCheckout', ['filter' => 'role:user']);

// ==================== INVOICE ====================
$routes->get('/admin/invoice/(:num)', 'OrderController::invoice/$1', ['filter' => 'role:admin']);
$routes->get('/user/invoice/(:num)', 'OrderController::invoice/$1', ['filter' => 'role:user']);

// ==================== KURIR & LOKASI RAJAONGKIR ====================
$routes->get('get-location', 'RajaOngkirController::getLocation', ['filter' => 'auth']);
$routes->get('get-cost', 'RajaOngkirController::getCost', ['filter' => 'auth']);        // AJAX ambil layanan + ongkir


// ==================== KELOLA PRODUK (admin) ====================
$routes->get('/admin/kelola-produk', 'ProductController::kelola', ['filter' => 'role:admin']);
$routes->get('/admin/kelola-produk/tambah', 'ProductController::create', ['filter' => 'role:admin']);
$routes->post('/admin/kelola-produk/simpan', 'ProductController::store', ['filter' => 'role:admin']);
$routes->get('/admin/kelola-produk/edit/(:num)', 'ProductController::edit/$1', ['filter' => 'role:admin']);
$routes->post('/admin/kelola-produk/update/(:num)', 'ProductController::update/$1', ['filter' => 'role:admin']);
$routes->get('/admin/kelola-produk/delete/(:num)', 'ProductController::delete/$1', ['filter' => 'role:admin']);

// ==================== CETAK PDF PRODUK (admin) ====================
$routes->get('/admin/produk/pdf', 'ProductController::pdf', ['filter' => 'role:admin']);

$routes->get('tes-api', 'OrderController::tesApiRajaongkir');

$routes->post('wa/send', 'WaController::send');

