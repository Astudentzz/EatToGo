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

$stmt = $pdo->prepare("SELECT total_seats, slot_duration FROM restaurants WHERE id = ?");
$stmt->execute([$restaurant_id]);
$rest = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$rest) {
    echo json_encode(['available' => 0, 'total' => 0]);
    exit;
}
$totalSeats = $rest['total_seats'];
$slotDuration = $rest['slot_duration'] ?? 60;

$slotStart = DateTime::createFromFormat('g:i A', $time);
if (!$slotStart) {
    echo json_encode(['available' => 0, 'total' => $totalSeats]);
    exit;
}
$slotEnd = clone $slotStart;
$slotEnd->modify("+{$slotDuration} minutes");
$startStr = $slotStart->format('H:i:s');
$endStr = $slotEnd->format('H:i:s');

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(num_people), 0) as booked
    FROM reservations
    WHERE restaurant_id = ? AND reservation_date = ? AND status = 'confirmed'
      AND reservation_time >= ? AND reservation_time < ?
");
$stmt->execute([$restaurant_id, $date, $startStr, $endStr]);
$booked = $stmt->fetchColumn();

$available = max(0, $totalSeats - $booked);
echo json_encode(['available' => $available, 'total' => $totalSeats]);
?>