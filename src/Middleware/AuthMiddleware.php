<?php

namespace App\Middleware;

use App\Service\AuthService;
use Exception;

class AuthMiddleware
{
    public function __construct(
        private AuthService $authService = new AuthService()
    ) {}

    public function requireAuth(string $requiredRole = 'user'): ?object
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';

        if (!str_starts_with($token, 'Bearer ')) {
            throw new Exception("Missing or invalid token");
        }

        $token = str_replace('Bearer ', '', $token);
        $payload = $this->authService->verifyToken($token);

        if ($requiredRole === 'admin' && $payload->role !== 'admin') {
            throw new Exception("Forbidden – admin only");
        }

        return $payload;
    }
}