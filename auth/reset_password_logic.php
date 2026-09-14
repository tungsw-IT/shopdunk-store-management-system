<?php
include __DIR__ . '/../config/config.php';
session_start();

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"] ?? '';
    $confirm = $_POST["confirm_password"] ?? '';
    $email = $_SESSION["reset_email"] ?? '';

    if (empty($email)) {
        $error = "Email không tồn tại trong phiên.";
    } elseif ($password !== $confirm) {
        $error = "Mật khẩu xác nhận không khớp.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{6,}$/', $password)) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự, bao gồm ít nhất một chữ hoa, một chữ thường và một chữ số. Không được chứa ký tự đặc biệt.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE accounts SET password=?, reset_code=NULL WHERE email=?");
        $stmt->bind_param("ss", $hashed, $email);
        if ($stmt->execute()) {
            session_destroy();
            header("Location: login.php");
            exit();
        } else {
            $error = "Đặt lại mật khẩu thất bại: " . $stmt->error;
        }
    }
}
?>






