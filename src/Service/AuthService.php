<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Database\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthService
{
    private string $jwtSecret = 'your-secret-key';

    public function __construct(
        private UserRepository $userRepo = new UserRepository()
    ) {}

    public function register(string $name, string $password): void
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = Database::getConnection()->prepare("
            INSERT INTO users (name, password, role) 
            VALUES (:name, :password, 'user')
        ");
        $stmt->execute([
            'name'     => $name,
            'password' => $hash
        ]);
    }

    public function login(string $name, string $password): string
    {
        $stmt = Database::getConnection()->prepare("
            SELECT * FROM users WHERE name = :name
        ");
        $stmt->execute(['name' => $name]);

        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid credentials");
        }

        return $this->createJwt($user['id'], $user['role']);
    }

    public function verifyToken(string $token): object
    {
        return JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
    }

    private function createJwt(int $userId, string $role): string
    {
        $payload = [
            'sub'  => $userId,
            'role' => $role,
            'iat'  => time(),
            'exp'  => time() + 3600 // Token gültig für 1h
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }
}
