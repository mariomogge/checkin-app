<?php

namespace App\Repository;

use App\Model\User;
use App\Database\Database;
use PDO;

class UserRepository
{
    public function findById(int $id): ?User
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new User($row['id'], $row['name'], (bool)$row['checked_in']);
    }

    public function markAsCheckedIn(int $userId): void
    {
        $stmt = Database::getConnection()->prepare("UPDATE users SET checked_in = 1 WHERE id = :id");
        $stmt->execute(['id' => $userId]);
    }


    public function findByName(string $name): ?User
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM users WHERE name = ?");
        $stmt->execute([$name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new User(
            $row['id'],
            $row['name'],
            (bool)$row['checked_in'],
            $row['password'] ?? null,
            $row['role'] ?? 'user'
        );
    }
}
