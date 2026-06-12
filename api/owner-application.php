<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

// Apply rate limiting (max 3 applications per day per IP)
rateLimit('owner_app_' . ($_SERVER['REMOTE_ADDR'] ?? ''), 3, 86400);

// Only POST method allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// For file uploads, we use $_POST and $_FILES – no JSON body
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if ($role !== 'owner' || !$name || !$email || !$phone || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email address']);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 8 characters']);
    exit;
}

// Check if email already registered as a user
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Email already registered']);
    exit;
}

// Check if there is already a pending application for this email
$stmt = $pdo->prepare("SELECT id FROM owner_applications WHERE email = ? AND status = 'pending'");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'An application with this email is already pending review']);
    exit;
}

// Upload certificate file (required)
if (!isset($_FILES['certificate']) || $_FILES['certificate']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'Certificate file is required']);
    exit;
}

try {
    $certPath = saveUploadedFile(
        $_FILES['certificate'],
        __DIR__ . '/../uploads/certificates/',
        '/uploads/certificates',
        ['jpg', 'jpeg', 'png', 'pdf'],
        ['image/jpeg', 'image/png', 'application/pdf']
    );
} catch (RuntimeException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// Hash password
$hashed = hashPassword($password);

// Insert application
$stmt = $pdo->prepare("INSERT INTO owner_applications (name, email, phone, password, certificate_path, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
$stmt->execute([$name, $email, $phone, $hashed, $certPath]);

echo json_encode(['success' => true, 'message' => 'Application submitted. Admin will review.']);
?>