<?php
session_start();
include __DIR__ . '/../config/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['account_id'])) {
    header('Location: ../auth/login.php?wishlist=1');
    exit;
}

$accountId = $_SESSION['account_id'];
$imeiNumber = $_POST['imei_number'] ?? '';   // Đổi biến cho đúng tên trường gửi lên form

if ($imeiNumber) {
    // Kiểm tra đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT 1 FROM wishlist WHERE account_id = ? AND imei_number = ?");
    $stmt->execute([$accountId, $imeiNumber]);
    if (!$stmt->fetch()) {
        // Chưa có thì thêm mới
        $stmtAdd = $pdo->prepare("INSERT INTO wishlist (account_id, imei_number) VALUES (?, ?)");
        $stmtAdd->execute([$accountId, $imeiNumber]);
        $msg = 'added';
    } else {
        $msg = 'exists';
    }
    // Quay về trang trước, kèm thông báo
    $url = $_SERVER['HTTP_REFERER'] ?? '../index.php';
    if (strpos($url, '?') !== false) {
        $url .= '&wishlist_msg=' . $msg;
    } else {
        $url .= '?wishlist_msg=' . $msg;
    }
    header('Location: ' . $url);
    exit;
} else {
    // Nếu thiếu dữ liệu, trở về trang trước hoặc index
    $url = $_SERVER['HTTP_REFERER'] ?? '../index.php';
    header('Location: ' . $url);
    exit;
}
?>






