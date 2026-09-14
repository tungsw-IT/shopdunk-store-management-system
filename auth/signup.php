<?php require_once __DIR__ . '/../config/paths.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <!-- VI_VALIDATION_MARKER: 2026-04-22 -->
  <title>Đăng ký tài khoản PulseTech</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Arial, sans-serif; background: #eee; }
    .container { display: flex; width: 100vw; height: 100vh; }
    .left { flex: 1; background: #000; }
    .left img { width: 100%; height: 100%; object-fit: cover; }
    .right {
      flex: 1;
      background: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .form-box {
      width: 100%;
      max-width: 400px;
      padding: 40px;
    }
    .brand-mark {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      margin-bottom: 18px;
    }
    .brand-mark img {
      width: 180px;
      height: auto;
      display: block;
    }
    .brand-mark strong {
      font-size: 28px;
      letter-spacing: 1px;
    }
    .form-box h2 {
      margin-bottom: 20px;
    }
    .form-box input, .form-box button {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }
    .form-box button {
      background: #000;
      color: #fff;
      font-weight: bold;
      border: none;
      cursor: pointer;
    }
    .form-box .error { color: red; }
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
    .form-box .links { text-align: center; margin-top: 10px; font-size: 14px; }
    .form-box .links a { text-decoration: none; color: #333; }
  </style>
</head>
<body>
<div class="container">
  <div class="left">
    <img src="https://techcrunch.com/wp-content/uploads/2024/09/apple-iphone-16-pro.jpg?resize=1200,675" alt="Signup Image">
  </div>
  <div class="right">
    <div class="form-box">
      <div class="brand-mark">
        <img src="../images/logoshopdunk.png" alt="ShopDunk Logo">
        <strong>ShopDunk</strong>
      </div>
      <h2>Tạo tài khoản PulseTech</h2>
      <div id="server-error" class="error" style="display:none"></div>
      <div id="form-error-summary" class="error-summary" style="display:none">Vui lòng nhập đầy đủ thông tin.</div>
      <form id="signupForm" method="post" novalidate>
        <input
          type="email"
          name="email"
          placeholder="Email"
          required
          oninvalid="this.setCustomValidity(this.validity.valueMissing ? 'Vui lòng nhập email.' : 'Email phải có định dạng hợp lệ.')"
          oninput="this.setCustomValidity('')"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
        />
        <input
          type="text"
          name="phone"
          placeholder="Số điện thoại"
          required
          inputmode="numeric"
          pattern="0[0-9]{9}"
          maxlength="10"
          oninvalid="this.setCustomValidity(this.validity.valueMissing ? 'Vui lòng nhập số điện thoại.' : 'Số điện thoại phải bắt đầu bằng 0 và gồm đúng 10 chữ số.')"
          oninput="this.setCustomValidity('')"
          value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
        />
        <input
          type="password"
          name="password"
          placeholder="Mật khẩu"
          required
          pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[A-Za-z0-9]{6,}"
          oninvalid="this.setCustomValidity(this.validity.valueMissing ? 'Vui lòng nhập mật khẩu.' : 'Mật khẩu phải có ít nhất 6 ký tự, gồm ít nhất 1 chữ hoa, 1 chữ thường và 1 chữ số (không ký tự đặc biệt).')"
          oninput="this.setCustomValidity('')"
        />
        <input
          type="password"
          name="confirm_password"
          placeholder="Xác nhận mật khẩu"
          required
          oninvalid="this.setCustomValidity('Vui lòng nhập xác nhận mật khẩu.')"
          oninput="this.setCustomValidity('')"
        />
        <button type="submit">Đăng ký</button>
      </form>
      <div class="links">
        <a href="login.php">Đã có tài khoản? Đăng nhập</a>
      </div>
    </div>
  </div>
</div>
<script>
  document.title = 'Đăng ký tài khoản ShopDunk';
  const signupHeading = document.querySelector('.form-box h2');
  if (signupHeading) signupHeading.textContent = 'Tạo tài khoản ShopDunk';
  const API_BASE_URL = window.API_BASE_URL || (window.location.origin + <?= json_encode(app_base_url()) ?>);

  const form = document.querySelector('form');
  if (form) {
    const summary = document.getElementById('form-error-summary');
    const serverErrorEl = document.getElementById('server-error');
    const serverMsg = (serverErrorEl && serverErrorEl.dataset && serverErrorEl.dataset.message)
      ? serverErrorEl.dataset.message.trim()
      : '';
    const serverErrors = {};
    if (serverMsg) {
      if (serverMsg.includes('@gmail.com')) serverErrors.email = serverMsg;
      else if (serverMsg.toLowerCase().includes('số điện thoại')) serverErrors.phone = serverMsg;
      else if (serverMsg.toLowerCase().includes('xác nhận')) serverErrors.confirm_password = serverMsg;
      else if (serverMsg.toLowerCase().includes('mật khẩu')) serverErrors.password = serverMsg;
      else serverErrors.__summary = serverMsg;
    }

    const messageFor = (input) => {
      const name = input.getAttribute('name') || '';
      if (input.validity.customError) {
        if (name === 'confirm_password') return 'Mật khẩu xác nhận không khớp.';
        if (serverErrors[name]) return serverErrors[name];
      }
      if (input.validity.customError && name === 'confirm_password') {
        return 'Mật khẩu xác nhận không khớp.';
      }
      if (input.validity.valueMissing) {
        if (name === 'email') return 'Vui lòng nhập email.';
        if (name === 'phone') return 'Vui lòng nhập số điện thoại.';
        if (name === 'password') return 'Vui lòng nhập mật khẩu.';
        if (name === 'confirm_password') return 'Vui lòng nhập xác nhận mật khẩu.';
        return 'Vui lòng điền vào trường này.';
      }
      if (name === 'email' && (input.validity.typeMismatch || input.validity.patternMismatch)) {
        return 'Email phải có định dạng @gmail.com (ví dụ: ten@gmail.com).';
      }
      if (name === 'phone' && input.validity.patternMismatch) {
        return 'Số điện thoại phải bắt đầu bằng 0 và gồm đúng 10 chữ số.';
      }
      if (name === 'password' && input.validity.patternMismatch) {
        return 'Mật khẩu phải có ít nhất 6 ký tự, gồm ít nhất 1 chữ hoa, 1 chữ thường và 1 chữ số (không ký tự đặc biệt).';
      }
      return '';
    };

    const inputs = Array.from(form.querySelectorAll('input'));
    const ordered = [
      form.querySelector('input[name="email"]'),
      form.querySelector('input[name="phone"]'),
      form.querySelector('input[name="password"]'),
      form.querySelector('input[name="confirm_password"]'),
    ].filter(Boolean);
    let submitted = false;

    const errorNodeFor = (input) => {
      let node = input.nextElementSibling;
      if (node && node.classList && node.classList.contains('field-error')) return node;
      node = document.createElement('div');
      node.className = 'field-error';
      node.style.display = 'none';
      input.insertAdjacentElement('afterend', node);
      return node;
    };

    const setEnabledUntil = (_idx) => {
      // Không khóa input theo thứ tự; user được nhập hết một lượt.
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

    const computeEnabledIndex = () => ordered.length - 1;

    const updateProgressOnly = () => {
      // Trước khi bấm Lưu/Đăng ký: không hiển thị lỗi.
      clearAllErrors();
    };

    const validateAndRender = () => {
      let anyInvalid = false;
      let anyMissing = false;
      let hasInvalidEmailPhoneOrPassword = false;

      const enabledIdx = computeEnabledIndex();
      setEnabledUntil(enabledIdx);

      // Validate only from top -> bottom until first invalid (sequential)
      for (let i = 0; i <= enabledIdx; i++) {
        const el = ordered[i];
        // ensure confirm password mismatch customError is kept
        if (el.getAttribute('name') === 'confirm_password') {
          const pw = form.querySelector('input[name="password"]');
          if (pw && el.value && pw.value && el.value !== pw.value) {
            el.setCustomValidity('Mật khẩu xác nhận không khớp.');
          }
        }
        const msg = messageFor(el);
        const err = errorNodeFor(el);
        if (msg) {
          anyInvalid = true;
          if (el.validity.valueMissing) anyMissing = true;
          const n = el.getAttribute('name') || '';
          const isEmailInvalid = n === 'email' && (el.validity.typeMismatch || el.validity.patternMismatch || el.validity.customError);
          const isPhoneInvalid = n === 'phone' && (el.validity.patternMismatch || el.validity.customError);
          const isPasswordInvalid = n === 'password' && (el.validity.patternMismatch || el.validity.customError);
          if (isEmailInvalid || isPhoneInvalid || isPasswordInvalid) hasInvalidEmailPhoneOrPassword = true;
          err.textContent = msg;
          err.style.display = 'block';
          el.classList.add('is-invalid');
          el.setAttribute('aria-invalid', 'true');
          // Stop at first invalid -> do not show errors for the rest
          break;
        } else {
          err.textContent = '';
          err.style.display = 'none';
          el.classList.remove('is-invalid');
          el.removeAttribute('aria-invalid');
        }
      }

      if (summary) {
        if (anyInvalid) {
          if (anyMissing) {
            summary.textContent = 'Vui lòng nhập đầy đủ thông tin.';
          } else if (hasInvalidEmailPhoneOrPassword) {
            summary.textContent = 'Email, SĐT hoặc mật khẩu không hợp lệ.';
          } else if (serverErrors.__summary) {
            summary.textContent = serverErrors.__summary;
          } else {
            summary.textContent = 'Vui lòng kiểm tra lại thông tin.';
          }
          summary.style.display = 'block';
        } else {
          summary.style.display = 'none';
        }
      }

      return !anyInvalid;
    };

    ordered.forEach((input) => input.addEventListener('input', () => {
      const name = input.getAttribute('name') || '';
      if (serverErrors[name]) delete serverErrors[name];
      if (!submitted) {
        clearAllErrors();
        updateProgressOnly();
      } else {
        validateAndRender();
      }
    }));

    const serverError = document.getElementById('server-error');
    const signupForm = document.getElementById('signupForm');

    signupForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      submitted = true;
      if (!validateAndRender()) {
        const firstInvalid = ordered.find((el) => !el.disabled && !el.checkValidity());
        if (firstInvalid) firstInvalid.focus();
        return;
      }

      const formData = new FormData(signupForm);
      const payload = {
        email: formData.get('email'),
        phone: formData.get('phone'),
        password: formData.get('password'),
        confirm_password: formData.get('confirm_password')
      };

      serverError.style.display = 'none';
      try {
        const response = await fetch(`${API_BASE_URL}/auth/api/register.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (!data.success) {
          serverError.textContent = data.message || 'Đăng ký thất bại.';
          serverError.style.display = 'block';
          return;
        }
        window.location.href = 'login.php?signup=1';
      } catch (err) {
        serverError.textContent = 'Lỗi máy chủ. Vui lòng thử lại.';
        serverError.style.display = 'block';
      }
    });

    // Render server-side errors (if any) under the correct field on load
    if (serverMsg) {
      submitted = true;
      validateAndRender();
    } else {
      // initial sequential state
      clearAllErrors();
      updateProgressOnly();
    }
  }
</script>
</body>
</html>






