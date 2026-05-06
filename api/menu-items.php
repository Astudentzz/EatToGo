<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'staff' && $_SESSION['user']['role'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$restaurant_id = $_SESSION['user']['restaurant_id'] ?? 0;
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO menu_items (restaurant_id, name, price, category, emoji) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$restaurant_id, $data['name'], $data['price'], $data['category'], $data['emoji']]);
    echo json_encode(['success' => true]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ? AND restaurant_id = ?");
    $stmt->execute([$id, $restaurant_id]);
    echo json_encode(['success' => true]);
}
?>