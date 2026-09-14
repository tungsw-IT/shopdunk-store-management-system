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
$phone = trim((string)(@$input['phone'] ?? ''));
$password = (string)(@$input['password'] ?? '');
$confirm = (string)(@$input['confirm_password'] ?? '');

if ($email === '' || $phone === '' || $password === '' || $confirm === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email.']);
    exit;
}

if (!preg_match('/^0\d{9}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid phone.']);
    exit;
}

if ($password !== $confirm) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password confirmation mismatch.']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password is too short.']);
    exit;
}

try {
    if (!$pdo) {
        throw new Exception('Database unavailable');
    }
    $stmt = $pdo->prepare('SELECT 1 FROM accounts WHERE email = ? OR phone = ? LIMIT 1');
    $stmt->execute([$email, $phone]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email or phone exists.']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO accounts (email, phone, password) VALUES (?, ?, ?)');
    $insert->execute([$email, $phone, $hash]);

    echo json_encode(['success' => true, 'message' => 'Register success.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}
?>





