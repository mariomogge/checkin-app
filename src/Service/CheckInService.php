<?php

namespace App\Service;

use App\Repository\UserRepository;
use Exception;

class CheckInService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function checkInUser(int $userId): void
    {
        $user = $this->userRepo->findById($userId);

        if (!$user) {
            throw new Exception("User not found");
        }

        if ($user->checkedIn) {
            throw new Exception("User already checked in");
        }

        $this->userRepo->markAsCheckedIn($userId);
    }
}
