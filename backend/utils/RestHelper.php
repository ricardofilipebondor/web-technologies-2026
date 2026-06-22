<?php

class RestHelper
{
  public static function userPayload(array $user): array
  {
    return [
      'id' => (int) $user['id'],
      'username' => $user['username'],
      'email' => $user['email'],
      'role' => $user['role_name'],
    ];
  }

  public static function index(string $module, string $path, array $items, array $itemLinks = []): void
  {
    AuthMiddleware::requireModule($module);
    Response::resource(Hateoas::collection($items, $path, $path, $itemLinks));
  }

  public static function show(string $module, string $path, ?array $item, array $extraLinks = []): void
  {
    AuthMiddleware::requireModule($module);
    if (!$item) {
      Response::problem('Inregistrare negasita.', 404);
    }
    Response::resource(Hateoas::item($item, $path, $extraLinks));
  }

  public static function created(string $path, int $id, array $item, array $extraLinks = []): void
  {
    $location = $path . '/' . $id;
    Response::created(Hateoas::item($item, $location, $extraLinks), $location);
  }

  public static function updated(string $path, ?array $item, array $extraLinks = []): void
  {
    if (!$item) {
      Response::problem('Inregistrare negasita.', 404);
    }
    Response::resource(Hateoas::item($item, $path, $extraLinks));
  }

  public static function deleted(): void
  {
    Response::noContent();
  }
}
