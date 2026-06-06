<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'owner' && $_SESSION['user']['role'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_FILES['qr_code']) || $_FILES['qr_code']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No QR code image uploaded']);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/qrcodes/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$ext = strtolower(pathinfo($_FILES['qr_code']['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($ext, $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid image format']);
    exit;
}

$filename = uniqid() . '.' . $ext;
$destination = $uploadDir . $filename;
if (move_uploaded_file($_FILES['qr_code']['tmp_name'], $destination)) {
    $filePath = '/uploads/qrcodes/' . $filename;
    echo json_encode(['success' => true, 'filePath' => $filePath]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save QR code']);
}
?>