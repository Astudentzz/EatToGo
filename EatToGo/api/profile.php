<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user    = $_SESSION['user'];
$role    = $user['role'];
$user_id = $user['id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Fetch fresh user data (no created_at — not in users table)
    $stmt = $pdo->prepare("SELECT id, name, email, role, restaurant_id FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    $result = [
        'id'    => $userData['id'],
        'name'  => $userData['name'],
        'email' => $userData['email'],
        'role'  => $userData['role'],
    ];

    // For owner: fetch their restaurants (column is "category", not "cuisine_type")
    if ($role === 'owner') {
        $stmt = $pdo->prepare("
            SELECT id, name, location, category, status
            FROM restaurants
            WHERE owner_id = ?
            ORDER BY name
        ");
        $stmt->execute([$user_id]);
        $result['restaurants'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // For staff: fetch their assigned restaurant
    if ($role === 'staff') {
        $rid = $userData['restaurant_id'];
        if ($rid) {
            $stmt = $pdo->prepare("SELECT id, name, location, category FROM restaurants WHERE id = ?");
            $stmt->execute([$rid]);
            $result['restaurant'] = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $result['restaurant'] = null;
        }
    }

    echo json_encode($result);

} elseif ($method === 'PUT') {
    $data        = json_decode(file_get_contents('php://input'), true);
    $newName     = trim($data['name'] ?? '');
    $newPassword = $data['password'] ?? '';

    if ($newName) {
        $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->execute([$newName, $user_id]);
        $_SESSION['user']['name'] = $newName;
    }

    if ($newPassword) {
        $hashed = md5($newPassword);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $user_id]);
    }

    echo json_encode(['success' => true, 'message' => 'Profile updated']);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
