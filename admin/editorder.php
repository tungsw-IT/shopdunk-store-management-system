<?php
require_once __DIR__ . '/auth.php';
require_permission('orders');
include __DIR__ . '/sidebar.php';

$error = '';
$success = '';

// Lấy id đơn hàng cần sửa từ GET
$order_id = $_GET['id'] ?? '';

if (!$order_id) {
    die('Không có ID đơn hàng để sửa.');
}

// Lấy dữ liệu đơn hàng hiện tại
$stmt = $conn->prepare("SELECT * FROM paymentbill WHERE pb_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows == 0) {
    die('Đơn hàng không tồn tại.');
}

$order = $order_result->fetch_assoc();
$stmt->close();

// Lấy danh sách điện thoại trong kho để dropdown
$mobiles_result = mysqli_query($conn, "SELECT imei_number, company_name, company_series, model_no, price FROM mobile ORDER BY company_name, company_series, model_no");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pb_date = $_POST['pb_date'] ?? '';
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_contact_no = $_POST['customer_contact_no'] ?? '';
    $customer_address = $_POST['customer_address'] ?? '';
    $purchase_mobile_imei_no = $_POST['purchase_mobile_imei_no'] ?? '';
    $purchase_mobile_company = $_POST['purchase_mobile_company'] ?? '';
    $purchase_mobile_series = $_POST['purchase_mobile_series'] ?? '';
    $purchase_mobile_model = $_POST['purchase_mobile_model'] ?? '';
    $purchase_mobile_price = $_POST['purchase_mobile_price'] ?? 0;
    $quantity = (int)($_POST['quantity'] ?? 1);
    $total_paid_amount = $_POST['total_paid_amount'] ?? 0;

    if (!$pb_date || !$customer_name || !$customer_contact_no || !$purchase_mobile_imei_no || !$purchase_mobile_price || !$total_paid_amount) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
    } else {
        // Cập nhật thông tin đơn hàng
        $stmt = $conn->prepare("UPDATE paymentbill SET pb_date = ?, customer_name = ?, customer_contact_no = ?, purchase_mobile_imei_no = ?, purchase_mobile_company = ?, purchase_mobile_series = ?, purchase_mobile_model = ?, purchase_mobile_price = ?, total_paid_amount = ? WHERE pb_id = ?");
        $stmt->bind_param("sssisssdii", $pb_date, $customer_name, $customer_contact_no, $purchase_mobile_imei_no, $purchase_mobile_company, $purchase_mobile_series, $purchase_mobile_model, $purchase_mobile_price, $total_paid_amount, $order_id);

        if ($stmt->execute()) {
            $success = 'Cập nhật đơn hàng thành công!';
            // Cập nhật lại biến $order để hiển thị form với dữ liệu mới
            $order['pb_date'] = $pb_date;
            $order['customer_name'] = $customer_name;
            $order['customer_contact_no'] = $customer_contact_no;
            $order['purchase_mobile_imei_no'] = $purchase_mobile_imei_no;
            $order['purchase_mobile_company'] = $purchase_mobile_company;
            $order['purchase_mobile_series'] = $purchase_mobile_series;
            $order['purchase_mobile_model'] = $purchase_mobile_model;
            $order['purchase_mobile_price'] = $purchase_mobile_price;
            $order['total_paid_amount'] = $total_paid_amount;
            $order['quantity'] = $quantity;
        } else {
            $error = 'Lỗi khi cập nhật đơn hàng: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Sửa đơn hàng</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        form { max-width: 700px; margin: auto; background: #f9f9f9; padding: 20px; border-radius: 10px; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        .btn-submit { margin-top: 20px; padding: 10px 15px; background: #4c2bb6; color: white; border: none; cursor: pointer; border-radius: 5px; }
        .btn-submit:hover { background: #3a1f87; }
        .message {
            font-weight: bold;
            padding: 14px 20px;
            border-radius: 7px;
            display: block;
            max-width: 420px;
            margin: 24px auto 18px auto;
            text-align: center;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px #ddd;
        }
        .error {
            color: #fff;
            background: #e53935;
        }
        .success {
            color: #fff;
            background: #43a047;
        }
    </style>
    <script>
        function fillMobileInfo() {
            var mobileSelect = document.getElementById('purchase_mobile_imei_no');
            var selectedOption = mobileSelect.options[mobileSelect.selectedIndex];
            document.getElementById('purchase_mobile_company').value = selectedOption.getAttribute('data-company') || '';
            document.getElementById('purchase_mobile_series').value = selectedOption.getAttribute('data-series') || '';
            document.getElementById('purchase_mobile_model').value = selectedOption.getAttribute('data-model') || '';
            document.getElementById('purchase_mobile_price').value = selectedOption.getAttribute('data-price') || '';
            calculateTotal();
        }

        function calculateTotal() {
            var price = parseFloat(document.getElementById('purchase_mobile_price').value) || 0;
            var quantity = parseInt(document.getElementById('quantity').value) || 1;
            document.getElementById('total_paid_amount').value = (price * quantity).toFixed(2);
        }
    </script>
</head>
<body>

<h2>Sửa đơn hàng</h2>

<?php if ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="message success">
        <?= htmlspecialchars($success) ?><br>
        <small style="display:block;margin-top:10px;">
            Bạn sẽ được chuyển về trang danh sách đơn hàng sau <span id="countdown" style="color:#e53935;font-weight:bold;">3</span> giây...
        </small>
    </div>
    <script>
        var timeLeft = 3;
        var countdown = document.getElementById('countdown');
        var timer = setInterval(function() {
            timeLeft--;
            countdown.textContent = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(timer);
                window.location.href = 'order.php';
            }
        }, 1000);
    </script>
<?php endif; ?>

<form method="POST" action="">
    <label for="pb_date">Ngày đặt đơn</label>
    <input type="date" id="pb_date" name="pb_date" required value="<?= htmlspecialchars($order['pb_date']) ?>" />

    <label for="customer_name">Tên khách hàng</label>
    <input type="text" id="customer_name" name="customer_name" required value="<?= htmlspecialchars($order['customer_name']) ?>" />

    <label for="customer_contact_no">Số điện thoại khách</label>
    <input type="text" id="customer_contact_no" name="customer_contact_no" required value="<?= htmlspecialchars($order['customer_contact_no']) ?>" />

    <label for="purchase_mobile_imei_no">Chọn điện thoại</label>
    <select id="purchase_mobile_imei_no" name="purchase_mobile_imei_no" onchange="fillMobileInfo()" required>
        <option value="">-- Chọn điện thoại --</option>
        <?php while ($mobile = mysqli_fetch_assoc($mobiles_result)): ?>
            <option 
                value="<?= $mobile['imei_number'] ?>" 
                data-company="<?= htmlspecialchars($mobile['company_name']) ?>"
                data-series="<?= htmlspecialchars($mobile['company_series']) ?>"
                data-model="<?= htmlspecialchars($mobile['model_no']) ?>"
                data-price="<?= $mobile['price'] ?>"
                <?= $mobile['imei_number'] == $order['purchase_mobile_imei_no'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($mobile['company_name'] . ' ' . $mobile['company_series'] . ' ' . $mobile['model_no']) ?>
                - <?= number_format($mobile['price'], 0, ',', '.') ?> đ
            </option>
        <?php endwhile; ?>
    </select>

    <label for="purchase_mobile_company">Hãng điện thoại</label>
    <input type="text" id="purchase_mobile_company" name="purchase_mobile_company" readonly value="<?= htmlspecialchars($order['purchase_mobile_company']) ?>" />

    <label for="purchase_mobile_series">Dòng điện thoại</label>
    <input type="text" id="purchase_mobile_series" name="purchase_mobile_series" readonly value="<?= htmlspecialchars($order['purchase_mobile_series']) ?>" />

    <label for="purchase_mobile_model">Model điện thoại</label>
    <input type="text" id="purchase_mobile_model" name="purchase_mobile_model" readonly value="<?= htmlspecialchars($order['purchase_mobile_model']) ?>" />

    <label for="purchase_mobile_price">Giá điện thoại</label>
    <input type="number" id="purchase_mobile_price" name="purchase_mobile_price" readonly value="<?= htmlspecialchars($order['purchase_mobile_price']) ?>" />

    <label for="quantity">Số lượng mua</label>
    <input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($order['quantity'] ?? 1) ?>" min="1" oninput="calculateTotal()" required />

    <label for="total_paid_amount">Tổng tiền thanh toán</label>
    <input type="number" id="total_paid_amount" name="total_paid_amount" readonly value="<?= htmlspecialchars($order['total_paid_amount']) ?>" required />

    <button class="btn-submit" type="submit">Cập nhật đơn hàng</button>
</form>

<script>
    // Tự động cập nhật các trường thông tin điện thoại khi tải trang lần đầu
    window.onload = function() {
        fillMobileInfo();
    };
</script>

</body>
</html>





