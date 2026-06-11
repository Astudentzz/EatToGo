<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Login required']);
    exit;
}
requireCsrfToken();
$data = readJsonBody();
$reservation_id = (int)($data['reservation_id'] ?? 0);

$stmt = $pdo->prepare("UPDATE reservations SET arrival_confirmed = 1 WHERE id = ? AND user_id = ?");
$stmt->execute([$reservation_id, $_SESSION['user']['id']]);
echo json_encode(['success' => true]);
?>
