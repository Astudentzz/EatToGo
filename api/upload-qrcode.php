<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'owner' && $_SESSION['user']['role'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
requireCsrfToken();

try {
    $filePath = saveUploadedFile(
        $_FILES['qr_code'] ?? null,
        __DIR__ . '/../uploads/qrcodes/',
        '/uploads/qrcodes',
        ['jpg', 'jpeg', 'png', 'gif'],
        ['image/jpeg', 'image/png', 'image/gif']
    );
    echo json_encode(['success' => true, 'filePath' => $filePath]);
} catch (RuntimeException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
