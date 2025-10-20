<?php

namespace App\Repository;

use App\Contract\BookingRepositoryInterface;
use App\Model\Booking;
use App\Database\Database;
use DateTime;
use PDO;

class BookingRepository implements BookingRepositoryInterface
{
    public function createBooking(int $userId, int $deskId, DateTime $start, ?DateTime $end = null): void
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            INSERT INTO bookings (user_id, desk_id, start_time, end_time)
            VALUES (:userId, :deskId, :start, :end)
        ");

        $stmt->execute([
            'userId' => $userId,
            'deskId' => $deskId,
            'start'  => $start->format('Y-m-d H:i:s'),
            'end'    => $end ? $end->format('Y-m-d H:i:s') : null
        ]);
    }

    public function getBookingsForUser(int $userId): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT * FROM bookings 
            WHERE user_id = :userId 
            ORDER BY start_time DESC
        ");
        $stmt->execute(['userId' => $userId]);

        $results = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = new Booking(
                $row['id'],
                $row['user_id'],
                $row['desk_id'],
                new DateTime($row['start_time']),
                $row['end_time'] ? new DateTime($row['end_time']) : null,
                $row['checkout_time'] ? new DateTime($row['checkout_time']) : null
            );
        }

        return $results;
    }

    public function getActiveBookingForUser(int $userId): ?Booking
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT * FROM bookings 
            WHERE user_id = :userId AND checkout_time IS NULL 
            ORDER BY start_time DESC 
            LIMIT 1
        ");
        $stmt->execute(['userId' => $userId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new Booking(
            $row['id'],
            $row['user_id'],
            $row['desk_id'],
            new DateTime($row['start_time']),
            $row['end_time'] ? new DateTime($row['end_time']) : null,
            $row['checkout_time'] ? new DateTime($row['checkout_time']) : null
        );
    }

    public function checkout(int $bookingId): void
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            UPDATE bookings 
            SET checkout_time = NOW() 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $bookingId]);
    }

    public function isDeskAvailableInPeriod(int $deskId, DateTime $start, DateTime $end): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM bookings
            WHERE desk_id = :deskId
              AND (
                (start_time <= :end AND end_time >= :start)
                OR
                (start_time <= :end AND checkout_time IS NULL)
              )
        ");

        $stmt->execute([
            'deskId' => $deskId,
            'end'    => $end->format('Y-m-d H:i:s'),
            'start'  => $start->format('Y-m-d H:i:s'),
        ]);

        return $stmt->fetchColumn() == 0;
    }

    public function getAll(): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->query("
            SELECT * FROM bookings ORDER BY start_time DESC
        ");

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Booking(
                $row['id'],
                $row['user_id'],
                $row['desk_id'],
                new DateTime($row['start_time']),
                $row['end_time'] ? new DateTime($row['end_time']) : null,
                $row['checkout_time'] ? new DateTime($row['checkout_time']) : null
            );
        }

        return $result;
    }

    public function getLatestBookingByUser(int $userId): ?Booking
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT * FROM bookings 
            WHERE user_id = :user_id AND checkout_time IS NULL 
            ORDER BY start_time DESC 
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? Booking::fromArray($data) : null;
    }

    public function setCheckoutTime(int $bookingId): void
    {
        $pdo = Database::getConnection();
        $time = new DateTime();

        $stmt = $pdo->prepare("
            UPDATE bookings 
            SET checkout_time = :checkout_time 
            WHERE id = :id
        ");
        $stmt->execute([
            'checkout_time' => $time->format('Y-m-d H:i:s'),
            'id'            => $bookingId
        ]);
    }
}
