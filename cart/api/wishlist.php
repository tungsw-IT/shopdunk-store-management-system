<?php
require_once __DIR__ . '/../../includes/api_cors.php';
require_once __DIR__ . '/../../includes/api_request.php';
require_once __DIR__ . '/../../config/api_db.php';
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
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->prepare(
            'SELECT w.imei_number, w.created_at, m.company_name, m.company_series, m.model_no, m.price
             FROM wishlist w
             JOIN mobile m ON w.imei_number = m.imei_number
             WHERE w.account_id = ?
             ORDER BY w.created_at DESC'
        );
        $stmt->execute([$accountId]);
        $wishlist = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $wishlist]);
        exit;
    }

    if ($method === 'POST') {
        $input = read_request_input();
        $imeiNumber = trim((string)($input['imei_number'] ?? ''));
        if ($imeiNumber === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'imei_number is required.']);
            exit;
        }

        $check = $pdo->prepare('SELECT 1 FROM wishlist WHERE account_id = ? AND imei_number = ? LIMIT 1');
        $check->execute([$accountId, $imeiNumber]);
        if (!$check->fetch()) {
            $insert = $pdo->prepare('INSERT INTO wishlist (account_id, imei_number) VALUES (?, ?)');
            $insert->execute([$accountId, $imeiNumber]);
        }

        echo json_encode(['success' => true, 'message' => 'Added to wishlist.']);
        exit;
    }

    if ($method === 'DELETE') {
        $input = read_request_input();
        $imeiNumber = trim((string)($input['imei_number'] ?? ''));
        if ($imeiNumber === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'imei_number is required.']);
            exit;
        }

        $delete = $pdo->prepare('DELETE FROM wishlist WHERE account_id = ? AND imei_number = ?');
        $delete->execute([$accountId, $imeiNumber]);
        echo json_encode(['success' => true, 'message' => 'Removed from wishlist.']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}






