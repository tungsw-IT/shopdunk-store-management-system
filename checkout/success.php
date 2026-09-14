<?php
session_start();
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['account_id'])) {
    header('Location: ../cart/cart.php');
    exit;
}

$orderId = $_SESSION['last_order_id'] ?? null;
$shipping = $_SESSION['shipping_info'] ?? [];
$payment_method = $_SESSION['order_payment_method'] ?? $_SESSION['payment_method'] ?? '';

if (!$orderId) {
    header('Location: ../cart/cart.php');
    exit;
}

$fullname = $shipping['fullname'] ?? '';
$phone = $shipping['phone'] ?? '';
$address = ($shipping['detail'] ?? '') . ', ' . ($shipping['city'] ?? '') . ', ' . ($shipping['country'] ?? '');
$gender = $shipping['gender'] ?? '';
$email = $shipping['email'] ?? '';

unset($_SESSION['shipping_info'], $_SESSION['payment_method']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Thành công</title>
  <link rel="stylesheet" href="../assets/css/style_cart.css">
  <style>
    .success-main { max-width: 420px; margin: 40px auto 0; padding: 30px 24px; background: #fff; border-radius: 18px; box-shadow: 0 4px 16px #0001; text-align: center; animation: fadeIn 0.7s; }
    .success-text { font-size: 19px; margin-bottom: 18px; color: #228B22; }
    .success-image img { width: 120px; margin-bottom: 20px; border-radius: 50%; box-shadow: 0 2px 8px #0001; }
    .success-actions { display: flex; flex-direction: column; gap: 14px; align-items: center; margin-top: 10px; }
    .btn-outline { background: #fff; border: 2px solid #1769aa; color: #1769aa; padding: 10px 26px; font-weight: 600; border-radius: 10px; cursor: pointer; transition: background .2s; }
    .btn-outline:hover { background: #f2f7fa; }
    .btn-primary, .btn.checkout { padding: 12px 36px; border-radius: 10px; background: #1769aa; color: #fff; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: background .2s; }
    @media (max-width: 600px) { .success-main { max-width: 100%; padding: 18px 8px; } .success-image img { width: 90px; } }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(18px);} to { opacity: 1; transform: none;} }
  </style>
</head>
<body>
  <header class="main-header">
    <a href="../index.php" class="btn-back">&larr;</a>
    <h1>Thành công</h1>
  </header>
  <main class="success-main">
    <p class="success-text">
      Đơn hàng <strong>#<?= htmlspecialchars($orderId) ?></strong> của bạn đã được đặt thành công!
    </p>
    <div class="success-image">
      <img src="../images/success.jpg" alt="Order Success">
    </div>
    <div class="success-actions">
      <form action="cancel.php" method="post" class="action-form" style="margin:0;">
        <button type="submit" class="btn-outline">HỦY ĐƠN HÀNG</button>
      </form>
      <a href="../index.php" class="btn-primary">TIẾP TỤC MUA SẮM</a>
    </div>
  </main>
</body>
</html>






