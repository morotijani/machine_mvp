<?php
namespace App\Middleware;

class AuthMiddleware {
    public static function isAuthenticated() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin() {
        if (!self::isAuthenticated()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public static function requireAdmin() {
        self::requireLogin();
        if ($_SESSION['role'] !== 'admin') {
            http_response_code(403);
            echo "Access Denied: Admin only.";
            exit;
        }
    }
}
