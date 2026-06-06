<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$reservation_id = $_POST['reservation_id'] ?? 0;
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

if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/payment_proofs/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $ext = strtolower(pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!in_array($ext, $allowed)) {
        echo json_encode(['error' => 'Only JPG, PNG, or PDF files allowed']);
        exit;
    }
    $filename = uniqid() . '.' . $ext;
    if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $uploadDir . $filename)) {
        $filePath = '/uploads/payment_proofs/' . $filename;
        $stmt = $pdo->prepare("UPDATE reservations SET payment_proof = ?, payment_submitted_at = NOW(), payment_verified = 0 WHERE id = ?");
        $stmt->execute([$filePath, $reservation_id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to save file']);
    }
} else {
    echo json_encode(['error' => 'No file uploaded']);
}
?>