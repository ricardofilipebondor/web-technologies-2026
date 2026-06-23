<?php

define('APP_NAME', 'eSC - Chess Club Manager');
define('JWT_SECRET', 'esc-jwt-secret-change-in-production-2026');
define('JWT_TTL', 86400);
define('API_BASE', '/backend/server.php');

$ROLE_PERMISSIONS = [
    'administrator' => [
        'dashboard', 'members', 'coaches', 'teams', 'groups', 'halls',
        'activities', 'competitions', 'participations', 'rankings',
        'prizes', 'trips', 'expenses', 'reimbursements', 'admin',
    ],
    'antrenor' => [
        'dashboard', 'members', 'coaches', 'teams', 'groups', 'halls',
        'activities', 'competitions', 'participations', 'rankings', 'prizes',
    ],
    'responsabil_financiar' => [
        'dashboard', 'trips', 'expenses', 'reimbursements',
    ],
];

$MENU_ITEMS = [
    ['module' => 'dashboard', 'label' => 'Dashboard'],
    ['module' => 'members', 'label' => 'Membri'],
    ['module' => 'coaches', 'label' => 'Antrenori'],
    ['module' => 'teams', 'label' => 'Echipe'],
    ['module' => 'groups', 'label' => 'Grupe'],
    ['module' => 'halls', 'label' => 'Sali'],
    ['module' => 'activities', 'label' => 'Activitati'],
    ['module' => 'competitions', 'label' => 'Concursuri'],
    ['module' => 'participations', 'label' => 'Participari'],
    ['module' => 'rankings', 'label' => 'Clasamente'],
    ['module' => 'prizes', 'label' => 'Premii'],
    ['module' => 'trips', 'label' => 'Deplasari'],
    ['module' => 'expenses', 'label' => 'Cheltuieli'],
    ['module' => 'reimbursements', 'label' => 'Deconturi'],
    ['module' => 'admin', 'label' => 'Administrare'],
];

function userCanAccess(string $module): bool
{
    global $ROLE_PERMISSIONS;
    $role = AuthMiddleware::role();
    if ($role === null) {
        return false;
    }
    return in_array($module, $ROLE_PERMISSIONS[$role] ?? [], true);
}

function getMenuItems(): array
{
    global $MENU_ITEMS;
    return $MENU_ITEMS;
}
