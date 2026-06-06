<?php
require_once 'config/database.php';
$pdo = getDB();

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

if (!$token || !$email) {
    die('Invalid verification link.');
}

$stmt = $pdo->prepare("SELECT id, email_verified FROM users WHERE email = ? AND verification_token = ? AND token_expiry > NOW()");
$stmt->execute([$email, $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Invalid or expired verification link. Please contact the restaurant owner.');
}

if ($user['email_verified']) {
    die('Email already verified. You can log in.');
}

// Mark email as verified
$stmt = $pdo->prepare("UPDATE users SET email_verified = 1, verification_token = NULL, token_expiry = NULL WHERE id = ?");
$stmt->execute([$user['id']]);

// Optionally auto-login or redirect to login page
session_start();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$_SESSION['user'] = $stmt->fetch(PDO::FETCH_ASSOC);

header('Location: ../staff-dashboard.html'); // or login page
exit;
?>