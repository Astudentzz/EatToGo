<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'staff' && $_SESSION['user']['role'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userRole = $_SESSION['user']['role'];

if ($userRole === 'staff') {
    $staffRestaurantId = $_SESSION['user']['restaurant_id'];
    if (!$staffRestaurantId) {
        echo json_encode([]);
        exit;
    }
    $query = "
        SELECT o.id, o.reservation_id, o.status,
               GROUP_CONCAT(CONCAT(mi.emoji, ' ', mi.name) SEPARATOR ', ') as items_summary
        FROM orders o
        JOIN reservations r ON o.reservation_id = r.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
        WHERE r.restaurant_id = ?
        GROUP BY o.id
        ORDER BY o.id DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$staffRestaurantId]);
} else {
    // Admin sees all orders
    $query = "
        SELECT o.id, o.reservation_id, o.status,
               GROUP_CONCAT(CONCAT(mi.emoji, ' ', mi.name) SEPARATOR ', ') as items_summary
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
        GROUP BY o.id
        ORDER BY o.id DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
}

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($orders);