<?php
require_once __DIR__ . '/../../includes/api_cors.php';
require_once __DIR__ . '/../../includes/api_request.php';
require_once __DIR__ . '/../../config/api_db.php';
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST allowed.']);
    exit;
}

if (!isset($_SESSION['account_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required.']);
    exit;
}

$input = read_request_input();
$accountId = (int) $_SESSION['account_id'];
$name = trim((string)($input['accounts_name'] ?? ''));
$email = trim((string)($input['email'] ?? ''));
$phone = trim((string)($input['phone'] ?? ''));
$gender = trim((string)($input['gender'] ?? ''));
$countryCode = trim((string)($input['country_code'] ?? '+84'));
$country = trim((string)($input['country'] ?? 'Viet Nam'));
$city = trim((string)($input['city'] ?? 'TP. Hồ Chí Minh'));
$detail = trim((string)($input['detail'] ?? ''));

if ($name === '' || $email === '' || $phone === '' || $gender === '' || $detail === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email.']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT account_id FROM accounts WHERE (email = ? OR phone = ?) AND account_id != ?');
    $stmt->execute([$email, $phone, $accountId]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email hoặc số điện thoại đã được sử dụng.']);
        exit;
    }

    $update = $pdo->prepare('UPDATE accounts SET accounts_name = ?, email = ?, phone = ? WHERE account_id = ?');
    $update->execute([$name, $email, $phone, $accountId]);

    $_SESSION['shipping_info'] = [
        'fullname' => $name,
        'country_code' => $countryCode,
        'phone' => $phone,
        'gender' => $gender,
        'email' => $email,
        'country' => $country,
        'city' => $city,
        'detail' => $detail,
    ];

    echo json_encode(['success' => true, 'message' => 'Profile updated.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}






