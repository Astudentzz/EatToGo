<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Login required']);
        exit;
    }
    requireCsrfToken();
    $data = readJsonBody();
    $reservation_id = (int)($data['reservation_id'] ?? 0);
    $items = $data['items'] ?? [];

    if (!$reservation_id || empty($items) || !is_array($items)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid order details']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT restaurant_id FROM reservations WHERE id = ? AND user_id = ?");
    $stmt->execute([$reservation_id, $_SESSION['user']['id']]);
    $restaurant_id = $stmt->fetchColumn();
    if (!$restaurant_id) {
        http_response_code(403);
        echo json_encode(['error' => 'Reservation not found']);
        exit;
    }

    $total = 0;
    $validatedItems = [];
    foreach ($items as $item) {
        $itemId = (int)($item['id'] ?? 0);
        $quantity = (int)($item['quantity'] ?? 0);
        if (!$itemId || $quantity < 1 || $quantity > 50) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid item quantity']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT price FROM menu_items WHERE id = ? AND restaurant_id = ? AND is_available = 1");
        $stmt->execute([$itemId, $restaurant_id]);
        $price = $stmt->fetchColumn();
        if ($price === false) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid menu item']);
            exit;
        }
        $validatedItems[] = ['id' => $itemId, 'quantity' => $quantity, 'price' => $price];
        $total += $price * $quantity;
    }

    $stmt = $pdo->prepare("INSERT INTO orders (reservation_id, total_amount, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$reservation_id, $total]);
    $order_id = $pdo->lastInsertId();

    foreach ($validatedItems as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
    }
    echo json_encode(['success' => true, 'order_id' => $order_id]);
}
?>
