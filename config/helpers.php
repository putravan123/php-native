<?php

$routes = [];

function route($method, $uri, $action, $middleware = []) {
    global $routes;

    $routes[strtoupper($method)][$uri] = [
        'action' => $action,
        'middleware' => $middleware,
    ];
}

function redirect($uri) {
    header("Location: $uri");
    exit;
}

function Tampilan($view, $data = [], $layouts = null) {
    $content = "resources/views/{$view}.php";

    if (is_array($data)) {
        $dataM = $data;
    } else {
        $dataM = [];
        $layouts = $data;
    }
    
    extract($data);

    if ($layouts) {
        $layoutpath = "resources/layouts/{$layouts}.php";
        if (file_exists($layoutpath)) {
            require $layoutpath;
        } else {
            die("Error: Layouts {$layouts}.php tidal di temukan");
        }
    } else {
        if (file_exists($content)) {
            require $content;
        } else {
            die("Error: view {$view}.php tidak di temukan");
        }
    }
}


function middleware($middlewareList = []) {
    foreach ($middlewareList as $middleware) {
        if ($middleware === 'auth') {
            auth();
        } elseif ($middleware === 'admin') {
            admin();
        }
    }
}


function auth() {
    if (!isset($_SESSION['user'])) {
        redirect('/login');
    }
}

function admin() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        redirect('/403');
    }
}
