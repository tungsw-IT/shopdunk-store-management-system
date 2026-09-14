<?php
require_once __DIR__ . '/../../includes/api_cors.php';
require_once __DIR__ . '/../../includes/api_request.php';
require_once __DIR__ . '/../../config/api_db.php';
header('Content-Type: application/json');
session_start();

$method = $_SERVER['REQUEST_METHOD'];

if (!$pdo) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'Database unavailable.']);
    exit;
}

try {
    if ($method === 'GET') {
        $modelNo = trim((string)($_GET['model_no'] ?? ''));
        $companySeries = trim((string)($_GET['company_series'] ?? ''));
        if ($modelNo === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'model_no is required.']);
            exit;
        }

        $stmt = $pdo->prepare(
            'SELECT r.rating, r.review_text, r.created_at, a.accounts_name
             FROM reviews r
             LEFT JOIN accounts a ON r.account_id = a.account_id
             WHERE r.model_no = ?' . ($companySeries !== '' ? ' AND r.company_series = ?' : '') . '
             ORDER BY r.created_at DESC'
        );
        if ($companySeries !== '') {
            $stmt->execute([$modelNo, $companySeries]);
        } else {
            $stmt->execute([$modelNo]);
        }

        $reviews = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $reviews]);
        exit;
    }

    if ($method === 'POST') {
        if (!isset($_SESSION['account_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Login required.']);
            exit;
        }

        $input = read_request_input();
        $modelNo = trim((string)($input['model_no'] ?? ''));
        $companySeries = trim((string)($input['company_series'] ?? ''));
        $rating = max(1, min(5, (int)($input['rating'] ?? 0)));
        $comment = trim((string)($input['comment'] ?? ''));

        if ($modelNo === '' || $rating < 1) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'model_no and rating are required.']);
            exit;
        }

        $insert = $pdo->prepare('INSERT INTO reviews (account_id, company_series, model_no, rating, review_text) VALUES (?, ?, ?, ?, ?)');
        $insert->execute([$_SESSION['account_id'], $companySeries, $modelNo, $rating, $comment !== '' ? $comment : null]);

        echo json_encode(['success' => true, 'message' => 'Review submitted.']);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}






