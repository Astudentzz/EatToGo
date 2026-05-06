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

$stmt = $pdo->prepare("SELECT r.*, res.name as restaurant_name FROM reservations r JOIN restaurants res ON r.restaurant_id = res.id WHERE r.user_id = ? ORDER BY r.reservation_date DESC, r.reservation_time DESC");
$stmt->execute([$_SESSION['user']['id']]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Also fetch order status for each reservation
foreach ($reservations as &$res) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE reservation_id = ?");
    $stmt->execute([$res['id']]);
    $res['order'] = $stmt->fetch(PDO::FETCH_ASSOC);
}
echo json_encode($reservations);
?>