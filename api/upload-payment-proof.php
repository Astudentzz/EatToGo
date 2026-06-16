<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
requireCsrfToken();

$reservation_id = (int)($_POST['reservation_id'] ?? 0);
if (!$reservation_id) {
    echo json_encode(['error' => 'Missing reservation ID']);
    exit;
}

// Verify reservation belongs to this customer
$stmt = $pdo->prepare("SELECT id FROM reservations WHERE id = ? AND user_id = ?");
$stmt->execute([$reservation_id, $_SESSION['user']['id']]);
if (!$stmt->fetch()) {
    echo json_encode(['error' => 'Invalid reservation']);
    exit;
}

try {
    $filePath = saveUploadedFile(
        $_FILES['payment_proof'] ?? null,
        __DIR__ . '/../uploads/payment_proofs/',
        '/uploads/payment_proofs',
        ['jpg', 'jpeg', 'png', 'pdf'],
        ['image/jpeg', 'image/png', 'application/pdf']
    );
    // Update payment_proof and change status from 'pending_payment' to 'pending'
    $stmt = $pdo->prepare("UPDATE reservations SET payment_proof = ?, payment_submitted_at = NOW(), payment_verified = 0, status = 'pending' WHERE id = ?");
    $stmt->execute([$filePath, $reservation_id]);
    echo json_encode(['success' => true]);
} catch (RuntimeException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>