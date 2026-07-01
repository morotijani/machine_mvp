<?php
// Simple Router class
class Router {
    private $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path using the globally defined BASE_URL
        if (defined('BASE_URL') && BASE_URL !== '') {
            if (strpos($path, BASE_URL) === 0) {
                $path = substr($path, strlen(BASE_URL));
            }
        }
        
        if ($path === '' || $path === '/') {
            $path = '/';
        }

        if (isset($this->routes[$method][$path])) {
            call_user_func($this->routes[$method][$path]);
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
    }
}
