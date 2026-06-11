<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
requireCsrfToken();
$data = readJsonBody();
$reservation_id = (int)($data['reservation_id'] ?? 0);
$customer_name = $data['customer_name'] ?? '';
$customer_phone = $data['customer_phone'] ?? '';
$customer_email = $data['customer_email'] ?? '';
$special_requests = $data['special_requests'] ?? '';

$stmt = $pdo->prepare("UPDATE reservations SET customer_name = ?, customer_phone = ?, customer_email = ?, special_requests = ? WHERE id = ? AND user_id = ?");
$stmt->execute([$customer_name, $customer_phone, $customer_email, $special_requests, $reservation_id, $_SESSION['user']['id']]);
echo json_encode(['success' => true]);
?>
