<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM owner_applications WHERE status = 'pending' ORDER BY created_at ASC");
$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($applications);
?>