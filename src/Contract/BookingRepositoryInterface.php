<?php

namespace App\Contract;

use App\Model\Booking;
use DateTime;

interface BookingRepositoryInterface
{
    public function createBooking(int $userId, int $deskId, DateTime $start, ?DateTime $end = null): void;

    public function getLatestBookingByUser(int $userId): ?Booking;

    public function getActiveBookingForUser(int $userId): ?Booking;

    public function setCheckoutTime(int $bookingId): void;

    public function isDeskAvailableInPeriod(int $deskId, DateTime $start, DateTime $end): bool;

    public function getAll(): array;
}
