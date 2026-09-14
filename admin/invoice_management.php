<?php
require_once __DIR__ . '/auth.php';
$currentAdmin = require_permission('invoice_management');

$totalInvoices = module_scalar($conn, "SELECT COUNT(*) FROM paymentbill");
$paidToday = module_scalar(
    $conn,
    "SELECT COUNT(*)
     FROM paymentbill
     WHERE STR_TO_DATE(pb_date, '%d-%m-%Y') = CURDATE()"
);
$totalRevenue = module_scalar($conn, "SELECT COALESCE(SUM(total_paid_amount), 0) FROM paymentbill");

$invoiceRows = mysqli_query(
    $conn,
    "SELECT pb_id,
            customer_name,
            customer_contact_no,
            purchase_mobile_company,
            purchase_mobile_series,
            purchase_mobile_model,
            purchase_mobile_imei_no,
            total_paid_amount,
            pb_date
     FROM paymentbill
     ORDER BY pb_id DESC
     LIMIT 100"
);

require_once __DIR__ . '/sidebar.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thanh toán hóa đơn</title>
  <style>
    body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif; background: #f4f7fb; }
    .main-content { margin-left: 220px; padding: 28px 36px; }
    .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
    .card { background: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 10px 30px rgba(15,23,42,.06); border: 1px solid #dbe6f4; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; vertical-align: top; }
    .muted { color: #64748b; font-size: 14px; }
    @media (max-width: 1100px) {
      .main-content { margin-left: 0; padding: 20px; }
      .stats { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="main-content">
    <div class="stats">
      <div class="card"><h2><?= (int) $totalInvoices ?></h2><p>Tổng hóa đơn</p></div>
      <div class="card"><h2><?= (int) $paidToday ?></h2><p>Hóa đơn hôm nay</p></div>
      <div class="card"><h2><?= number_format((float) $totalRevenue, 0, ',', '.') ?> VND</h2><p>Tổng doanh thu ghi nhận</p></div>
    </div>

    <div class="card">
      <h1>Thanh toán hóa đơn</h1>
      <p>Danh sách hóa đơn đã ghi nhận để thu ngân và CSKH đối chiếu giao dịch.</p>
      <table>
        <thead>
          <tr>
            <th>Mã hóa đơn</th>
            <th>Khách hàng</th>
            <th>Sản phẩm</th>
            <th>Số tiền</th>
            <th>Ngày thanh toán</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$invoiceRows): ?>
            <tr><td colspan="5">Không thể tải dữ liệu hóa đơn.</td></tr>
          <?php else: ?>
            <?php while ($row = $invoiceRows->fetch_assoc()): ?>
              <tr>
                <td>#<?= (int) $row['pb_id'] ?></td>
                <td>
                  <strong><?= htmlspecialchars((string) $row['customer_name']) ?></strong><br>
                  <span class="muted"><?= htmlspecialchars((string) ($row['customer_contact_no'] ?? '')) ?></span>
                </td>
                <td>
                  <?= htmlspecialchars(trim((string) $row['purchase_mobile_company'] . ' ' . (string) $row['purchase_mobile_series'] . ' ' . (string) $row['purchase_mobile_model'])) ?><br>
                  <span class="muted">IMEI: <?= htmlspecialchars((string) $row['purchase_mobile_imei_no']) ?></span>
                </td>
                <td><?= number_format((float) $row['total_paid_amount'], 0, ',', '.') ?> VND</td>
                <td><?= htmlspecialchars((string) $row['pb_date']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>





