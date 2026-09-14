<?php
require_once __DIR__ . '/../config/admin.php';

function ensure_admin_table(mysqli $conn): void
{
    mysqli_query(
        $conn,
        "CREATE TABLE IF NOT EXISTS admin (
            admin_username VARCHAR(100) NOT NULL PRIMARY KEY,
            admin_password VARCHAR(255) NOT NULL
        )"
    );
}

ensure_admin_table($conn);

function admin_column_exists(mysqli $conn, string $column): bool
{
    $escaped = mysqli_real_escape_string($conn, $column);
    $result = mysqli_query($conn, "SHOW COLUMNS FROM admin LIKE '{$escaped}'");

    return $result instanceof mysqli_result && $result->num_rows > 0;
}

function ensure_admin_schema(mysqli $conn): void
{
    $schemaUpdates = [
        "admin_full_name" => "ALTER TABLE admin ADD COLUMN admin_full_name VARCHAR(100) NULL AFTER admin_username",
        "admin_role" => "ALTER TABLE admin ADD COLUMN admin_role VARCHAR(50) NOT NULL DEFAULT 'quan_ly' AFTER admin_password",
        "admin_status" => "ALTER TABLE admin ADD COLUMN admin_status VARCHAR(20) NOT NULL DEFAULT 'active' AFTER admin_role",
        "created_at" => "ALTER TABLE admin ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP",
        "updated_at" => "ALTER TABLE admin ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
    ];

    foreach ($schemaUpdates as $column => $sql) {
        if (!admin_column_exists($conn, $column)) {
            mysqli_query($conn, $sql);
        }
    }

    $superAdminCheck = mysqli_query(
        $conn,
        "SELECT admin_username FROM admin WHERE admin_role = 'quan_ly_tong' LIMIT 1"
    );

    if ($superAdminCheck instanceof mysqli_result && $superAdminCheck->num_rows === 0) {
        $firstAdminResult = mysqli_query(
            $conn,
            "SELECT admin_username FROM admin ORDER BY admin_username ASC LIMIT 1"
        );

        if ($firstAdminResult instanceof mysqli_result && ($firstAdmin = $firstAdminResult->fetch_assoc())) {
            $stmt = $conn->prepare("UPDATE admin SET admin_role = 'quan_ly_tong', admin_status = 'active' WHERE admin_username = ?");
            if ($stmt) {
                $stmt->bind_param("s", $firstAdmin['admin_username']);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}

ensure_admin_schema($conn);

function ensure_default_admin(mysqli $conn): void
{
    $defaultUsername = 'admin@gmail.com';
    $defaultPassword = 'Aa12345';
    $defaultRole = 'quan_ly_tong';
    $defaultStatus = 'active';

    $stmt = $conn->prepare("SELECT admin_password, admin_role, admin_status FROM admin WHERE admin_username = ? LIMIT 1");
    if (!$stmt) {
        return;
    }

    $stmt->bind_param("s", $defaultUsername);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if ($row) {
        $needsPasswordUpdate = !password_verify($defaultPassword, (string) ($row['admin_password'] ?? ''));
        $needsRoleUpdate = (($row['admin_role'] ?? '') !== $defaultRole);
        $needsStatusUpdate = (($row['admin_status'] ?? '') !== $defaultStatus);

        if ($needsPasswordUpdate || $needsRoleUpdate || $needsStatusUpdate) {
            $newHash = $needsPasswordUpdate ? password_hash($defaultPassword, PASSWORD_DEFAULT) : (string) ($row['admin_password'] ?? '');
            $update = $conn->prepare("UPDATE admin SET admin_password = ?, admin_role = ?, admin_status = ? WHERE admin_username = ?");
            if ($update) {
                $update->bind_param("ssss", $newHash, $defaultRole, $defaultStatus, $defaultUsername);
                $update->execute();
                $update->close();
            }
        }

        return;
    }

    $hash = password_hash($defaultPassword, PASSWORD_DEFAULT);
    $insert = $conn->prepare("INSERT INTO admin (admin_username, admin_password, admin_role, admin_status) VALUES (?, ?, ?, ?)");
    if (!$insert) {
        return;
    }

    $insert->bind_param("ssss", $defaultUsername, $hash, $defaultRole, $defaultStatus);
    $insert->execute();
    $insert->close();
}

ensure_default_admin($conn);

function ensure_management_tables(mysqli $conn): void
{
    $tables = [
        "CREATE TABLE IF NOT EXISTS brands (
            id INT AUTO_INCREMENT PRIMARY KEY,
            brand_name VARCHAR(120) NOT NULL,
            brand_code VARCHAR(50) NULL,
            note TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS system_tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(150) NOT NULL,
            description TEXT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'pending',
            priority VARCHAR(30) NOT NULL DEFAULT 'medium',
            owner_username VARCHAR(100) NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS operations_tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(150) NOT NULL,
            description TEXT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'pending',
            assigned_role VARCHAR(50) NULL,
            due_date DATE NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS repair_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(120) NOT NULL,
            contact_phone VARCHAR(30) NULL,
            device_model VARCHAR(120) NOT NULL,
            issue_description TEXT NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'pending',
            technician_username VARCHAR(100) NULL,
            request_date DATE NOT NULL,
            completed_date DATE NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS support_tickets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(120) NOT NULL,
            contact_phone VARCHAR(30) NULL,
            topic VARCHAR(150) NOT NULL,
            message TEXT NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'open',
            assigned_to VARCHAR(100) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS sales_team_tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(150) NOT NULL,
            team_name VARCHAR(120) NOT NULL,
            assignee_username VARCHAR(100) NULL,
            note TEXT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'open',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS sales_incidents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(150) NOT NULL,
            detail TEXT NOT NULL,
            priority VARCHAR(30) NOT NULL DEFAULT 'medium',
            status VARCHAR(30) NOT NULL DEFAULT 'open',
            assignee_username VARCHAR(100) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
    ];

    foreach ($tables as $sql) {
        mysqli_query($conn, $sql);
    }

    mysqli_query(
        $conn,
        "INSERT INTO brands (brand_name, brand_code, note)
         SELECT 'Apple', 'APPLE', 'Thuong hieu mac dinh'
         WHERE NOT EXISTS (
             SELECT 1 FROM brands WHERE brand_name = 'Apple'
         )"
    );
}

ensure_management_tables($conn);

function ensure_demo_accounts(mysqli $conn): void
{
    return;
}

function get_admin_roles(): array
{
    return [
        'quan_ly_tong' => 'Quản lý tổng',
        'quan_ly' => 'Quản lý',
        'ky_thuat_vien' => 'Kỹ thuật viên',
        'thu_ngan_cskh' => 'Thu ngan - CSKH',
        'nhan_vien_ban_hang' => 'Nhân viên bán hàng',
        'to_truong_ban_hang' => 'Tổ trưởng bán hàng',
    ];
}

function get_permission_matrix(): array
{
    return [
        'quan_ly_tong' => ['*', 'employees_manage'],
        'quan_ly' => ['dashboard', 'products', 'brands', 'reviews', 'orders', 'customers', 'human_resources', 'system_management', 'operations_management', 'reports'],
        'ky_thuat_vien' => ['dashboard', 'reviews', 'repair_management', 'repair_history'],
        'thu_ngan_cskh' => ['dashboard', 'orders', 'customers', 'customer_support', 'invoice_management', 'reports'],
        'nhan_vien_ban_hang' => ['dashboard', 'products', 'orders', 'customers', 'sales_management', 'store_inventory', 'reports'],
        'to_truong_ban_hang' => ['dashboard', 'products', 'orders', 'customers', 'sales_management', 'store_inventory', 'sales_team_management', 'sales_issue_support', 'reports'],
    ];
}

function role_label(?string $role): string
{
    $roles = get_admin_roles();
    return $roles[$role ?? ''] ?? 'Nhan vien';
}

function status_label(?string $status): string
{
    return ($status ?? 'active') === 'blocked' ? 'Da khoa' : 'Dang hoat dong';
}

function admin_has_permission(?string $role, string $permission): bool
{
    if (!$role) {
        return false;
    }

    $permissions = get_permission_matrix()[$role] ?? [];
    return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
}

function admin_table_has_records(mysqli $conn): bool
{
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM admin");
    $row = $result ? $result->fetch_assoc() : null;

    return (int) ($row['total'] ?? 0) > 0;
}

function count_super_admins(mysqli $conn, bool $activeOnly = false): int
{
    $sql = "SELECT COUNT(*) AS total FROM admin WHERE admin_role = 'quan_ly_tong'";
    if ($activeOnly) {
        $sql .= " AND admin_status = 'active'";
    }

    $result = mysqli_query($conn, $sql);
    $row = $result ? $result->fetch_assoc() : null;

    return (int) ($row['total'] ?? 0);
}

function module_scalar(mysqli $conn, string $sql): int
{
    $result = mysqli_query($conn, $sql);
    $row = $result ? $result->fetch_row() : null;

    return (int) ($row[0] ?? 0);
}

function current_admin(): ?array
{
    static $admin = null;
    static $loaded = false;

    if ($loaded) {
        return $admin;
    }

    $loaded = true;
    $username = $_SESSION['username'] ?? '';
    if ($username === '') {
        return null;
    }

    global $conn;
    $stmt = $conn->prepare("SELECT * FROM admin WHERE admin_username = ? LIMIT 1");
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $admin;
}

function require_login(): array
{
    $admin = current_admin();

    if (!$admin) {
        $_SESSION['login_error'] = "Vui long dang nhap de tiep tuc.";
        header("Location: login.php");
        exit;
    }

    if (($admin['admin_status'] ?? 'active') === 'blocked') {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['login_error'] = "Tai khoan cua ban dang bi khoa.";
        header("Location: login.php");
        exit;
    }

    return $admin;
}

function require_permission(string $permission): array
{
    $admin = require_login();

    if (!admin_has_permission($admin['admin_role'] ?? null, $permission)) {
        header("Location: access_denied.php");
        exit;
    }

    return $admin;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash_message'] ?? null;
    unset($_SESSION['flash_message']);

    return $flash;
}
?>





