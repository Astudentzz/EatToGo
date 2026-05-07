<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Fetch all reservations with restaurant and customer names
$stmt = $pdo->prepare("
    SELECT r.*, u.name as customer_name, res.name as restaurant_name
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN restaurants res ON r.restaurant_id = res.id
    ORDER BY r.reservation_date DESC, r.reservation_time DESC
");
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Optionally include order information
foreach ($reservations as &$res) {
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE reservation_id = ?");
    $stmt->execute([$res['id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    $res['order_status'] = $order ? $order['status'] : null;
}

echo json_encode($reservations);
?>