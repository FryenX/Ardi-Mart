<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Main::index');
$routes->post('main/fetchSalesData', 'Main::fetchSalesData');
$routes->get('main/fetchSaleYears', 'Main::fetchSaleYears');
$routes->post('main/fetchProductsData', 'Main::fetchProductsData');
$routes->get('/restricted', 'restricted::index');

// Login 
$routes->get('login', 'Login::index');
$routes->post('login/auth', 'Login::auth');
$routes->post('login/logout', 'Login::logout');
$routes->get('login/rememberMe', 'Login::rememberMe');
$routes->get('login/credential', 'Login::credential');
$routes->get('login/forget', 'Login::emailAuth');
$routes->get('login/change/(:segment)', 'Login::changePassword/$1', ['filter' => 'UuidFilter']);
$routes->get('login/confirmEmail', 'Login::confirmEmail');
$routes->post('login/verifyCredential', 'Login::verifyCredential');
$routes->post('login/changePassword', 'Login::changePassword');
$routes->post('login/updatePassword', 'Login::updatePassword');
$routes->post('login/rememberMe', 'Login::rememberMe');
$routes->post('login/verifyEmail', 'Login::verifyEmail');
$routes->post('login/newPassword', 'Login::newPassword');
$routes->get('login/resetPassword/(:segment)', 'Login::resetPassword/$1', ['filter' => 'TokenFilter']);
$routes->get('login/isLoggedIn', 'Login::isLoggedIn');

// Users
$routes->get('users', 'users::index');
$routes->get('users/add', 'users::add');
$routes->post('users/saveData', 'users::saveData');
$routes->post('users/index', 'users::index');
$routes->post('users/edit', 'users::edit');
$routes->get('users/edit/(:segment)', 'users::edit/$1', ['filter' => 'UuidFilter']);
$routes->get('users/changePassword/(:segment)', 'users::changePassword/$1', ['filter' => 'UuidFilter']);
$routes->post('users/update', 'users::updateData');
$routes->post('users/updatePassword', 'users::updatePassword');
$routes->post('users/delete', 'users::delete');
$routes->get('users/index', 'users::index');
$routes->get('users/fetchDataLevels', 'Users::fetchDataLevels');
$routes->get('profile/(:segment)', 'Users::profile/$1', ['filter' => 'UuidFilter']);
$routes->get('profile/edit/(:segment)', 'Users::editProfile/$1', ['filter' => 'UuidFilter']);
$routes->post('users/updateProfile', 'Users::saveUserData');

// Categories
$routes->get('categories', 'Categories::index');
$routes->match(['get', 'post'], 'categories', 'Categories::index');
$routes->get('/categories/add', 'Categories::add');
$routes->post('/categories/add', 'Categories::add');
$routes->post('/categories/saveData', 'Categories::saveData');
$routes->post('/categories/delete', 'Categories::delete');
$routes->post('/categories/edit', 'Categories::edit');
$routes->post('/categories/update', 'Categories::update');

// Units
$routes->get('units', 'Units::index');
$routes->post('units/showUnitData', 'Units::showUnitData');
$routes->post('/units/delete', 'Units::delete');
$routes->match(['get', 'post'], 'Units', 'Units::index');
$routes->post('/units/edit', 'Units::edit');
$routes->post('/units/update', 'Units::update');
$routes->post('/units/add', 'Units::add');
$routes->post('/units/saveData', 'Units::saveData');

// Products
$routes->get('products', 'Products::index');
$routes->get('products/index', 'Products::index');
$routes->post('products/index', 'Products::index');
$routes->get('products/add', 'Products::add');
$routes->get('products/fetchDataUnits', 'Products::fetchDataUnits');
$routes->get('products/fetchDataCategories', 'Products::fetchDataCategories');
$routes->post('products/saveData', 'Products::saveData');
$routes->post('products/delete', 'Products::delete');
$routes->get('products/edit/(:any)', 'Products::edit/$1');
$routes->post('products/update', 'Products::updateData');
$routes->post('products/showProductsData', 'Products::showProductsData');
$routes->get('products/showProductsData', 'Products::showProductsData');

// Levels
$routes->get('levels', 'levels::index');
$routes->get('levels/add', 'levels::add');
$routes->post('levels/add', 'levels::add');
$routes->post('levels/saveData', 'levels::saveData');
$routes->post('levels/delete', 'levels::delete');
$routes->post('levels/edit', 'levels::edit');
$routes->post('levels/update', 'levels::update');

// Transactions
$routes->get('transactions', 'Transactions::index');
$routes->get('transactions/input', 'Transactions::input');
$routes->post('transactions/createInvoice', 'Transactions::createInvoice');
$routes->post('transactions/dataDetail', 'Transactions::dataDetail');
$routes->get('transactions/viewProductData', 'Transactions::viewProductData');
$routes->post('transactions/productDataList', 'Transactions::productDataList');
$routes->post('transactions/checkCode', 'Transactions::checkCode');
$routes->post('transactions/saveTemp', 'Transactions::saveTemp');
$routes->post('transactions/viewProductData', 'Transactions::viewProductData');
$routes->post('transactions/sumTotal', 'Transactions::sumTotal');
$routes->post('transactions/deleteItem', 'Transactions::deleteItem');
$routes->post('transactions/cancel', 'Transactions::cancelTransaction');
$routes->post('transactions/payment', 'Transactions::payment');
$routes->post('transactions/paymentTransfer', 'Transactions::paymentTransfer');
$routes->post('transactions/saveData', 'Transactions::saveData');
$routes->post('transactions/saveTransfer', 'Transactions::saveTransfer');
$routes->get('transactions/printInvoice', 'Transactions::printInvoice');
$routes->post('transactions/printInvoice', 'Transactions::printInvoice');
$routes->get('transactions/data', 'Transactions::data');
$routes->post('transactions/delete', 'Transactions::delete');
$routes->post('transactions/showTransactionsData', 'Transactions::showTransactionsData');
$routes->get('transactions/showTransactionsData', 'Transactions::showTransactionsData');
$routes->get('transactions/exportToCSV', 'Transactions::exportToCSV');
$routes->post('transactions/exportToCSV', 'Transactions::exportToCSV');

$routes->set404Override();

// $routes->get('pay', 'Pay::index');