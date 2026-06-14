<?php

class AuthMiddleware
{
    public static function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            Response::error('Autentificare necesara.', 401);
        }
    }

    public static function requireModule(string $module): void
    {
        self::requireLogin();
        if (!userCanAccess($module)) {
            Response::error('Nu aveti acces la acest modul.', 403);
        }
    }

    public static function getJsonBody(): array
    {
        $input = json_decode(file_get_contents('php://input'), true);
        return is_array($input) ? $input : [];
    }
}
