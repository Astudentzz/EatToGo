<?php
require_once __DIR__ . '/security.php';

function getDB() {
    $host = 'localhost';           // XAMPP default host
    $dbname = 'eattogo';   // keep the same database name (or change if needed)
    $user = 'root';                // XAMPP default username
    $pass = '';                    // XAMPP default password (empty)
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