<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (preg_match('#^/backend/server\.php#', $uri)) {
    require __DIR__ . '/backend/server.php';
    return true;
}

if ($uri === '/' || $uri === '/index.php') {
    header('Location: /frontend/index.html');
    exit;
}

$frontendFile = __DIR__ . $uri;
if (str_starts_with($uri, '/frontend/') && is_file($frontendFile)) {
    return false;
}

if (str_starts_with($uri, '/frontend/')) {
    http_response_code(404);
    echo '404 Not Found';
    return true;
}

return false;
