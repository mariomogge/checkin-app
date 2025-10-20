<?php

namespace App\Model;

use DateTime;

class Booking
{
    public function __construct(
        public int $id,
        public int $userId,
        public int $deskId,
        public DateTime $startTime,
        public ?DateTime $endTime = null,
        public ?DateTime $checkoutTime = null
    ) {}

    public static function fromArray(array $data): Booking
    {
        $bookingId = (int) $data['id'];
        $userId = (int) $data['userId'];
        $deskId = (int) $data['deskId'];
        $startTime = new DateTime($data['start_time']);
        $endTime = isset($data['end_time']) && $data['end_time']
            ? new DateTime($data['end_time'])
            : null;
        $checkOutTime = isset($data['checkout_time']) && $data['checkout_time']
            ? new DateTime($data['checkout_time'])
            : null;

        $booking = new Booking(
            $bookingId, 
            $userId, 
            $deskId, 
            $startTime, 
            $endTime, 
            $checkOutTime
        );

        return $booking;
    }
}
