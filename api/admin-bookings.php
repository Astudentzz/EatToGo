<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized', 'role_detected' => $_SESSION['user']['role'] ?? 'none']);
    exit;
}
$stmt = $pdo->prepare("
    SELECT r.*, u.name as customer_name, res.name as restaurant_name
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN restaurants res ON r.restaurant_id = res.id
    ORDER BY r.reservation_date DESC, r.reservation_time DESC
");
$stmt->execute();
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>