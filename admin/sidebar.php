<?php
require_once __DIR__ . '/auth.php';
$sidebarAdmin = current_admin();

$role = $sidebarAdmin['admin_role'] ?? null;
$menuItems = [
    ['permission' => 'dashboard', 'href' => 'admin.php', 'label' => 'Trang chính'],
    ['permission' => 'products', 'href' => 'product.php', 'label' => 'Sản phẩm'],
    ['permission' => 'brands', 'href' => 'brand.php', 'label' => 'Thương hiệu'],
    ['permission' => 'orders', 'href' => 'order.php', 'label' => 'Đơn hàng'],
    ['permission' => 'customers', 'href' => 'customer.php', 'label' => 'Khách hàng'],
    ['permission' => 'reviews', 'href' => 'reviews.php', 'label' => 'Đánh giá'],
    ['permission' => 'employees_manage', 'href' => 'employees.php', 'label' => 'Nhân viên'],
    ['permission' => 'human_resources', 'href' => 'human_resources.php', 'label' => 'Nhân sự'],
    ['permission' => 'system_management', 'href' => 'system_management.php', 'label' => 'Hệ thống'],
    ['permission' => 'operations_management', 'href' => 'operations_management.php', 'label' => 'Vận hành'],
    ['permission' => 'repair_management', 'href' => 'repair_management.php', 'label' => 'Sửa chữa'],
    ['permission' => 'repair_history', 'href' => 'repair_history.php', 'label' => 'Lịch sử sửa'],
    ['permission' => 'customer_support', 'href' => 'customer_support.php', 'label' => 'Hỗ trợ KH'],
    ['permission' => 'invoice_management', 'href' => 'invoice_management.php', 'label' => 'Hóa đơn'],
    ['permission' => 'sales_management', 'href' => 'sales_management.php', 'label' => 'Bán hàng'],
    ['permission' => 'reports', 'href' => 'reports.php', 'label' => 'Báo cáo thống kê'],
    ['permission' => 'store_inventory', 'href' => 'store_inventory.php', 'label' => 'Tồn kho'],
    ['permission' => 'sales_team_management', 'href' => 'sales_team_management.php', 'label' => 'Đội bán hàng'],
    ['permission' => 'sales_issue_support', 'href' => 'sales_issue_support.php', 'label' => 'Sự cố bán hàng'],
];
?>
<div style="width:220px;background:#353E8A;color:#fff;min-height:100vh;position:fixed;overflow-y:auto;">
  <div style="padding:18px 20px 14px;">
    <a href="admin.php" style="display:block;text-decoration:none;">
      <img src="../images/logoshopdunk.png" alt="ShopDunk Logo" style="width:100%;max-width:160px;height:auto;display:block;margin:0 auto;">
    </a>
  </div>
  <?php if ($sidebarAdmin): ?>
    <div style="padding:0 20px 16px;border-bottom:1px solid rgba(255,255,255,0.15);margin-bottom:10px;">
      <div style="font-size:15px;font-weight:700;"><?= htmlspecialchars($sidebarAdmin['admin_full_name'] ?: $sidebarAdmin['admin_username']) ?></div>
      <div style="font-size:12px;opacity:.85;margin-top:4px;"><?= htmlspecialchars(role_label($role)) ?></div>
      <div style="font-size:12px;opacity:.85;margin-top:4px;"><?= htmlspecialchars(status_label($sidebarAdmin['admin_status'] ?? null)) ?></div>
    </div>
  <?php endif; ?>
  <ul style="list-style:none;padding:0;margin:0;">
    <?php foreach ($menuItems as $item): ?>
      <?php if ($sidebarAdmin && admin_has_permission($role, $item['permission'])): ?>
        <li>
          <a href="<?= htmlspecialchars($item['href']) ?>" style="display:block;padding:12px 20px;color:#fff;text-decoration:none;"><?= htmlspecialchars($item['label']) ?></a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
    <li>
      <a href="../index.php" target="_blank" style="display:block;padding:12px 20px;color:#fff;text-decoration:none;">Trang web bán hàng</a>
    </li>
  </ul>
