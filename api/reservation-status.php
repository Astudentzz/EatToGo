<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    exit;
}
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'staff' && $_SESSION['user']['role'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
requireCsrfToken();
$data = readJsonBody();
$reservation_id = (int)($data['reservation_id'] ?? 0);
$status = $data['status'] ?? '';
$allowedStatuses = ['pending', 'confirmed', 'cancelled', 'rejected'];
if (!$reservation_id || !in_array($status, $allowedStatuses, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid reservation status']);
    exit;
}
if ($_SESSION['user']['role'] === 'staff') {
    $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ? AND restaurant_id = ?");
    $stmt->execute([$status, $reservation_id, $_SESSION['user']['restaurant_id'] ?? 0]);
} else {
    $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt->execute([$status, $reservation_id]);
}
echo json_encode(['success' => true]);
?>
