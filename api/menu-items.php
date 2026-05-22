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
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? '';
    $emoji = $_POST['emoji'] ?? '🍽️';
    $imagePath = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/menu_items/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $filename = uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                $imagePath = '/EatToGo/uploads/menu_items/' . $filename;
            }
        }
    }

    $stmt = $pdo->prepare("INSERT INTO menu_items (restaurant_id, name, price, category, emoji, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$restaurant_id, $name, $price, $category, $emoji, $imagePath]);
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
    // Delete image file if exists
    $stmt = $pdo->prepare("SELECT image FROM menu_items WHERE id = ? AND restaurant_id = ?");
    $stmt->execute([$id, $restaurant_id]);
    $item = $stmt->fetch();
    if ($item && $item['image'] && file_exists(__DIR__ . '/..' . $item['image'])) {
        unlink(__DIR__ . '/..' . $item['image']);
    }
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ? AND restaurant_id = ?");
    $stmt->execute([$id, $restaurant_id]);
    echo json_encode(['success' => true]);
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>