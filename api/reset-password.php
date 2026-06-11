<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

$data = readJsonBody();
$token = $data['token'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
rateLimit('reset_' . strtolower($email), 5, 600);

if (!$token || !$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 8 characters']);
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

$hashed = hashPassword($password);
$stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
$stmt->execute([$hashed, $user['id']]);

echo json_encode(['success' => true]);
?>
