<?php
function applySecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: same-origin');
}

function startSecureSession() {
    if (session_status() === PHP_SESSION_ACTIVE) return;

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'secure' => $secure,
        'samesite' => 'Lax'
    ]);
    session_start();
}

function csrfToken() {
    startSecureSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function requireCsrfToken() {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) return;

    $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $expected = $_SESSION['csrf_token'] ?? '';
    if (!$sent || !$expected || !hash_equals($expected, $sent)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid security token. Please refresh and try again.']);
        exit;
    }
}

function rateLimit($key, $maxAttempts = 10, $windowSeconds = 300) {
    startSecureSession();
    $now = time();
    $recentAttempts = [];
    foreach ($_SESSION['rate_limits'][$key] ?? [] as $timestamp) {
        if ($timestamp > ($now - $windowSeconds)) {
            $recentAttempts[] = $timestamp;
        }
    }
    $_SESSION['rate_limits'][$key] = $recentAttempts;

    if (count($_SESSION['rate_limits'][$key]) >= $maxAttempts) {
        http_response_code(429);
        echo json_encode(['error' => 'Too many attempts. Please wait and try again.']);
        exit;
    }

    $_SESSION['rate_limits'][$key][] = $now;
}

function readJsonBody() {
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : [];
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPasswordAndUpgrade($pdo, $user, $password) {
    $hash = $user['password'] ?? '';
    if (password_verify($password, $hash)) {
        return true;
    }

    if (strlen($hash) === 32 && ctype_xdigit($hash) && hash_equals($hash, md5($password))) {
        $newHash = hashPassword($password);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$newHash, $user['id']]);
        return true;
    }

    return false;
}

function publicUser($user) {
    unset($user['password'], $user['verification_token'], $user['reset_token'], $user['token_expiry']);
    return $user;
}

function requireRole($roles) {
    startSecureSession();
    $roles = is_array($roles) ? $roles : [$roles];
    if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'] ?? '', $roles, true)) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    requireCsrfToken();
    return $_SESSION['user'];
}

function saveUploadedFile($file, $uploadDir, $publicDir, $allowedExtensions, $allowedMimeTypes, $maxBytes = 5242880) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('No file uploaded or upload error');
    }
    if ($file['size'] > $maxBytes) {
        throw new RuntimeException('File is too large');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions, true)) {
        throw new RuntimeException('Invalid file extension');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowedMimeTypes, true)) {
        throw new RuntimeException('Invalid file type');
    }

    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        throw new RuntimeException('Upload directory could not be created');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $destination = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Failed to save file');
    }

    return rtrim($publicDir, '/') . '/' . $filename;
}

applySecurityHeaders();
?>
