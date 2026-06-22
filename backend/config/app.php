<?php

define('APP_NAME', 'eSC - Chess Club Manager');
define('JWT_SECRET', 'esc-jwt-secret-change-in-production-2026');
define('JWT_TTL', 86400);

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

function userCanAccess(string $module): bool
{
    global $ROLE_PERMISSIONS;

    $role = AuthMiddleware::role();
    if ($role === null) {
        return false;
    }

    return in_array($module, $ROLE_PERMISSIONS[$role] ?? [], true);
}
