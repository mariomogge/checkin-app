<?php

namespace App\Support;

class JsonResponse
{
    public static function success(array $data = [], int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public static function error(string $message, int $code = 400): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
    }
}
