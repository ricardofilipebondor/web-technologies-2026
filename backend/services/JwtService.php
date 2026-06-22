<?php

class JwtService
{
  private const ALGORITHM = 'HS256';

  public static function encode(array $payload): string
  {
    $header = self::base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => self::ALGORITHM]));
    $payload['iat'] = time();
    $payload['exp'] = time() + JWT_TTL;
    $body = self::base64UrlEncode(json_encode($payload));
    $signature = self::base64UrlEncode(
      hash_hmac('sha256', $header . '.' . $body, JWT_SECRET, true)
    );

    return $header . '.' . $body . '.' . $signature;
  }

  public static function decode(string $token): ?array
  {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
      return null;
    }

    [$header, $body, $signature] = $parts;
    $expected = self::base64UrlEncode(
      hash_hmac('sha256', $header . '.' . $body, JWT_SECRET, true)
    );

    if (!hash_equals($expected, $signature)) {
      return null;
    }

    $payload = json_decode(self::base64UrlDecode($body), true);
    if (!is_array($payload) || ($payload['exp'] ?? 0) < time()) {
      return null;
    }

    return $payload;
  }

  private static function base64UrlEncode(string $data): string
  {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

  private static function base64UrlDecode(string $data): string
  {
    $remainder = strlen($data) % 4;
    if ($remainder > 0) {
      $data .= str_repeat('=', 4 - $remainder);
    }

    return base64_decode(strtr($data, '-_', '+/'));
  }
}
