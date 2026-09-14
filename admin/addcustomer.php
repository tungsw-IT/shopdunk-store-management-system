<?php
require_once __DIR__ . '/auth.php';
require_permission('customers');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name && $gender && $email && $phone && $address) {
        if (!preg_match('/^[A-Za-z0-9._%+\-]+@gmail\.com$/i', $email)) {
            $error = "Email phải có định dạng @gmail.com.";
        } elseif (!preg_match('/^0\d{9}$/', $phone)) {
            $error = "Số điện thoại phải bắt đầu bằng 0 và gồm đúng 10 chữ số.";
        } else {
            // Kiểm tra trùng email / số điện thoại
            $emailExists = false;
            $phoneExists = false;

            $checkEmail = $conn->prepare("SELECT customer_id FROM customer WHERE customer_email = ? LIMIT 1");
            if ($checkEmail) {
                $checkEmail->bind_param("s", $email);
                $checkEmail->execute();
                $res = $checkEmail->get_result();
                $emailExists = $res && $res->num_rows > 0;
                $checkEmail->close();
            }

            $checkPhone = $conn->prepare("SELECT customer_id FROM customer WHERE customer_contact_no = ? LIMIT 1");
            if ($checkPhone) {
                $checkPhone->bind_param("s", $phone);
                $checkPhone->execute();
                $res = $checkPhone->get_result();
                $phoneExists = $res && $res->num_rows > 0;
                $checkPhone->close();
            }

            if ($emailExists && $phoneExists) {
                $error = "Email và số điện thoại đã tồn tại.";
            } elseif ($emailExists) {
                $error = "Email đã tồn tại.";
            } elseif ($phoneExists) {
                $error = "Số điện thoại đã tồn tại.";
            } else {
                $stmt = $conn->prepare("INSERT INTO customer (customer_name, customer_gender, customer_email, customer_contact_no, customer_address) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $name, $gender, $email, $phone, $address);
                $stmt->execute();
                // Thay vì hiển thị thông báo tại đây, chuyển hướng về customer.php và truyền flag
                header('Location: customer.php?success=1');
                exit;
            }
        }
    } else {
        $error = "Vui lòng điền đầy đủ thông tin.";
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm khách hàng</title>
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
            background: #28a745; color: #fff; padding: 12px 22px;
            border: none; border-radius: 6px; font-weight: bold;
            cursor: pointer;
        }
        button:hover { background: #218838; }
    .msg { margin-bottom: 15px; font-weight: bold; }
    .error { color: red; }
    .success { color: green; }
    .error-summary {
        color: #b00020;
        background: #ffeaea;
        padding: 10px 12px;
        border-radius: 8px;
        font-weight: 600;
        margin-bottom: 12px;
    }
    .field-error {
        color: #b00020;
        font-size: 13px;
        margin: -6px 0 10px 0;
    }
    .is-invalid {
        border-color: #b00020 !important;
        outline: none;
    }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Thêm Khách Hàng Mới</h2>
    <div id="server-error" class="msg error" style="display:none" data-message="<?= htmlspecialchars($error ?? '', ENT_QUOTES) ?>"></div>
    <div id="form-error-summary" class="error-summary" style="display:none">Vui lòng nhập đầy đủ thông tin.</div>
    <?php if ($success): ?><div class="msg success"><?= $success ?></div><?php endif; ?>
    <form method="POST" novalidate>
        <input type="text" name="name" placeholder="Tên khách hàng" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        <select name="gender" required>
            <option value="">-- Giới tính --</option>
            <option value="Nam" <?= (($_POST['gender'] ?? '') === 'Nam') ? 'selected' : '' ?>>Nam</option>
            <option value="Nữ" <?= (($_POST['gender'] ?? '') === 'Nữ') ? 'selected' : '' ?>>Nữ</option>
        </select>
        <input
            type="email"
            name="email"
            placeholder="Email"
            required
            pattern="[A-Za-z0-9._%+-]+@gmail[.]com"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            oninvalid="this.setCustomValidity(this.validity.valueMissing ? 'Vui lòng nhập email.' : 'Email phải có định dạng @gmail.com (ví dụ: ten@gmail.com).')"
            oninput="this.setCustomValidity('')"
        >
        <input
            type="text"
            name="phone"
            placeholder="Số điện thoại"
            required
            inputmode="numeric"
            pattern="0[0-9]{9}"
            maxlength="10"
            value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
            oninvalid="this.setCustomValidity(this.validity.valueMissing ? 'Vui lòng nhập số điện thoại.' : 'Số điện thoại phải bắt đầu bằng 0 và gồm đúng 10 chữ số.')"
            oninput="this.setCustomValidity('')"
        >
        <textarea name="address" placeholder="Địa chỉ" rows="3" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
        <button type="submit">Thêm</button>
    </form>
</div>

<script>
  (function () {
    const form = document.querySelector('form');
    if (!form) return;

    const summary = document.getElementById('form-error-summary');
    const serverErrorEl = document.getElementById('server-error');
    const serverMsg = (serverErrorEl && serverErrorEl.dataset && serverErrorEl.dataset.message)
      ? serverErrorEl.dataset.message.trim()
      : '';

    const serverErrors = {};
    if (serverMsg) {
      const msgLower = serverMsg.toLowerCase();
      if (serverMsg.includes('@gmail.com')) serverErrors.email = serverMsg;
      else if (msgLower.includes('email đã tồn tại')) serverErrors.email = serverMsg;
      else if (msgLower.includes('số điện thoại đã tồn tại')) serverErrors.phone = serverMsg;
      else if (msgLower.includes('email và số điện thoại đã tồn tại')) {
        serverErrors.email = serverMsg;
        serverErrors.phone = serverMsg;
      } else if (msgLower.includes('số điện thoại')) serverErrors.phone = serverMsg;
      else serverErrors.__summary = serverMsg;
    }

    const ordered = [
      form.querySelector('input[name="name"]'),
      form.querySelector('select[name="gender"]'),
      form.querySelector('input[name="email"]'),
      form.querySelector('input[name="phone"]'),
      form.querySelector('textarea[name="address"]'),
    ].filter(Boolean);

    const emailRegex = /^[A-Za-z0-9._%+\-]+@gmail\.com$/i;
    const phoneRegex = /^0\d{9}$/;

    const errorNodeFor = (el) => {
      let node = el.nextElementSibling;
      if (node && node.classList && node.classList.contains('field-error')) return node;
      node = document.createElement('div');
      node.className = 'field-error';
      node.style.display = 'none';
      el.insertAdjacentElement('afterend', node);
      return node;
    };

    const clearAllErrors = () => {
      if (summary) summary.style.display = 'none';
      ordered.forEach((el) => {
        const err = errorNodeFor(el);
        err.textContent = '';
        err.style.display = 'none';
        el.classList.remove('is-invalid');
        el.removeAttribute('aria-invalid');
      });
    };

    const getValue = (el) => (el.tagName === 'SELECT' ? (el.value || '') : ((el.value || '').trim()));

    const validateField = (el) => {
      const name = el.getAttribute('name') || '';
      const value = getValue(el);

      if (serverErrors[name]) return serverErrors[name];

      if (!value) {
        if (name === 'name') return 'Vui lòng nhập tên khách hàng.';
        if (name === 'gender') return 'Vui lòng chọn giới tính.';
        if (name === 'email') return 'Vui lòng nhập email.';
        if (name === 'phone') return 'Vui lòng nhập số điện thoại.';
        if (name === 'address') return 'Vui lòng nhập địa chỉ.';
        return 'Vui lòng điền vào trường này.';
      }

      if (name === 'email' && !emailRegex.test(value)) {
        return 'Email phải có định dạng @gmail.com (ví dụ: ten@gmail.com).';
      }
      if (name === 'phone' && !phoneRegex.test(value)) {
        return 'Số điện thoại phải bắt đầu bằng 0 và gồm đúng 10 chữ số.';
      }

      return '';
    };

    const showOneError = (el, msg) => {
      const err = errorNodeFor(el);
      err.textContent = msg;
      err.style.display = 'block';
      el.classList.add('is-invalid');
      el.setAttribute('aria-invalid', 'true');
    };

    const showSummaryFor = (el, msg) => {
      if (!summary) return;
      const name = el.getAttribute('name') || '';
      const isMissing = !getValue(el);

      if (isMissing) summary.textContent = 'Vui lòng nhập đầy đủ thông tin.';
      else if (/đã tồn tại/i.test(msg)) summary.textContent = 'Email hoặc số điện thoại đã tồn tại.';
      else if (name === 'email' || name === 'phone') summary.textContent = 'Email hoặc số điện thoại không hợp lệ.';
      else if (serverErrors.__summary) summary.textContent = serverErrors.__summary;
      else summary.textContent = 'Vui lòng kiểm tra lại thông tin.';

      summary.style.display = 'block';
    };

    // Khi đang gõ: không validate, chỉ xóa lỗi của chính field đó
    ordered.forEach((el) => {
      const clearSelf = () => {
        const name = el.getAttribute('name') || '';
        if (serverErrors[name]) delete serverErrors[name];
        const err = errorNodeFor(el);
        err.textContent = '';
        err.style.display = 'none';
        el.classList.remove('is-invalid');
        el.removeAttribute('aria-invalid');
        if (summary) summary.style.display = 'none';
      };
      el.addEventListener('input', clearSelf);
      el.addEventListener('change', clearSelf);
    });

    form.addEventListener('submit', (e) => {
      clearAllErrors();

      // Nếu tất cả hợp lệ thì submit luôn (không bắt bấm nhiều lần)
      const allValid = ordered.every((el) => validateField(el) === '');
      if (allValid) return;

      e.preventDefault();

      // Mỗi lần bấm: chỉ show lỗi đầu tiên từ trên xuống
      for (const el of ordered) {
        const msg = validateField(el);
        if (msg) {
          showOneError(el, msg);
          showSummaryFor(el, msg);
          el.focus();
          break;
        }
      }
    });

    // Nếu có lỗi server-side thì hiển thị đúng field đầu tiên bị lỗi
    if (serverMsg) {
      clearAllErrors();
      for (const el of ordered) {
        const msg = validateField(el);
        if (msg) {
          showOneError(el, msg);
          showSummaryFor(el, msg);
          break;
        }
      }
    }
  })();
</script>
</body>
</html>





