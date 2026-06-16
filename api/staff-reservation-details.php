<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$reservation_id = (int)($_GET['id'] ?? 0);
if (!$reservation_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing reservation ID']);
    exit;
}

$staff_restaurant_id = $_SESSION['user']['restaurant_id'];

$stmt = $pdo->prepare("
    SELECT r.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone,
           res.name as restaurant_name, res.location
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN restaurants res ON r.restaurant_id = res.id
    WHERE r.id = ? AND r.restaurant_id = ?
");
$stmt->execute([$reservation_id, $staff_restaurant_id]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$reservation) {
    http_response_code(404);
    echo json_encode(['error' => 'Reservation not found']);
    exit;
}

$order = null;
$stmt = $pdo->prepare("SELECT * FROM orders WHERE reservation_id = ?");
$stmt->execute([$reservation_id]);
$orderRow = $stmt->fetch(PDO::FETCH_ASSOC);

if ($orderRow) {
    $stmt = $pdo->prepare("
        SELECT oi.*, mi.name, mi.emoji, mi.price as menu_price
        FROM order_items oi
        JOIN menu_items mi ON oi.menu_item_id = mi.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderRow['id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = array_sum(array_map(function($i) {
        return $i['quantity'] * $i['price'];
    }, $items));
    $order = [
        'id' => $orderRow['id'],
        'status' => $orderRow['status'],
        'items' => $items,
        'total_amount' => $total
    ];
}

echo json_encode([
    'reservation' => $reservation,
    'order' => $order
]);