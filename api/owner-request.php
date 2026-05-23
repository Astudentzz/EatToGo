<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$owner_id = $_SESSION['user']['id'];
$restaurant_name = $_POST['restaurant_name'] ?? '';
$location = $_POST['location'] ?? '';
$cuisine = $_POST['cuisine'] ?? '';
$description = $_POST['description'] ?? '';
$price_range = $_POST['price_range'] ?? '';
$hours = $_POST['hours'] ?? '';
$deal = $_POST['deal'] ?? '';
$total_seats = (int)($_POST['total_seats'] ?? 0);
$slot_duration = (int)($_POST['slot_duration'] ?? 60);

if (!$restaurant_name || !$location) {
    http_response_code(400);
    echo json_encode(['error' => 'Restaurant name and location required']);
    exit;
}

// Validate hours format
$hoursPattern = '/^\d{1,2}:\d{2}\s?(?:AM|PM)\s*-\s*\d{1,2}:\d{2}\s?(?:AM|PM)$/i';
if (!empty($hours) && !preg_match($hoursPattern, $hours)) {
    http_response_code(400);
    echo json_encode(['error' => 'Operating hours format invalid. Use "10:00 AM - 10:00 PM".']);
    exit;
}

$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/restaurants/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($ext, $allowed)) {
        $filename = uniqid() . '.' . $ext;
        $destination = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $imagePath = 'uploads/restaurants/' . $filename;
        }
    }
}

$stmt = $pdo->prepare("INSERT INTO restaurants (name, location, category, description, price_range, hours, deal, total_seats, slot_duration, status, owner_id, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)");
$stmt->execute([$restaurant_name, $location, $cuisine, $description, $price_range, $hours, $deal, $total_seats, $slot_duration, $owner_id, $imagePath]);

echo json_encode(['success' => true, 'message' => 'Request submitted for admin approval']);
?>