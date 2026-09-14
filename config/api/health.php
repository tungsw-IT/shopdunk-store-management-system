<?php
require_once __DIR__ . '/../../includes/api_cors.php';
header('Content-Type: application/json');

echo json_encode(['success' => true, 'message' => 'API is online']);
?>





