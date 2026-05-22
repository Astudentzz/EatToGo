<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

$stmt = $pdo->query("SELECT id, name, category, image, location FROM restaurants WHERE status = 'approved'");
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($restaurants);
?>