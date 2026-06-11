<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$owner_id = $_SESSION['user']['id'];

// Get reservations for all restaurants owned by this owner
$stmt = $pdo->prepare("
    SELECT r.*, u.name as customer_name, res.name as restaurant_name
    FROM reservations r
    JOIN restaurants res ON r.restaurant_id = res.id
    JOIN users u ON r.user_id = u.id
    WHERE res.owner_id = ?
    ORDER BY r.reservation_date DESC, r.reservation_time DESC
");
$stmt->execute([$owner_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Also fetch order information for each reservation
foreach ($reservations as &$res) {
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE reservation_id = ?");
    $stmt->execute([$res['id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    $res['order'] = $order ? $order : null;
}

echo json_encode($reservations);
?>