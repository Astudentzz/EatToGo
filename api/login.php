<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

// Include SMTP configuration (same as forgot-password.php)
$smtpConfigPath = __DIR__ . '/../contact/config.php';
if (!file_exists($smtpConfigPath)) {
    // Fallback – log error but do not break login
    error_log('SMTP config missing for login email');
}
if (file_exists($smtpConfigPath)) {
    require_once $smtpConfigPath;
}

// Include PHPMailer (same as forgot-password.php)
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = readJsonBody();
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$role = $data['role'] ?? 'customer';
rateLimit('login_' . strtolower($email), 8, 300);

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'No account found with this email.']);
    exit;
}

// --- Email verification check (enabled) ---
if (!$user['email_verified']) {
    http_response_code(401);
    echo json_encode(['error' => 'Please verify your email before logging in. Check your inbox for the verification link.']);
    exit;
}
// -----------------------------------------

if (verifyPasswordAndUpgrade($pdo, $user, $password) && $user['role'] === $role) {
    session_regenerate_id(true);
    $safeUser = publicUser($user);
    $_SESSION['user'] = $safeUser;

    // --- Send login notification email (optional) ---
    if (defined('SMTP_HOST') && SMTP_HOST !== '') {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_ENCRYPTION;
            $mail->Port       = SMTP_PORT;

            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($user['email'], $user['name']);
            $mail->isHTML(true);
            $mail->Subject = 'New login to your EatToGo account';
            $safeName = htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8');
            $mail->Body    = "
                <p>Hello {$safeName},</p>
                <p>Your account was just used to log in at " . date('Y-m-d H:i:s') . ".</p>
                <p>If this was you, you can ignore this message. If you did not log in, please contact support.</p>
                <p>— EatToGo Team</p>
            ";
            $mail->AltBody = "Hello {$user['name']},\n\nYour account was just used to log in at " . date('Y-m-d H:i:s') . ".\nIf this was not you, please contact support.";
            $mail->send();
        } catch (Exception $e) {
            // Log error but do not prevent login
            error_log('Login notification email failed: ' . $mail->ErrorInfo);
        }
    }

    echo json_encode(['success' => true, 'user' => $safeUser]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials or role mismatch']);
}
?>
