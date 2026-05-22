<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

$restaurant_id = $_GET['restaurant_id'] ?? 0;
$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

if (!$restaurant_id || !$date || !$time) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

// Get total seats
$stmt = $pdo->prepare("SELECT total_seats FROM restaurants WHERE id = ?");
$stmt->execute([$restaurant_id]);
$totalSeats = $stmt->fetchColumn();
if (!$totalSeats) {
    echo json_encode(['available' => 0, 'total' => 0]);
    exit;
}

// Sum confirmed reservations
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(num_people), 0) as booked
    FROM reservations
    WHERE restaurant_id = ? AND reservation_date = ? AND reservation_time = ? AND status = 'confirmed'
");
$stmt->execute([$restaurant_id, $date, $time]);
$booked = $stmt->fetchColumn();

$available = max(0, $totalSeats - $booked);
echo json_encode(['available' => $available, 'total' => $totalSeats]);
?>