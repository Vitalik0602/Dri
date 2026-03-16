<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

// CSRF check
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Invalid token']);
    exit;
}

$name  = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$msg   = trim($_POST['message'] ?? '');
$car_id = (int)($_POST['car_id'] ?? 0) ?: null;

if (empty($name) || empty($phone)) {
    echo json_encode(['ok' => false, 'error' => 'Заполните имя и телефон']);
    exit;
}

// Rate limiting (один запрос в 60 сек с IP)
$rate_key = 'req_' . md5($_SERVER['REMOTE_ADDR']);
if (!empty($_SESSION[$rate_key]) && time() - $_SESSION[$rate_key] < 60) {
    echo json_encode(['ok' => false, 'error' => 'Подождите немного перед следующей заявкой']);
    exit;
}
$_SESSION[$rate_key] = time();

$ok = save_request([
    'car_id'  => $car_id,
    'name'    => mb_substr($name, 0, 120),
    'phone'   => mb_substr($phone, 0, 30),
    'message' => mb_substr($msg, 0, 1000),
]);

if ($ok) {
    // Обновляем CSRF-токен
    unset($_SESSION['csrf_token']);
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Ошибка базы данных']);
}
