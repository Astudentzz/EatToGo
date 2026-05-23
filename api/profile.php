<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];
$user_id = $user['id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Basic user info
    $data = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone'] ?? '',
        'role' => $user['role'],
        'created_at' => $user['created_at'] ?? ''
    ];

    if ($role === 'owner') {
        // Fetch all restaurants owned by this owner
        $stmt = $pdo->prepare("SELECT id, name, location, category, status, image FROM restaurants WHERE owner_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        $data['restaurants'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($role === 'staff') {
        // Fetch assigned restaurant
        $restaurant_id = $user['restaurant_id'] ?? 0;
        if ($restaurant_id) {
            $stmt = $pdo->prepare("SELECT id, name, location, category, image FROM restaurants WHERE id = ?");
            $stmt->execute([$restaurant_id]);
            $data['restaurant'] = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $data['restaurant'] = null;
        }
    }

    echo json_encode($data);
}
elseif ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $new_name = trim($input['name'] ?? '');
    $new_password = trim($input['password'] ?? '');

    if (empty($new_name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Name cannot be empty']);
        exit;
    }

    $updateFields = [];
    $params = [];
    $updateFields[] = "name = ?";
    $params[] = $new_name;

    if (!empty($new_password)) {
        $updateFields[] = "password = ?";
        $params[] = md5($new_password);
    }

    $params[] = $user_id;
    $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Update session data
    $_SESSION['user']['name'] = $new_name;
    if (!empty($new_password)) {
        $_SESSION['user']['password'] = md5($new_password);
    }

    echo json_encode(['success' => true]);
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>