<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$order_id = (int)($_GET['order_id'] ?? 0);
if (!$order_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing order ID']);
    exit;
}

// Verify the order belongs to this customer
$stmt = $pdo->prepare("
    SELECT o.id FROM orders o
    JOIN reservations r ON o.reservation_id = r.id
    WHERE o.id = ? AND r.user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user']['id']]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Delete order items first, then order
$stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
$stmt->execute([$order_id]);
$stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
$stmt->execute([$order_id]);

echo json_encode(['success' => true]);
?>