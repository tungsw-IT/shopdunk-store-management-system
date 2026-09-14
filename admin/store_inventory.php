<?php
require_once __DIR__ . '/auth.php';
$currentAdmin = require_permission('store_inventory');

$stockColumnCheck = mysqli_query($conn, "SHOW COLUMNS FROM mobile LIKE 'stock_quantity'");
if (!$stockColumnCheck || mysqli_num_rows($stockColumnCheck) === 0) {
    mysqli_query($conn, "ALTER TABLE mobile ADD COLUMN stock_quantity INT NOT NULL DEFAULT 0");
}

$inventoryRows = mysqli_query(
    $conn,
    "SELECT company_name, company_series, model_no, SUM(COALESCE(stock_quantity, 0)) AS stock_count, MIN(price) AS base_price
     FROM mobile
     GROUP BY company_name, company_series, model_no
     ORDER BY stock_count DESC, company_name ASC, model_no ASC"
);
require_once __DIR__ . '/sidebar.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Tồn kho tại chỗ</title>
  <style>
    body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif; background: #f4f7fb; }
    .main-content { margin-left: 220px; padding: 28px 36px; }
    .card { background: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 10px 30px rgba(15,23,42,.06); border: 1px solid #dbe6f4; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
    @media (max-width: 1100px) { .main-content { margin-left: 0; padding: 20px; } }
  </style>
</head>
<body>
  <div class="main-content">
    <div class="card">
      <h1>Tồn kho tại chỗ</h1>
      <p>Tồn kho được tổng hợp theo số lượng sản phẩm đã nhập ở phần quản lý sản phẩm.</p>
      <table>
        <thead><tr><th>Thương hiệu</th><th>Dòng sản phẩm</th><th>Model</th><th>Số lượng tồn</th><th>Giá tham khảo</th></tr></thead>
        <tbody>
          <?php if (!$inventoryRows): ?>
            <tr><td colspan="5">Không thể tải dữ liệu tồn kho.</td></tr>
          <?php else: ?>
            <?php while ($row = $inventoryRows->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars((string) $row['company_name']) ?></td>
                <td><?= htmlspecialchars((string) $row['company_series']) ?></td>
                <td><?= htmlspecialchars((string) $row['model_no']) ?></td>
                <td><?= (int) $row['stock_count'] ?></td>
                <td><?= number_format((float) $row['base_price'], 0, ',', '.') ?> VND</td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>





