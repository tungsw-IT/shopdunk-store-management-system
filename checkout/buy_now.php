<?php
session_start();
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['account_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$account_id = $_SESSION['account_id'];
$imei = $_POST['imei_number'] ?? $_GET['imei_number'] ?? '';
if (!$imei) {
    header('Location: ../index.php');
    exit;
}

// Kiểm tra sản phẩm theo imei_number
$stmt = $pdo->prepare("SELECT * FROM mobile WHERE imei_number = ?");
$stmt->execute([$imei]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    header('Location: ../index.php');
    exit;
}

// Kiểm tra đã có trong giỏ chưa
$stmt = $pdo->prepare("SELECT quantity FROM cart WHERE account_id = ? AND product_imei = ?");
$stmt->execute([$account_id, $imei]);
$exists = $stmt->fetchColumn();

if ($exists) {
    // Nếu đã có, tăng số lượng lên 1
    $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE account_id = ? AND product_imei = ?");
    $stmt->execute([$account_id, $imei]);
} else {
    // Nếu chưa có, thêm vào giỏ
    $stmt = $pdo->prepare("INSERT INTO cart (account_id, product_imei, quantity) VALUES (?, ?, 1)");
    $stmt->execute([$account_id, $imei]);
}

// Chuyển sang bước nhập địa chỉ
header("Location: ../customer/address.php");
exit;
?>






