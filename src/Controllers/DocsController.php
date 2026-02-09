<?php
namespace App\Controllers;

use App\Middleware\AuthMiddleware;

class DocsController {
    public function index() {
        AuthMiddleware::requireLogin();
        require __DIR__ . '/../../views/docs/index.php';
    }
}
