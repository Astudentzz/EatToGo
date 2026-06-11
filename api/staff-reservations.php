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
$staffRestaurantId = $_SESSION['user']['restaurant_id'];
if (!$staffRestaurantId) {
    echo json_encode([]);
    exit;
}
$stmt = $pdo->prepare("SELECT r.*, u.name as customer_name FROM reservations r JOIN users u ON r.user_id = u.id WHERE r.restaurant_id = ? ORDER BY r.reservation_date DESC, r.reservation_time DESC");
$stmt->execute([$staffRestaurantId]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($reservations);
?>