<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$reservation_id = (int)($_GET['reservation_id'] ?? 0);
if (!$reservation_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing reservation ID']);
    exit;
}

// Verify reservation belongs to this customer and is still pending_payment
$stmt = $pdo->prepare("
    SELECT id, status FROM reservations 
    WHERE id = ? AND user_id = ? AND status = 'pending_payment'
");
$stmt->execute([$reservation_id, $_SESSION['user']['id']]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$res) {
    http_response_code(404);
    echo json_encode(['error' => 'Reservation not found or already processed']);
    exit;
}

// Change status to 'pending' (staff will confirm later)
$stmt = $pdo->prepare("UPDATE reservations SET status = 'pending' WHERE id = ?");
$stmt->execute([$reservation_id]);

echo json_encode(['success' => true]);
?>