<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

// SMTP & PHPMailer (for sending verification email)
$smtpConfigPath = __DIR__ . '/../contact/config.php';
if (!file_exists($smtpConfigPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'SMTP configuration missing']);
    exit;
}
require_once $smtpConfigPath;
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Only owner can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$owner_id = $_SESSION['user']['id'];
$method = $_SERVER['REQUEST_METHOD'];

// Helper: verify owner owns a specific restaurant
function verifyOwnership($pdo, $owner_id, $restaurant_id) {
    $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE id = ? AND owner_id = ?");
    $stmt->execute([$restaurant_id, $owner_id]);
    return (bool) $stmt->fetch();
}

if ($method === 'GET') {
    // GET expects ?restaurant_id=123
    $restaurant_id = (int)($_GET['restaurant_id'] ?? 0);
    if (!$restaurant_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing restaurant_id']);
        exit;
    }
    if (!verifyOwnership($pdo, $owner_id, $restaurant_id)) {
        http_response_code(403);
        echo json_encode(['error' => 'You do not own this restaurant']);
        exit;
    }
    $stmt = $pdo->prepare("SELECT id, name, email, role, created_at, email_verified FROM users WHERE restaurant_id = ? AND role = 'staff'");
    $stmt->execute([$restaurant_id]);
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($staff);
}
elseif ($method === 'POST') {
    requireCsrfToken();
    $data = readJsonBody();
    $restaurant_id = (int)($data['restaurant_id'] ?? 0);
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!$restaurant_id || !$name || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields (restaurant_id, name, email, password)']);
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
    if (!verifyOwnership($pdo, $owner_id, $restaurant_id)) {
        http_response_code(403);
        echo json_encode(['error' => 'You do not own this restaurant']);
        exit;
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already registered']);
        exit;
    }

    // Generate verification token (expires in 24 hours)
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
    $hashed = hashPassword($password);

    // Insert staff with email_verified = 0
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, role, restaurant_id, email_verified, verification_token, token_expiry)
        VALUES (?, ?, ?, 'staff', ?, 0, ?, ?)
    ");
    $stmt->execute([$name, $email, $hashed, $restaurant_id, $token, $expiry]);

    // Build verification link
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $domain = $_SERVER['HTTP_HOST'];
    $verificationLink = $protocol . $domain . "/api/verify-email.php?token=$token";

    // Send verification email
    $mail = new PHPMailer(true);
    $emailSent = false;
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port       = SMTP_PORT;
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Verify your staff account – EatToGo';
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeLink = htmlspecialchars($verificationLink, ENT_QUOTES, 'UTF-8');
        $mail->Body    = "
            <p>Hello $safeName,</p>
            <p>You have been added as a staff member for a restaurant on EatToGo.</p>
            <p>Please verify your email address by clicking the link below (valid for 24 hours):</p>
            <p><a href='$safeLink'>$safeLink</a></p>
            <p>After verification, you can log in using your email and the password provided by your restaurant owner.</p>
            <p>If you did not expect this, please ignore this email.</p>
            <p>— EatToGo Team</p>
        ";
        $mail->AltBody = "Hello $name,\n\nYou have been added as staff. Please verify your email by visiting:\n$verificationLink\n\nThis link expires in 24 hours.\n\nLogin credentials have been provided by your restaurant owner.\n\n— EatToGo Team";
        $mail->send();
        $emailSent = true;
    } catch (Exception $e) {
        error_log("Staff verification email failed: " . $mail->ErrorInfo);
        $emailSent = false;
    }

    echo json_encode([
        'success' => true,
        'message' => $emailSent
            ? 'Staff account created. A verification email has been sent to the staff member.'
            : 'Staff account created but verification email could not be sent. Please check SMTP settings.'
    ]);
}
elseif ($method === 'DELETE') {
    requireCsrfToken();
    $staff_id = (int)($_GET['id'] ?? 0);
    $restaurant_id = (int)($_GET['restaurant_id'] ?? 0);
    if (!$staff_id || !$restaurant_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing staff_id or restaurant_id']);
        exit;
    }
    if (!verifyOwnership($pdo, $owner_id, $restaurant_id)) {
        http_response_code(403);
        echo json_encode(['error' => 'You do not own this restaurant']);
        exit;
    }
    // Ensure staff belongs to this restaurant
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND restaurant_id = ? AND role = 'staff'");
    $stmt->execute([$staff_id, $restaurant_id]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Staff member not found in your restaurant']);
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$staff_id]);
    echo json_encode(['success' => true]);
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>