<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config/database.php';
startSecureSession();
$pdo = getDB();

// Only staff can manage menu items
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$restaurant_id = $_SESSION['user']['restaurant_id'] ?? 0;
if (!$restaurant_id) {
    echo json_encode([]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE restaurant_id = ? AND is_available = 1");
    $stmt->execute([$restaurant_id]);
    $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($menu);
}
elseif ($method === 'POST') {
    requireCsrfToken();
    // Check if this is an update (FormData with _method=PUT or with an 'id' field)
    $isUpdate = false;
    $id = null;

    if (isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
        $isUpdate = true;
        $id = $_POST['id'] ?? 0;
    } elseif (isset($_POST['id'])) {
        $isUpdate = true;
        $id = $_POST['id'];
    }

    if ($isUpdate && $id) {
        // ---- UPDATE EXISTING ITEM ----
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $category = $_POST['category'] ?? '';
        $emoji = $_POST['emoji'] ?? '';
        $description = $_POST['description'] ?? '';

        // Handle image upload (replace old one)
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $imagePath = saveUploadedFile(
                    $_FILES['image'],
                    __DIR__ . '/../uploads/menu_items/',
                    '/uploads/menu_items',
                    ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
                );
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
                exit;
            }
        }

        // Build SQL dynamically if an image is provided
        if ($imagePath) {
            $stmt = $pdo->prepare("UPDATE menu_items SET name = ?, price = ?, category = ?, emoji = ?, description = ?, image = ? WHERE id = ? AND restaurant_id = ?");
            $stmt->execute([$name, $price, $category, $emoji, $description, $imagePath, $id, $restaurant_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE menu_items SET name = ?, price = ?, category = ?, emoji = ?, description = ? WHERE id = ? AND restaurant_id = ?");
            $stmt->execute([$name, $price, $category, $emoji, $description, $id, $restaurant_id]);
        }

        echo json_encode(['success' => true]);
    } else {
        // ---- ADD NEW ITEM ----
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $category = $_POST['category'] ?? '';
        $emoji = $_POST['emoji'] ?? '🍽️';
        $description = $_POST['description'] ?? '';
        $imagePath = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $imagePath = saveUploadedFile(
                    $_FILES['image'],
                    __DIR__ . '/../uploads/menu_items/',
                    '/uploads/menu_items',
                    ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
                );
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
                exit;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO menu_items (restaurant_id, name, price, category, emoji, image, description, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$restaurant_id, $name, $price, $category, $emoji, $imagePath, $description]);
        echo json_encode(['success' => true]);
    }
}
elseif ($method === 'DELETE') {
    requireCsrfToken();
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing item ID']);
        exit;
    }
    $stmt = $pdo->prepare("UPDATE menu_items SET is_available = 0 WHERE id = ? AND restaurant_id = ?");
    $stmt->execute([$id, $restaurant_id]);
    echo json_encode(['success' => true]);
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
