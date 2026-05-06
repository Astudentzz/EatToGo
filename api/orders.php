<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Login required']);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $reservation_id = $data['reservation_id'] ?? 0;
    $items = $data['items'] ?? []; // array of {id, quantity}

    // Calculate total
    $total = 0;
    foreach ($items as $item) {
        $stmt = $pdo->prepare("SELECT price FROM menu_items WHERE id = ?");
        $stmt->execute([$item['id']]);
        $price = $stmt->fetchColumn();
        $total += $price * $item['quantity'];
    }

    $stmt = $pdo->prepare("INSERT INTO orders (reservation_id, total_amount, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$reservation_id, $total]);
    $order_id = $pdo->lastInsertId();

    foreach ($items as $item) {
        $stmt = $pdo->prepare("SELECT price FROM menu_items WHERE id = ?");
        $stmt->execute([$item['id']]);
        $price = $stmt->fetchColumn();
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['id'], $item['quantity'], $price]);
    }
    echo json_encode(['success' => true, 'order_id' => $order_id]);
}
?>