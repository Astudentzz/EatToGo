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
    // Fetch the order (if any)
    $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE reservation_id = ?");
    $orderStmt->execute([$res['id']]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($order) {
        // Fetch all order items with menu details
        $itemStmt = $pdo->prepare("
            SELECT oi.*, mi.name, mi.emoji, mi.price as menu_price
            FROM order_items oi
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            WHERE oi.order_id = ?
        ");
        $itemStmt->execute([$order['id']]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Build items_summary (string) for backward compatibility
        $itemsSummary = [];
        foreach ($items as $it) {
            $itemsSummary[] = ($it['emoji'] ?? '🍽️') . ' ' . $it['name'] . ' (x' . $it['quantity'] . ')';
        }
        $order['items_summary'] = implode(', ', $itemsSummary);
        $order['items'] = $items;   // detailed array for frontend
        $order['total_amount'] = array_sum(array_map(function($i) {
            return $i['quantity'] * $i['price'];
        }, $items));
        
        $res['order'] = $order;
    } else {
        $res['order'] = null;
    }
}

echo json_encode($reservations);
?>