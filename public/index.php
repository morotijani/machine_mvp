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
$baseUrl = ($scriptName === '/' || $scriptName === '\\') ? '' : $scriptName;
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
// Change this to use ReportController instead of closure
$router->get('/dashboard', [new ReportController(), 'dashboard']);

// Items
$itemController = new ItemController();
$router->get('/items', [$itemController, 'index']);
$router->get('/items/create', [$itemController, 'create']);
$router->post('/items/create', [$itemController, 'create']);
$router->get('/items/edit', [$itemController, 'edit']);
$router->post('/items/edit', [$itemController, 'edit']);

// Customers
$customerController = new CustomerController();
$router->get('/customers', [$customerController, 'index']);
$router->post('/customers/create', [$customerController, 'create']);
$router->get('/api/customers/search', [$customerController, 'apiSearch']);

// Sales
$saleController = new SaleController();
$router->get('/sales', [$saleController, 'index']);
$router->get('/sales/create', [$saleController, 'create']);
$router->post('/sales/create', [$saleController, 'create']);
$router->get('/sales/view', [$saleController, 'view']);
$router->post('/sales/pay', [$saleController, 'pay']);

// Dispatch
$router->dispatch();
