<?php
require_once __DIR__ . '/auth.php';
require_permission('products');

if (isset($_GET['imei'])) {
    $imei = $_GET['imei'];

    // Xóa sản phẩm theo IMEI bằng mysqli
    $stmt = $conn->prepare("DELETE FROM mobile WHERE imei_number = ?");
    $stmt->bind_param("s", $imei);
    $stmt->execute();
    $stmt->close();
}

// Sau khi xóa, quay lại trang quản lý sản phẩm
header("Location: product.php");
exit;
?>





