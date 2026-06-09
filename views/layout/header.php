<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?></title>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="app-shell">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle" aria-label="Meniu">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
            </button>
            <div class="topbar-brand">eSC <span>Chess Club</span></div>
        </div>
        <div class="topbar-right">
            <div class="topbar-user">
                <strong><?= e($_SESSION['username'] ?? '') ?></strong>
                <span> · <?= e($_SESSION['role'] ?? '') ?></span>
            </div>
            <a href="index.php?route=auth/logout" class="btn btn-secondary btn-sm">Logout</a>
        </div>
    </header>
    <div class="app-body">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
