<?php
require_once __DIR__ . '/security.php';

function getDB() {
    $host = 'localhost';
    $dbname = 'eattogo_db';   // use your local database name
    $user = 'root';                         // default XAMPP username
    $pass = '';                             // default XAMPP password is empty
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
