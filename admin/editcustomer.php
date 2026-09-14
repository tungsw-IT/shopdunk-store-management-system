<?php
require_once __DIR__ . '/auth.php';
require_permission('customers');

$id = $_GET['id'] ?? '';
if (!$id) die('Thiếu ID khách hàng.');

$stmt = $conn->prepare("SELECT * FROM customer WHERE customer_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die('Khách hàng không tồn tại.');
$customer = $result->fetch_assoc();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $gender = $_POST['gender'];
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if ($name && $gender && $email && $phone && $address) {
        $update = $conn->prepare("UPDATE customer SET customer_name=?, customer_gender=?, customer_email=?, customer_contact_no=?, customer_address=? WHERE customer_id=?");
        $update->bind_param("sssssi", $name, $gender, $email, $phone, $address, $id);
        $update->execute();
        // Chuyển hướng về danh sách, hiện popup
        header('Location: customer.php?success=3');
        exit;
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa khách hàng</title>
    <style>
        body { font-family: Arial; background: #f5f7fa; padding: 40px; }
        .form-box {
            background: #fff; padding: 30px; max-width: 600px; margin: auto;
            box-shadow: 0 0 16px rgba(0,0,0,0.1); border-radius: 10px;
        }
        h2 { margin-bottom: 20px; color: #333; }
        input, select, textarea {
            width: 100%; padding: 10px; margin-bottom: 15px;
            border-radius: 6px; border: 1px solid #ccc; font-size: 1rem;
        }
        button {
            background: #007bff; color: #fff; padding: 12px 22px;
            border: none; border-radius: 6px; font-weight: bold;
            cursor: pointer;
        }
        button:hover { background: #0069d9; }
        .msg { margin-bottom: 15px; font-weight: bold; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Sửa Thông Tin Khách Hàng</h2>
    <?php if ($error): ?><div class="msg error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="msg success"><?= $success ?></div><?php endif; ?>
    <form method="POST">
        <input type="text" name="name" value="<?= htmlspecialchars($customer['customer_name']) ?>" required>
        <select name="gender" required>
            <option value="Nam" <?= $customer['customer_gender'] === 'Nam' ? 'selected' : '' ?>>Nam</option>
            <option value="Nữ" <?= $customer['customer_gender'] === 'Nữ' ? 'selected' : '' ?>>Nữ</option>
        </select>
        <input type="email" name="email" value="<?= htmlspecialchars($customer['customer_email']) ?>" required>
        <input type="text" name="phone" value="<?= htmlspecialchars($customer['customer_contact_no']) ?>" required>
        <textarea name="address" rows="3" required><?= htmlspecialchars($customer['customer_address']) ?></textarea>
        <button type="submit">Cập nhật</button>
    </form>
</div>
</body>
</html>





