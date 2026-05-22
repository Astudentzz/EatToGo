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
$staff_id = $data['id'] ?? 0;
$restaurant_id = $data['restaurant_id'] ?? 0;
$name = trim($data['name'] ?? '');
$password = trim($data['password'] ?? '');

if (!$staff_id || !$restaurant_id || !$name) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Verify owner owns the restaurant
$stmt = $pdo->prepare("SELECT id FROM restaurants WHERE id = ? AND owner_id = ?");
$stmt->execute([$restaurant_id, $owner_id]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'You do not own this restaurant']);
    exit;
}

// Update staff
$updateFields = ["name = ?"];
$params = [$name];
if (!empty($password)) {
    $updateFields[] = "password = ?";
    $params[] = md5($password);
}
$params[] = $staff_id;
$sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ? AND role = 'staff' AND restaurant_id = ?";
$params[] = $restaurant_id;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode(['success' => true]);
?>