<?php
require_once __DIR__ . '/../../includes/api_cors.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/api_db.php';

session_start();

if (!isset($_SESSION['account_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required.']);
    exit;
}

$accountId = (int) $_SESSION['account_id'];
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only GET allowed.']);
    exit;
}

if (!$pdo) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'Database unavailable.']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT email, phone FROM accounts WHERE account_id = ? LIMIT 1');
    $stmt->execute([$accountId]);
    $account = $stmt->fetch();

    if (!$account) {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }

    $stmt = $pdo->prepare('SELECT * FROM paymentbill WHERE customer_email = ? OR customer_contact_no = ? ORDER BY pb_id DESC');
    $stmt->execute([$account['email'], $account['phone']]);
    $orders = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $orders]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to load orders.']);
}
?>





