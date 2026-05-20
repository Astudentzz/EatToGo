<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

$restaurant_id = $_GET['restaurant_id'] ?? 0;
if (!$restaurant_id) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, price, category, emoji, image FROM menu_items WHERE restaurant_id = ? AND is_available = 1");
$stmt->execute([$restaurant_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>