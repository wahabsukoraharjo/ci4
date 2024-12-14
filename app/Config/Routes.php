<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/home', 'Home::index');
$routes->get('/profile', 'Profile::index');
$routes->get('/about', 'About::index');

$routes->get('/products', 'Products::index');
$routes->get('/products/create', 'Products::create');
$routes->post('/products/store', 'Products::store');
$routes->get('/products/edit/(:num)', 'Products::edit/$1');
$routes->post('/products/update/(:num)', 'Products::update/$1');
$routes->get('/products/delete/(:num)', 'Products::delete/$1');
$routes->post('/products/getProducts', 'Products::getProducts');
$routes->get('/export/excel', 'Export::exportExcel');
$routes->get('/export/pdf', 'Export::exportPDF');

$routes->get('/', 'Logins::index');
$routes->post('/login/auth', 'Logins::auth');
$routes->get('/logout', 'Logins::logout');

$routes->get('/register', 'Register::index');
$routes->post('/register/create', 'Register::create');
$routes->post('/users/new', 'UserApi::create');

$routes->group('productsapi', function ($routes) {
    $routes->get('/', 'ProductApi::index');       // Menampilkan semua data produk
    $routes->post('/', 'ProductApi::create');     // Menambah data produk
    $routes->delete('/(:num)', 'ProductApi::delete/$1'); // Menghapus data produk berdasarkan ID
});


$routes->group('users', function ($routes) {
    $routes->get('/', 'UserApi::index');       // Menampilkan semua data user
    // $routes->post('/', 'UserApi::create');     // Menambah data user
    $routes->delete('/(:num)', 'UserApi::delete/$1'); // Menghapus data user berdasarkan ID
});
