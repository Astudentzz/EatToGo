<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
requireCsrfToken();
$data = readJsonBody();
$app_id = (int)($data['id'] ?? 0);

if (!$app_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing application ID']);
    exit;
}

// Fetch pending application
$stmt = $pdo->prepare("SELECT * FROM owner_applications WHERE id = ? AND status = 'pending'");
$stmt->execute([$app_id]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$app) {
    http_response_code(404);
    echo json_encode(['error' => 'Application not found or already processed']);
    exit;
}

// Generate verification token (expires in 24 hours)
$token = bin2hex(random_bytes(32));
$expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));

// Create user account with email_verified = 0
$hashed = $app['password']; // already bcrypt hash from owner-application.php
$stmt = $pdo->prepare("
    INSERT INTO users (name, email, phone, password, role, email_verified, verification_token, token_expiry, created_at)
    VALUES (?, ?, ?, ?, 'owner', 0, ?, ?, NOW())
");
$stmt->execute([$app['name'], $app['email'], $app['phone'], $hashed, $token, $expiry]);

// Update application status to approved
$stmt = $pdo->prepare("UPDATE owner_applications SET status = 'approved', reviewed_by = ?, reviewed_at = NOW() WHERE id = ?");
$stmt->execute([$_SESSION['user']['id'], $app_id]);

// Build verification link
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$domain = $_SERVER['HTTP_HOST'];
$verificationLink = $protocol . $domain . "/api/verify-email.php?token=$token";

// Include SMTP config and PHPMailer
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
    $mail->addAddress($app['email'], $app['name']);
    $mail->isHTML(true);
    $mail->Subject = 'Verify your restaurant owner account – EatToGo';
    $mail->Body    = "
        <div style='font-family: Arial, sans-serif; max-width: 600px;'>
            <h2>Welcome, {$app['name']}!</h2>
            <p>Your restaurant owner application has been <strong>approved</strong>.</p>
            <p>Please verify your email address by clicking the link below:</p>
            <p><a href='$verificationLink' style='background:#ff6b35; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Verify Email</a></p>
            <p>Or copy this link: <br>$verificationLink</p>
            <p>This link expires in 24 hours.</p>
            <p>After verification, you can log in using the email and password you provided during application.</p>
            <p>— EatToGo Team</p>
        </div>
    ";
    $mail->AltBody = "Welcome {$app['name']}!\n\nYour restaurant owner application has been approved.\n\nPlease verify your email by visiting:\n$verificationLink\n\nThis link expires in 24 hours.\n\nAfter verification, you can log in with your credentials.\n\n— EatToGo Team";
    $mail->send();
    $emailSent = true;
} catch (Exception $e) {
    error_log('Owner verification email failed: ' . $mail->ErrorInfo);
}

echo json_encode([
    'success' => true,
    'message' => $emailSent
        ? 'Owner approved. A verification email has been sent to the owner.'
        : 'Owner approved but verification email could not be sent. Please check SMTP settings.'
]);
?>