<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
requireCsrfToken();

try {
    $imagePath = saveUploadedFile(
        $_FILES['image'] ?? null,
        __DIR__ . '/../uploads/restaurants/',
        '/uploads/restaurants',
        ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
    );
    echo json_encode(['success' => true, 'imagePath' => $imagePath]);
} catch (RuntimeException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
