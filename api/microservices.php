<?php

/**
 * Gateway Web Micro-services
 * Fiecare modul (plugin) expune date prin acest endpoint JSON.
 * Exemple:
 *   api/microservices.php?service=members&action=list
 *   api/microservices.php?service=members&action=profile&id=1
 *   api/microservices.php?service=competitions&action=participations&competition_id=1
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../plugins/PluginManager.php';

foreach (glob(__DIR__ . '/../models/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/../services/*.php') as $file) {
    require_once $file;
}

$service = $_GET['service'] ?? '';
$action = $_GET['action'] ?? 'list';

$plugin = PluginManager::findByService($service);
if (!$plugin) {
    http_response_code(404);
    echo json_encode(['error' => 'Micro-service not found']);
    exit;
}

try {
    $result = null;

    switch ($service) {
        case 'members':
            $svc = new MembersService();
            $result = $action === 'profile'
                ? $svc->profile((int) ($_GET['id'] ?? 0))
                : $svc->list();
            break;

        case 'competitions':
            $svc = new CompetitionsService();
            $result = $action === 'participations'
                ? $svc->participationsReport((int) ($_GET['competition_id'] ?? 0))
                : $svc->list();
            break;

        case 'teams':
            $svc = new TeamsService();
            $result = $action === 'history'
                ? $svc->performanceHistory((int) ($_GET['id'] ?? 0))
                : $svc->list();
            break;

        case 'trips':
            $svc = new TripsService();
            $result = $action === 'report'
                ? $svc->report((int) ($_GET['id'] ?? 0))
                : $svc->list();
            break;

        default:
            $result = ['plugin' => $plugin->getName(), 'message' => 'Service available via web UI'];
    }

    echo json_encode([
        'service' => $service,
        'action' => $action,
        'plugin' => $plugin->getName(),
        'data' => $result,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
