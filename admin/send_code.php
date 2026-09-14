<?php
session_start();
include __DIR__ . '/../config/admin.php';

$error = '';
$username = $_POST['admin_username'] ?? '';

if (empty($username)) {
    $error = "❌ Vui lòng nhập tên đăng nhập để tiếp tục!";
} else {
    $stmt = $conn->prepare("SELECT * FROM admin WHERE admin_username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $error = "❌ Tên đăng nhập không tồn tại trong hệ thống.";
    } else {
        $code = rand(100000, 999999);
        $_SESSION['code'] = $code;
        $_SESSION['admin_username_reset'] = $username;

        echo "<script>
            alert('Mã xác nhận của bạn là: $code');
            window.location.href = 'confirm.php';
        </script>";
        exit;
    }
}

include __DIR__ . '/forgot.php'; // Trả lại form với biến $error





