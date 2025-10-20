<?php

// CORS-Handling für Preflight & alle anderen Requests
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Beende Preflight-Anfrage sofort
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

use App\Routing\Router;
use App\Controller\WorkspaceController;

require_once __DIR__ . '/../vendor/autoload.php';

$router = new Router();
$controller = new WorkspaceController();

$router->post('/login', [$controller, 'login']);
$router->get('/user/:userId', [$controller, 'getUser']);
$router->post('/check-in', [$controller, 'checkIn']);
$router->post('/check-out', [$controller, 'checkOut']);
$router->post('/book', [$controller, 'book']);
$router->get('/desks', [$controller, 'availableDesks']);
$router->get('/bookings/active/:userId', [$controller, 'getActiveBooking']);
$router->get('/admin/bookings/', [$controller, 'allBookings']);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($method, $path);

