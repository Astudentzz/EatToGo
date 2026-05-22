<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');

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

    $resetLink = "http://localhost/EatToGo/reset-password.html?token=$token&email=" . urlencode($email);

    // Load SMTP credentials from external config file (ignored by Git)
    $configPath = __DIR__ . '/../config/smtp.php';
    if (!file_exists($configPath)) {
        // Fallback to environment variables (safer for production)
        $smtp_host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $smtp_username = getenv('SMTP_USERNAME') ?: '';
        $smtp_password = getenv('SMTP_PASSWORD') ?: '';
        $smtp_port = getenv('SMTP_PORT') ?: 587;
        $smtp_secure = getenv('SMTP_SECURE') ?: 'tls';
    } else {
        require $configPath;
    }

    // Include PHPMailer
    require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';
    require_once __DIR__ . '/../lib/PHPMailer/Exception.php';

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_username;
        $mail->Password   = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port       = $smtp_port;

        $mail->setFrom('noreply@eattogo.com', 'EatToGo Support');
        $mail->addAddress($email, $user['name']);
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset - EatToGo';
        $mail->Body    = "Hello {$user['name']},<br><br>Click the link to reset your password:<br><a href='$resetLink'>$resetLink</a><br><br>Expires in 1 hour.";
        $mail->AltBody = "Hello {$user['name']},\n\nReset link: $resetLink\n\nExpires in 1 hour.";
        $mail->send();
        echo json_encode(['success' => true, 'message' => 'Reset link sent to your email.']);
        exit;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('Mail error: ' . $mail->ErrorInfo);
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Could not send email.']);
        exit;
    }
} catch (Throwable $e) {
    error_log('Forgot password error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error.']);
}
?>