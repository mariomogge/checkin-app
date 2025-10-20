<?php

namespace App\Middleware;

use App\Service\AuthService;
use App\Support\Request;
use Exception;

class AuthMiddleware
{
    public function __construct(
        private AuthService $authService = new AuthService()
    ) {}

    public function requireAuth(?string $requiredRole = null): object
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (!str_starts_with($authHeader, 'Bearer ')) {
            throw new Exception("Unauthorized: Missing token");
        }

        $token = substr($authHeader, 7);
        $payload = $this->authService->verifyToken($token);

        if ($requiredRole && ($payload->role !== $requiredRole)) {
            throw new Exception("Forbidden: Insufficient role");
        }

        return $payload; // enthält userId & Rolle
    }
}
