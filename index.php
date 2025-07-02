<?php 
session_start();

require_once 'config/helpers.php';
require_once 'route/web.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = strtok($_SERVER['REQUEST_URI'], '?');
global $routes;

if (isset($routes[$requestMethod][$requestUri])) {
    $route = $routes[$requestMethod][$requestUri];
    $action = $route['action'];
    $middlewareList = $route['middleware'] ?? [];

    middleware($middlewareList);

    if (is_string($action) && strpos($action, '@') !== false) {
        [$controller, $method] = explode('@', $action);
        if (class_exists($controller)) {
            $instance = new $controller;
            if (method_exists($instance, $method)) {
                $instance -> $method();
                exit;
            } else {
                die("Method {$method} tidak ditemukan di controller");
            }
        } else {
            die("Controller {$controller} tdiak di temukan");
        }
    }

    if (is_callable($action)) {
        call_user_func($action);
        exit;
    }
}

http_response_code(404);
echo "404 Not Found";