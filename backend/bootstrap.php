<?php

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/utils/Response.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';

foreach (glob(__DIR__ . '/../database/models/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/services/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/exports/*.php') as $file) {
    require_once $file;
}
require_once __DIR__ . '/plugins/PluginManager.php';
