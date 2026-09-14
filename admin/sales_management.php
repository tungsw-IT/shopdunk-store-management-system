<?php
require_once __DIR__ . '/auth.php';
$currentAdmin = require_permission('sales_management');

$totalOrders = module_scalar($conn, "SELECT COUNT(*) FROM paymentbill");
$monthRevenue = module_scalar(
    $conn,
    "SELECT COALESCE(SUM(total_paid_amount), 0)
     FROM paymentbill
     WHERE STR_TO_DATE(pb_date, '%d-%m-%Y') IS NOT NULL
       AND YEAR(STR_TO_DATE(pb_date, '%d-%m-%Y')) = YEAR(CURDATE())
       AND MONTH(STR_TO_DATE(pb_date, '%d-%m-%Y')) = MONTH(CURDATE())"
);
$topBrands = mysqli_query(
    $conn,
    "SELECT purchase_mobile_company, COUNT(*) AS total_sold, COALESCE(SUM(total_paid_amount), 0) AS revenue
     FROM paymentbill
     GROUP BY purchase_mobile_company
     ORDER BY total_sold DESC, revenue DESC
     LIMIT 8"
);
require_once __DIR__ . '/sidebar.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý bán hàng</title>
  <style>
    body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif; background: #f4f7fb; }
    .main-content { margin-left: 220px; padding: 28px 36px; }
    .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
    .card { background: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 10px 30px rgba(15,23,42,.06); border: 1px solid #dbe6f4; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
    @media (max-width: 1100px) { .main-content { margin-left: 0; padding: 20px; } .stats { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <div class="main-content">
    <div class="stats">
      <div class="card"><h2><?= $totalOrders ?></h2><p>Tổng đơn bán</p></div>
      <div class="card"><h2><?= number_format((float) $monthRevenue, 0, ',', '.') ?> VND</h2><p>Doanh thu tháng này</p></div>
      <div class="card"><h2><?= module_scalar($conn, "SELECT COUNT(*) FROM customer") ?></h2><p>Khách hàng đang quản lý</p></div>
    </div>
    <div class="card">
      <h1>Quản lý bán hàng</h1>
      <p>Theo dõi nhanh hiệu quả bán hàng theo hãng và tổng quan đơn đã ghi nhận.</p>
      <table>
        <thead><tr><th>Thương hiệu</th><th>Số lượng đã bán</th><th>Doanh thu</th></tr></thead>
        <tbody>
          <?php if (!$topBrands): ?>
            <tr><td colspan="3">Không thể tải dữ liệu bán hàng.</td></tr>
          <?php else: ?>
            <?php while ($row = $topBrands->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars((string) $row['purchase_mobile_company']) ?></td>
                <td><?= (int) $row['total_sold'] ?></td>
                <td><?= number_format((float) $row['revenue'], 0, ',', '.') ?> VND</td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>





