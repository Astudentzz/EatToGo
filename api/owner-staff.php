<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

// Only owner can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$owner_id = $_SESSION['user']['id'];
// Get the owner's restaurant_id from the users table
$stmt = $pdo->prepare("SELECT restaurant_id FROM users WHERE id = ?");
$stmt->execute([$owner_id]);
$restaurant_id = $stmt->fetchColumn();

if (!$restaurant_id) {
    // Owner hasn't been assigned a restaurant yet
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode([]);
        exit;
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'You do not own any restaurant yet.']);
        exit;
    }
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Fetch staff members for this restaurant
    $stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE restaurant_id = ? AND role = 'staff'");
    $stmt->execute([$restaurant_id]);
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($staff);
}
elseif ($method === 'POST') {
    // Create new staff account
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    if (!$name || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Name, email and password required']);
        exit;
    }
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already registered']);
        exit;
    }
    $hashed = md5($password);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, restaurant_id) VALUES (?, ?, ?, 'staff', ?)");
    $stmt->execute([$name, $email, $hashed, $restaurant_id]);
    echo json_encode(['success' => true, 'message' => 'Staff account created']);
}
elseif ($method === 'DELETE') {
    // Remove staff account
    $staff_id = $_GET['id'] ?? 0;
    // Ensure this staff belongs to owner's restaurant
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND restaurant_id = ? AND role = 'staff'");
    $stmt->execute([$staff_id, $restaurant_id]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Not authorized to delete this staff']);
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$staff_id]);
    echo json_encode(['success' => true]);
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>