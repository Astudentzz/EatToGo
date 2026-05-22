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

    $mailerPath = __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
    $smtpPath = __DIR__ . '/../lib/PHPMailer/SMTP.php';
    $exceptionPath = __DIR__ . '/../lib/PHPMailer/Exception.php';

    if (file_exists($mailerPath) && file_exists($smtpPath) && file_exists($exceptionPath)) {
        require_once $mailerPath;
        require_once $smtpPath;
        require_once $exceptionPath;

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ngyueyang@graduate.utm.my';
            $mail->Password   = 'hrhr phds lwjg dvdw';
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
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
    }

    echo json_encode([
        'success' => true,
        'message' => 'Reset link generated. Email sending is not configured.',
        'reset_link' => $resetLink
    ]);
} catch (Throwable $e) {
    error_log('Forgot password error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error.']);
}
?>