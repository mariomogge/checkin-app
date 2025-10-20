<?php

namespace App\Repository;

use App\Model\Desk;
use App\Database\Database;
use PDO;

class DeskRepository
{
    public function getAvailableDesks(): array
    {
        $stmt = Database::getConnection()->query("SELECT * FROM desks WHERE is_available = 1");
        $desks = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $desks[] = new Desk($row['id'], $row['location'], (bool)$row['is_available']);
        }

        return $desks;
    }

    public function markAsUnavailable(int $deskId): void
    {
        $stmt = Database::getConnection()->prepare("UPDATE desks SET is_available = 0 WHERE id = ?");
        $stmt->execute([$deskId]);
    }

    public function findById(int $deskId): ?Desk
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM desks WHERE id = ?");
        $stmt->execute([$deskId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Desk($row['id'], $row['location'], (bool)$row['is_available']);
    }

    public function markAsAvailable(int $deskId): void
    {
        $stmt = Database::getConnection()->prepare("UPDATE desks SET is_available = 1 WHERE id = ?");
        $stmt->execute([$deskId]);
    }

    public function exists(int $deskId): bool
    {
        $stmt = Database::getConnection()->prepare("SELECT 1 FROM desks WHERE id = :id");
        $stmt->execute(['id' => $deskId]);
        return (bool) $stmt->fetchColumn();
    }
}
