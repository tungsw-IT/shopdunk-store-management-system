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
$paymentMethod = trim((string)($input['payment_method'] ?? ''));

if ($paymentMethod === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Payment method is required.']);
    exit;
}

$_SESSION['payment_method'] = $paymentMethod;

echo json_encode(['success' => true, 'message' => 'Payment method saved.']);
?>





