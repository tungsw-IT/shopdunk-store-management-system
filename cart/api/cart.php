<?php
require_once __DIR__ . '/../../includes/api_cors.php';
require_once __DIR__ . '/../../includes/api_request.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/api_db.php';
require_once __DIR__ . '/../../products/product_images_helper.php';

session_start();

if (!isset($_SESSION['account_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required.']);
    exit;
}

$accountId = (int) $_SESSION['account_id'];
if (!$pdo) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'Database unavailable.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $stmt = $pdo->prepare('SELECT c.product_imei, c.quantity, m.company_name, m.company_series, m.model_no, m.price FROM cart c JOIN mobile m ON c.product_imei = m.imei_number WHERE c.account_id = ?');
        $stmt->execute([$accountId]);
        $items = $stmt->fetchAll();
        foreach ($items as &$item) {
            $item['imei_number'] = $item['product_imei'];
            $item['image_path'] = resolve_primary_product_image_pdo($pdo, $item, '');
        }
        unset($item);
        echo json_encode(['success' => true, 'data' => $items]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Unable to load cart.']);
    }
    exit;
}

$input = read_request_input();
$action = $input['action'] ?? 'add';
$productImei = trim((string)($input['product_imei'] ?? ''));
$quantity = isset($input['quantity']) ? max(1, (int)$input['quantity']) : 1;

if ($productImei === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'product_imei is required.']);
    exit;
}

try {
    if ($action === 'remove') {
        $stmt = $pdo->prepare('DELETE FROM cart WHERE account_id = ? AND product_imei = ?');
        $stmt->execute([$accountId, $productImei]);
    } elseif ($action === 'update') {
        $delta = (int)$quantity;
        $stmt = $pdo->prepare('SELECT quantity FROM cart WHERE account_id = ? AND product_imei = ?');
        $stmt->execute([$accountId, $productImei]);
        $current = (int)$stmt->fetchColumn();

        if ($current > 0) {
            $newQuantity = $current + $delta;
            if ($newQuantity > 0) {
                $stmt = $pdo->prepare('UPDATE cart SET quantity = ? WHERE account_id = ? AND product_imei = ?');
                $stmt->execute([$newQuantity, $accountId, $productImei]);
            } else {
                $stmt = $pdo->prepare('DELETE FROM cart WHERE account_id = ? AND product_imei = ?');
                $stmt->execute([$accountId, $productImei]);
            }
        } elseif ($delta > 0) {
            $stmt = $pdo->prepare('INSERT INTO cart (account_id, product_imei, quantity) VALUES (?, ?, ?)');
            $stmt->execute([$accountId, $productImei, $delta]);
        }
    } else {
        $stmt = $pdo->prepare('SELECT quantity FROM cart WHERE account_id = ? AND product_imei = ?');
        $stmt->execute([$accountId, $productImei]);
        $current = (int)$stmt->fetchColumn();

        if ($current > 0) {
            $stmt = $pdo->prepare('UPDATE cart SET quantity = quantity + ? WHERE account_id = ? AND product_imei = ?');
            $stmt->execute([$quantity, $accountId, $productImei]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO cart (account_id, product_imei, quantity) VALUES (?, ?, ?)');
            $stmt->execute([$accountId, $productImei, $quantity]);
        }
    }
    echo json_encode(['success' => true, 'message' => 'Cart updated.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to update cart.']);
}
?>





