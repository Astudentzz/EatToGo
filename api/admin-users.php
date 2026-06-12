<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

// Only admin can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get all users (safe for display)
    $stmt = $pdo->query("SELECT id, name, email, role, restaurant_id, created_at FROM users ORDER BY id");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif ($method === 'DELETE') {
    requireCsrfToken();

    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing user ID']);
        exit;
    }

    // Prevent admin from deleting their own account
    if ($id == $_SESSION['user']['id']) {
        http_response_code(400);
        echo json_encode(['error' => 'You cannot delete your own admin account']);
        exit;
    }

    try {
        // First get user email and role
        $stmt = $pdo->prepare("SELECT email, role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            exit;
        }

        $pdo->beginTransaction();

        // 1. If this is an owner, delete their application record (so email can be reused)
        if ($user['role'] === 'owner') {
            $stmt = $pdo->prepare("DELETE FROM owner_applications WHERE email = ?");
            $stmt->execute([$user['email']]);
        }

        // 2. Delete order items for reservations of this user
        $stmt = $pdo->prepare("
            DELETE oi FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN reservations r ON o.reservation_id = r.id
            WHERE r.user_id = ?
        ");
        $stmt->execute([$id]);

        // 3. Delete orders linked to this user's reservations
        $stmt = $pdo->prepare("
            DELETE o FROM orders o
            JOIN reservations r ON o.reservation_id = r.id
            WHERE r.user_id = ?
        ");
        $stmt->execute([$id]);

        // 4. Delete feedbacks from this user's reservations
        $stmt = $pdo->prepare("
            DELETE f FROM feedbacks f
            JOIN reservations r ON f.reservation_id = r.id
            WHERE r.user_id = ?
        ");
        $stmt->execute([$id]);

        // 5. Delete reservations belonging to this user
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE user_id = ?");
        $stmt->execute([$id]);

        // 6. If user is an owner, detach any restaurants they own (set owner_id to NULL)
        $stmt = $pdo->prepare("UPDATE restaurants SET owner_id = NULL WHERE owner_id = ?");
        $stmt->execute([$id]);

        // 7. If user is staff, remove restaurant_id link
        $stmt = $pdo->prepare("UPDATE users SET restaurant_id = NULL WHERE id = ? AND role = 'staff'");
        $stmt->execute([$id]);

        // 8. Finally delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        $pdo->commit();
        echo json_encode(['success' => true]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>