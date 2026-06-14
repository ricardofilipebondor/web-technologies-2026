<?php

define('APP_NAME', 'eSC - Chess Club Manager');

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

    if (!isset($_SESSION['role'])) {
        return false;
    }

    return in_array($module, $ROLE_PERMISSIONS[$_SESSION['role']] ?? [], true);
}
