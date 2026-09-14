<?php
require_once __DIR__ . '/auth.php';
require_permission('customers');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Thực hiện xóa
    $stmt = $conn->prepare("DELETE FROM customer WHERE customer_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Sau khi xóa xong chuyển về danh sách, kèm thông báo popup (success=2 là xóa)
header('Location: customer.php?success=2');
exit;
?>





