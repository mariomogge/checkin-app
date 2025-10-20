<?php

namespace App\Service;

use App\Contract\UserServiceInterface;
use App\Repository\BookingRepository;
use App\Repository\UserRepository;
use App\Model\User;
use Exception;
use DateTime;

class UserService implements UserServiceInterface
{
    public function __construct(
        private BookingRepository $bookingRepo = new BookingRepository(),
        private UserRepository $userRepo = new UserRepository()
    ) {}

    public function checkIn(int $userId): void
    {
        $activeBooking = $this->bookingRepo->getActiveBookingForUser($userId);

        if ($activeBooking) {
            throw new Exception("User already checked in");
        }
    }

    public function checkOut(int $userId): void
    {
        $booking = $this->bookingRepo->getActiveBookingForUser($userId);

        if (!$booking) {
            throw new Exception("No active booking to check out");
        }

        $this->bookingRepo->setCheckoutTime($booking->id);
    }

    public function getLastCheckIn(int $userId): ?DateTime
    {
        return $this->userRepo->getLastCheckIn($userId);
    }

    public function getUserById(int $userId): ?User 
    {
        return $this->userRepo->findById($userId);
    }
}
