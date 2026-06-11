<?php
require_once 'config/database.php';
$pdo = getDB();

$token = $_GET['token'] ?? '';
if (!$token) {
    die('No verification token provided.');
}

$stmt = $pdo->prepare("SELECT id, email FROM users WHERE verification_token = ? AND token_expiry > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    die('Invalid or expired verification link. Please request a new one.');
}

// Mark email as verified
$stmt = $pdo->prepare("UPDATE users SET email_verified = 1, verification_token = NULL, token_expiry = NULL WHERE id = ?");
$stmt->execute([$user['id']]);

// Auto-login the user
startSecureSession();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$_SESSION['user'] = publicUser($stmt->fetch(PDO::FETCH_ASSOC));

// Redirect to success page
header('Location: ../verify-success.html');
exit;
?>
