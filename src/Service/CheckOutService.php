<?php

namespace App\Service;

use App\Repository\BookingRepository;
use App\Repository\DeskRepository;
use Exception;

class CheckoutService
{
    public function __construct(
        private BookingRepository $bookingRepo = new BookingRepository(),
        private DeskRepository $deskRepo = new DeskRepository()
    ) {}

    public function checkOut(int $userId): void
    {
        $booking = $this->bookingRepo->getActiveBookingForUser($userId);

        if (!$booking) {
            throw new Exception("No active booking found");
        }

        $this->bookingRepo->checkout($booking->id);
        $this->deskRepo->markAsAvailable($booking->deskId);

        $booking = $this->bookingRepo->getLatestBookingByUser($userId);

        if (!$booking || $booking->checkoutTime !== null) {
            throw new Exception("No active booking found to checkout");
        }

        $this->bookingRepo->setCheckoutTime($booking->id);
    }
}
