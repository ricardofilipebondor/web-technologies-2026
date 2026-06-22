<?php

class Hateoas
{
  public static function apiBase(): string
  {
    return '/backend/server.php';
  }

  public static function href(string $path): string
  {
    return self::apiBase() . $path;
  }

  /** @param array<string, string> $rels */
  public static function links(array $rels): array
  {
    $links = [];
    foreach ($rels as $rel => $path) {
      $links[] = ['rel' => $rel, 'href' => self::href($path)];
    }

    return $links;
  }

  public static function item(array $data, string $selfPath, array $extra = []): array
  {
    return array_merge($data, ['_links' => self::links(array_merge(['self' => $selfPath], $extra))]);
  }

  /** @param array<int, array<string, mixed>> $items */
  public static function collection(array $items, string $collectionPath, string $itemPathPrefix, array $itemExtra = []): array
  {
    $mapped = [];
    foreach ($items as $item) {
      $id = $item['id'] ?? null;
      $self = $id !== null ? $itemPathPrefix . '/' . $id : $itemPathPrefix;
      $mapped[] = self::item($item, $self, $itemExtra);
    }

    return [
      'items' => $mapped,
      '_links' => self::links(['self' => $collectionPath]),
    ];
  }
}
