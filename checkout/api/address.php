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
$fullname = trim((string)($input['fullname'] ?? ''));
$phone = trim((string)($input['phone'] ?? ''));
$gender = trim((string)($input['gender'] ?? ''));
$email = trim((string)($input['email'] ?? ''));
$countryCode = trim((string)($input['country_code'] ?? '+84'));
$country = trim((string)($input['country'] ?? 'Viet Nam'));
$city = trim((string)($input['city'] ?? 'TP. Hồ Chí Minh'));
$detail = trim((string)($input['detail'] ?? ''));

if ($fullname === '' || $phone === '' || $gender === '' || $email === '' || $detail === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing address details.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email.']);
    exit;
}

$_SESSION['shipping_info'] = [
    'fullname' => $fullname,
    'country_code' => $countryCode,
    'phone' => $phone,
    'gender' => $gender,
    'email' => $email,
    'country' => $country,
    'city' => $city,
    'detail' => $detail,
];

echo json_encode(['success' => true, 'message' => 'Address saved.']);
?>





