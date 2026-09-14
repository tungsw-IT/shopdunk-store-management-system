<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Địa chỉ giao hàng</title>
  <link rel="stylesheet" href="../assets/css/style_cart.css" />
  <style>
    body { background: #f4f6fb; margin: 0; font-family: Arial, sans-serif; }
    .page-shell { max-width: 900px; margin: 36px auto; padding: 16px; }
    .page-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
    .page-header h1 { margin: 0; font-size: 1.9rem; color: #111827; }
    .form-card { background: #fff; border-radius: 22px; box-shadow: 0 16px 44px rgba(15,23,42,.1); padding: 26px; }
    .address-form .form-group { margin-bottom: 18px; }
    .address-form label { display: block; margin-bottom: 8px; color: #475569; font-weight: 600; }
    .address-form input, .address-form select { width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 12px; font-size: 15px; color: #111827; }
    .input-group { display: flex; gap: 12px; flex-wrap: wrap; }
    .select-short { max-width: 160px; }
    .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 52px; padding: 0 24px; border: none; border-radius: 14px; background: #1d4ed8; color: #fff; font-weight: 700; cursor: pointer; }
    .form-errors { margin-bottom: 20px; padding: 18px 20px; border-radius: 16px; background: #fee2e2; color: #991b1b; }
    .info-note { margin-bottom: 16px; color: #475569; }
    .field-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
    @media (max-width: 720px) { .input-group { flex-direction: column; } }
  </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="page-shell">
  <div class="page-header">
    <a href="javascript:history.back()" style="font-size:24px; color:#1d4ed8; text-decoration:none;">←</a>
    <h1>Địa chỉ giao hàng</h1>
  </div>

  <div class="form-card">
    <div id="formErrors" class="form-errors" style="display:none;"></div>
    <p class="info-note">Nhập thông tin địa chỉ giao hàng để tiếp tục.</p>
    <form id="addressForm" class="address-form" autocomplete="off">
      <div class="field-row">
        <div class="form-group">
          <label for="fullname">Họ & tên</label>
          <input type="text" id="fullname" name="fullname" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required>
        </div>
      </div>

      <div class="field-row">
        <div class="form-group">
          <label>Giới tính</label>
          <div class="input-group">
            <label><input type="radio" name="gender" value="male"> Nam</label>
            <label><input type="radio" name="gender" value="female"> Nữ</label>
          </div>
        </div>
        <div class="form-group">
          <label for="country_code">Mã quốc gia</label>
          <select id="country_code" name="country_code" class="select-short" required>
            <option value="+84">VN +84</option>
            <option value="+1">US +1</option>
            <option value="+7">RU +7</option>
            <option value="+44">UK +44</option>
            <option value="+81">JP +81</option>
            <option value="+82">KR +82</option>
            <option value="+86">CN +86</option>
            <option value="+49">DE +49</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="phone">Số điện thoại</label>
        <input type="tel" id="phone" name="phone" required>
      </div>

      <div class="form-group">
        <label for="country">Quốc gia</label>
        <select id="country" name="country" required>
          <option value="Viet Nam">Viet Nam</option>
          <option value="USA">USA</option>
          <option value="Russia">Russia</option>
          <option value="United Kingdom">United Kingdom</option>
          <option value="Japan">Japan</option>
          <option value="Korea">Korea</option>
          <option value="China">China</option>
          <option value="Germany">Germany</option>
          <option value="France">France</option>
          <option value="Italy">Italy</option>
          <option value="Australia">Australia</option>
          <option value="Singapore">Singapore</option>
          <option value="Thailand">Thailand</option>
          <option value="Indonesia">Indonesia</option>
          <option value="Philippines">Philippines</option>
          <option value="Malaysia">Malaysia</option>
          <option value="India">India</option>
          <option value="Spain">Spain</option>
          <option value="Portugal">Portugal</option>
          <option value="Sweden">Sweden</option>
          <option value="Netherlands">Netherlands</option>
          <option value="Switzerland">Switzerland</option>
          <option value="Hong Kong">Hong Kong</option>
          <option value="New Zealand">New Zealand</option>
          <option value="Brazil">Brazil</option>
          <option value="Egypt">Egypt</option>
        </select>
      </div>

      <div class="form-group">
        <label for="city">Tỉnh/Thành</label>
        <select id="city" name="city" required>
          <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
          <option value="Hà Nội">Hà Nội</option>
          <option value="Huế">Huế</option>
          <option value="Bắc Giang">Bắc Giang</option>
          <option value="Quảng Trị">Quảng Trị</option>
          <option value="Lai Châu">Lai Châu</option>
          <option value="Điện Biên">Điện Biên</option>
          <option value="Quảng Ninh">Quảng Ninh</option>
          <option value="Sơn La">Sơn La</option>
          <option value="Thanh Hóa">Thanh Hóa</option>
          <option value="Nghệ An">Nghệ An</option>
          <option value="Hà Tĩnh">Hà Tĩnh</option>
          <option value="Cao Bằng">Cao Bằng</option>
          <option value="Lào Cai">Lào Cai</option>
          <option value="Thái Nguyên">Thái Nguyên</option>
          <option value="Phú Thọ">Phú Thọ</option>
        </select>
      </div>

      <div class="form-group">
        <label for="detail">Chi tiết địa chỉ</label>
        <input type="text" id="detail" name="detail" placeholder="Số nhà, đường, phường, quận" required>
      </div>

      <button type="submit" class="btn">Lưu địa chỉ</button>
    </form>
  </div>
</div>
<script>
const API_BASE_URL = window.API_BASE_URL || (window.location.origin + <?= json_encode(app_base_url()) ?>);
const form = document.getElementById('addressForm');
const formErrors = document.getElementById('formErrors');
const apiUrl = `${API_BASE_URL}/checkout/api/address.php`;
const summaryUrl = `${API_BASE_URL}/checkout/api/summary.php`;

const codeToCountry = {
  '+84': 'Viet Nam', '+1': 'USA', '+7': 'Russia', '+44': 'United Kingdom',
  '+81': 'Japan', '+82': 'Korea', '+86': 'China', '+49': 'Germany'
};
const countryToCode = Object.fromEntries(Object.entries(codeToCountry).map(([k,v]) => [v,k]));

function showError(message) {
  formErrors.style.display = 'block';
  formErrors.innerHTML = `<div>${message}</div>`;
}

function hideError() {
  formErrors.style.display = 'none';
  formErrors.innerHTML = '';
}

function populateFields(data) {
  if (!data) return;
  document.getElementById('fullname').value = data.fullname || '';
  document.getElementById('email').value = data.email || '';
  document.getElementById('phone').value = data.phone || '';
  document.getElementById('country_code').value = data.country_code || '+84';
  document.getElementById('country').value = data.country || 'Viet Nam';
  document.getElementById('city').value = data.city || 'TP. Hồ Chí Minh';
  document.getElementById('detail').value = data.detail || '';
  if (data.gender === 'female') {
    form.querySelector('input[name="gender"][value="female"]').checked = true;
  } else {
    form.querySelector('input[name="gender"][value="male"]').checked = true;
  }
}

async function loadInitialData() {
  try {
    const response = await fetch(summaryUrl, { credentials: 'include' });
    if (response.status === 401) {
      window.location.href = '../auth/login.php';
      return;
    }
    const result = await response.json();
    if (result.success && result.data && result.data.shipping_info) {
      populateFields(result.data.shipping_info);
    }
  } catch (error) {
    console.error(error);
  }
}

document.getElementById('country_code').addEventListener('change', function() {
  const country = codeToCountry[this.value];
  if (country) {
    document.getElementById('country').value = country;
  }
});

document.getElementById('country').addEventListener('change', function() {
  const code = countryToCode[this.value];
  if (code) {
    document.getElementById('country_code').value = code;
  }
});

form.addEventListener('submit', async function(event) {
  event.preventDefault();
  hideError();

  const formData = new FormData(form);
  const payload = {};
  for (const [key, value] of formData.entries()) {
    payload[key] = value.trim();
  }

  try {
    const response = await fetch(apiUrl, {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    if (response.status === 401) {
      window.location.href = '../auth/login.php';
      return;
    }
    const result = await response.json();
    if (!result.success) {
      showError(result.message || 'Lưu địa chỉ thất bại.');
      return;
    }
    window.location.href = '../checkout/payment_method.php';
  } catch (error) {
    showError('Lỗi máy chủ. Vui lòng thử lại.');
  }
});

loadInitialData();
</script>
</body>
</html>






