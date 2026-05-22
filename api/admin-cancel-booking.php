<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$id = $_GET['id'] ?? 0;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing booking ID']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
$stmt->execute([$id]);

echo json_encode(['success' => true]);
?>