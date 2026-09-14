<?php
session_start();
session_destroy(); // Hủy toàn bộ session
header("Location: ../index.php"); // Quay về trang chủ
exit;
?>






