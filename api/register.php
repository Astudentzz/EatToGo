<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

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

// Prepare verification link
$verificationLink = "http://localhost/EatToGo/api/verify-email.php?token=$token";

// PHPMailer paths (same as forgot-password.php)
$mailerPath = __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
$smtpPath   = __DIR__ . '/../lib/PHPMailer/SMTP.php';
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
        $mail->Username   = 'ngyueyang@graduate.utm.my';   // Use your actual email
        $mail->Password   = 'peow fsei esyu icpu';         // Use your app password
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('noreply@eattogo.com', 'EatToGo Support');
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
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('Verification email failed: ' . $mail->ErrorInfo);
        $emailSent = false;
    }
} else {
    // Fallback: log the verification link
    file_put_contents(__DIR__ . '/../verification_links.txt', date('Y-m-d H:i:s') . " - $email : $verificationLink\n", FILE_APPEND);
    $emailSent = false;
}

echo json_encode([
    'success' => true,
    'message' => $emailSent 
        ? 'Account created! Please check your email to verify your account.' 
        : 'Account created but verification email could not be sent. Please check your spam folder or contact support.'
]);
?>