</div>
<?php return; ?>
<div style="width: 220px; background: #353E8A; color: #fff; min-height: 100vh; position: fixed;">
  <div style="padding: 20px; font-size: 20px; font-weight: bold;">
    <span style="color: #f56c57;">P</span>ulseTech
  </div>
  <?php if ($sidebarAdmin): ?>
    <div style="padding: 0 20px 16px; border-bottom: 1px solid rgba(255,255,255,0.15); margin-bottom: 10px;">
      <div style="font-size: 15px; font-weight: 700;"><?= htmlspecialchars($sidebarAdmin['admin_full_name'] ?: $sidebarAdmin['admin_username']) ?></div>
      <div style="font-size: 12px; opacity: .85; margin-top: 4px;"><?= htmlspecialchars(role_label($sidebarAdmin['admin_role'] ?? null)) ?></div>
      <div style="font-size: 12px; opacity: .85; margin-top: 4px;"><?= htmlspecialchars(status_label($sidebarAdmin['admin_status'] ?? null)) ?></div>
    </div>
  <?php endif; ?>
  <ul style="list-style: none; padding: 0;">
    <li>
      <a href="admin.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;">
        <img src="../icons/admin/home.svg" alt="Home" style="width: 18px; height: 18px;"> Trang chủ
      </a>
    </li>
    <li>
      <a href="product.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;">
        <img src="../icons/admin/smartphone.svg" alt="Product" style="width: 18px; height: 18px;"> Sản phẩm
      </a>
    </li>
    <li>
      <a href="reviews.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;">
        <img src="../icons/admin/bookmark-check.svg" alt="Reviews" style="width: 18px; height: 18px;"> Đánh giá 
      </a>
    </li>
    <li>
      <a href="order.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;">
        <img src="../icons/admin/album.svg" alt="Order" style="width: 18px; height: 18px;"> Đơn hàng
      </a>
    </li>
    <li>
      <a href="customer.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;">
        <img src="../icons/admin/bookmark-check.svg" alt="Customer" style="width: 18px; height: 18px;"> Khách hàng 
      </a>
    </li>
    <?php if ($sidebarAdmin && admin_has_permission($sidebarAdmin['admin_role'] ?? null, 'employees_manage')): ?>
    <li>
      <a href="employees.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;">
        <img src="../icons/admin/chart-network.svg" alt="Employees" style="width: 18px; height: 18px;"> Nhan vien
      </a>
    </li>
    <?php endif; ?>
    <?php if ($sidebarAdmin && admin_has_permission($sidebarAdmin['admin_role'] ?? null, 'human_resources')): ?>
    <li><a href="human_resources.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;"><img src="../icons/admin/bookmark-check.svg" alt="" style="width: 18px; height: 18px;"> Nhan su</a></li>
    <?php endif; ?>
    <?php if ($sidebarAdmin && admin_has_permission($sidebarAdmin['admin_role'] ?? null, 'system_management')): ?>
    <li><a href="system_management.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;"><img src="../icons/admin/chart-network.svg" alt="" style="width: 18px; height: 18px;"> He thong</a></li>
    <?php endif; ?>
    <?php if ($sidebarAdmin && admin_has_permission($sidebarAdmin['admin_role'] ?? null, 'operations_management')): ?>
    <li><a href="operations_management.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;"><img src="../icons/admin/album.svg" alt="" style="width: 18px; height: 18px;"> Van hanh</a></li>
    <?php endif; ?>
    <?php if ($sidebarAdmin && admin_has_permission($sidebarAdmin['admin_role'] ?? null, 'repair_management')): ?>
    <li><a href="repair_management.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;"><img src="../icons/admin/smartphone.svg" alt="" style="width: 18px; height: 18px;"> Sua chua</a></li>
    <?php endif; ?>
    <?php if ($sidebarAdmin && admin_has_permission($sidebarAdmin['admin_role'] ?? null, 'repair_history')): ?>
    <li><a href="repair_history.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;"><img src="../icons/admin/album.svg" alt="" style="width: 18px; height: 18px;"> Lich su sua</a></li>
    <?php endif; ?>
    <?php if ($sidebarAdmin && admin_has_permission($sidebarAdmin['admin_role'] ?? null, 'customer_support')): ?>
    <li><a href="customer_support.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;"><img src="../icons/admin/bookmark-check.svg" alt="" style="width: 18px; height: 18px;"> Ho tro KH</a></li>
    <?php endif; ?>
    <?php if ($sidebarAdmin && admin_has_permission($sidebarAdmin['admin_role'] ?? null, 'invoice_management')): ?>
    <li><a href="invoice_management.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;"><img src="../icons/admin/album.svg" alt="" style="width: 18px; height: 18px;"> Hoa don</a></li>
    <?php endif; ?>
    <?php if ($sidebarAdmin && admin_has_permission($sidebarAdmin['admin_role'] ?? null, 'sales_management')): ?>
    <li><a href="sales_management.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;"><img src="../icons/admin/chart-network.svg" alt="" style="width: 18px; height: 18px;"> Ban hang</a></li>
    <?php endif; ?>
    <?php if ($sidebarAdmin && admin_has_permission($sidebarAdmin['admin_role'] ?? null, 'store_inventory')): ?>
    <li><a href="store_inventory.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;"><img src="../icons/admin/smartphone.svg" alt="" style="width: 18px; height: 18px;"> Ton kho</a></li>
    <?php endif; ?>
    <?php if ($sidebarAdmin && admin_has_permission($sidebarAdmin['admin_role'] ?? null, 'sales_team_management')): ?>
    <li><a href="sales_team_management.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;"><img src="../icons/admin/bookmark-check.svg" alt="" style="width: 18px; height: 18px;"> Doi ban hang</a></li>
    <?php endif; ?>
    <?php if ($sidebarAdmin && admin_has_permission($sidebarAdmin['admin_role'] ?? null, 'sales_issue_support')): ?>
    <li><a href="sales_issue_support.php" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;"><img src="../icons/admin/chart-network.svg" alt="" style="width: 18px; height: 18px;"> Su co ban hang</a></li>
    <?php endif; ?>
  <li>
  <a href="../index.php" target="_blank" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #fff; text-decoration: none;">
    <img src="../icons/admin/chart-network.svg" alt="Shop" style="width: 18px; height: 18px;">
    Trang web bán hàng
  </a>
</li>
  </ul>
  <div style="padding:16px 20px 24px;border-top:1px solid rgba(255,255,255,0.15);margin-top:10px;">
    <form method="post" action="logout.php" style="margin:0;">
      <button type="submit" style="width:100%;padding:12px 16px;border:none;border-radius:12px;background:#0f172a;color:#fff;font-weight:700;cursor:pointer;">Đăng xuất</button>
    </form>
  </div>
</div>





