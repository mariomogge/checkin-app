<?php

use PHPUnit\Framework\TestCase;
use App\Repository\BookingRepository;
use DateTime;

final class BookingRepositoryTest extends TestCase
{
    private BookingRepository $repo;

    protected function setUp(): void
    {
        $this->repo = new BookingRepository();
        // Optional: Datenbank vorbereiten
    }

    public function testCreateBooking(): void
    {
        $start = new DateTime();
        $userId = 1;
        $deskId = 2;

        $this->repo->createBooking($userId, $deskId, $start);
        $booking = $this->repo->getLatestBookingByUser($userId);

        $this->assertNotNull($booking);
        $this->assertEquals($userId, $booking->userId);
        $this->assertEquals($deskId, $booking->deskId);
    }

    public function testIsDeskAvailableReturnsFalse(): void
    {
        $now = new DateTime();
        $end = (clone $now)->modify('+4 hours');

        $result = $this->repo->isDeskAvailableInPeriod(2, $now, $end);
        $this->assertFalse($result);
    }
}
