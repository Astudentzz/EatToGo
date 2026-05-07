<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'config/database.php';
$pdo = getDB();

// Only staff can manage menu items (admin removed)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$restaurant_id = $_SESSION['user']['restaurant_id'] ?? 0;
if (!$restaurant_id) {
    http_response_code(400);
    echo json_encode(['error' => 'You are not assigned to any restaurant']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
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
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO menu_items (restaurant_id, name, price, category, emoji) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$restaurant_id, $data['name'], $data['price'], $data['category'], $data['emoji']]);
    echo json_encode(['success' => true]);
}
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? 0;
    $name = $data['name'] ?? '';
    $price = $data['price'] ?? 0;
    $category = $data['category'] ?? '';
    $emoji = $data['emoji'] ?? '';
    $stmt = $pdo->prepare("UPDATE menu_items SET name = ?, price = ?, category = ?, emoji = ? WHERE id = ? AND restaurant_id = ?");
    $stmt->execute([$name, $price, $category, $emoji, $id, $restaurant_id]);
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