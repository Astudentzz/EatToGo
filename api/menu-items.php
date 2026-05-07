<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'config/database.php';
$pdo = getDB();

// Only staff can manage menu items
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$restaurant_id = $_SESSION['user']['restaurant_id'] ?? 0;
if (!$restaurant_id) {
    // No restaurant assigned – return empty array (no error)
    echo json_encode([]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($menu);
}
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO menu_items (restaurant_id, name, price, category, emoji) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$restaurant_id, $data['name'], $data['price'], $data['category'], $data['emoji']]);
    echo json_encode(['success' => true]);
}
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? 0;
    $stmt = $pdo->prepare("UPDATE menu_items SET name = ?, price = ?, category = ?, emoji = ? WHERE id = ? AND restaurant_id = ?");
    $stmt->execute([$data['name'], $data['price'], $data['category'], $data['emoji'], $id, $restaurant_id]);
    echo json_encode(['success' => true]);
}
elseif ($method === 'DELETE') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ? AND restaurant_id = ?");
    $stmt->execute([$id, $restaurant_id]);
    echo json_encode(['success' => true]);
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>