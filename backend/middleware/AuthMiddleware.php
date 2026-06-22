<?php

class AuthMiddleware
{
  private static ?array $user = null;

  public static function authenticate(): void
  {
    $header = $_SERVER['HTTP_AUTHORIZATION']
      ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
      ?? '';

    if ($header === '' && function_exists('apache_request_headers')) {
      $headers = apache_request_headers();
      $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    }

    if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
      return;
    }

    $payload = JwtService::decode(trim($matches[1]));
    if (!$payload || !isset($payload['sub'])) {
      return;
    }

    $user = User::findById((int) $payload['sub']);
    if ($user) {
      self::$user = $user;
    }
  }

  public static function user(): ?array
  {
    return self::$user;
  }

  public static function userId(): ?int
  {
    return self::$user ? (int) self::$user['id'] : null;
  }

  public static function role(): ?string
  {
    return self::$user['role_name'] ?? null;
  }

  public static function requireLogin(): void
  {
    if (!self::$user) {
      Response::problem('Autentificare necesara.', 401);
    }
  }

  public static function requireModule(string $module): void
  {
    self::requireLogin();
    if (!userCanAccess($module)) {
      Response::problem('Nu aveti acces la acest modul.', 403);
    }
  }

  public static function getJsonBody(): array
  {
    $input = json_decode(file_get_contents('php://input'), true);

    return is_array($input) ? $input : [];
  }
}
