<?php

namespace App\Contract;

interface UserServiceInterface
{
    public function checkIn(int $userId): void;
    public function checkOut(int $userId): void;
}
