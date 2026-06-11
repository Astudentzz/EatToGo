<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$stmt = $pdo->prepare("
    SELECT r.*, u.name as customer_name, res.name as restaurant_name
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN restaurants res ON r.restaurant_id = res.id
    ORDER BY r.reservation_date DESC, r.reservation_time DESC
");
$stmt->execute();
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
