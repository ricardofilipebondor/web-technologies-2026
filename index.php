<?php

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/plugins/PluginManager.php';

require_once __DIR__ . '/exports/DataExporter.php';
require_once __DIR__ . '/exports/DataImporter.php';
require_once __DIR__ . '/exports/PdfExporter.php';

foreach (glob(__DIR__ . '/models/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/services/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/controllers/*.php') as $file) {
    require_once $file;
}

$route = $_GET['route'] ?? '';
if ($route === '') {
    $route = isLoggedIn() ? 'dashboard/index' : 'auth/login';
}

$parts = explode('/', $route);
$controllerName = ucfirst($parts[0]) . 'Controller';
$action = $parts[1] ?? 'index';

$controllerMap = [
    'Auth' => 'AuthController',
    'Dashboard' => 'DashboardController',
    'Members' => 'MemberController',
    'Coaches' => 'CoachController',
    'Teams' => 'TeamController',
    'Groups' => 'GroupController',
    'Halls' => 'HallController',
    'Activities' => 'ActivityController',
    'Competitions' => 'CompetitionController',
    'Participations' => 'ParticipationController',
    'Rankings' => 'RankingController',
    'Prizes' => 'PrizeController',
    'Trips' => 'TripController',
    'Expenses' => 'ExpenseController',
    'Reimbursements' => 'ReimbursementController',
];

$ctrlKey = ucfirst($parts[0]);
$className = $controllerMap[$ctrlKey] ?? $controllerName;

if (!class_exists($className) || !method_exists($className, $action)) {
    http_response_code(404);
    echo '<h2>Pagina nu a fost gasita.</h2>';
    echo '<p><a href="index.php">Inapoi</a></p>';
    exit;
}

$controller = new $className();
$controller->$action();
