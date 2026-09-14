<?php
require_once __DIR__ . '/auth.php';
$currentAdmin = require_permission('employees_manage');

$flash = get_flash();
$roles = get_admin_roles();
$editingUsername = $_GET['edit'] ?? '';
$editingEmployee = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $fullName = trim($_POST['admin_full_name'] ?? '');
        $username = trim($_POST['admin_username'] ?? '');
        $password = $_POST['admin_password'] ?? '';
        $role = $_POST['admin_role'] ?? '';
        $status = $_POST['admin_status'] ?? 'active';

        if ($username === '' || $password === '' || !isset($roles[$role])) {
            set_flash('error', 'Thong tin tao tai khoan chua hop le.');
            header('Location: employees.php');
            exit;
        }

        if (!in_array($status, ['active', 'blocked'], true)) {
            $status = 'active';
        }

        $checkStmt = $conn->prepare("SELECT admin_username FROM admin WHERE admin_username = ? LIMIT 1");
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->fetch_assoc();
        $checkStmt->close();

        if ($exists) {
            set_flash('error', 'Ten dang nhap da ton tai.');
            header('Location: employees.php');
            exit;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            "INSERT INTO admin (admin_username, admin_full_name, admin_password, admin_role, admin_status) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $username, $fullName, $passwordHash, $role, $status);

        if ($stmt->execute()) {
            set_flash('success', 'Da tao tai khoan nhan vien moi.');
        } else {
            set_flash('error', 'Khong the tao tai khoan moi.');
        }

        $stmt->close();
        header('Location: employees.php');
        exit;
    }

    if ($action === 'update') {
        $targetUsername = $_POST['target_username'] ?? '';
        $fullName = trim($_POST['admin_full_name'] ?? '');
        $role = $_POST['admin_role'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';

        if ($targetUsername === '' || !isset($roles[$role])) {
            set_flash('error', 'Thong tin cap nhat chua hop le.');
            header('Location: employees.php');
            exit;
        }

        $targetStmt = $conn->prepare("SELECT * FROM admin WHERE admin_username = ? LIMIT 1");
        $targetStmt->bind_param("s", $targetUsername);
        $targetStmt->execute();
        $targetEmployee = $targetStmt->get_result()->fetch_assoc();
        $targetStmt->close();

        if (!$targetEmployee) {
            set_flash('error', 'Khong tim thay tai khoan nhan vien.');
            header('Location: employees.php');
            exit;
        }

        if (($targetEmployee['admin_role'] ?? '') === 'quan_ly_tong' && $role !== 'quan_ly_tong' && count_super_admins($conn, true) <= 1) {
            set_flash('error', 'He thong phai co it nhat mot Quan ly tong dang hoat dong.');
            header('Location: employees.php?edit=' . urlencode($targetUsername));
            exit;
        }

        if ($newPassword !== '') {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                "UPDATE admin SET admin_full_name = ?, admin_role = ?, admin_password = ? WHERE admin_username = ?"
            );
            $stmt->bind_param("ssss", $fullName, $role, $passwordHash, $targetUsername);
        } else {
            $stmt = $conn->prepare(
                "UPDATE admin SET admin_full_name = ?, admin_role = ? WHERE admin_username = ?"
            );
            $stmt->bind_param("sss", $fullName, $role, $targetUsername);
        }

        if ($stmt->execute()) {
            set_flash('success', 'Da cap nhat thong tin nhan vien.');
        } else {
            set_flash('error', 'Khong the cap nhat tai khoan.');
        }
        $stmt->close();

        header('Location: employees.php?edit=' . urlencode($targetUsername));
        exit;
    }

    if ($action === 'toggle_status') {
        $targetUsername = $_POST['target_username'] ?? '';
        $targetStmt = $conn->prepare("SELECT * FROM admin WHERE admin_username = ? LIMIT 1");
        $targetStmt->bind_param("s", $targetUsername);
        $targetStmt->execute();
        $targetEmployee = $targetStmt->get_result()->fetch_assoc();
        $targetStmt->close();

        if (!$targetEmployee) {
            set_flash('error', 'Khong tim thay tai khoan nhan vien.');
            header('Location: employees.php');
            exit;
        }

        $nextStatus = ($targetEmployee['admin_status'] ?? 'active') === 'active' ? 'blocked' : 'active';

        if ($targetUsername === ($currentAdmin['admin_username'] ?? '') && $nextStatus === 'blocked') {
            set_flash('error', 'Ban khong the tu khoa tai khoan cua chinh minh.');
            header('Location: employees.php');
            exit;
        }

        if (($targetEmployee['admin_role'] ?? '') === 'quan_ly_tong' && $nextStatus === 'blocked' && count_super_admins($conn, true) <= 1) {
            set_flash('error', 'He thong phai co it nhat mot Quan ly tong dang hoat dong.');
            header('Location: employees.php');
            exit;
        }

        $stmt = $conn->prepare("UPDATE admin SET admin_status = ? WHERE admin_username = ?");
        $stmt->bind_param("ss", $nextStatus, $targetUsername);

        if ($stmt->execute()) {
            set_flash('success', $nextStatus === 'active' ? 'Da mo lai tai khoan nhan vien.' : 'Da khoa tai khoan nhan vien.');
        } else {
            set_flash('error', 'Khong the cap nhat trang thai tai khoan.');
        }

        $stmt->close();
        header('Location: employees.php');
        exit;
    }

    if ($action === 'delete') {
        $targetUsername = $_POST['target_username'] ?? '';
        $targetStmt = $conn->prepare("SELECT * FROM admin WHERE admin_username = ? LIMIT 1");
        $targetStmt->bind_param("s", $targetUsername);
        $targetStmt->execute();
        $targetEmployee = $targetStmt->get_result()->fetch_assoc();
        $targetStmt->close();

        if (!$targetEmployee) {
            set_flash('error', 'Khong tim thay tai khoan nhan vien.');
            header('Location: employees.php');
            exit;
        }

        if ($targetUsername === ($currentAdmin['admin_username'] ?? '')) {
            set_flash('error', 'Ban khong the tu xoa tai khoan cua chinh minh.');
            header('Location: employees.php');
            exit;
        }

        if (($targetEmployee['admin_role'] ?? '') === 'quan_ly_tong' && count_super_admins($conn, false) <= 1) {
            set_flash('error', 'Khong the xoa Quan ly tong cuoi cung cua he thong.');
            header('Location: employees.php');
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM admin WHERE admin_username = ?");
        $stmt->bind_param("s", $targetUsername);

        if ($stmt->execute()) {
            set_flash('success', 'Da xoa tai khoan nhan vien.');
        } else {
            set_flash('error', 'Khong the xoa tai khoan nhan vien.');
        }

        $stmt->close();
        header('Location: employees.php');
        exit;
    }
}

