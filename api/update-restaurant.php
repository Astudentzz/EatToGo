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
$total_seats = (int)($data['total_seats'] ?? 0);
$slot_duration = (int)($data['slot_duration'] ?? 60);

if (!$restaurant_id || !$name || !$location) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Validate hours format
$hoursPattern = '/^\d{1,2}:\d{2}\s?(?:AM|PM)\s*-\s*\d{1,2}:\d{2}\s?(?:AM|PM)$/i';
if (!empty($hours) && !preg_match($hoursPattern, $hours)) {
    http_response_code(400);
    echo json_encode(['error' => 'Operating hours format invalid. Use "10:00 AM - 10:00 PM".']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM restaurants WHERE id = ? AND owner_id = ?");
$stmt->execute([$restaurant_id, $owner_id]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'You do not own this restaurant']);
    exit;
}

$stmt = $pdo->prepare("UPDATE restaurants SET name = ?, location = ?, category = ?, description = ?, price_range = ?, hours = ?, deal = ?, total_seats = ?, slot_duration = ? WHERE id = ?");
$stmt->execute([$name, $location, $category, $description, $price_range, $hours, $deal, $total_seats, $slot_duration, $restaurant_id]);

echo json_encode(['success' => true]);
?>