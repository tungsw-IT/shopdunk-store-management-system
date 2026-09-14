<?php
require_once __DIR__ . '/auth.php';
$currentAdmin = require_permission('dashboard');
include __DIR__ . '/sidebar.php';

$role = $currentAdmin['admin_role'] ?? '';
$roleName = role_label($role);
$quickModules = [
    ['permission' => 'employees_manage', 'href' => 'employees.php', 'title' => 'Quản lý nhân viên', 'desc' => 'Phân quyền, khóa mở và cấp tài khoản nhân viên.'],
    ['permission' => 'human_resources', 'href' => 'human_resources.php', 'title' => 'Quản lý nhân sự', 'desc' => 'Theo dõi nhân sự và tài khoản nội bộ.'],
    ['permission' => 'system_management', 'href' => 'system_management.php', 'title' => 'Quản lý hệ thống', 'desc' => 'Quản lý công việc hệ thống và cấu hình nội bộ.'],
    ['permission' => 'operations_management', 'href' => 'operations_management.php', 'title' => 'Quản lý vận hành', 'desc' => 'Điều phối và theo dõi công việc vận hành.'],
    ['permission' => 'products', 'href' => 'product.php', 'title' => 'Sản phẩm', 'desc' => 'Quản lý danh sách sản phẩm.'],
    ['permission' => 'brands', 'href' => 'brand.php', 'title' => 'Thương hiệu', 'desc' => 'Quản lý thương hiệu và mã hàng.'],
    ['permission' => 'orders', 'href' => 'order.php', 'title' => 'Đơn hàng', 'desc' => 'Theo dõi và cập nhật đơn hàng.'],
    ['permission' => 'customers', 'href' => 'customer.php', 'title' => 'Khách hàng', 'desc' => 'Quản lý dữ liệu khách hàng.'],
    ['permission' => 'reviews', 'href' => 'reviews.php', 'title' => 'Đánh giá', 'desc' => 'Theo dõi đánh giá sản phẩm.'],
    ['permission' => 'repair_management', 'href' => 'repair_management.php', 'title' => 'Sửa chữa', 'desc' => 'Tiếp nhận và xử lý yêu cầu sửa chữa.'],
    ['permission' => 'repair_history', 'href' => 'repair_history.php', 'title' => 'Lịch sử sửa chữa', 'desc' => 'Tra cứu phiếu sửa đã hoàn tất.'],
    ['permission' => 'customer_support', 'href' => 'customer_support.php', 'title' => 'Hỗ trợ khách hàng', 'desc' => 'Xử lý ticket và phản hồi khách hàng.'],
    ['permission' => 'invoice_management', 'href' => 'invoice_management.php', 'title' => 'Thanh toán hóa đơn', 'desc' => 'Đối chiếu hóa đơn và giao dịch.'],
    ['permission' => 'sales_management', 'href' => 'sales_management.php', 'title' => 'Quản lý bán hàng', 'desc' => 'Xem tổng quan bán hàng theo role.'],
    ['permission' => 'reports', 'href' => 'reports.php', 'title' => 'Báo cáo thống kê', 'desc' => 'Xem doanh thu, bán hàng, khách hàng và đánh giá.'],
    ['permission' => 'store_inventory', 'href' => 'store_inventory.php', 'title' => 'Tồn kho tại chỗ', 'desc' => 'Kiểm tra tồn kho hiện có.'],
    ['permission' => 'sales_team_management', 'href' => 'sales_team_management.php', 'title' => 'Đội ngũ bán hàng', 'desc' => 'Quản lý và phân công đội bán hàng.'],
    ['permission' => 'sales_issue_support', 'href' => 'sales_issue_support.php', 'title' => 'Sự cố bán hàng', 'desc' => 'Ghi nhận và xử lý sự cố tại quầy.'],
];
$allowedModules = array_values(array_filter($quickModules, fn($module) => admin_has_permission($role, $module['permission'])));
$stats = [
    ['label' => 'Sản phẩm', 'value' => module_scalar($conn, "SELECT COUNT(*) FROM mobile")],
    ['label' => 'Khách hàng', 'value' => module_scalar($conn, "SELECT COUNT(*) FROM customer")],
    ['label' => 'Đơn hàng', 'value' => module_scalar($conn, "SELECT COUNT(*) FROM paymentbill")],
];
if ($role === 'quan_ly_tong') {
    $stats[] = ['label' => 'Nhân viên', 'value' => module_scalar($conn, "SELECT COUNT(*) FROM admin")];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Trang Theo Vai Trò</title>
  <style>
    body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif; background: linear-gradient(135deg, #dfeeff 0%, #eef6ff 100%); }
    .main-role { margin-left: 220px; min-height: 100vh; padding: 28px 32px 40px; }
    .hero, .panel { background: rgba(255,255,255,.94); border: 1px solid #dbe6f4; border-radius: 22px; box-shadow: 0 18px 40px rgba(15,23,42,.08); }
    .hero { padding: 28px; display: flex; justify-content: space-between; gap: 20px; align-items: center; }
    .hero h1 { margin: 0 0 8px; color: #0f172a; font-size: 32px; }
    .hero p { margin: 0; color: #475569; line-height: 1.6; max-width: 720px; }
    .role-badge { display: inline-block; margin-top: 12px; padding: 8px 14px; border-radius: 999px; background: #dbeafe; color: #1d4ed8; font-weight: 700; }
    .logout button { border: none; border-radius: 12px; padding: 12px 18px; background: #0f172a; color: #fff; font-weight: 700; cursor: pointer; }
    .stats { margin-top: 22px; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; }
    .stat-card, .module-card { background: #fff; border: 1px solid #dbe6f4; border-radius: 18px; padding: 20px; box-shadow: 0 10px 22px rgba(15,23,42,.04); }
    .stat-card .value { font-size: 36px; font-weight: 800; color: #2563eb; margin-bottom: 6px; }
    .panel { margin-top: 24px; padding: 24px; }
    .panel h2 { margin: 0 0 8px; color: #0f172a; }
    .panel p { margin: 0 0 18px; color: #64748b; }
    .modules { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; }
    .module-card h3 { margin: 0 0 8px; color: #0f172a; }
    .module-card p { margin: 0 0 16px; color: #64748b; line-height: 1.5; min-height: 44px; }
    .module-card a { display: inline-block; padding: 10px 14px; border-radius: 12px; background: #2563eb; color: #fff; text-decoration: none; font-weight: 700; }
    @media (max-width: 1100px) { .main-role { margin-left: 0; padding: 20px; } .hero { flex-direction: column; align-items: flex-start; } }
  </style>
</head>
<body>
  <div class="main-role">
    <div class="hero">
      <div>
        <h1>Xin chào, <?= htmlspecialchars($currentAdmin['admin_full_name'] ?: $currentAdmin['admin_username']) ?></h1>
        <p>Đăng nhập role nào thì hệ thống sẽ đưa bạn vào đúng khu vực làm việc của role đó. Tài khoản hiện tại chỉ thấy và chỉ vào được các chức năng đã được phân quyền.</p>
        <div class="role-badge"><?= htmlspecialchars($roleName) ?></div>
      </div>
      <form class="logout" method="post" action="logout.php">
        <button type="submit">Đăng xuất</button>
      </form>
    </div>

    <div class="stats">
      <?php foreach ($stats as $stat): ?>
        <div class="stat-card">
          <div class="value"><?= (int) $stat['value'] ?></div>
          <div><?= htmlspecialchars($stat['label']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="panel">
      <h2>Chức năng của tài khoản này</h2>
      <p>Bạn bấm vào ô chức năng để vào thẳng module phù hợp. Nếu đăng nhập tài khoản quản lý thì sẽ thấy chức năng của quản lý. Nếu đăng nhập tài khoản nhân viên thì chỉ hiện chức năng của nhân viên đó.</p>
      <div class="modules">
        <?php foreach ($allowedModules as $module): ?>
          <div class="module-card">
            <h3><?= htmlspecialchars($module['title']) ?></h3>
            <p><?= htmlspecialchars($module['desc']) ?></p>
            <a href="<?= htmlspecialchars($module['href']) ?>">Mở chức năng</a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</body>
</html>
