<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No image uploaded or upload error']);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/restaurants/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if (!in_array($extension, $allowed)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid image format']);
    exit;
}

$filename = uniqid() . '.' . $extension;
$destination = $uploadDir . $filename;
if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
    $imagePath = '/uploads/restaurants/' . $filename;
    echo json_encode(['success' => true, 'imagePath' => $imagePath]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save image']);
}
?>