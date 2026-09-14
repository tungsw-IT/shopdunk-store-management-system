<?php
require_once __DIR__ . '/auth.php';
$currentAdmin = require_permission('human_resources');
$flash = get_flash();
$roles = get_admin_roles();
$staffResult = mysqli_query($conn, "SELECT admin_username, admin_full_name, admin_role, admin_status, created_at FROM admin ORDER BY admin_role = 'quan_ly_tong' DESC, admin_username ASC");
$activeCount = module_scalar($conn, "SELECT COUNT(*) FROM admin WHERE admin_status = 'active'");
$blockedCount = module_scalar($conn, "SELECT COUNT(*) FROM admin WHERE admin_status = 'blocked'");
require_once __DIR__ . '/sidebar.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quan ly nhan su</title>
  <style>
    body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif; background: #f4f7fb; }
    .main-content { margin-left: 220px; padding: 28px 36px; }
    .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
    .card { background: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 10px 30px rgba(15,23,42,.06); border: 1px solid #dbe6f4; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
    .pill { display: inline-block; padding: 6px 10px; border-radius: 999px; background: #dbeafe; color: #1d4ed8; font-size: 12px; font-weight: 700; }
    .flash { margin-bottom: 18px; padding: 14px 16px; border-radius: 12px; font-weight: 600; }
    .flash.success { background: #dcfce7; color: #166534; }
    .flash.error { background: #fee2e2; color: #991b1b; }
    a.btn { display: inline-block; margin-top: 10px; padding: 10px 14px; border-radius: 10px; background: #2563eb; color: #fff; text-decoration: none; font-weight: 600; }
    @media (max-width: 1100px) { .main-content { margin-left: 0; padding: 20px; } .stats { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <div class="main-content">
    <?php if ($flash): ?><div class="flash <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>
    <div class="stats">
      <div class="card"><h2><?= module_scalar($conn, "SELECT COUNT(*) FROM admin") ?></h2><p>Tong nhan vien</p></div>
      <div class="card"><h2><?= $activeCount ?></h2><p>Tai khoan dang hoat dong</p></div>
      <div class="card"><h2><?= $blockedCount ?></h2><p>Tai khoan dang khoa</p></div>
    </div>
    <div class="card">
      <h1>Quan ly nhan su</h1>
      <p>Trang nay cho phep theo doi danh sach nhan su va tinh hinh tai khoan trong he thong. Viec phan quyen va khoa/mo tai khoan duoc thuc hien tai trang Quan ly nhan vien.</p>
      <?php if (admin_has_permission($currentAdmin['admin_role'] ?? null, 'employees_manage')): ?>
        <a class="btn" href="employees.php">Mo trang Quan ly nhan vien</a>
      <?php endif; ?>
      <table style="margin-top:18px;">
        <thead>
          <tr>
            <th>Nhan vien</th>
            <th>Vai tro</th>
            <th>Trang thai</th>
            <th>Ngay tao</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($staff = $staffResult->fetch_assoc()): ?>
            <tr>
              <td><strong><?= htmlspecialchars($staff['admin_full_name'] ?: $staff['admin_username']) ?></strong><br><?= htmlspecialchars($staff['admin_username']) ?></td>
              <td><span class="pill"><?= htmlspecialchars($roles[$staff['admin_role']] ?? $staff['admin_role']) ?></span></td>
              <td><?= htmlspecialchars(status_label($staff['admin_status'])) ?></td>
              <td><?= htmlspecialchars((string) $staff['created_at']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>





