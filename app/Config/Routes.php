<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================== AUTH ====================
$routes->get('/', 'AuthController::login');                              // Halaman login
$routes->post('/login', 'AuthController::loginProcess');                 // Proses login
$routes->get('/logout', 'AuthController::logout');                       // Logout

// ==================== DASHBOARD ====================
$routes->get('/admin/dashboard', 'DashboardController::admin', ['filter' => 'role:admin']); // Dashboard admin
$routes->get('/user/dashboard', 'DashboardController::user', ['filter' => 'role:user']);    // Dashboard user

// ==================== STATISTIK PENJUALAN (Admin) ====================
$routes->get('/admin/statistik', 'DashboardController::statistik', ['filter' => 'role:admin']); // Statistik penjualan

// ==================== PRODUK (User & Admin Bisa Melihat) ====================
$routes->get('/admin/produk', 'OrderController::produk', ['filter' => 'role:admin']); // Daftar produk (admin)
$routes->get('/user/produk', 'OrderController::produk', ['filter' => 'role:user']);   // Daftar produk (user)

// ==================== KERANJANG ====================
$routes->get('/admin/keranjang', 'OrderController::keranjang', ['filter' => 'role:admin']); // Halaman keranjang admin
$routes->get('/user/keranjang', 'OrderController::keranjang', ['filter' => 'role:user']);   // Halaman keranjang user

$routes->get('/admin/keranjang/tambah/(:num)', 'OrderController::tambah/$1', ['filter' => 'role:admin']); // Tambah ke keranjang
$routes->get('/user/keranjang/tambah/(:num)', 'OrderController::tambah/$1', ['filter' => 'role:user']);

$routes->post('/admin/keranjang/update', 'OrderController::updateKeranjang', ['filter' => 'role:admin']); // Update jumlah
$routes->post('/user/keranjang/update', 'OrderController::updateKeranjang', ['filter' => 'role:user']);

$routes->get('/admin/keranjang/hapus/(:num)', 'OrderController::hapus/$1', ['filter' => 'role:admin']); // Hapus item dari keranjang
$routes->get('/user/keranjang/hapus/(:num)', 'OrderController::hapus/$1', ['filter' => 'role:user']);

// ==================== CHECKOUT ====================
$routes->get('/admin/checkout', 'OrderController::checkout', ['filter' => 'role:admin']);     // Halaman checkout admin
$routes->post('/admin/checkout/simpan', 'OrderController::checkoutSimpan', ['filter' => 'role:admin']); // Simpan pesanan admin

$routes->get('/user/checkout', 'OrderController::checkout', ['filter' => 'role:user']);       // Halaman checkout user
$routes->post('/user/checkout/simpan', 'OrderController::checkoutSimpan', ['filter' => 'role:user']);   // Simpan pesanan user

// ==================== BATAL CHECKOUT ====================
$routes->get('/admin/batalCheckout', 'OrderController::batalCheckout', ['filter' => 'role:admin']); // Batalkan proses checkout
$routes->get('/user/batalCheckout', 'OrderController::batalCheckout', ['filter' => 'role:user']);

// ==================== INVOICE ====================
$routes->get('/admin/invoice/(:num)', 'OrderController::invoice/$1', ['filter' => 'role:admin']); // Invoice admin
$routes->get('/user/invoice/(:num)', 'OrderController::invoice/$1', ['filter' => 'role:user']);   // Invoice user

// ==================== KURIR & LOKASI RAJAONGKIR ====================
$routes->get('/get-location', 'RajaOngkirController::getLocation', ['filter' => 'auth']); // API get lokasi
$routes->get('/get-cost', 'RajaOngkirController::getCost', ['filter' => 'auth']);         // API get ongkir

// ==================== KELOLA PRODUK (Admin) ====================
$routes->get('/admin/kelola-produk', 'ProductController::kelola', ['filter' => 'role:admin']); // Halaman kelola produk
$routes->get('/admin/kelola-produk/tambah', 'ProductController::create', ['filter' => 'role:admin']); // Tambah produk
$routes->post('/admin/kelola-produk/simpan', 'ProductController::store', ['filter' => 'role:admin']); // Simpan produk
$routes->get('/admin/kelola-produk/edit/(:num)', 'ProductController::edit/$1', ['filter' => 'role:admin']); // Edit produk
$routes->post('/admin/kelola-produk/update/(:num)', 'ProductController::update/$1', ['filter' => 'role:admin']); // Update produk
$routes->get('/admin/kelola-produk/delete/(:num)', 'ProductController::delete/$1', ['filter' => 'role:admin']); // Hapus produk

