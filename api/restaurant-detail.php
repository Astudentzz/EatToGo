<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ? AND status = 'approved'");
$stmt->execute([$id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$restaurant) {
    http_response_code(404);
    echo json_encode(['error' => 'Restaurant not found']);
    exit;
}

// Include 'image' column
$stmt = $pdo->prepare("SELECT id, name, price, category, emoji, image FROM menu_items WHERE restaurant_id = ? AND is_available = 1");
$stmt->execute([$id]);
$menu = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['restaurant' => $restaurant, 'menu' => $menu]);
?>