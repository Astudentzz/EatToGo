<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$staffRestaurantId = $_SESSION['user']['restaurant_id'];
if (!$staffRestaurantId) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT o.id, o.reservation_id, o.status,
               GROUP_CONCAT(CONCAT(mi.emoji, ' ', mi.name) SEPARATOR ', ') as items_summary
        FROM orders o
        JOIN reservations r ON o.reservation_id = r.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
        WHERE r.restaurant_id = ?
        GROUP BY o.id
        ORDER BY o.id DESC
    ");
    $stmt->execute([$staffRestaurantId]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($orders);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}