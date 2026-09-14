<?php
require_once __DIR__ . '/auth.php';
$error = $_SESSION['signup_error'] ?? '';
unset($_SESSION['signup_error']);

if (admin_table_has_records($conn)) {
    $admin = current_admin();
    if (!$admin || ($admin['admin_role'] ?? '') !== 'quan_ly_tong') {
        $_SESSION['login_error'] = "He thong da co tai khoan quan tri. Chi Quan ly tong moi duoc tao them nhan vien.";
        header("Location: login.php");
        exit;
    }

    header("Location: employees.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng ký</title>
  <style>
    body { margin: 0; font-family: system-ui, sans-serif; background: #eee; }
    .container { width: 900px; margin: 60px auto; background: #fff; border-radius: 10px;
      overflow: hidden; display: flex; box-shadow: 0 0 30px rgba(0,0,0,0.2); }
    .left { flex: 1; background: #000; }
    .left img { width: 100%; height: 100%; object-fit: cover; }
    .right { flex: 1; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
    h2 { margin-bottom: 20px; }
    input { padding: 12px; width: 100%; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ccc; }
    button {
      background: #000; color: #fff; border: none; padding: 12px; border-radius: 8px;
      font-weight: bold; cursor: pointer; width: 100%;
    }
    .link { margin-top: 15px; text-align: center; font-size: 14px; }
  </style>
</head>
<body>
<div class="container">
  <div class="left"><img src="https://vatvostudio.vn/wp-content/uploads/2023/10/Flagship-2023.jpg" alt="Phone Image"></div>
  <div class="right">
    <h2>Đăng ký tài khoản</h2>
    
    <?php if (!empty($error)): ?>
  <div style="color:red; font-weight:bold; margin-bottom: 10px;">
    <?= $error ?>
  </div>
<?php endif; ?>

    <form action="signup_action.php" method="POST">
  <label for="username">Tên đăng nhập:</label><br>
  <input type="text" name="username" required><br><br>

  <label for="password">Mật khẩu:</label><br>
  <input type="password" name="password" required><br><br>

  <label for="confirm_password">Xác nhận mật khẩu:</label><br>
  <input type="password" name="confirm_password" required><br><br>

  <button type="submit">Đăng ký</button>
</form>

    <div class="link">Đã có tài khoản? <a href="login.php">Đăng nhập</a></div>
  </div>
</div>
</body>
</html>
    





