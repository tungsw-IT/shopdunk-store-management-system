<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Phương thức thanh toán</title>
  <link rel="stylesheet" href="../assets/css/style_cart.css" />
  <style>
    body { background: #f4f6fb; margin: 0; font-family: Arial, sans-serif; }
    .page-shell { max-width: 980px; margin: 36px auto; padding: 16px; }
    .page-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
    .page-header h1 { margin: 0; font-size: 1.9rem; color: #111827; }
    .section-card { background: #fff; border-radius: 22px; box-shadow: 0 16px 44px rgba(15,23,42,.1); padding: 26px; margin-bottom: 22px; }
    .section-title { margin: 0 0 18px; font-size: 1.2rem; font-weight: 700; color: #111827; }
    .address-box, .error-box { border-radius: 16px; padding: 18px; background: #f8fafc; color: #475569; }
    .payment-list { display: grid; gap: 16px; }
    .payment-item { display: flex; align-items: center; gap: 14px; padding: 16px 18px; border: 1px solid #e2e8f0; border-radius: 16px; cursor: pointer; }
    .payment-item input { accent-color: #1d4ed8; }
    .payment-icon { width: 52px; height: 32px; object-fit: contain; }
    .payment-label { font-size: 1rem; color: #0f172a; font-weight: 600; }
    .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 52px; padding: 0 24px; border: none; border-radius: 14px; background: #1d4ed8; color: #fff; font-weight: 700; cursor: pointer; }
    .btn:disabled { opacity: .6; cursor: not-allowed; }
    .warning { margin-top: 14px; color: #b91c1c; }
    a.section-link { color: #1d4ed8; text-decoration: none; font-weight: 700; }
  </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="page-shell">
  <div class="page-header">
    <a href="javascript:history.back()" style="font-size:24px; color:#1d4ed8; text-decoration:none;">←</a>
    <h1>Phương thức thanh toán</h1>
  </div>
  <div class="section-card">
    <div class="section-title">Địa chỉ giao hàng</div>
    <div id="shippingSection" class="address-box">Đang tải địa chỉ...</div>
    <div style="margin-top:16px;"><a class="section-link" href="../customer/address.php">Sửa địa chỉ</a></div>
  </div>
  <div class="section-card">
    <div class="section-title">Chọn phương thức thanh toán</div>
    <form id="paymentForm" class="payment-list">
      <label class="payment-item"><input type="radio" name="payment_method" value="momo"> <img class="payment-icon" src="../images/momo.png" alt="MoMo"><span class="payment-label">Ví điện tử MoMo</span></label>
      <label class="payment-item"><input type="radio" name="payment_method" value="zalopay"> <img class="payment-icon" src="../images/ZaloPay.png" alt="Zalo Pay"><span class="payment-label">Zalo Pay</span></label>
      <label class="payment-item"><input type="radio" name="payment_method" value="cod"> <img class="payment-icon" src="../images/COD.png" alt="COD"><span class="payment-label">Thanh toán khi nhận hàng</span></label>
      <label class="payment-item"><input type="radio" name="payment_method" value="atm"> <img class="payment-icon" src="../images/ATM.png" alt="ATM"><span class="payment-label">Thẻ ATM nội địa</span></label>
      <label class="payment-item"><input type="radio" name="payment_method" value="card"> <img class="payment-icon" src="../images/visa.png" alt="Card"><span class="payment-label">Thẻ tín dụng/ghi nợ</span></label>
      <label class="payment-item"><input type="radio" name="payment_method" value="mbank"> <img class="payment-icon" src="../images/bank.png" alt="MB Bank"><span class="payment-label">Ngân hàng di động</span></label>
      <label class="payment-item"><input type="radio" name="payment_method" value="shopeepay"> <img class="payment-icon" src="../images/ShopeePay.png" alt="Shopee Pay"><span class="payment-label">Shopee Pay</span></label>
      <button type="submit" class="btn" id="continueBtn">TIẾP THEO</button>
      <div id="paymentWarning" class="warning" style="display:none;"></div>
    </form>
  </div>
</div>
<script>
const API_BASE_URL = window.API_BASE_URL || (window.location.origin + <?= json_encode(app_base_url()) ?>);
const summaryUrl = `${API_BASE_URL}/checkout/api/summary.php`;
const paymentApiUrl = `${API_BASE_URL}/checkout/api/payment.php`;
const shippingSection = document.getElementById('shippingSection');
const paymentForm = document.getElementById('paymentForm');
const paymentWarning = document.getElementById('paymentWarning');
const continueBtn = document.getElementById('continueBtn');

function formatContact(info) {
  return `${info.fullname || ''} • ${info.country_code || ''} ${info.phone || ''}`;
}

function renderShipping(info) {
  if (!info) {
    shippingSection.innerHTML = 'Vui lòng nhập địa chỉ giao hàng trước khi tiếp tục. <a href="../customer/address.php">Đi tới địa chỉ</a>';
    continueBtn.disabled = true;
    return;
  }
  continueBtn.disabled = false;
  shippingSection.innerHTML = `
    <div style="font-weight:700; margin-bottom:10px;">${formatContact(info)}</div>
    <div>Email: ${info.email || 'Chưa có'}</div>
    <div>Giới tính: ${info.gender === 'female' ? 'Nữ' : info.gender === 'male' ? 'Nam' : 'Chưa rõ'}</div>
    <div>Địa chỉ: ${info.detail || ''}, ${info.city || ''}, ${info.country || ''}</div>
  `;
}

async function loadPayment() {
  try {
    const response = await fetch(summaryUrl, { credentials: 'include' });
    if (response.status === 401) {
      window.location.href = '../auth/login.php';
      return;
    }
    const result = await response.json();
    if (!result.success) {
      shippingSection.textContent = result.message || 'Không thể tải địa chỉ.';
      continueBtn.disabled = true;
      return;
    }

    renderShipping(result.data.shipping_info);
    const selected = result.data.payment_method || '';
    if (selected) {
      const radio = paymentForm.querySelector(`input[name="payment_method"][value="${selected}"]`);
      if (radio) radio.checked = true;
    }
  } catch (error) {
    shippingSection.textContent = 'Lỗi kết nối máy chủ.';
    continueBtn.disabled = true;
  }
}

paymentForm.addEventListener('submit', async function(event) {
  event.preventDefault();
  paymentWarning.style.display = 'none';

  const data = new FormData(paymentForm);
  const payload = { payment_method: data.get('payment_method') };
  if (!payload.payment_method) {
    paymentWarning.textContent = 'Vui lòng chọn phương thức thanh toán.';
    paymentWarning.style.display = 'block';
    return;
  }

  try {
    const response = await fetch(paymentApiUrl, {
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
      paymentWarning.textContent = result.message || 'Lưu phương thức thanh toán thất bại.';
      paymentWarning.style.display = 'block';
      return;
    }
    window.location.href = 'payment_details.php';
  } catch (error) {
    paymentWarning.textContent = 'Lỗi máy chủ. Vui lòng thử lại.';
    paymentWarning.style.display = 'block';
  }
});

loadPayment();
</script>
</body>
</html>






