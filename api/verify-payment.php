<?php
header('Content-Type: application/json');
session_start();
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

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$reservation_id = $data['reservation_id'] ?? 0;
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

// Update: set payment_verified = 1 and status = 'confirmed'
$stmt = $pdo->prepare("
    UPDATE reservations 
    SET payment_verified = 1, status = 'confirmed' 
    WHERE id = ?
");
$stmt->execute([$reservation_id]);

// Send email to customer
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
    $mail->isHTML(true);
    $mail->Subject = 'Payment verified – Your reservation is confirmed! – EatToGo';

    $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #28a745;'>Payment Verified ✅</h2>
            <p>Dear {$reservation['customer_name']},</p>
            <p>Your payment for the reservation at <strong>{$reservation['restaurant_name']}</strong> has been <strong>verified</strong>.</p>
            <p>Your booking is now <strong style='color: #28a745;'>fully confirmed</strong>. We look forward to serving you!</p>
            <p>If you have any questions, please contact the restaurant directly.</p>
            <p>— EatToGo Team</p>
        </div>
    ";
    $mail->AltBody = "Payment verified! Your reservation at {$reservation['restaurant_name']} is now confirmed. Thank you for using EatToGo.";

    $mail->send();
} catch (Exception $e) {
    error_log("Failed to send payment verification email: " . $mail->ErrorInfo);
    // Do not block the API response
}

echo json_encode(['success' => true]);
?>