<?php

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $route, array $params = []): void
{
    $query = http_build_query(array_merge(['route' => $route], $params));
    header('Location: index.php?' . $query);
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('auth/login');
    }
}

function render(string $view, array $data = []): void
{
    extract($data);
    $flash = getFlash();
    $currentRoute = $_GET['route'] ?? 'dashboard/index';
    $module = explode('/', $currentRoute)[0];

    include __DIR__ . '/../views/layout/header.php';
    include __DIR__ . '/../views/layout/sidebar.php';
    include __DIR__ . '/../views/' . $view . '.php';
    include __DIR__ . '/../views/layout/footer.php';
}

function renderLogin(string $view, array $data = []): void
{
    extract($data);
    $flash = getFlash();
    include __DIR__ . '/../views/layout/header_login.php';
    include __DIR__ . '/../views/' . $view . '.php';
    include __DIR__ . '/../views/layout/footer_login.php';
}

function post(string $key, $default = ''): string
{
    return trim($_POST[$key] ?? $default);
}

function get(string $key, $default = ''): string
{
    return trim($_GET[$key] ?? $default);
}
