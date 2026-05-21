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

function verifyOwnership($pdo, $owner_id, $restaurant_id) {
    $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE id = ? AND owner_id = ?");
    $stmt->execute([$restaurant_id, $owner_id]);
    return $stmt->fetch() !== false;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $restaurant_id = $_GET['restaurant_id'] ?? 0;
    if (!$restaurant_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing restaurant_id']);
        exit;
    }
    if (!verifyOwnership($pdo, $owner_id, $restaurant_id)) {
        http_response_code(403);
        echo json_encode(['error' => 'You do not own this restaurant']);
        exit;
    }
    $stmt = $pdo->prepare("SELECT id, name, email, created_at FROM users WHERE restaurant_id = ? AND role = 'staff'");
    $stmt->execute([$restaurant_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $restaurant_id = $data['restaurant_id'] ?? 0;
    if (!$name || !$email || !$password || !$restaurant_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing fields (name, email, password, restaurant_id)']);
        exit;
    }
    if (!verifyOwnership($pdo, $owner_id, $restaurant_id)) {
        http_response_code(403);
        echo json_encode(['error' => 'You do not own this restaurant']);
        exit;
    }
    // Check if email exists
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
    echo json_encode(['success' => true]);
}
elseif ($method === 'DELETE') {
    $staff_id = $_GET['id'] ?? 0;
    $restaurant_id = $_GET['restaurant_id'] ?? 0;
    if (!$staff_id || !$restaurant_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing staff_id or restaurant_id']);
        exit;
    }
    if (!verifyOwnership($pdo, $owner_id, $restaurant_id)) {
        http_response_code(403);
        echo json_encode(['error' => 'You do not own this restaurant']);
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND restaurant_id = ? AND role = 'staff'");
    $stmt->execute([$staff_id, $restaurant_id]);
    echo json_encode(['success' => true]);
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>