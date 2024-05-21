<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('LoginController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->setAutoRoute(true);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.


// Route Rest API
$routes->post('login-mobile', 'LoginController::signin');
$routes->post('register', 'UserController::create');
$routes->post('check-user', 'UserController::search');

$routes->get('get-layanan', 'LayananController::index');
$routes->post('create-layanan', 'LayananController::create');

$routes->get('get-jambooking', 'WaktuBookingController::index');
$routes->post('create-jambooking', 'WaktuBookingController::create');

$routes->get('get-jamselesai', 'WaktuSelesaiController::index');
$routes->post('create-jamselesai', 'WaktuSelesaiController::create');

$routes->get('get-designer', 'AdminController::index');

$routes->get('send-push-notification', 'AntrianController::send_push_notification');

$routes->get('get-antrian', 'AntrianController::index');
$routes->post('create-antrian', 'AntrianController::create');
$routes->post('check-antrian', 'AntrianController::check');
$routes->put('cancel-antrian/(:num)', 'AntrianController::update/$1');
$routes->post('reminder-antrian/(:num)', 'AntrianController::remindernotification/$1');
$routes->post('update-fcm', 'AntrianController::updatefcmtoken');
$routes->post('verifikasi-antrian/(:num)', 'AntrianController::verifikasi/$1');
$routes->get('get-antrian/(:num)', 'AntrianController::show/$1');
$routes->get('get-riwayat/(:num)', 'AntrianController::riwayat/$1');

$routes->post('push-notification', 'AntrianController::notification');

// Route Web Service
$routes->get('login', 'LoginController::index');
$routes->get('logout', 'LoginController::logout');

$routes->get('antrian', 'Home::antrian');
$routes->get('tentang', 'Home::tentang');

$routes->get('dashboard', 'AdminController::dashboard');
$routes->get('dashboard/edit/(:num)', 'AdminController::edit/$1');
$routes->post('dashboard/updateuser/(:num)', 'AdminController::updateuser/$1');
$routes->post('dashboard/updateadmin/(:num)', 'AdminController::updateadmin/$1');
$routes->get('dashboard/profil', 'AdminController::profil');
$routes->get('dashboard/logout', 'AdminController::logout');
$routes->get('dashboard/master-user', 'AdminController::muser');
$routes->get('dashboard/master-antrian', 'AdminController::mantrian');

$routes->resource('user', ['controller' => 'UserController']);
$routes->resource('admin', ['controller' => 'AdminController']);

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
