<?php
require_once __DIR__ . '/auth.php';
require_permission('orders');

// Lấy id từ URL
$pb_id = $_GET['id'] ?? '';
if (!$pb_id) {
    header("Location: order.php");
    exit;
}

// Xóa đơn hàng theo pb_id
$stmt = $conn->prepare("DELETE FROM paymentbill WHERE pb_id = ?");
$stmt->bind_param("i", $pb_id);
if ($stmt->execute()) {
    // Xóa thành công, chuyển về trang order với thông báo
    header("Location: order.php?msg=deleted");
    exit;
} else {
    // Lỗi khi xóa
    echo "Lỗi khi xóa đơn hàng: " . $stmt->error;
}
$stmt->close();
?>





