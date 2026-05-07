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

foreach ($reservations as &$res) {
    $stmt = $pdo->prepare("SELECT o.*, GROUP_CONCAT(CONCAT(mi.emoji, ' ', mi.name) SEPARATOR ', ') as items_summary FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE o.reservation_id = ? GROUP BY o.id");
    $stmt->execute([$res['id']]);
    $res['order'] = $stmt->fetch(PDO::FETCH_ASSOC);
}
echo json_encode($reservations);
?>