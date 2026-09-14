<?php
require_once __DIR__ . '/auth.php';
if (!isset($error)) {
  $error = $_SESSION['login_error'] ?? '';
}
unset($_SESSION['login_error']);
$allowSignup = !admin_table_has_records($conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <!-- VI_VALIDATION_MARKER: 2026-04-22 -->
  <title>Đăng nhập</title>
  <style>
    body {
      margin: 0;
      font-family: system-ui, sans-serif;
      background: #eee;
    }
    .container {
      width: 900px;
      margin: 60px auto;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      display: flex;
      box-shadow: 0 0 30px rgba(0,0,0,0.2);
    }
    .left {
      flex: 1;
      background: #000;
    }
    .left img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .right {
      flex: 1;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    h2 {
      margin-bottom: 20px;
    }
    .error-message {
      color: red;
      background: #ffeaea;
      padding: 12px 15px;
      border-radius: 8px;
      font-weight: bold;
      margin-bottom: 15px;
    }
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
    input {
      padding: 12px;
      width: 100%;
      margin-bottom: 15px;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #000;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background: #333;
    }
    .bottom {
      margin-top: 20px;
      text-align: center;
    }
    .bottom a {
      color: purple;
      text-decoration: none;
    }
    .bottom a:hover {
      text-decoration: underline;
    }
    .forgot {
      margin-top: -10px;
      margin-bottom: 20px;
      text-align: right;
    }
    .forgot a {
      font-size: 14px;
      color: #007bff;
      text-decoration: none;
    }
    .forgot a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="left">
      <img src="https://vatvostudio.vn/wp-content/uploads/2023/10/Flagship-2023.jpg" alt="banner">
    </div>
    <div class="right">
      <h2>Đăng nhập tài khoản</h2>

      <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <div id="form-error-summary" class="error-summary" style="display:none">Vui lòng nhập đầy đủ thông tin.</div>

      <form method="post" action="login_action.php" novalidate>
        <input
          type="email"
          name="username"
          placeholder="Nhập email"
          required
          value="<?= htmlspecialchars($username ?? '') ?>"
          oninvalid="this.setCustomValidity(this.validity.valueMissing ? 'Vui lòng nhập email.' : 'Vui lòng nhập địa chỉ email hợp lệ (ví dụ: ten@gmail.com).')"
          oninput="this.setCustomValidity('')"
        >
        <input
          type="password"
          name="password"
          placeholder="Mật khẩu"
          required
          oninvalid="this.setCustomValidity('Vui lòng nhập mật khẩu.')"
          oninput="this.setCustomValidity('')"
        >
        <div class="forgot">
          <a href="forgot.php">Quên mật khẩu?</a>
        </div>
        <button type="submit">Đăng nhập</button>
      </form>

      <script>
        (function () {
          const form = document.querySelector('form');
          if (!form) return;
          const summary = document.getElementById('form-error-summary');
          const messageFor = (input) => {
            const name = input.getAttribute('name') || '';
            if (input.validity.valueMissing) {
              if (name === 'username') return 'Vui lòng nhập email.';
              if (name === 'password') return 'Vui lòng nhập mật khẩu.';
              return 'Vui lòng điền vào trường này.';
            }
            if (input.validity.typeMismatch && input.type === 'email') {
              return 'Vui lòng nhập địa chỉ email hợp lệ (ví dụ: ten@gmail.com).';
            }
            return '';
          };

          const inputs = Array.from(form.querySelectorAll('input'));

          const errorNodeFor = (input) => {
            let node = input.nextElementSibling;
            if (node && node.classList && node.classList.contains('field-error')) return node;
            node = document.createElement('div');
            node.className = 'field-error';
            node.style.display = 'none';
            input.insertAdjacentElement('afterend', node);
            return node;
          };

          const validateAndRender = () => {
            let anyInvalid = false;
            let anyMissing = false;
            inputs.forEach((input) => {
              input.setCustomValidity('');
              const msg = messageFor(input);
              const err = errorNodeFor(input);
              if (msg) {
                anyInvalid = true;
                if (input.validity.valueMissing) anyMissing = true;
                err.textContent = msg;
                err.style.display = 'block';
                input.classList.add('is-invalid');
                input.setAttribute('aria-invalid', 'true');
              } else {
                err.textContent = '';
                err.style.display = 'none';
                input.classList.remove('is-invalid');
                input.removeAttribute('aria-invalid');
              }
            });

            if (summary) {
              if (anyInvalid) {
                summary.textContent = anyMissing
                  ? 'Vui lòng nhập đầy đủ thông tin.'
                  : 'Vui lòng kiểm tra lại thông tin.';
                summary.style.display = 'block';
              } else {
                summary.style.display = 'none';
              }
            }

            return !anyInvalid;
          };

          inputs.forEach((input) => input.addEventListener('input', validateAndRender));

          form.addEventListener('submit', (e) => {
            if (!validateAndRender()) {
              e.preventDefault();
            }
          });
        })();
      </script>

      <div class="bottom">
        Chưa có tài khoản? <a href="signup.php">Đăng ký</a>
      </div>
    </div>
  </div>
</body>
</html>





