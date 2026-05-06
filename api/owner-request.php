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
$data = json_decode(file_get_contents('php://input'), true);
$restaurant_name = $data['restaurant_name'] ?? '';
$location = $data['location'] ?? '';
$cuisine = $data['cuisine'] ?? '';

$stmt = $pdo->prepare("INSERT INTO restaurants (name, location, category, status) VALUES (?, ?, ?, 'pending')");
$stmt->execute([$restaurant_name, $location, $cuisine]);
echo json_encode(['success' => true, 'message' => 'Request submitted for admin approval']);
?>