<?php

namespace App\Controller;

use App\Service\CheckInService;
use App\Service\DeskBookingService;
use App\Service\CheckOutService;
use App\Service\AuthService;
use App\Repository\DeskRepository;
use App\Repository\BookingRepository;
use App\Middleware\AuthMiddleware;
use Exception;
use DateTime;

class WorkspaceController
{
    private CheckInService $checkInService;
    private DeskBookingService $bookingService;
    private DeskRepository $deskRepo;
    private AuthMiddleware $authMiddleware;
    private AuthService $authService;
    private CheckOutService $checkOutService;

    public function __construct()
    {
        $this->checkInService   = new CheckInService();
        $this->bookingService   = new DeskBookingService();
        $this->deskRepo         = new DeskRepository();
        $this->authMiddleware   = new AuthMiddleware();
        $this->authService      = new AuthService();
        $this->checkOutService  = new CheckOutService();

        header('Content-Type: application/json'); // Standard-Header
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['name'] ?? null;
        $password = $data['password'] ?? null;

        try {
            $token = $this->authService->login($name, $password);
            echo json_encode(['token' => $token]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function checkIn(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $userId = $data['userId'] ?? null;

        if (!$userId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing userId']);
            return;
        }

        try {
            $this->checkInService->checkInUser($userId);
            echo json_encode(['status' => 'Checked in']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }


    public function checkOut(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $userId = $data['userId'] ?? null;

        if (!$userId) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing userId']);
            return;
        }

        try {
            $this->checkOutService->checkOut($userId);
            echo json_encode(['status' => 'Checked out']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function book(): void
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $userId  = $data['userId'] ?? null;
        $deskId  = $data['deskId'] ?? null;
        $start   = $data['start'] ?? null;
        $end     = $data['end'] ?? null;

        if (!$userId || !$deskId || !$start) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing userId, deskId or start']);
            return;
        }

        try {
            $startTime = new DateTime($start);
            $endTime   = $end ? new DateTime($end) : null;

            $this->bookingService->book($userId, $deskId);

            echo json_encode(['status' => 'Desk booked']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function availableDesks(): void
    {
        $desks = $this->deskRepo->getAvailableDesks();

        $response = array_map(fn($desk) => [
            'id'       => $desk->id,
            'location' => $desk->location,
        ], $desks);

        echo json_encode($response);
    }

    public function allBookings(): void
    {
        try {
            $payload = (new AuthMiddleware())->requireAuth('admin');
            $bookings = (new BookingRepository())->getAll();

            echo json_encode(array_map(fn($b) => [
                'id' => $b->id,
                'userId' => $b->userId,
                'deskId' => $b->deskId,
                'start' => $b->startTime->format('Y-m-d H:i:s'),
                'end' => $b->endTime?->format('Y-m-d H:i:s'),
                'checkout' => $b->checkoutTime?->format('Y-m-d H:i:s'),
            ], $bookings));
        } catch (\Exception $e) {
            http_response_code(403);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }


    public function getActiveBooking(int $userId): void
    {
        try {
            $booking = (new BookingRepository())->getLatestBookingByUser($userId);

            if (!$booking || $booking->checkoutTime !== null) {
                http_response_code(404);
                echo json_encode(['error' => 'No active booking']);
                return;
            }

            echo json_encode([
                'deskId' => $booking->deskId,
                'startTime' => $booking->startTime->format('Y-m-d H:i:s'),
                'endTime' => $booking->endTime?->format('Y-m-d H:i:s'),
                'checkoutTime' => $booking->checkoutTime?->format('Y-m-d H:i:s'),
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
