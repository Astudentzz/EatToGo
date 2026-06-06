<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}
$method = $_SERVER['REQUEST_METHOD'];
$hoursPattern = '/^\d{1,2}:\d{2}\s?(?:AM|PM)\s*-\s*\d{1,2}:\d{2}\s?(?:AM|PM)$/i';

if ($method === 'GET') {
    // Return only approved restaurants, join with users to get owner name
    $stmt = $pdo->query("
        SELECT r.*, u.name as owner_name
        FROM restaurants r
        LEFT JOIN users u ON r.owner_id = u.id
        WHERE r.status = 'approved'
        ORDER BY r.created_at DESC
    ");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $hours = $data['hours'] ?? '';
    if (!empty($hours) && !preg_match($hoursPattern, $hours)) {
        http_response_code(400);
        echo json_encode(['error' => 'Operating hours format invalid. Use "10:00 AM - 10:00 PM".']);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO restaurants (name, category, description, image, location, price_range, hours, deal, total_seats, slot_duration, qr_code, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved')");
    $stmt->execute([
        $data['name'],
        $data['category'] ?? '',
        $data['description'] ?? '',
        $data['image'] ?? '',
        $data['location'],
        $data['price_range'] ?? '',
        $hours,
        $data['deal'] ?? '',
        (int)($data['total_seats'] ?? 0),
        (int)($data['slot_duration'] ?? 60),
        $data['qr_code'] ?? ''
    ]);
    echo json_encode(['success' => true]);
}
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $hours = $data['hours'] ?? '';
    if (!empty($hours) && !preg_match($hoursPattern, $hours)) {
        http_response_code(400);
        echo json_encode(['error' => 'Operating hours format invalid. Use "10:00 AM - 10:00 PM".']);
        exit;
    }
    $id = $data['id'] ?? 0;
    $fields = [];
    $params = [];
    $allowed = ['name', 'category', 'location', 'price_range', 'description', 'hours', 'deal', 'image', 'total_seats', 'slot_duration', 'qr_code'];
    foreach ($allowed as $field) {
        if (array_key_exists($field, $data)) {
            $fields[] = "$field = ?";
            $params[] = $data[$field];
        }
    }
    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        exit;
    }
    $params[] = $id;
    $sql = "UPDATE restaurants SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => true]);
}
elseif ($method === 'DELETE') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM restaurants WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>