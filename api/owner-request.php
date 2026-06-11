<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$owner_id = $_SESSION['user']['id'];
requireCsrfToken();
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

// QR code is required for new submission
if (!isset($_FILES['qr_code']) || $_FILES['qr_code']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'Payment QR code is required']);
    exit;
}

$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    try {
        $imagePath = ltrim(saveUploadedFile(
            $_FILES['image'],
            __DIR__ . '/../uploads/restaurants/',
            '/uploads/restaurants',
            ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
        ), '/');
    } catch (RuntimeException $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
if ($total_seats < 1 || $slot_duration < 15 || $slot_duration > 240) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid seat count or slot duration']);
    exit;
}

// Handle QR code upload (required)
try {
    $qrCodePath = ltrim(saveUploadedFile(
        $_FILES['qr_code'],
        __DIR__ . '/../uploads/qrcodes/',
        '/uploads/qrcodes',
        ['jpg', 'jpeg', 'png', 'gif'],
        ['image/jpeg', 'image/png', 'image/gif']
    ), '/');
} catch (RuntimeException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO restaurants (name, location, category, description, price_range, hours, deal, total_seats, slot_duration, status, owner_id, image, qr_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?)");
$stmt->execute([$restaurant_name, $location, $cuisine, $description, $price_range, $hours, $deal, $total_seats, $slot_duration, $owner_id, $imagePath, $qrCodePath]);

echo json_encode(['success' => true, 'message' => 'Request submitted for admin approval']);
?>
