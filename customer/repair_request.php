<?php
session_start();
include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['account_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$accountId = (int) $_SESSION['account_id'];
$message = null;
$messageType = 'success';

$accountStmt = $pdo->prepare("SELECT accounts_name, email, phone FROM accounts WHERE account_id = ?");
$accountStmt->execute([$accountId]);
$account = $accountStmt->fetch(PDO::FETCH_ASSOC) ?: [];

$customerName = trim((string) ($account['accounts_name'] ?? ''));
$contactPhone = trim((string) ($account['phone'] ?? ''));
$deviceModel = '';
$issueDescription = '';

$purchasedStmt = $pdo->prepare("
    SELECT DISTINCT pb.purchase_mobile_imei_no AS imei_number,
           pb.purchase_mobile_company AS company_name,
           pb.purchase_mobile_series AS company_series,
           pb.purchase_mobile_model AS model_no
    FROM paymentbill pb
    WHERE pb.customer_id = ?
    ORDER BY pb.purchase_mobile_company ASC, pb.purchase_mobile_series ASC, pb.purchase_mobile_model ASC
");
$purchasedStmt->execute([$accountId]);
$purchasedDevices = $purchasedStmt->fetchAll(PDO::FETCH_ASSOC);
$hasPurchasedProducts = count($purchasedDevices) > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$hasPurchasedProducts) {
        $message = 'Bạn chưa có sản phẩm đã mua nên chưa thể gửi yêu cầu sửa chữa.';
        $messageType = 'error';
    } else {
        $customerName = trim((string) ($_POST['customer_name'] ?? ''));
        $contactPhone = trim((string) ($_POST['contact_phone'] ?? ''));
        $deviceModel = trim((string) ($_POST['device_model'] ?? ''));
        $issueDescription = trim((string) ($_POST['issue_description'] ?? ''));

        $allowedModels = array_map(static function (array $item): string {
            return trim(($item['company_name'] ?? '') . ' ' . ($item['company_series'] ?? '') . ' ' . ($item['model_no'] ?? ''));
        }, $purchasedDevices);

        if ($customerName === '' || $deviceModel === '' || $issueDescription === '') {
            $message = 'Vui lòng nhập đầy đủ họ tên, thiết bị và mô tả lỗi.';
            $messageType = 'error';
        } elseif (!in_array($deviceModel, $allowedModels, true)) {
            $message = 'Bạn chỉ có thể gửi yêu cầu sửa chữa cho sản phẩm đã mua trong tài khoản này.';
            $messageType = 'error';
        } else {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS repair_requests (
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
                )
            ");

            $insert = $pdo->prepare("
                INSERT INTO repair_requests (
                    customer_name,
                    contact_phone,
                    device_model,
                    issue_description,
                    status,
                    request_date
                ) VALUES (?, ?, ?, ?, 'pending', ?)
            ");

            $ok = $insert->execute([
                $customerName,
                $contactPhone,
                $deviceModel,
                $issueDescription,
                date('Y-m-d'),
            ]);

            if ($ok) {
                $message = 'Đã gửi yêu cầu sửa chữa thành công. Bộ phận kỹ thuật sẽ tiếp nhận sớm.';
                $messageType = 'success';
                $deviceModel = '';
                $issueDescription = '';
            } else {
                $message = 'Không thể gửi yêu cầu sửa chữa. Vui lòng thử lại.';
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gửi Yêu Cầu Sửa Chữa | ShopDunk</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
            color: #111827;
        }

        .page-wrap {
            max-width: 1080px;
            margin: 32px auto 60px;
            padding: 0 20px;
        }

        .hero-card,
        .form-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            border: 1px solid #e6edf5;
        }

        .hero-card {
            padding: 28px 30px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 20px;
            align-items: center;
        }

        .hero-card h1 {
            margin: 0 0 10px;
            font-size: 34px;
            line-height: 1.15;
        }

        .hero-card p {
            margin: 0;
            color: #4b5563;
            line-height: 1.65;
            font-size: 16px;
        }

        .hero-side {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 20px;
            padding: 20px;
        }

        .hero-side ul {
            margin: 0;
            padding-left: 18px;
            color: #1e3a8a;
            line-height: 1.8;
            font-weight: 600;
        }

        .form-card {
            padding: 28px 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 22px;
        }

        .field-full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #334155;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            font-size: 15px;
            box-sizing: border-box;
            background: #fff;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        .actions {
            margin-top: 22px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            border-radius: 14px;
            padding: 13px 22px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-primary:disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #0f172a;
        }

        .flash {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            font-weight: 700;
        }

        .flash.success {
            background: #dcfce7;
            color: #166534;
        }

        .flash.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .owned-list {
            margin: 0;
            padding-left: 20px;
            color: #334155;
            line-height: 1.8;
        }

        .owned-box {
            margin-bottom: 20px;
            padding: 16px 18px;
            background: #f8fbff;
            border: 1px solid #dbeafe;
            border-radius: 16px;
        }

        @media (max-width: 900px) {
            .hero-card,
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-wrap">
    <section class="hero-card">
        <div>
            <h1>Gửi yêu cầu sửa chữa</h1>
            <p>Mô tả nhanh tình trạng thiết bị của bạn để ShopDunk tiếp nhận, theo dõi và chuyển thẳng về bộ phận quản lý sửa chữa trong hệ thống nội bộ.</p>
        </div>
        <div class="hero-side">
            <ul>
                <li>Tiếp nhận yêu cầu ngay trong ngày</li>
                <li>Chuyển thẳng sang module quản lý sửa chữa</li>
                <li>Kỹ thuật viên có thể phân công và cập nhật tiến độ</li>
            </ul>
        </div>
    </section>

    <section class="form-card">
        <?php if ($message !== null): ?>
            <div class="flash <?= htmlspecialchars($messageType) ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (!$hasPurchasedProducts): ?>
            <div class="flash error">Bạn chưa mua sản phẩm nào nên hiện chưa thể gửi yêu cầu sửa chữa.</div>
        <?php else: ?>
            <div class="owned-box">
                <strong>Sản phẩm đã mua có thể gửi sửa chữa:</strong>
                <ul class="owned-list">
                    <?php foreach ($purchasedDevices as $item): ?>
                        <li><?= htmlspecialchars(trim(($item['company_name'] ?? '') . ' ' . ($item['company_series'] ?? '') . ' ' . ($item['model_no'] ?? ''))) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-grid">
                <div>
                    <label for="customer_name">Họ và tên</label>
                    <input id="customer_name" name="customer_name" type="text" required value="<?= htmlspecialchars($customerName) ?>" <?= !$hasPurchasedProducts ? 'disabled' : '' ?>>
                </div>

                <div>
                    <label for="contact_phone">Số điện thoại</label>
                    <input id="contact_phone" name="contact_phone" type="text" value="<?= htmlspecialchars($contactPhone) ?>" <?= !$hasPurchasedProducts ? 'disabled' : '' ?>>
                </div>

                <div class="field-full">
                    <label for="device_model">Thiết bị cần sửa</label>
                    <select id="device_model" name="device_model" required <?= !$hasPurchasedProducts ? 'disabled' : '' ?>>
                        <option value="">-- Chọn sản phẩm đã mua --</option>
                        <?php foreach ($purchasedDevices as $item): ?>
                            <?php $optionLabel = trim(($item['company_name'] ?? '') . ' ' . ($item['company_series'] ?? '') . ' ' . ($item['model_no'] ?? '')); ?>
                            <option value="<?= htmlspecialchars($optionLabel) ?>" <?= $deviceModel === $optionLabel ? 'selected' : '' ?>><?= htmlspecialchars($optionLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field-full">
                    <label for="issue_description">Mô tả lỗi</label>
                    <textarea id="issue_description" name="issue_description" required placeholder="Ví dụ: màn hình chớp, pin tụt nhanh, loa rè, máy không lên nguồn..." <?= !$hasPurchasedProducts ? 'disabled' : '' ?>><?= htmlspecialchars($issueDescription) ?></textarea>
                </div>
            </div>

            <div class="actions">
                <button class="btn btn-primary" type="submit" <?= !$hasPurchasedProducts ? 'disabled' : '' ?>>Gửi yêu cầu</button>
                <a class="btn btn-secondary" href="profile.php">Quay lại tài khoản</a>
            </div>
        </form>
    </section>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>






