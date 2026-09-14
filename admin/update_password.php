<?php
session_start();
include __DIR__ . '/../config/admin.php';

$new = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

// Kiểm tra xác nhận mật khẩu
if ($new != $confirm) {
    echo "<script>
        alert('Mật khẩu không khớp. Vui lòng thử lại.');
        window.location.href = 'reset.php';
    </script>";
    exit();
}

// Kiểm tra định dạng mật khẩu:
// - ít nhất 6 ký tự
// - ít nhất một chữ hoa
// - ít nhất một chữ thường
// - ít nhất một chữ số
// - không chứa ký tự đặc biệt
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{6,}$/', $new)) {
    echo "<script>
        alert('Mật khẩu phải có ít nhất 6 ký tự, bao gồm ít nhất một chữ hoa, một chữ thường và một chữ số. Không được chứa ký tự đặc biệt.');
        window.location.href = 'reset.php';
    </script>";
    exit();
}

$username = $_SESSION['admin_username_reset'] ?? '';

if (empty($username)) {
    die("Không có tên đăng nhập trong session.");
}

$hashed = password_hash($new, PASSWORD_DEFAULT);

$sql = "UPDATE admin SET admin_password='$hashed' WHERE admin_username='$username'";

if (!mysqli_query($conn, $sql)) {
    die("Lỗi SQL: " . mysqli_error($conn) . "<br>Câu lệnh: $sql");
}

unset($_SESSION['admin_username_reset']);
unset($_SESSION['code']);

echo "<script>
    alert('Đặt lại mật khẩu thành công!');
    window.location.href = 'login.php';
</script>";
?>





