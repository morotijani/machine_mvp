<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/Router.php';

use App\Controllers\AuthController;
use App\Controllers\ItemController;
use App\Controllers\CustomerController;
use App\Controllers\SaleController;
use App\Controllers\ReportController;
use App\Middleware\AuthMiddleware;
use App\Config\Database;

// Start session
session_start();

// Initialize Router
$router = new Router();

// Define Base URL for subfolder support
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$scriptName = str_replace('\\', '/', $scriptName); // Normalize slashes for Windows
$baseUrl = ($scriptName === '/') ? '' : $scriptName;
define('BASE_URL', $baseUrl);

// Routes
$router->get('/', function() {
    if (AuthMiddleware::isAuthenticated()) {
        header('Location: ' . BASE_URL . '/sales/create');
    } else {
        header('Location: ' . BASE_URL . '/login');
    }
    exit;
});

// Auth
$router->get('/login', [new AuthController(), 'login']);
$router->post('/login', [new AuthController(), 'login']);
$router->get('/logout', [new AuthController(), 'logout']);

// Dashboard
$router->get('/dashboard', [new ReportController(), 'dashboard']);

// Reports
$router->get('/reports', [new ReportController(), 'index']);

// Users
$userController = new \App\Controllers\UserController();
$router->get('/users', [$userController, 'index']);
$router->get('/users/create', [$userController, 'create']);
$router->post('/users/create', [$userController, 'create']);
$router->post('/users/toggle-status', [$userController, 'toggleStatus']);
$router->post('/users/delete', [$userController, 'delete']);

// Profile
$profileController = new \App\Controllers\ProfileController();
$router->get('/profile', [$profileController, 'index']);
$router->post('/profile/update', [$profileController, 'update']);

// Company Settings (Admin)
$settingController = new \App\Controllers\SettingController();
$router->get('/settings', [$settingController, 'index']);
$router->post('/settings/update', [$settingController, 'update']);

// Items
$itemController = new ItemController();
$router->get('/items', [$itemController, 'index']);
$router->get('/items/create', [$itemController, 'create']);
$router->post('/items/create', [$itemController, 'create']);
$router->get('/items/edit', [$itemController, 'edit']);
$router->post('/items/edit', [$itemController, 'edit']);
$router->get('/items/create-bundle', [$itemController, 'createBundle']);
$router->post('/items/create-bundle', [$itemController, 'createBundle']);
$router->post('/items/ungroup-bundle', [$itemController, 'ungroupBundle']);
$router->post('/items/delete', [$itemController, 'delete']);

// Customers
$customerController = new CustomerController();
$router->get('/customers', [$customerController, 'index']);
$router->post('/customers/create', [$customerController, 'create']);
$router->get('/customers/edit', [$customerController, 'edit']);
$router->post('/customers/edit', [$customerController, 'edit']);
$router->get('/api/customers/search', [$customerController, 'apiSearch']);

// Sales
$saleController = new SaleController();
$router->get('/sales', [$saleController, 'index']);
$router->get('/sales/create', [$saleController, 'create']);
$router->post('/sales/create', [$saleController, 'create']);
$router->post('/sales/request-delete', [$saleController, 'requestDelete']);
$router->post('/sales/process-delete', [$saleController, 'processDeleteRequest']);
$router->get('/sales/view', [$saleController, 'view']);
$router->post('/sales/pay', [$saleController, 'pay']);

// Dispatch
$router->dispatch();
