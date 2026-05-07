<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    http_response_code(403);
    exit;
}
$ownerId = $_SESSION['user']['id'];
// Owner's restaurant request status (assuming owner is linked to restaurant after approval)
// For simplicity, fetch restaurants where created_by_owner_id = ? (but we store owner user, we can link by email later. Simplified: just return empty for now.
echo json_encode([]);
?>