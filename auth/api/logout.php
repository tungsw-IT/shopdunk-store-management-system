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

session_unset();
session_destroy();

echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
?>





