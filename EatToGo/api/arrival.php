<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Login required']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
$reservation_id = $data['reservation_id'] ?? 0;

$stmt = $pdo->prepare("UPDATE reservations SET arrival_confirmed = 1 WHERE id = ? AND user_id = ?");
$stmt->execute([$reservation_id, $_SESSION['user']['id']]);
echo json_encode(['success' => true]);
?>