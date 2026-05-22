<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Please login']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$restaurant_id = $data['restaurant_id'] ?? 0;
$date = $data['date'] ?? '';
$time = $data['time'] ?? '';
$guests = $data['guests'] ?? 1;

// Validate inputs
if (!$restaurant_id || !$date || !$time || !$guests) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Get total seats of the restaurant
$stmt = $pdo->prepare("SELECT total_seats FROM restaurants WHERE id = ?");
$stmt->execute([$restaurant_id]);
$totalSeats = $stmt->fetchColumn();
if ($totalSeats === false) {
    http_response_code(404);
    echo json_encode(['error' => 'Restaurant not found']);
    exit;
}

// Sum already confirmed guests for the same date & time slot
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(num_people), 0) as booked 
    FROM reservations 
    WHERE restaurant_id = ? 
      AND reservation_date = ? 
      AND reservation_time = ? 
      AND status = 'confirmed'
");
$stmt->execute([$restaurant_id, $date, $time]);
$booked = $stmt->fetchColumn();

$remaining = $totalSeats - $booked;
if ($guests > $remaining) {
    http_response_code(400);
    echo json_encode(['error' => "Only $remaining seat(s) available for this time slot. Please choose a different time or reduce guests."]);
    exit;
}

// Insert the new reservation (pending status)
$stmt = $pdo->prepare("INSERT INTO reservations (user_id, restaurant_id, reservation_date, reservation_time, num_people, status) VALUES (?, ?, ?, ?, ?, 'pending')");
$stmt->execute([$_SESSION['user']['id'], $restaurant_id, $date, $time, $guests]);
$reservation_id = $pdo->lastInsertId();

echo json_encode(['id' => $reservation_id]);
?>