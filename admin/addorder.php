<?php
require_once __DIR__ . '/auth.php';
require_permission('orders');
include __DIR__ . '/sidebar.php';

$error = '';
$success = '';

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
        // Thêm khách hàng mới
        $stmt = $conn->prepare("INSERT INTO customer (customer_name, customer_contact_no, customer_address) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $customer_name, $customer_contact_no, $customer_address);
        if (!$stmt->execute()) {
            $error = 'Lỗi khi thêm khách hàng: ' . $stmt->error;
        } else {
            $customer_id = $stmt->insert_id;
            $stmt->close();

            // Thêm đơn hàng
            $stmt2 = $conn->prepare("INSERT INTO paymentbill (pb_date, customer_id, customer_name, customer_contact_no, purchase_mobile_imei_no, purchase_mobile_company, purchase_mobile_series, purchase_mobile_model, purchase_mobile_price, total_paid_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("sississsdd", $pb_date, $customer_id, $customer_name, $customer_contact_no, $purchase_mobile_imei_no, $purchase_mobile_company, $purchase_mobile_series, $purchase_mobile_model, $purchase_mobile_price, $total_paid_amount);
            if ($stmt2->execute()) {
                $success = 'Thêm đơn hàng thành công!';
            } else {
                $error = 'Lỗi khi thêm đơn hàng: ' . $stmt2->error;
            }
            $stmt2->close();
        }
    }
}

// Lấy danh sách điện thoại trong kho
$mobiles_result = mysqli_query($conn, "SELECT imei_number, company_name, company_series, model_no, price FROM mobile ORDER BY company_name, company_series, model_no");

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Thêm đơn hàng mới</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        form { max-inline-size: 700px; margin: auto; background: #f9f9f9; padding: 20px; border-radius: 10px; }
        label { display: block; margin-block-start: 15px; font-weight: bold; }
        input, select { inline-size: 100%; padding: 8px; margin-block-start: 5px; }
        .btn-submit { margin-block-start: 20px; padding: 10px 15px; background: #4c2bb6; color: white; border: none; cursor: pointer; border-radius: 5px; }
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

<h2>Thêm đơn hàng mới</h2>

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
    <input type="date" id="pb_date" name="pb_date" required value="<?= date('Y-m-d') ?>" />

    <label for="customer_name">Tên khách hàng</label>
    <input type="text" id="customer_name" name="customer_name" required />

    <label for="customer_contact_no">Số điện thoại khách</label>
    <input type="text" id="customer_contact_no" name="customer_contact_no" required />

    <label for="customer_address">Địa chỉ khách hàng</label>
    <input type="text" id="customer_address" name="customer_address" />

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
            >
                <?= htmlspecialchars($mobile['company_name'] . ' ' . $mobile['company_series'] . ' ' . $mobile['model_no']) ?>
                - <?= number_format($mobile['price'], 0, ',', '.') ?> đ
            </option>
        <?php endwhile; ?>
    </select>

    <label for="purchase_mobile_company">Hãng điện thoại</label>
    <input type="text" id="purchase_mobile_company" name="purchase_mobile_company" readonly />

    <label for="purchase_mobile_series">Dòng điện thoại</label>
    <input type="text" id="purchase_mobile_series" name="purchase_mobile_series" readonly />

    <label for="purchase_mobile_model">Model điện thoại</label>
    <input type="text" id="purchase_mobile_model" name="purchase_mobile_model" readonly />

    <label for="purchase_mobile_price">Giá điện thoại</label>
    <input type="number" id="purchase_mobile_price" name="purchase_mobile_price" readonly />

    <label for="quantity">Số lượng mua</label>
    <input type="number" id="quantity" name="quantity" value="1" min="1" oninput="calculateTotal()" required />

    <label for="total_paid_amount">Tổng tiền thanh toán</label>
    <input type="number" id="total_paid_amount" name="total_paid_amount" readonly required />

    <button class="btn-submit" type="submit">Thêm đơn hàng</button>
</form>

</body>
</html>





