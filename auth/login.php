<?php require_once __DIR__ . '/../config/paths.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <!-- VI_VALIDATION_MARKER: 2026-04-22 -->
  <title>Đăng nhập vào PulseTech</title>
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
    .form-box .alt-login {
      display: flex;
      justify-content: space-between;
      margin: 10px 0;
    }
    .form-box .alt-login button {
      flex: 1;
      margin: 0 5px;
      background: #fff;
      border: 1px solid #ccc;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
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
    <img src="https://techcrunch.com/wp-content/uploads/2024/09/apple-iphone-16-pro.jpg?resize=1200,675" alt="Login Image">
  </div>
  <div class="right">
    <div class="form-box">
      <div class="brand-mark">
        <img src="../images/logoshopdunk.png" alt="ShopDunk Logo">
        <strong>ShopDunk</strong>
      </div>
      <h2>Đăng nhập vào PulseTech</h2>
      <?php if (isset($_GET['signup']) && $_GET['signup'] == '1'): ?>
        <div style="color: #155724; background: #d4edda; padding: 10px 12px; border-radius: 8px; font-weight: 600; margin-bottom: 12px;">
          Đăng ký thành công. Vui lòng đăng nhập.
        </div>
      <?php endif; ?>
      <?php if (!empty($_SESSION['login_error'])): ?>
  <div style="color: red; margin-bottom: 15px;">
    <?= htmlspecialchars($_SESSION['login_error']) ?>
  </div>
  <?php unset($_SESSION['login_error']); ?>
<?php endif; ?>     
      <div id="server-error" class="error" style="display:none"></div>
      <div id="form-error-summary" class="error-summary" style="display:none">Vui lòng nhập đầy đủ thông tin.</div>
      <form id="loginForm" method="post" novalidate>
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
          type="password"
          name="password"
          placeholder="Mật khẩu"
          required
          oninvalid="this.setCustomValidity('Vui lòng nhập mật khẩu.')"
          oninput="this.setCustomValidity('')"
        />
        <button type="submit">Đăng nhập</button>
      </form>
      <div class="links">
        <a href="signup.php">Đăng ký ngay</a> |
        <a href="forgot_password.php">Quên mật khẩu?</a>
      </div>
    </div>
  </div>
</div>
<script>
  document.title = 'Đăng nhập ShopDunk';
  const loginHeading = document.querySelector('.form-box h2');
  if (loginHeading) loginHeading.textContent = 'Đăng nhập ShopDunk';
  const API_BASE_URL = window.API_BASE_URL || (window.location.origin + <?= json_encode(app_base_url()) ?>);

  const form = document.querySelector('form');
  if (form) {
    const summary = document.getElementById('form-error-summary');
    const messageFor = (input) => {
      const name = input.getAttribute('name') || '';
      if (input.validity.valueMissing) {
        if (name === 'email') return 'Vui lòng nhập email.';
        if (name === 'password') return 'Vui lòng nhập mật khẩu.';
        return 'Vui lòng điền vào trường này.';
      }
      if (name === 'email' && (input.validity.typeMismatch || input.validity.patternMismatch)) {
        return 'Email phải có định dạng @gmail.com (ví dụ: ten@gmail.com).';
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

    const serverError = document.getElementById('server-error');
    const loginForm = document.getElementById('loginForm');

    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (!validateAndRender()) return;

      const formData = new FormData(loginForm);
      const payload = {
        email: formData.get('email'),
        password: formData.get('password')
      };

      serverError.style.display = 'none';
      try {
        const response = await fetch(`${API_BASE_URL}/auth/api/login.php`, {
          method: 'POST',
          mode: 'cors',
          credentials: 'include',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (!data.success) {
          serverError.textContent = data.message || 'Đăng nhập thất bại.';
          serverError.style.display = 'block';
          return;
        }
        window.location.href = `${API_BASE_URL}/index.php`;
      } catch (err) {
        serverError.textContent = 'Lỗi máy chủ. Vui lòng thử lại.';
        serverError.style.display = 'block';
      }
    });
  }
</script>
</body>
</html>