if ($editingUsername !== '') {
    $editStmt = $conn->prepare("SELECT * FROM admin WHERE admin_username = ? LIMIT 1");
    $editStmt->bind_param("s", $editingUsername);
    $editStmt->execute();
    $editingEmployee = $editStmt->get_result()->fetch_assoc();
    $editStmt->close();
}

$employeesResult = mysqli_query($conn, "SELECT admin_username, admin_full_name, admin_role, admin_status, created_at FROM admin ORDER BY admin_role = 'quan_ly_tong' DESC, created_at ASC, admin_username ASC");
require_once __DIR__ . '/sidebar.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quan ly nhan vien</title>
  <style>
    body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif; background: #f4f7fb; }
    .main-content { margin-left: 220px; padding: 28px 36px; }
    .grid { display: grid; grid-template-columns: 1.1fr 1.6fr; gap: 20px; align-items: start; }
    .card {
      background: #fff;
      border-radius: 18px;
      padding: 24px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
      border: 1px solid #dbe6f4;
    }
    h1, h2 { margin-top: 0; color: #0f172a; }
    p { color: #475569; }
    label { display: block; margin: 14px 0 6px; color: #334155; font-weight: 600; }
    input, select {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #cbd5e1;
      border-radius: 10px;
      font-size: 14px;
      box-sizing: border-box;
    }
    button, .link-button {
      border: none;
      border-radius: 10px;
      padding: 10px 14px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
    }
    .primary { background: #2563eb; color: #fff; }
    .secondary { background: #e2e8f0; color: #0f172a; }
    .danger { background: #dc2626; color: #fff; }
    .success { background: #16a34a; color: #fff; }
    .toolbar { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
    .flash {
      margin-bottom: 18px;
      padding: 14px 16px;
      border-radius: 12px;
      font-weight: 600;
    }
    .flash.success { background: #dcfce7; color: #166534; }
    .flash.error { background: #fee2e2; color: #991b1b; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
    th { color: #334155; font-size: 14px; }
    td { color: #475569; }
    .badge {
      display: inline-block;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
    }
    .badge.role { background: #dbeafe; color: #1d4ed8; }
    .badge.active { background: #dcfce7; color: #166534; }
    .badge.blocked { background: #fee2e2; color: #991b1b; }
    .row-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .row-actions form { margin: 0; }
    @media (max-width: 1100px) {
      .grid { grid-template-columns: 1fr; }
      .main-content { margin-left: 0; padding: 20px; }
    }
  </style>
</head>
<body>
  <div class="main-content">
    <?php if ($flash): ?>
      <div class="flash <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>

    <div class="grid">
      <div class="card">
        <h1>Quan ly nhan vien</h1>
        <p>Quan ly tong co the tao tai khoan, phan quyen vai tro va dong/mo tai khoan nhan vien tai day.</p>

        <form method="post">
          <input type="hidden" name="action" value="create">

          <label for="admin_full_name">Ho ten</label>
          <input type="text" id="admin_full_name" name="admin_full_name" placeholder="Nguyen Van A">

          <label for="admin_username">Ten dang nhap</label>
          <input type="text" id="admin_username" name="admin_username" required>

          <label for="admin_password">Mat khau</label>
          <input type="password" id="admin_password" name="admin_password" required>

          <label for="admin_role">Vai tro</label>
          <select id="admin_role" name="admin_role" required>
            <?php foreach ($roles as $roleKey => $roleName): ?>
              <option value="<?= htmlspecialchars($roleKey) ?>"><?= htmlspecialchars($roleName) ?></option>
            <?php endforeach; ?>
          </select>

          <label for="admin_status">Trang thai</label>
          <select id="admin_status" name="admin_status">
            <option value="active">Dang hoat dong</option>
            <option value="blocked">Da khoa</option>
          </select>

          <div class="toolbar">
            <button class="primary" type="submit">Tao tai khoan nhan vien</button>
          </div>
        </form>
      </div>

      <div class="card">
        <h2>Danh sach tai khoan</h2>
        <table>
          <thead>
            <tr>
              <th>Nhan vien</th>
              <th>Vai tro</th>
              <th>Trang thai</th>
              <th>Ngay tao</th>
              <th>Thao tac</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($employee = $employeesResult->fetch_assoc()): ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($employee['admin_full_name'] ?: $employee['admin_username']) ?></strong><br>
                  <?= htmlspecialchars($employee['admin_username']) ?>
                </td>
                <td><span class="badge role"><?= htmlspecialchars(role_label($employee['admin_role'])) ?></span></td>
                <td>
                  <span class="badge <?= htmlspecialchars($employee['admin_status']) ?>">
                    <?= htmlspecialchars(status_label($employee['admin_status'])) ?>
                  </span>
                </td>
                <td><?= htmlspecialchars((string) ($employee['created_at'] ?? '')) ?></td>
                <td>
                  <div class="row-actions">
                    <a class="link-button secondary" href="employees.php?edit=<?= urlencode($employee['admin_username']) ?>">Sua quyen</a>
                    <form method="post">
                      <input type="hidden" name="action" value="toggle_status">
                      <input type="hidden" name="target_username" value="<?= htmlspecialchars($employee['admin_username']) ?>">
                      <button class="<?= ($employee['admin_status'] ?? 'active') === 'active' ? 'danger' : 'success' ?>" type="submit">
                        <?= ($employee['admin_status'] ?? 'active') === 'active' ? 'Khoa tai khoan' : 'Mo tai khoan' ?>
                      </button>
                    </form>
                    <form method="post" onsubmit="return confirm('Ban chac chan muon xoa tai khoan nay?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="target_username" value="<?= htmlspecialchars($employee['admin_username']) ?>">
                      <button class="danger" type="submit">Xoa tai khoan</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php if ($editingEmployee): ?>
      <div class="card" style="margin-top: 20px;">
        <h2>Cap nhat nhan vien: <?= htmlspecialchars($editingEmployee['admin_username']) ?></h2>
        <form method="post">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="target_username" value="<?= htmlspecialchars($editingEmployee['admin_username']) ?>">

          <label for="edit_admin_full_name">Ho ten</label>
          <input type="text" id="edit_admin_full_name" name="admin_full_name" value="<?= htmlspecialchars($editingEmployee['admin_full_name'] ?? '') ?>">

          <label for="edit_admin_role">Vai tro</label>
          <select id="edit_admin_role" name="admin_role" required>
            <?php foreach ($roles as $roleKey => $roleName): ?>
              <option value="<?= htmlspecialchars($roleKey) ?>" <?= ($editingEmployee['admin_role'] ?? '') === $roleKey ? 'selected' : '' ?>>
                <?= htmlspecialchars($roleName) ?>
              </option>
            <?php endforeach; ?>
          </select>

          <label for="new_password">Mat khau moi (de trong neu khong doi)</label>
          <input type="password" id="new_password" name="new_password">

          <div class="toolbar">
            <button class="primary" type="submit">Luu thay doi</button>
            <a class="link-button secondary" href="employees.php">Dong</a>
          </div>
        </form>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>





