<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

$restaurant_id = $_GET['restaurant_id'] ?? 0;
if (!$restaurant_id) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT f.rating, f.comment, u.name as customer_name, f.created_at
    FROM feedbacks f
    JOIN reservations r ON f.reservation_id = r.id
    JOIN users u ON r.user_id = u.id
    WHERE r.restaurant_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$restaurant_id]);
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($feedbacks);
?>