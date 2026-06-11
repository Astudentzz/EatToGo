<?php
header('Content-Type: application/json');
require_once 'config/security.php';
startSecureSession();
if (isset($_SESSION['user'])) {
    echo json_encode(['loggedIn' => true, 'user' => publicUser($_SESSION['user']), 'csrfToken' => csrfToken()]);
} else {
    echo json_encode(['loggedIn' => false, 'csrfToken' => csrfToken()]);
}
?>
