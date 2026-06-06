<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$token || !$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND token_expiry > NOW()");
$stmt->execute([$email, $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or expired reset link. Please request a new one.']);
    exit;
}

$hashed = md5($password);
$stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
$stmt->execute([$hashed, $user['id']]);

echo json_encode(['success' => true]);
?>