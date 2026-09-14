<?php
require_once __DIR__ . '/../../includes/api_cors.php';
require_once __DIR__ . '/../../config/api_db.php';
require_once __DIR__ . '/../../products/product_images_helper.php';
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

try {
    $accountId = (int) $_SESSION['account_id'];

    $stmt = $pdo->prepare(
        'SELECT c.quantity, m.company_name, m.company_series, m.model_no, m.price, m.imei_number
         FROM cart c
         JOIN mobile m ON c.product_imei = m.imei_number
         WHERE c.account_id = ?'
    );
    $stmt->execute([$accountId]);
    $cartItems = $stmt->fetchAll();

    $subTotal = 0;
    foreach ($cartItems as &$item) {
        $item['image_path'] = resolve_primary_product_image_pdo($pdo, $item, '');
        $item['quantity'] = (int)$item['quantity'];
        $item['price'] = (float)$item['price'];
        $item['line_total'] = $item['quantity'] * $item['price'];
        $subTotal += $item['line_total'];
    }
    unset($item);

    $shippingInfo = $_SESSION['shipping_info'] ?? null;
    $paymentMethod = $_SESSION['payment_method'] ?? null;
    $appliedPromo = $_SESSION['applied_promo'] ?? null;
    $discountAmount = 0;
    $promoDesc = '';

    if ($appliedPromo) {
        $promoStmt = $pdo->prepare('SELECT * FROM promotions WHERE promo_code = ? LIMIT 1');
        $promoStmt->execute([$appliedPromo]);
        $promo = $promoStmt->fetch();
        if ($promo) {
            $promoDesc = $promo['description'] ?? $promo['promo_code'];
            if ($promo['discount_type'] === 'fixed') {
                $discountAmount = min((float)$promo['discount_value'], $subTotal);
            } elseif ($promo['discount_type'] === 'percent') {
                $discountAmount = round($subTotal * (float)$promo['discount_value'] / 100, 0);
            }
        }
    }

    $shippingFee = 0;
    $total = max(0, $subTotal - $discountAmount + $shippingFee);

    echo json_encode([
        'success' => true,
        'data' => [
            'cart_items' => $cartItems,
            'subtotal' => $subTotal,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discountAmount,
            'promo_description' => $promoDesc,
            'total' => $total,
            'shipping_info' => $shippingInfo,
            'payment_method' => $paymentMethod,
        ],
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to load checkout summary.']);
}






