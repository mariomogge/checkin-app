<?php

namespace App\Controller;

use App\Service\CheckInService;
use App\Service\CheckOutService;
use App\Service\DeskBookingService;
use App\Service\AuthService;
use App\Service\UserService;
use App\Repository\DeskRepository;
use App\Repository\BookingRepository;
use App\Support\Request;
use App\Support\JsonResponse;
use App\Middleware\AuthMiddleware;
use Exception;

class WorkspaceController
{
    public function __construct(
        private BookingRepository $bookingRepo = new BookingRepository(),
        private DeskRepository $deskRepo = new DeskRepository(),
        private ?DeskBookingService $bookingService = null,
        private CheckInService $checkInService = new CheckInService(),
        private CheckOutService $checkOutService = new CheckOutService(),
        private AuthService $authService = new AuthService()
    ) {
        $this->bookingService = $this->bookingService ?? new DeskBookingService(
            $this->bookingRepo,
            $this->deskRepo
        );

        header('Content-Type: application/json');
    }

    public function login(): void
    {
        $data = Request::json();
        $name = $data['name'] ?? null;
        $password = $data['password'] ?? null;

        try {
            $token = $this->authService->login($name, $password);
            JsonResponse::success(['token' => $token]);
        } catch (Exception $e) {
            JsonResponse::error($e->getMessage(), 401);
        }
    }

    public function getUser(int $userId): void
    {
        try {
            $userService = new UserService();
            $lastCheckIn = $userService->getLastCheckIn($userId);
            $user = $userService->getUserById($userId);

            if (!$user) {
                JsonResponse::error('User not found', 404);
                return;
            }

            JsonResponse::success([
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'lastCheckIn' => $lastCheckIn?->format('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            JsonResponse::error($e->getMessage(), 500);
        }
    }

    public function checkIn(): void
    {
        $data = Request::json();
        $userId = $data['userId'] ?? null;

        if (!$userId) {
            JsonResponse::error('Missing userId', 400);
            return;
        }

        try {
            (new UserService())->checkIn($userId);
            JsonResponse::success(['status' => 'Checked in']);
        } catch (Exception $e) {
            JsonResponse::error($e->getMessage(), 500);
        }
    }

    public function checkOut(): void
    {
        $data = Request::json();
        $userId = $data['userId'] ?? null;

        if (!$userId) {
            JsonResponse::error('Missing userId', 400);
            return;
        }

        try {
            (new UserService())->checkOut($userId);
            JsonResponse::success(['status' => 'Checked out']);
        } catch (Exception $e) {
            JsonResponse::error($e->getMessage(), 400);
        }
    }

    public function book(): void
    {
        $data = Request::json();
        $userId = $data['userId'] ?? null;
        $deskId = $data['deskId'] ?? null;

        if (!$userId || !$deskId) {
            JsonResponse::error('Missing userId or deskId', 400);
            return;
        }

        try {
            $this->bookingService->book($userId, $deskId);
            JsonResponse::success(['status' => 'Desk booked']);
        } catch (Exception $e) {
            JsonResponse::error($e->getMessage());
        }
    }

    public function availableDesks(): void
    {
        try {
            $desks = $this->deskRepo->getAvailableDesks();
            $response = array_map(fn($desk) => [
                'id'       => $desk->id,
                'location' => $desk->location,
            ], $desks);
            JsonResponse::success($response);
        } catch (Exception $e) {
            JsonResponse::error($e->getMessage(), 500);
        }
    }

    public function allBookings(): void
    {
        try {
            (new AuthMiddleware())->requireAuth('admin');
            $bookings = $this->bookingRepo->getAll();

            $response = array_map(fn($b) => [
                'id'       => $b->id,
                'userId'   => $b->userId,
                'deskId'   => $b->deskId,
                'start'    => $b->startTime->format('Y-m-d H:i:s'),
                'end'      => $b->endTime?->format('Y-m-d H:i:s'),
                'checkout' => $b->checkoutTime?->format('Y-m-d H:i:s'),
            ], $bookings);

            JsonResponse::success($response);
        } catch (Exception $e) {
            JsonResponse::error($e->getMessage(), 403);
        }
    }

    public function getActiveBooking(int $userId): void
    {
        try {
            $booking = $this->bookingRepo->getLatestBookingByUser($userId);

            if (!$booking || $booking->checkoutTime !== null) {
                JsonResponse::error('No active booking', 404);
                return;
            }

            JsonResponse::success([
                'deskId'        => $booking->deskId,
                'startTime'     => $booking->startTime->format('Y-m-d H:i:s'),
                'endTime'       => $booking->endTime?->format('Y-m-d H:i:s'),
                'checkoutTime'  => $booking->checkoutTime?->format('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            JsonResponse::error($e->getMessage(), 500);
        }
    }
}
