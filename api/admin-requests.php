<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM restaurants WHERE status = 'pending'");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? 0;
    $action = $data['action'] ?? ''; // approve or reject
    $newStatus = $action === 'approve' ? 'approved' : 'rejected';
    
    // Update restaurant status
    $stmt = $pdo->prepare("UPDATE restaurants SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $id]);
    
    // If approved, update the owner's restaurant_id
    if ($newStatus === 'approved') {
        // Get owner_id from restaurant
        $stmt = $pdo->prepare("SELECT owner_id FROM restaurants WHERE id = ?");
        $stmt->execute([$id]);
        $owner_id = $stmt->fetchColumn();
        if ($owner_id) {
            $stmt = $pdo->prepare("UPDATE users SET restaurant_id = ? WHERE id = ?");
            $stmt->execute([$id, $owner_id]);
        }
    }
    
    echo json_encode(['success' => true]);
}
?>