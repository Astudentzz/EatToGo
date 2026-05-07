<?php
header('Content-Type: application/json');
require_once 'config/database.php';
$pdo = getDB();

$stmt = $pdo->query("SELECT id, name, category, image, location FROM restaurants WHERE status = 'approved'");
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($restaurants as &$restaurant) {
    if (!empty($restaurant['image'])) {
        $restaurant['image'] = ltrim($restaurant['image'], '/');
    }
}
echo json_encode($restaurants);
?>