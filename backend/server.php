<?php

require_once __DIR__ . '/bootstrap.php';

foreach (glob(__DIR__ . '/controllers/*.php') as $file) {
    require_once $file;
}

require_once __DIR__ . '/routes/Router.php';
require_once __DIR__ . '/routes/api.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = $_SERVER['PATH_INFO'] ?? '';

if ($path === '' && isset($_GET['path'])) {
    $path = $_GET['path'];
}

if ($path === '' || $path[0] !== '/') {
    $path = '/' . ltrim($path, '/');
}

AuthMiddleware::authenticate();

$router = new Router();
registerRoutes($router);

try {
    $router->dispatch($method, $path);
} catch (Throwable $e) {
    Response::problem($e->getMessage(), 500);
}
