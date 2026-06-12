<?php
header('Content-Type: application/json');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

requireCsrfToken();

$data = readJsonBody();
$reservation_id = (int)($data['reservation_id'] ?? 0);
if (!$reservation_id) {
    echo json_encode(['error' => 'Missing reservation ID']);
    exit;
}

// Get reservation details + restaurant + owner
$stmt = $pdo->prepare("
    SELECT r.id, r.restaurant_id, res.name as restaurant_name, res.owner_id, u.name as customer_name
    FROM reservations r
    JOIN restaurants res ON r.restaurant_id = res.id
    JOIN users u ON r.user_id = u.id
    WHERE r.id = ? AND r.user_id = ?
");
$stmt->execute([$reservation_id, $_SESSION['user']['id']]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$res) {
    echo json_encode(['error' => 'Invalid reservation']);
    exit;
}

// --- SMTP & PHPMailer (same pattern as forgot-password.php) ---
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

try {
    // Get all staff of this restaurant
    $stmt = $pdo->prepare("SELECT email, name FROM users WHERE restaurant_id = ? AND role = 'staff'");
    $stmt->execute([$res['restaurant_id']]);
    $staffList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get owner
    $stmt = $pdo->prepare("SELECT email, name FROM users WHERE id = ?");
    $stmt->execute([$res['owner_id']]);
    $owner = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($owner) $staffList[] = $owner;

    if (empty($staffList)) {
        echo json_encode(['success' => false, 'message' => 'No staff or owner found']);
        exit;
    }

    $customerName = $res['customer_name'];
    $restaurantName = $res['restaurant_name'];
    $submittedAt = date('Y-m-d H:i:s');

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_ENCRYPTION;
    $mail->Port       = SMTP_PORT;
    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
    $mail->isHTML(false);  // plain text, more reliable

    foreach ($staffList as $staff) {
        $mail->clearAddresses();
        $mail->addAddress($staff['email'], $staff['name']);
        $mail->Subject = 'New Payment Proof Submitted – EatToGo';
        $mail->Body    = "Dear {$staff['name']},\n\n"
                       . "A customer has submitted a payment proof for reservation at {$restaurantName}.\n\n"
                       . "Customer: {$customerName}\n"
                       . "Reservation ID: {$reservation_id}\n"
                       . "Submitted at: {$submittedAt}\n\n"
                       . "Please log in to the staff dashboard to verify the payment.\n\n"
                       . "— EatToGo System";
        $mail->send();
    }

    echo json_encode(['success' => true, 'message' => 'Notifications sent']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Mail error: ' . $mail->ErrorInfo]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>