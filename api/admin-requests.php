<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("
        SELECT r.*, u.name as owner_name 
        FROM restaurants r
        JOIN users u ON r.owner_id = u.id
        WHERE r.status = 'pending'
    ");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    requireCsrfToken();
    $data = readJsonBody();
    $id = (int)($data['id'] ?? 0);
    $action = $data['action'] ?? '';
    if (!$id || !in_array($action, ['approve', 'reject'], true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request action']);
        exit;
    }
    $newStatus = $action === 'approve' ? 'approved' : 'rejected';
    $stmt = $pdo->prepare("UPDATE restaurants SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $id]);

    // If approved, update the owner's restaurant_id
    if ($newStatus === 'approved') {
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
