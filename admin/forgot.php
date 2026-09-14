<?php if (!isset($error)) $error = ''; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quên mật khẩu</title>
  <style>
    body {
      margin: 0;
      font-family: system-ui, sans-serif;
      background: #eee;
    }
    .container {
      width: 900px;
      margin: 60px auto;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      display: flex;
      box-shadow: 0 0 30px rgba(0,0,0,0.2);
    }
    .left {
      flex: 1;
      background: #000;
    }
    .left img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .right {
      flex: 1;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    h2 {
      margin-bottom: 20px;
    }
    .error-message {
      color: red;
      background: #ffeaea;
      padding: 12px;
      border-radius: 8px;
      font-weight: bold;
      margin-bottom: 15px;
    }
    input {
      padding: 12px;
      width: 100%;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #000;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background: #333;
    }
    .bottom {
      margin-top: 20px;
      text-align: center;
    }
    .bottom a {
      color: purple;
      text-decoration: none;
    }
    .bottom a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left">
      <img src="https://vatvostudio.vn/wp-content/uploads/2023/10/Flagship-2023.jpg" alt="banner">
    </div>
    <div class="right">
      <h2>Quên mật khẩu</h2>

      <?php if (!empty($error)): ?>
        <div class="error-message"><i class="fa fa-times-circle"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" action="send_code.php">
        <input type="text" name="admin_username" placeholder="Tên đăng nhập" required>
        <button type="submit">Gửi mã xác nhận</button>
      </form>

      <div class="bottom">
        Đã nhớ mật khẩu? <a href="login.php">Đăng nhập</a>
      </div>
    </div>
  </div>
</body>
</html>





