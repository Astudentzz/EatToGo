<?php
require_once __DIR__ . '/security.php';

function getDB() {
    $host = 'sql210.infinityfree.com';
    $dbname = 'if0_42158944_eattogo';   // use your local database name
    $user = 'if0_42158944';                         // default XAMPP username
    $pass = 'L87vr063xhvL';                             // default XAMPP password is empty
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        http_response_code(500);
        error_log('DB connection failed: ' . $e->getMessage());
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
}
?>
