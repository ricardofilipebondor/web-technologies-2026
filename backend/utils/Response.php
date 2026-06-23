<?php

class Response
{
    public static function json(mixed $data, int $status = 200, array $headers = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        foreach ($headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function resource(array $data, int $status = 200): void
    {
        self::json($data, $status);
    }

    public static function created(array $data, string $locationPath): void
    {
        self::json($data, 201, ['Location' => Hateoas::href($locationPath)]);
    }

    public static function noContent(): void
    {
        http_response_code(204);
        exit;
    }

    public static function problem(string $detail, int $status = 400, ?string $title = null): void
    {
        $titles = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            409 => 'Conflict',
            500 => 'Internal Server Error',
        ];
        self::json([
            'type' => 'about:blank',
            'title' => $title ?? ($titles[$status] ?? 'Error'),
            'status' => $status,
            'detail' => $detail,
        ], $status);
    }
}
