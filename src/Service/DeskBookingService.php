<?php

namespace App\Service;

use App\Contract\BookingRepositoryInterface;
use App\Repository\DeskRepository;
use DateTime;
use Exception;

class DeskBookingService
{
    private BookingRepositoryInterface $bookingRepo;
    private DeskRepository $deskRepo;

    public function __construct(
        BookingRepositoryInterface $bookingRepo,
        DeskRepository $deskRepo
    ) {
        $this->bookingRepo = $bookingRepo;
        $this->deskRepo = $deskRepo;
    }

    public function book(int $userId, int $deskId, ?DateTime $start = null, ?DateTime $end = null): void
    {
        $start = $start ?? new DateTime();
        $end   = $end   ?? (clone $start)->modify('+8 hours');

        if (!$this->deskRepo->exists($deskId)) {
            throw new Exception("Desk not found");
        }

        if (!$this->bookingRepo->isDeskAvailableInPeriod($deskId, $start, $end)) {
            throw new Exception("Desk is not available");
        }

        if ($this->bookingRepo->getActiveBookingForUser($userId)) {
            throw new Exception("User already has an active booking");
        }

        $this->bookingRepo->createBooking($userId, $deskId, $start, $end);
    }
}
