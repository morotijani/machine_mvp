<?php
/**
 * Security Helper Functions
 */

if (!function_exists('e')) {
    /**
     * Escape HTML entities in a string.
     */
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate or retrieve the current CSRF token from session.
     */
    function csrf_token() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('verify_csrf_token')) {
    /**
     * Verify the provided CSRF token against the session.
     */
    function verify_csrf_token($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}

/**
 * Middleware style check for CSRF on POST requests.
 */
function protect_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        
        // Handle JSON payloads
        if (empty($token)) {
            $json = json_decode(file_get_contents('php://input'), true);
            $token = $json['csrf_token'] ?? '';
        }
        
        // Handle Header (useful for Fetch/AJAX)
        if (empty($token)) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        }

        if (!verify_csrf_token($token)) {
            http_response_code(403);
            die("CSRF token validation failed. Request denied.");
        }
    }
}
