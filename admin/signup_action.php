<?php
require_once __DIR__ . '/auth.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($password !== $confirm_password) {
    $_SESSION['signup_error'] = "❌ Mật khẩu xác nhận không khớp.";
    header("Location: signup.php");
    exit;
}

if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{6,}$/', $password)) {
    $_SESSION['signup_error'] = "❌ Mật khẩu phải có ít nhất 6 ký tự, bao gồm chữ hoa, chữ thường và số. Không chứa ký tự đặc biệt.";
    header("Location: signup.php");
    exit;
}

// Kiểm tra tên trùng
$stmtCheck = $conn->prepare("SELECT * FROM admin WHERE admin_username = ?");
$stmtCheck->bind_param("s", $username);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    $_SESSION['signup_error'] = "❌ Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
    header("Location: signup.php");
    exit;
}

$existingAdmins = admin_table_has_records($conn);
if ($existingAdmins) {
    $currentAdmin = current_admin();
    if (!$currentAdmin || ($currentAdmin['admin_role'] ?? '') !== 'quan_ly_tong') {
        $_SESSION['signup_error'] = "Chi Quan ly tong moi duoc tao them tai khoan nhan vien.";
        header("Location: login.php");
        exit;
    }
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$role = $existingAdmins ? 'quan_ly' : 'quan_ly_tong';
$status = 'active';
$stmt = $conn->prepare("INSERT INTO admin (admin_username, admin_password, admin_role, admin_status) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $hashedPassword, $role, $status);

if ($stmt->execute()) {
    header("Location: login.php");
    exit;
} else {
    $_SESSION['signup_error'] = "❌ Lỗi đăng ký: " . $conn->error;
    header("Location: signup.php");
    exit;
}
?>





