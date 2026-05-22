<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$owner_id = $_SESSION['user']['id'];
$data = json_decode(file_get_contents('php://input'), true);
$restaurant_id = $data['id'] ?? 0;
$name = $data['name'] ?? '';
$location = $data['location'] ?? '';
$category = $data['category'] ?? '';
$description = $data['description'] ?? '';
$price_range = $data['price_range'] ?? '';
$hours = $data['hours'] ?? '';
$deal = $data['deal'] ?? '';

if (!$restaurant_id || !$name || !$location) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Verify ownership
$stmt = $pdo->prepare("SELECT id FROM restaurants WHERE id = ? AND owner_id = ?");
$stmt->execute([$restaurant_id, $owner_id]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'You do not own this restaurant']);
    exit;
}

$stmt = $pdo->prepare("UPDATE restaurants SET name = ?, location = ?, category = ?, description = ?, price_range = ?, hours = ?, deal = ? WHERE id = ?");
$stmt->execute([$name, $location, $category, $description, $price_range, $hours, $deal, $restaurant_id]);

echo json_encode(['success' => true]);
?>