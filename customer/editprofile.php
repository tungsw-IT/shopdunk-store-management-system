<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Chỉnh sửa thông tin</title>
  <link rel="stylesheet" href="../assets/css/style_cart.css" />
  <style>
    body { background: #f4f6fb; margin: 0; font-family: Arial, sans-serif; }
    .page-shell { max-width: 900px; margin: 36px auto; padding: 16px; }
    .page-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
    .page-header h1 { margin: 0; font-size: 1.9rem; color: #111827; }
    .form-card { background: #fff; border-radius: 22px; box-shadow: 0 16px 44px rgba(15,23,42,.1); padding: 26px; }
    .form-card .form-group { margin-bottom: 18px; }
    .form-card label { display: block; margin-bottom: 8px; color: #475569; font-weight: 600; }
    .form-card input, .form-card select { width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 12px; font-size: 15px; color: #111827; }
    .form-card .input-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; }
    .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 52px; padding: 0 24px; border: none; border-radius: 14px; background: #1d4ed8; color: #fff; font-weight: 700; cursor: pointer; }
    .error-box { margin-bottom: 20px; padding: 18px 20px; border-radius: 16px; background: #fee2e2; color: #991b1b; }
    .success-box { margin-bottom: 20px; padding: 18px 20px; border-radius: 16px; background: #dcfce7; color: #166534; }
    .field-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; }
    @media (max-width: 720px) { .field-grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="page-shell">
  <div class="page-header">
    <a href="javascript:history.back()" style="font-size:24px; color:#1d4ed8; text-decoration:none;">←</a>
    <h1>Chỉnh sửa thông tin</h1>
  </div>
  <div class="form-card">
    <div id="messageBox" style="display:none;"></div>
    <form id="editProfileForm">
      <div class="field-grid">
        <div class="form-group">
          <label for="accounts_name">Họ và tên</label>
          <input type="text" id="accounts_name" name="accounts_name" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required>
        </div>
      </div>
      <div class="field-grid">
        <div class="form-group">
          <label for="phone">Số điện thoại</label>
          <input type="text" id="phone" name="phone" required>
        </div>
        <div class="form-group">
          <label>Giới tính</label>
          <div class="input-row">
            <label><input type="radio" name="gender" value="male"> Nam</label>
            <label><input type="radio" name="gender" value="female"> Nữ</label>
          </div>
        </div>
      </div>
      <div class="field-grid">
        <div class="form-group">
          <label for="country_code">Mã quốc gia</label>
          <select id="country_code" name="country_code" required>
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
        <input type="text" id="detail" name="detail" required>
      </div>
      <button type="submit" class="btn">Lưu thay đổi</button>
    </form>
  </div>
</div>
<script>
const API_BASE_URL = window.API_BASE_URL || (window.location.origin + <?= json_encode(app_base_url()) ?>);
const userApiUrl = `${API_BASE_URL}/customer/api/user.php`;
const updateApiUrl = `${API_BASE_URL}/customer/api/user_update.php`;
const form = document.getElementById('editProfileForm');
const messageBox = document.getElementById('messageBox');
const codeToCountry = {
  '+84': 'Viet Nam', '+1': 'USA', '+7': 'Russia', '+44': 'United Kingdom',
  '+81': 'Japan', '+82': 'Korea', '+86': 'China', '+49': 'Germany'
};
const countryToCode = Object.fromEntries(Object.entries(codeToCountry).map(([k,v]) => [v,k]));

function showMessage(type, text) {
  messageBox.style.display = 'block';
  messageBox.className = type === 'error' ? 'error-box' : 'success-box';
  messageBox.textContent = text;
}

function hideMessage() {
  messageBox.style.display = 'none';
  messageBox.textContent = '';
}

function populateFields(data) {
  document.getElementById('accounts_name').value = data.user.accounts_name || '';
  document.getElementById('email').value = data.user.email || '';
  document.getElementById('phone').value = data.user.phone || '';
  const shipping = data.shipping_info || {};
  document.getElementById('country_code').value = shipping.country_code || '+84';
  document.getElementById('country').value = shipping.country || 'Viet Nam';
  document.getElementById('city').value = shipping.city || 'TP. Hồ Chí Minh';
  document.getElementById('detail').value = shipping.detail || '';
  if (shipping.gender === 'female') {
    form.querySelector('input[name="gender"][value="female"]').checked = true;
  } else {
    form.querySelector('input[name="gender"][value="male"]').checked = true;
  }
}

async function loadProfile() {
  try {
    const response = await fetch(userApiUrl, { credentials: 'include' });
    if (response.status === 401) {
      window.location.href = '../auth/login.php';
      return;
    }
    const result = await response.json();
    if (!result.success) {
      showMessage('error', result.message || 'Không thể tải thông tin người dùng.');
      return;
    }
    populateFields(result.data);
  } catch (error) {
    showMessage('error', 'Lỗi kết nối máy chủ.');
  }
}

document.getElementById('country_code').addEventListener('change', function() {
  const country = codeToCountry[this.value];
  if (country) document.getElementById('country').value = country;
});

document.getElementById('country').addEventListener('change', function() {
  const code = countryToCode[this.value];
  if (code) document.getElementById('country_code').value = code;
});

form.addEventListener('submit', async function(event) {
  event.preventDefault();
  hideMessage();
  const formData = new FormData(form);
  const payload = {};
  for (const [key, value] of formData.entries()) {
    payload[key] = value.trim();
  }

  try {
    const response = await fetch(updateApiUrl, {
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
      showMessage('error', result.message || 'Cập nhật thất bại.');
      return;
    }
    window.location.href = 'profile.php?update=success';
  } catch (error) {
    showMessage('error', 'Lỗi máy chủ. Vui lòng thử lại.');
  }
});

loadProfile();
</script>
</body>
</html>






