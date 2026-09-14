<?php
require_once __DIR__ . '/auth.php';
require_permission('reports');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>B?o c?o th?ng k?</title>
  <link rel="stylesheet" href="../assets/css/admin_reports.css">
</head>
<body>
<?php include __DIR__ . "/sidebar.php"; ?>
  <div class="main">
    <div class="header">
      <div class="title">📊 B?o c?o th?ng k?</div>
      <div class="search-box">
        <form method="post" action="logout.php" style="margin: 0;">
          <button type="submit">Đăng xuất</button>
        </form>
      </div>
    </div>
    <div class="content" style="padding-top: 20px;">
      <p class="report-note">Gi? tr? h?a ??n ???c t?ng h?p t? s? ti?n ghi tr?n h?a ??n, bao g?m h?a ??n ?ang ch? x? l?. B? l?c n?m v? h?ng ?p d?ng cho hai b?o c?o h?a ??n; kh?ch h?ng v? ??nh gi? t?nh tr?n to?n b? d? li?u.</p>
      <div class="dashboard-box">
        <?php
        $result = $conn->query("SELECT COUNT(*) AS total FROM mobile");
        $products = ($result) ? $result->fetch_assoc()['total'] : 0;
        $result = $conn->query("SELECT COUNT(*) AS total FROM customer");
        $customers = ($result) ? $result->fetch_assoc()['total'] : 0;
        $result = $conn->query("SELECT COUNT(*) AS total FROM paymentbill");
        $invoices = ($result) ? $result->fetch_assoc()['total'] : 0;
        ?>
        <div class="card">
          <h2><?= $products ?></h2>
          <p>📱 Sản phẩm</p>
        </div>
        <div class="card">
          <h2><?= $customers ?></h2>
          <p>👥 Khách hàng</p>
        </div>
        <div class="card">
          <h2><?= $invoices ?></h2>
          <p>📰 Bài viết blog</p>
        </div>
      </div>
      <div class="dashboard-content">
        <div class="dashboard-item">
          <h3>📈 Doanh thu theo hãng</h3>
          <div class="filters">
            <label for="yearSelect">Năm:</label>
            <select id="yearSelect"></select>
            <label for="brandSelect">Hãng:</label>
            <select id="brandSelect">
              <option value="">Tất cả</option>
            </select>
          </div>
          <div class="chart-container"><canvas id="revenueChart" aria-label="Bi?u ?? th?ng k?" role="img"></canvas></div>
          <p class="chart-status" id="revenueChartStatus" role="status">?ang t?i d? li?u...</p>
          <table class="chart-table" id="revenueChartTable" aria-label="D? li?u b?o c?o"></table>
        </div>
        <div class="dashboard-item">
          <h3>📦 Sản phẩm đã bán theo hãng</h3>
          <div class="chart-container"><canvas id="soldChart" aria-label="Bi?u ?? th?ng k?" role="img"></canvas></div>
          <p class="chart-status" id="soldChartStatus" role="status">?ang t?i d? li?u...</p>
          <table class="chart-table" id="soldChartTable" aria-label="D? li?u b?o c?o"></table>
        </div>
        <div class="dashboard-item">
          <h3>👥 Phân bố giới tính khách hàng</h3>
          <div class="chart-container"><canvas id="genderChart" aria-label="Bi?u ?? th?ng k?" role="img"></canvas></div>
          <p class="chart-status" id="genderChartStatus" role="status">?ang t?i d? li?u...</p>
          <table class="chart-table" id="genderChartTable" aria-label="D? li?u b?o c?o"></table>
        </div>
        <div class="dashboard-item">
          <h3>⭐ Thống kê đánh giá</h3>
          <div class="chart-container"><canvas id="reviewChart" aria-label="Bi?u ?? th?ng k?" role="img"></canvas></div>
          <p class="chart-status" id="reviewChartStatus" role="status">?ang t?i d? li?u...</p>
          <table class="chart-table" id="reviewChartTable" aria-label="D? li?u b?o c?o"></table>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/admin_reports.js"></script>
  </div>
</body>
</html>
