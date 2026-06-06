<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

// Include SMTP configuration
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

$data = json_decode(file_get_contents('php://input'), true);
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? 'customer';

if (!$name || !$email || !$phone || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing fields']);
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
$stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, verification_token, token_expiry, email_verified) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
$stmt->execute([$name, $email, $phone, $hashed, $role, $token, $expiry]);

// Build verification link – NO /EatToGo folder
$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$domain = $_SERVER['HTTP_HOST']; // e.g., eattogo.infinityfreeapp.com
$verificationLink = $protocol . $domain . "/api/verify-email.php?token=$token";

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
    $mail->Subject = 'Verify your email – EatToGo';
    $mail->Body    = "Hello $name,<br><br>Please verify your email by clicking the link below:<br><br>
                      <a href='$verificationLink'>$verificationLink</a><br><br>
                      This link expires in 24 hours.<br><br>
                      If you did not create an account, please ignore this email.";
    $mail->AltBody = "Hello $name,\n\nPlease verify your email by visiting this link:\n$verificationLink\n\nThis link expires in 24 hours.";
    $mail->send();
    $emailSent = true;
} catch (Exception $e) {
    error_log('Verification email failed: ' . $mail->ErrorInfo);
    $emailSent = false;
}

echo json_encode([
    'success' => true,
    'message' => $emailSent 
        ? 'Account created! Please check your email to verify your account.' 
        : 'Account created but verification email could not be sent. Please contact support.'
]);
?>