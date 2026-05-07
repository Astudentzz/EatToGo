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

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM restaurants");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO restaurants (name, category, description, image, location, price_range, status) VALUES (?, ?, ?, ?, ?, ?, 'approved')");
    $stmt->execute([
        $data['name'] ?? '',
        $data['category'] ?? '',
        $data['description'] ?? '',
        $data['image'] ?? '',
        $data['location'] ?? '',
        $data['price_range'] ?? ''
    ]);
    echo json_encode(['success' => true]);
}
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing id']);
        exit;
    }
    $stmt = $pdo->prepare("UPDATE restaurants SET name=?, category=?, location=?, price_range=? WHERE id=?");
    $stmt->execute([
        $data['name'] ?? '',
        $data['category'] ?? '',
        $data['location'] ?? '',
        $data['price_range'] ?? '',
        $data['id']
    ]);
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