<?php
require_once __DIR__ . '/auth.php';

$error = '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("SELECT * FROM admin WHERE admin_username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['admin_password'])) {
            if (($row['admin_status'] ?? 'active') === 'blocked') {
                $error = "Tai khoan nay dang bi khoa.";
            } else {
                $_SESSION['username'] = $username;
                header("Location: admin.php");
                exit;
            }
        } else {
            $error = "❌ Sai mật khẩu.";
        }
    } else {
            $error = "❌ Email không tồn tại.";
    }
}

$error = $_SESSION['login_error'] ?? $error;
unset($_SESSION['login_error']);
?>

<?php include __DIR__ . '/login.php'; ?>





