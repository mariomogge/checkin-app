<?php

namespace App\Service;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthService
{
    private string $jwtSecret = 'your-secret-key';

    public function __construct(
        private UserRepository $userRepo = new UserRepository()
    ) {}

    public function login(string $name, string $password): string
    {
        $user = $this->userRepo->findByName($name);

        if (!$user || !password_verify($password, $user->password)) {
            throw new Exception("Invalid credentials");
        }

        $payload = [
            'sub' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + 3600
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    public function verifyToken(string $token): object
    {
        return JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
    }
}