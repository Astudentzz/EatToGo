<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$owner_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT id, name, status, created_at FROM restaurants WHERE owner_id = ? ORDER BY created_at DESC");
$stmt->execute([$owner_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($requests);
?>