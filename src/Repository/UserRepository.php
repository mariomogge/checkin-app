<?php

namespace App\Repository;

use App\Model\User;
use App\Database\Database;
use PDO;
use DateTime;

class UserRepository
{
    public function findById(int $id): ?User
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new User(
            id: $row['id'],
            name: $row['name'],
            checkedIn: $row['checkedIn'],
            password: $row['password'],
            role: $row['role']
        );
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

    public function getLastCheckIn(int $userId): ?DateTime
    {
        $stmt = Database::getConnection()->prepare("
        SELECT start_time 
        FROM bookings 
        WHERE user_id = :userId 
          AND checkout_time IS NULL 
        ORDER BY start_time DESC 
        LIMIT 1
    ");
        $stmt->execute(['userId' => $userId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new DateTime($row['start_time']) : null;
    }
}
