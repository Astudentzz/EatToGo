<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}
requireCsrfToken();
$data = readJsonBody();
$reservation_id = (int)($data['reservation_id'] ?? 0);
$rating = (int)($data['rating'] ?? 0);
$comment = $data['comment'] ?? '';
if (!$reservation_id || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid feedback']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM reservations WHERE id = ? AND user_id = ?");
$stmt->execute([$reservation_id, $_SESSION['user']['id']]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'Reservation not found']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO feedbacks (reservation_id, rating, comment) VALUES (?, ?, ?)");
$stmt->execute([$reservation_id, $rating, $comment]);
echo json_encode(['success' => true]);
?>
