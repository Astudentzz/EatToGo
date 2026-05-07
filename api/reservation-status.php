<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
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
$data = json_decode(file_get_contents('php://input'), true);
$reservation_id = $data['reservation_id'] ?? 0;
$status = $data['status'] ?? '';
$stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
$stmt->execute([$status, $reservation_id]);
echo json_encode(['success' => true]);
?>