<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

$restaurant_id = $_GET['restaurant_id'] ?? 0;
$date = $_GET['date'] ?? '';

if (!$restaurant_id || !$date) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$stmt = $pdo->prepare("SELECT hours, slot_duration, total_seats FROM restaurants WHERE id = ?");
$stmt->execute([$restaurant_id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$restaurant) {
    echo json_encode([]);
    exit;
}

$hours = $restaurant['hours'];
$slotDuration = $restaurant['slot_duration'] ?? 60;
$totalSeats = $restaurant['total_seats'];

preg_match('/(\d{1,2}:\d{2}\s?(?:AM|PM))\s*-\s*(\d{1,2}:\d{2}\s?(?:AM|PM))/i', $hours, $matches);
if (empty($matches)) {
    echo json_encode([]);
    exit;
}

$start = DateTime::createFromFormat('h:i A', $matches[1]);
$end = DateTime::createFromFormat('h:i A', $matches[2]);
if (!$start || !$end) {
    echo json_encode([]);
    exit;
}

$slots = [];
$current = clone $start;
while ($current < $end) {
    $slotTime = $current->format('g:i A');
    $slots[] = $slotTime;
    $current->modify("+{$slotDuration} minutes");
}

$availableSlots = [];
foreach ($slots as $slot) {
    $slotStart = DateTime::createFromFormat('g:i A', $slot);
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
    $available = $totalSeats - $booked;
    if ($available > 0) {
        $availableSlots[] = ['time' => $slot, 'available' => $available];
    }
}

echo json_encode($availableSlots);
?>