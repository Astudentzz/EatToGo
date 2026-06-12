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
$reason = trim($data['reason'] ?? 'Your certificate could not be verified.');

if (!$app_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing application ID']);
    exit;
}

// Fetch application before deleting
$stmt = $pdo->prepare("SELECT * FROM owner_applications WHERE id = ? AND status = 'pending'");
$stmt->execute([$app_id]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$app) {
    http_response_code(404);
    echo json_encode(['error' => 'Application not found or already processed']);
    exit;
}

// Delete the application record (so email can be reused)
$stmt = $pdo->prepare("DELETE FROM owner_applications WHERE id = ?");
$stmt->execute([$app_id]);

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
    $mail->Subject = 'Your Restaurant Owner Application – EatToGo';
    $safeReason = nl2br(htmlspecialchars($reason, ENT_QUOTES, 'UTF-8'));
    $mail->Body    = "Dear {$app['name']},<br><br>
                      Thank you for your interest in becoming a restaurant owner on EatToGo.<br><br>
                      After reviewing your application and the submitted certificate, we regret to inform you that your application has been <strong>rejected</strong>.<br><br>
                      Reason given by admin:<br><em>$safeReason</em><br><br>
                      You may re‑apply with a corrected certificate at any time.<br><br>
                      — EatToGo Team";
    $mail->AltBody = "Dear {$app['name']},\n\nYour application was rejected.\nReason: $reason\n\nYou may re‑apply at any time.";
    $mail->send();
} catch (Exception $e) {
    error_log('Rejection email failed: ' . $mail->ErrorInfo);
}

echo json_encode(['success' => true]);
?>