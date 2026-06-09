<?php

define('APP_NAME', 'eSC - Chess Club Manager');
define('APP_URL', '');

// Module accesibile per rol
$ROLE_PERMISSIONS = [
    'administrator' => [
        'dashboard', 'members', 'coaches', 'teams', 'groups', 'halls',
        'activities', 'competitions', 'participations', 'rankings',
        'prizes', 'trips', 'expenses', 'reimbursements',
    ],
    'antrenor' => [
        'dashboard', 'members', 'coaches', 'teams', 'groups', 'halls',
        'activities', 'competitions', 'participations', 'rankings', 'prizes',
    ],
    'responsabil_financiar' => [
        'dashboard', 'trips', 'expenses', 'reimbursements',
    ],
];

function userCanAccess(string $module): bool
{
    global $ROLE_PERMISSIONS;

    if (!isset($_SESSION['role'])) {
        return false;
    }

    $role = $_SESSION['role'];
    return in_array($module, $ROLE_PERMISSIONS[$role] ?? [], true);
}
