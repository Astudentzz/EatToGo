<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

$data = json_decode(file_get_contents('php://input'), true);
$token = trim($data['token'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$token || !$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

// Check token and expiry
$stmt = $pdo->prepare("SELECT id, reset_token, token_expiry FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(400);
    echo json_encode(['error' => 'Email not found']);
    exit;
}

if ($user['reset_token'] !== $token) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid token', 'db_token' => $user['reset_token']]);
    exit;
}

if (strtotime($user['token_expiry']) < time()) {
    http_response_code(400);
    echo json_encode(['error' => 'Token expired']);
    exit;
}

$hashed = md5($password);
$stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
$stmt->execute([$hashed, $user['id']]);

echo json_encode(['success' => true]);
?>