<?php
require_once __DIR__ . '/../../includes/api_cors.php';
require_once __DIR__ . '/../../config/api_db.php';

header('Content-Type: application/json');
$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'httponly' => true,
    'samesite' => $secure ? 'None' : 'Lax',
    'secure' => $secure,
    'path' => '/',
]);
session_start();

if (!isset($_SESSION['account_id']) || !$pdo) {
    if (!$pdo) {
        http_response_code(503);
        echo json_encode(['success' => false, 'message' => 'Database unavailable.']);
        exit;
    }

    echo json_encode(['success' => true, 'logged_in' => false]);
    exit;
}

$accountId = (int) $_SESSION['account_id'];
try {
    $stmt = $pdo->prepare('SELECT account_id, accounts_name, email, phone FROM accounts WHERE account_id = ? LIMIT 1');
    $stmt->execute([$accountId]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        echo json_encode(['success' => true, 'logged_in' => false]);
        exit;
    }

    echo json_encode(['success' => true, 'logged_in' => true, 'data' => $user]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}






