<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
requireCsrfToken();

$data = readJsonBody();
$reservation_id = (int)($data['reservation_id'] ?? 0);
if (!$reservation_id) {
    echo json_encode(['error' => 'Missing reservation ID']);
    exit;
}

$staff_restaurant_id = $_SESSION['user']['restaurant_id'];

// Verify that this reservation belongs to the staff's restaurant and is confirmed
$stmt = $pdo->prepare("
    SELECT id FROM reservations 
    WHERE id = ? AND restaurant_id = ? AND status = 'confirmed'
");
$stmt->execute([$reservation_id, $staff_restaurant_id]);
if (!$stmt->fetch()) {
    echo json_encode(['error' => 'Unauthorized or reservation not confirmed']);
    exit;
}

// Update arrival_confirmed to 1
$stmt = $pdo->prepare("UPDATE reservations SET arrival_confirmed = 1 WHERE id = ? AND restaurant_id = ?");
$stmt->execute([$reservation_id, $staff_restaurant_id]);

echo json_encode(['success' => true]);
?>
