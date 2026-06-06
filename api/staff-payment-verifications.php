<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$staff_restaurant_id = $_SESSION['user']['restaurant_id'] ?? 0;
if (!$staff_restaurant_id) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        r.id AS reservation_id,
        r.customer_name,
        res.name AS restaurant_name,
        o.total_amount,
        r.payment_proof,
        r.payment_submitted_at
    FROM reservations r
    JOIN restaurants res ON r.restaurant_id = res.id
    LEFT JOIN orders o ON o.reservation_id = r.id
    WHERE r.restaurant_id = ?
      AND r.payment_proof IS NOT NULL
      AND r.payment_verified = 0
    ORDER BY r.payment_submitted_at ASC
");
$stmt->execute([$staff_restaurant_id]);
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($pending as &$p) {
    if ($p['total_amount'] === null) $p['total_amount'] = 0;
}
echo json_encode($pending);
?>