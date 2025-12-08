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
        
        // Remove base path if needed (e.g. /machine_mvp/public)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        $path = str_replace($scriptName, '', $path);
        
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