// ==================== CETAK PDF PRODUK ====================
$routes->get('/admin/produk/pdf', 'ProductController::pdf', ['filter' => 'role:admin']); // Cetak daftar produk PDF

// ==================== TES API RAJAONGKIR ====================
$routes->get('/tes-api', 'OrderController::tesApiRajaongkir'); // Tes endpoint API Rajaongkir

// ==================== ORDER & DETAIL PESANAN (Admin) ====================
$routes->get('admin/order', 'OrderController::adminIndex');                 // Halaman utama order admin
$routes->get('admin/order/detail/(:num)', 'OrderController::detail/$1');    // Detail pesanan admin
$routes->get('admin/order/resi/(:num)', 'OrderController::resi/$1');        // Cetak resi pesanan
$routes->get('admin/orders', 'OrderController::index');                     // Daftar semua pesanan
$routes->get('admin/orders/printAll', 'OrderController::printAll');         // Cetak semua pesanan (PDF)
$routes->get('/admin/order/status/ubah/(:num)', 'OrderController::ubahStatus/$1', ['filter' => 'role:admin']); // Ubah status pesanan
$routes->get('admin/dashboard', 'OrderController::dashboard');              // Dashboard admin (duplikat?)


// ==================== USER PESANAN ====================
$routes->get('/user/pesanan', 'OrderController::pesanan', ['filter' => 'role:user']); // Daftar pesanan user
$routes->get('/user/invoice/(:num)', 'OrderController::invoice/$1', ['filter' => 'role:user']); // Invoice user
$routes->post('user/pesanan/selesaikan/(:num)', 'OrderController::selesaikanPesanan/$1', ['filter' => 'role:user']); // Selesaikan pesanan
$routes->get('user/orders/detail/(:num)', 'OrderController::invoice/$1', ['filter' => 'role:user']); // Detail invoice user
$routes->get('user/orders/invoicepdf/(:num)', 'OrderController::invoicePdf/$1', ['filter' => 'role:user']); // Cetak invoice PDF

$routes->get('/user/pesanan/bayar/(:num)', 'OrderController::bayar/$1'); // Halaman bayar pesanan
$routes->get('/user/pesanan/gantiMetode/(:num)', 'OrderController::gantiMetode/$1'); // Ganti metode pembayaran
$routes->post('/user/pesanan/updateMetode/(:num)', 'OrderController::updateMetode/$1'); // Simpan metode pembayaran

$routes->post('user/checkout/pilih', 'OrderController::pilih');         // Pilih item untuk checkout
$routes->get('user/checkout/batalkan', 'OrderController::batalkanCheckout'); // Batalkan checkout

$routes->get('/admin/users', 'DashboardController::daftarUser');
$routes->get('/admin/user/edit/(:num)', 'DashboardController::editUser/$1');
$routes->post('/admin/user/update/(:num)', 'DashboardController::updateUser/$1');
$routes->post('/admin/user/delete/(:num)', 'DashboardController::deleteUser/$1');

$routes->get('/admin/users/admin', 'DashboardController::listAdmin');
$routes->get('/admin/users/user', 'DashboardController::listUser');
$routes->get('/admin/user/edit/(:num)', 'DashboardController::editUser/$1');
$routes->post('/admin/user/update/(:num)', 'DashboardController::updateUser/$1');

$routes->get('/admin/user/create', 'DashboardController::create');
$routes->post('/admin/user/store', 'DashboardController::store');
$routes->get('admin/user/delete/(:num)', 'DashboardController::delete/$1');


// FORM REGISTER (GET)
$routes->get('register', 'AuthController::register');

// SIMPAN USER (POST)
$routes->post('register', 'AuthController::registerProcess');


$routes->get('register', 'AuthController::registerForm'); // Tampilkan form register
$routes->post('register', 'AuthController::register');    // Proses simpan user baru


$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::registerProcess');

$routes->get('/forgot-password', 'AuthController::forgotPassword');
$routes->post('/forgot-password', 'AuthController::forgotPasswordSubmit');

// Forgot & Reset Password
$routes->get('forgot-password', 'AuthController::forgotPassword');
$routes->post('forgot-password', 'AuthController::forgotPasswordSubmit');

$routes->get('reset-password/(:segment)', 'AuthController::resetPassword/$1');
$routes->post('reset-password/(:segment)', 'AuthController::resetPasswordSubmit');

$routes->get('/login', 'AuthController::login'); // Tambahkan ini
