<?php include __DIR__ . '/verify_code_logic.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Xác minh mã - PulseTech</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Arial, sans-serif; background: #eee; }
    .container { display: flex; width: 100vw; height: 100vh; }
    .left { flex: 1; background: #000; }
    .left img { width: 100%; height: 100%; object-fit: cover; }
    .right {
      flex: 1;
      background: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .form-box {
      width: 100%;
      max-width: 400px;
      padding: 40px;
    }
    .form-box h2 {
      margin-bottom: 20px;
    }
    .form-box input, .form-box button {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }
    .form-box button {
      background: #000;
      color: #fff;
      font-weight: bold;
      border: none;
      cursor: pointer;
    }
    .form-box .error { color: red; }
    .form-box .links {
      text-align: center;
      margin-top: 10px;
      font-size: 14px;
    }
    .form-box .links a {
      text-decoration: none;
      color: #333;
    }
  </style>
</head>
<body>
<script>document.title = 'Xac minh ma - ShopDunk';</script>
<div class="container">
  <div class="left">
    <img src="https://techcrunch.com/wp-content/uploads/2024/09/apple-iphone-16-pro.jpg?resize=1200,675" alt="Verification Image">
  </div>
  <div class="right">
    <div class="form-box">
      <h2>Nhập mã xác thực</h2>
      <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
      <form method="post">
        <input type="text" name="code" placeholder="Mã xác thực" required />
        <button type="submit">Xác nhận</button>
      </form>
      <div class="links">
        <a href="forgot_password.php">Gửi lại mã</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>






