<?php
require_once __DIR__ . '/auth.php';
$currentAdmin = require_login();
require_once __DIR__ . '/sidebar.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Khong co quyen truy cap</title>
  <style>
    body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif; background: #f4f7fb; }
    .main-content { margin-left: 220px; padding: 40px; }
    .card {
      max-width: 720px;
      background: #fff;
      border-radius: 18px;
      padding: 32px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
      border: 1px solid #dbe6f4;
    }
    h1 { margin-top: 0; color: #0f172a; }
    p { color: #475569; line-height: 1.6; }
    a {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 18px;
      border-radius: 10px;
      background: #2563eb;
      color: #fff;
      text-decoration: none;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <div class="main-content">
    <div class="card">
      <h1>Ban khong co quyen truy cap chuc nang nay</h1>
      <p>Tai khoan hien tai khong duoc cap quyen cho trang ban vua mo. Neu can thao tac nay, hay dang nhap bang tai khoan co vai tro phu hop hoac nho Quan ly tong cap lai quyen.</p>
      <a href="admin.php">Quay ve trang tong quan</a>
    </div>
  </div>
</body>
</html>





