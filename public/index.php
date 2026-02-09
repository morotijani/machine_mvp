<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/Router.php';
require_once __DIR__ . '/../src/Config/Security.php';

use App\Controllers\AuthController;
use App\Controllers\ItemController;
use App\Controllers\CustomerController;
use App\Controllers\SaleController;
use App\Controllers\ReportController;
use App\Controllers\ExpenditureController;
use App\Controllers\FinanceController;
use App\Middleware\AuthMiddleware;
use App\Config\Database;

// Start session
session_start();

// Initialize Router
$router = new Router();

// Define Base URL for subfolder support
$scriptName = $_SERVER['SCRIPT_NAME']; // e.g. /machine_mvp/public/index.php
$baseUrl = str_replace('\\', '/', dirname($scriptName));
if ($baseUrl === '/' || $baseUrl === '\\' || $baseUrl === '.') {
    $baseUrl = '';
}
$baseUrl = rtrim($baseUrl, '/');
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

// Documentation
$router->get('/docs', [new \App\Controllers\DocsController(), 'index']);

// Reports
$router->get('/reports', [new ReportController(), 'index']);
$router->get('/reports/export', [new ReportController(), 'export']);

// Staff Performance
$staffController = new \App\Controllers\StaffController();
$router->get('/admin/staff', [$staffController, 'index']);
$router->get('/admin/staff/detail', [$staffController, 'detail']);

// Users
$userController = new \App\Controllers\UserController();
$router->get('/users', [$userController, 'index']);
$router->get('/users/create', [$userController, 'create']);
$router->post('/users/create', [$userController, 'create']);
$router->get('/users/edit', [$userController, 'edit']);
$router->post('/users/update', [$userController, 'update']);
$router->post('/users/toggle-status', [$userController, 'toggleStatus']);
$router->post('/users/update-role', [$userController, 'updateRole']);
$router->post('/users/delete', [$userController, 'delete']);

// Profile
$profileController = new \App\Controllers\ProfileController();
$router->get('/profile', [$profileController, 'index']);
$router->post('/profile/update', [$profileController, 'update']);

// Company Settings (Admin)
$settingController = new \App\Controllers\SettingController();
$router->get('/settings', [$settingController, 'index']);
$router->post('/settings/update', [$settingController, 'update']);

// Expenditures (previously incorrectly labeled expenses in routes)
$expenditureController = new ExpenditureController();
$router->get('/expenses', [$expenditureController, 'index']);
$router->post('/expenses/create', [$expenditureController, 'create']);
$router->post('/expenses/delete', [$expenditureController, 'delete']);

// Finance
$financeController = new FinanceController();
$router->get('/admin/finance', [$financeController, 'index']);
$router->post('/admin/finance/withdraw', [$financeController, 'withdraw']);
$router->post('/admin/finance/update', [$financeController, 'update']);
$router->post('/admin/finance/delete', [$financeController, 'delete']);

// Items
$itemController = new ItemController();
$router->get('/items', [$itemController, 'index']);
$router->get('/items/create', [$itemController, 'create']);
$router->post('/items/create', [$itemController, 'create']);
$router->get('/items/edit', [$itemController, 'edit']);
$router->post('/items/edit', [$itemController, 'edit']);
$router->get('/items/preview', [$itemController, 'preview']);
$router->get('/api/items/find-by-sku', [$itemController, 'apiFindItemBySku']);
$router->get('/items/create-bundle', [$itemController, 'createBundle']);
$router->post('/items/create-bundle', [$itemController, 'createBundle']);
$router->post('/items/ungroup-bundle', [$itemController, 'ungroupBundle']);
$router->post('/items/delete', [$itemController, 'delete']);

// Admin Recycle Bin
$adminController = new \App\Controllers\AdminController();
$router->get('/admin/trash', [$adminController, 'trash']);
$router->post('/admin/restore', [$adminController, 'restore']);
$router->post('/admin/delete-forever', [$adminController, 'deleteForever']);

// Customers
$customerController = new CustomerController();
$router->get('/customers', [$customerController, 'index']);
$router->post('/customers/create', [$customerController, 'create']);
$router->post('/customers/edit', [$customerController, 'edit']);
$router->get('/customers/view', [$customerController, 'view']);
$router->get('/customers/api-search', [$customerController, 'apiSearch']);
$router->post('/customers/delete', [$customerController, 'delete']);
$router->post('/customers/restore', [$customerController, 'restore']);

// Sales
$saleController = new SaleController();
$router->get('/sales', [$saleController, 'index']);
$router->get('/sales/create', [$saleController, 'create']);
$router->post('/sales/create', [$saleController, 'create']);
$router->post('/sales/request-delete', [$saleController, 'requestDelete']);
$router->post('/sales/process-delete', [$saleController, 'processDeleteRequest']);
$router->get('/sales/view', [$saleController, 'view']);
$router->post('/sales/pay', [$saleController, 'pay']);
$router->post('/sales/return', [$saleController, 'returns']);

// Expenditures
$expenditureController = new \App\Controllers\ExpenditureController();
$router->get('/expenditures', [$expenditureController, 'index']);
$router->get('/expenditures/create', [$expenditureController, 'create']);
$router->post('/expenditures/create', [$expenditureController, 'create']);
$router->get('/expenditures/edit', [$expenditureController, 'edit']);
$router->post('/expenditures/edit', [$expenditureController, 'edit']);
$router->post('/expenditures/delete', [$expenditureController, 'delete']);

// Debtors
$debtorController = new \App\Controllers\DebtorController();
$router->get('/debtors', [$debtorController, 'index']);
$router->get('/debtors/create', [$debtorController, 'create']);
$router->post('/debtors/create', [$debtorController, 'create']);
$router->get('/debtors/payment', [$debtorController, 'recordPayment']);
$router->post('/debtors/payment', [$debtorController, 'recordPayment']);
$router->get('/debtors/history', [$debtorController, 'history']);
$router->get('/debtors/increase', [$debtorController, 'increaseDebt']);
$router->post('/debtors/increase', [$debtorController, 'increaseDebt']);
$router->post('/debtors/delete', [$debtorController, 'delete']);

// API Status
$router->get('/api/status', [new \App\Controllers\StatusController(), 'check']);

// Dispatch
$router->dispatch();
