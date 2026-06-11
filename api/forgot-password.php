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

$data = readJsonBody();
$email = trim($data['email'] ?? '');
rateLimit('forgot_' . strtolower($email), 5, 600);

if (!$email) {
    http_response_code(400);
    echo json_encode(['error' => 'Email required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        echo json_encode(['success' => true, 'message' => 'If the email exists, a reset link has been sent.']);
        exit;
    }

    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE id = ?");
    $stmt->execute([$token, $expiry, $user['id']]);

    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $domain = $_SERVER['HTTP_HOST'];
    $resetLink = $protocol . $domain . "/reset-password.html?token=$token&email=" . urlencode($email);

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_ENCRYPTION;
    $mail->Port       = SMTP_PORT;

    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
    $mail->addAddress($email, $user['name']);
    $mail->isHTML(false);                    // plain text
    $mail->Subject = 'Password reset request for EatToGo';
    $mail->Body    = "Hello {$user['name']},\n\n"
                   . "We received a request to reset the password for your EatToGo account.\n\n"
                   . "Click or copy the link below to set a new password (valid for 1 hour):\n"
                   . $resetLink . "\n\n"
                   . "If you did not request this, please ignore this email. Your password will not change.\n\n"
                   . "— EatToGo Team";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Reset link sent to your email.']);
} catch (Exception $e) {
    error_log('Mail error: ' . $mail->ErrorInfo);
    http_response_code(500);
    echo json_encode(['error' => 'Could not send email. Please try again later.']);
} catch (Throwable $e) {
    error_log('Forgot password error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error.']);
}
?>
