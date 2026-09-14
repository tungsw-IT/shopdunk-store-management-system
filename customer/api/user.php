<?php
require_once __DIR__ . '/../../includes/api_cors.php';
require_once __DIR__ . '/../../config/api_db.php';
require_once __DIR__ . '/../../includes/api_request.php';
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['account_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required.']);
    exit;
}

if (!$pdo) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'Database unavailable.']);
    exit;
}

$accountId = (int) $_SESSION['account_id'];
try {
    $stmt = $pdo->prepare('SELECT account_id, accounts_name, email, phone FROM accounts WHERE account_id = ? LIMIT 1');
    $stmt->execute([$accountId]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Login required.']);
        exit;
    }

    $shippingInfo = $_SESSION['shipping_info'] ?? [];

    $wishlistStmt = $pdo->prepare(
        'SELECT w.imei_number, w.created_at, m.company_name, m.company_series, m.model_no, m.price
         FROM wishlist w
         JOIN mobile m ON w.imei_number = m.imei_number
         WHERE w.account_id = ?
         ORDER BY w.created_at DESC
         LIMIT 4'
    );
    $wishlistStmt->execute([$accountId]);
    $wishlist = $wishlistStmt->fetchAll();

    $purchaseStmt = $pdo->prepare(
        'SELECT DISTINCT pb.purchase_mobile_imei_no AS imei_number, m.company_name, m.company_series, m.model_no, m.price
         FROM paymentbill pb
         JOIN mobile m ON pb.purchase_mobile_imei_no = m.imei_number
         WHERE pb.customer_email = ? OR pb.customer_contact_no = ?
         LIMIT 4'
    );
    $purchaseStmt->execute([$user['email'], $user['phone']]);
    $purchased = $purchaseStmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => [
            'user' => $user,
            'shipping_info' => $shippingInfo,
            'wishlist' => $wishlist,
            'purchased' => $purchased,
        ],
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}






