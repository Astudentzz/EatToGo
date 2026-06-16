<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    http_response_code(401);
    echo json_encode(['error' => 'Please login as customer']);
    exit;
}
requireCsrfToken();

$data = readJsonBody();
$restaurant_id = (int)($data['restaurant_id'] ?? 0);
$date = $data['date'] ?? '';
$time = $data['time'] ?? '';
$guests = (int)($data['guests'] ?? 1);

if (!$restaurant_id || !$date || !$time || !$guests) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}
if ($guests < 1 || $guests > 50) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid guest count']);
    exit;
}

$reservationDate = DateTime::createFromFormat('Y-m-d', $date);
if (!$reservationDate || $reservationDate->format('Y-m-d') !== $date || $date < date('Y-m-d')) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid reservation date']);
    exit;
}

// Convert time from 12‑hour to 24‑hour
if (preg_match('/^\d{1,2}:\d{2}\s?(?:AM|PM)$/i', $time)) {
    $dt = DateTime::createFromFormat('g:i A', $time);
    if ($dt) {
        $time = $dt->format('H:i:s');
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid time format']);
        exit;
    }
}
if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid reservation time']);
    exit;
}

// Auto‑delete abandoned pending_payment reservations older than 30 minutes
$stmt = $pdo->prepare("DELETE FROM reservations WHERE status = 'pending_payment' AND created_at < DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
$stmt->execute();

// Check for duplicate reservation (same user, restaurant, date, time)
$stmt = $pdo->prepare("
    SELECT id, status FROM reservations 
    WHERE user_id = ? 
      AND restaurant_id = ? 
      AND reservation_date = ? 
      AND reservation_time = ?
");
$stmt->execute([$_SESSION['user']['id'], $restaurant_id, $date, $time]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);
if ($existing) {
    http_response_code(409);
    echo json_encode([
        'error' => 'You already have a reservation for this time slot.',
        'existing_reservation_id' => $existing['id'],
        'existing_status' => $existing['status']
    ]);
    exit;
}

// Get total seats of the restaurant
$stmt = $pdo->prepare("SELECT total_seats FROM restaurants WHERE id = ? AND status = 'approved'");
$stmt->execute([$restaurant_id]);
$totalSeats = $stmt->fetchColumn();
if ($totalSeats === false) {
    http_response_code(404);
    echo json_encode(['error' => 'Restaurant not found']);
    exit;
}

// Check availability for the chosen slot (only confirmed reservations block seats)
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(num_people), 0) as booked 
    FROM reservations 
    WHERE restaurant_id = ? 
      AND reservation_date = ? 
      AND reservation_time = ? 
      AND status IN ('confirmed', 'pending')
");
$stmt->execute([$restaurant_id, $date, $time]);
$booked = $stmt->fetchColumn();

$remaining = $totalSeats - $booked;
if ($guests > $remaining) {
    http_response_code(400);
    echo json_encode(['error' => "Only $remaining seat(s) available for this time slot. Please choose a different time or reduce guests."]);
    exit;
}

// Insert new reservation with status 'pending_payment'
$stmt = $pdo->prepare("
    INSERT INTO reservations (user_id, restaurant_id, reservation_date, reservation_time, num_people, status)
    VALUES (?, ?, ?, ?, ?, 'pending_payment')
");
$stmt->execute([$_SESSION['user']['id'], $restaurant_id, $date, $time, $guests]);
$reservation_id = $pdo->lastInsertId();

echo json_encode(['id' => $reservation_id]);
?>