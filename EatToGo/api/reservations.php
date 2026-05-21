<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Please login']);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $restaurant_id = $data['restaurant_id'] ?? 0;
    $date = $data['date'] ?? '';
    $time = $data['time'] ?? '';
    $guests = $data['guests'] ?? 1;

    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, restaurant_id, reservation_date, reservation_time, num_people, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$_SESSION['user']['id'], $restaurant_id, $date, $time, $guests]);
    $reservation_id = $pdo->lastInsertId();
    echo json_encode(['id' => $reservation_id]);
}
?>