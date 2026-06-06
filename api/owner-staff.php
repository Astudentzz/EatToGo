<?php
header('Content-Type: application/json');
session_start();
require_once 'config/database.php';
$pdo = getDB();

// Include SMTP configuration (assuming same as register.php)
$smtpConfigPath = __DIR__ . '/../contact/config.php';
if (!file_exists($smtpConfigPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'SMTP configuration missing']);
    exit;
}
require_once $smtpConfigPath;

// Include PHPMailer
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$owner_id = $_SESSION['user']['id'];

function verifyOwnership($pdo, $owner_id, $restaurant_id) {
    $stmt = $pdo->prepare("SELECT id FROM restaurants WHERE id = ? AND owner_id = ?");
    $stmt->execute([$restaurant_id, $owner_id]);
    return $stmt->fetch() !== false;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $restaurant_id = $_GET['restaurant_id'] ?? 0;
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
    $stmt = $pdo->prepare("SELECT id, name, email, created_at FROM users WHERE restaurant_id = ? AND role = 'staff'");
    $stmt->execute([$restaurant_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $restaurant_id = $data['restaurant_id'] ?? 0;
    if (!$name || !$email || !$password || !$restaurant_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing fields (name, email, password, restaurant_id)']);
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
    $hashed = md5($password);

    // Insert staff as unverified
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, restaurant_id, verification_token, token_expiry, email_verified) VALUES (?, ?, ?, 'staff', ?, ?, ?, 0)");
    $stmt->execute([$name, $email, $hashed, $restaurant_id, $token, $expiry]);

    // Build verification link (adjust domain as needed)
    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $domain = $_SERVER['HTTP_HOST'];
    $verificationLink = $protocol . $domain . "/api/verify-staff-email.php?token=$token&email=" . urlencode($email);

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
        $mail->Body    = "Hello $name,<br><br>
                          You have been added as a staff member for a restaurant on EatToGo.<br>
                          Please verify your email by clicking the link below:<br><br>
                          <a href='$verificationLink'>$verificationLink</a><br><br>
                          This link expires in 24 hours.<br><br>
                          After verification, you can log in with the password provided by your restaurant owner.";
        $mail->AltBody = "Hello $name,\n\nPlease verify your staff account by visiting:\n$verificationLink\n\nThis link expires in 24 hours.";
        $mail->send();
        $emailSent = true;
    } catch (Exception $e) {
        error_log('Staff verification email failed: ' . $mail->ErrorInfo);
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
    $staff_id = $_GET['id'] ?? 0;
    $restaurant_id = $_GET['restaurant_id'] ?? 0;
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
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND restaurant_id = ? AND role = 'staff'");
    $stmt->execute([$staff_id, $restaurant_id]);
    echo json_encode(['success' => true]);
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>