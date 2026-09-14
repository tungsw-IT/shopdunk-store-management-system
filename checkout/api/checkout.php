<?php
require_once __DIR__ . '/../../includes/api_cors.php';
require_once __DIR__ . '/../../includes/api_request.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/api_db.php';

session_start();

if (!isset($_SESSION['account_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required.']);
    exit;
}

$accountId = (int) $_SESSION['account_id'];
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST allowed.']);
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
$paymentMethod = trim((string)($input['payment_method'] ?? ''));

$sessionShipping = $_SESSION['shipping_info'] ?? null;
$sessionPaymentMethod = $_SESSION['payment_method'] ?? null;

if ($fullname === '' || $phone === '' || $gender === '' || $email === '' || $detail === '' || $paymentMethod === '') {
    if ($sessionShipping && $sessionPaymentMethod) {
        $fullname = $sessionShipping['fullname'] ?? $fullname;
        $phone = $sessionShipping['phone'] ?? $phone;
        $gender = $sessionShipping['gender'] ?? $gender;
        $email = $sessionShipping['email'] ?? $email;
        $countryCode = $sessionShipping['country_code'] ?? $countryCode;
        $country = $sessionShipping['country'] ?? $country;
        $city = $sessionShipping['city'] ?? $city;
        $detail = $sessionShipping['detail'] ?? $detail;
        $paymentMethod = $sessionPaymentMethod;
    }
}

if ($fullname === '' || $phone === '' || $gender === '' || $email === '' || $detail === '' || $paymentMethod === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing checkout details.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email.']);
    exit;
}

try {
    if (!$pdo) {
        throw new Exception('Database unavailable');
    }

    $stmt = $pdo->prepare('SELECT c.product_imei, c.quantity, m.company_name, m.company_series, m.model_no, m.price FROM cart c JOIN mobile m ON c.product_imei = m.imei_number WHERE c.account_id = ?');
    $stmt->execute([$accountId]);
    $items = $stmt->fetchAll();

    if (!$items) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
        exit;
    }

    $orderId = 'VN' . time() . rand(1000, 9999);
    foreach ($items as $item) {
        for ($i = 0; $i < $item['quantity']; $i++) {
            $insert = $pdo->prepare('INSERT INTO paymentbill (pb_date, customer_id, customer_name, customer_contact_no, purchase_mobile_imei_no, purchase_mobile_company, purchase_mobile_series, purchase_mobile_model, purchase_mobile_price, total_paid_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $insert->execute([date('Y-m-d'), $accountId, $fullname, $phone, $item['product_imei'], $item['company_name'], $item['company_series'], $item['model_no'], $item['price'], $item['price'], 'pending']);
        }
    }

    $_SESSION['shipping_info'] = [
        'fullname' => $fullname,
        'country_code' => $countryCode,
        'phone' => $phone,
        'country' => $country,
        'city' => $city,
        'detail' => $detail,
        'gender' => $gender,
        'email' => $email,
    ];
    $_SESSION['last_order_id'] = $orderId;
    $_SESSION['order_payment_method'] = $paymentMethod;

    $delete = $pdo->prepare('DELETE FROM cart WHERE account_id = ?');
    $delete->execute([$accountId]);

    echo json_encode(['success' => true, 'message' => 'Checkout completed.', 'order_id' => $orderId]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}
?>





