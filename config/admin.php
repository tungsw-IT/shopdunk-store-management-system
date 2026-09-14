<?php
// Đảm bảo chỉ gọi session_start() khi cần
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kết nối cơ sở dữ liệu
$conn = mysqli_connect("localhost", "root", "", "db_shop_clean");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');
?>




