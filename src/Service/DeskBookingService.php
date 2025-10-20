<?php

namespace App\Service;

use App\Repository\BookingRepository;
use App\Repository\DeskRepository;
use DateTime;
use Exception;

class DeskBookingService
{
    private BookingRepository $bookingRepo;
    private DeskRepository $deskRepo;

    public function __construct()
    {
        $this->bookingRepo = new BookingRepository();
        $this->deskRepo = new DeskRepository();
    }

    public function book(int $userId, int $deskId): void
    {
        $now = new DateTime();
        $end = (clone $now)->modify('+8 hours');

        // 1. Prüfen ob Platz existiert
        if (!$this->deskRepo->exists($deskId)) {
            throw new Exception("Desk not found");
        }

        // 2. Prüfen ob Platz in diesem Zeitraum frei ist
        if (!$this->bookingRepo->isDeskAvailableInPeriod($deskId, $now, $end)) {
            throw new Exception("Desk is not available in this time period");
        }

        // 3. Prüfen ob User bereits aktive Buchung hat
        $activeBooking = $this->bookingRepo->getActiveBookingForUser($userId);
        if ($activeBooking) {
            throw new Exception("User already has an active booking");
        }

        // 4. Buchung speichern
        $this->bookingRepo->createBooking($userId, $deskId, $now, $end);
    }
}
