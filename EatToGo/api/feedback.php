<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
$reservation_id = $data['reservation_id'] ?? 0;
$rating = $data['rating'] ?? 0;
$comment = $data['comment'] ?? '';

$stmt = $pdo->prepare("INSERT INTO feedbacks (reservation_id, rating, comment) VALUES (?, ?, ?)");
$stmt->execute([$reservation_id, $rating, $comment]);
echo json_encode(['success' => true]);
?>