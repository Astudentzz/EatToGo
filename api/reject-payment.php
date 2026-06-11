<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

// SMTP and PHPMailer setup
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

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
requireCsrfToken();

$data = readJsonBody();
$reservation_id = (int)($data['reservation_id'] ?? 0);
$reason = $data['reason'] ?? 'Payment proof could not be verified.';

if (!$reservation_id) {
    echo json_encode(['error' => 'Missing reservation ID']);
    exit;
}

$staff_restaurant_id = $_SESSION['user']['restaurant_id'];
$stmt = $pdo->prepare("
    SELECT r.id, r.user_id, u.email as customer_email, u.name as customer_name, res.name as restaurant_name
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN restaurants res ON r.restaurant_id = res.id
    WHERE r.id = ? AND r.restaurant_id = ?
");
$stmt->execute([$reservation_id, $staff_restaurant_id]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$reservation) {
    echo json_encode(['error' => 'Unauthorized or reservation not found']);
    exit;
}

// Update: set payment_verified = 0, status = 'rejected', and clear payment_proof
$stmt = $pdo->prepare("
    UPDATE reservations 
    SET payment_verified = 0, status = 'rejected', payment_proof = NULL
    WHERE id = ? AND restaurant_id = ?
");
$stmt->execute([$reservation_id, $staff_restaurant_id]);

// Send rejection email to customer
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_ENCRYPTION;
    $mail->Port       = SMTP_PORT;

    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
    $mail->addAddress($reservation['customer_email'], $reservation['customer_name']);
    $mail->isHTML(false);
    $mail->Subject = 'Payment verification failed – Reservation cancelled – EatToGo';

    $mail->Body = "Dear {$reservation['customer_name']},\n\n"
                . "Your payment proof for the reservation at {$reservation['restaurant_name']} has been REJECTED.\n"
                . "Reason: $reason\n\n"
                . "Unfortunately, your reservation has been cancelled. If you believe this is an error, please contact the restaurant directly.\n\n"
                . "You may make a new reservation on our website.\n\n"
                . "— EatToGo Team";

    $mail->send();
} catch (Exception $e) {
    error_log("Failed to send payment rejection email: " . $mail->ErrorInfo);
}

echo json_encode(['success' => true]);
?>
