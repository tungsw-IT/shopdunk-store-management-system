<?php
$host = 'localhost';
$db   = 'db_shop_clean';
$user = 'root';
$pass = ''; // Thường để trống nếu dùng XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Lỗi kết nối CSDL: " . $e->getMessage();
    exit;
}
?>






