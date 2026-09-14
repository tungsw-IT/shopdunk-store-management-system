<?php
require_once __DIR__ . '/../../includes/api_cors.php';
require_once __DIR__ . '/../../includes/api_request.php';
require_once __DIR__ . '/../../config/api_db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST allowed.']);
    exit;
}

$input = read_request_input();
$email = trim((string)(@$input['email'] ?? ''));
$password = (string)(@$input['password'] ?? '');

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email.']);
    exit;
}

try {
    if (!$pdo) {
        throw new Exception('Database unavailable');
    }
    $stmt = $pdo->prepare('SELECT account_id, accounts_name, email, phone, password FROM accounts WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
        exit;
    }

    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => $secure ? 'None' : 'Lax',
        'secure' => $secure,
        'path' => '/',
    ]);
    session_start();
    session_regenerate_id(true);
    $_SESSION['account_id'] = $user['account_id'];
    $_SESSION['accounts_name'] = $user['accounts_name'];

    echo json_encode(['success' => true, 'message' => 'Login success.', 'data' => ['account_id' => $user['account_id'], 'accounts_name' => $user['accounts_name']]]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}
?>





