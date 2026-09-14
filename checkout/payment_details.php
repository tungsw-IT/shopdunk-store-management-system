<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Tóm tắt đơn hàng</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/style_cart.css">
  <style>
    body { background: #f4f6fb; margin: 0; font-family: Arial, sans-serif; }
    .checkout-summary { max-width: 1180px; margin: 30px auto; padding: 0 20px; }
    .section-card { background: #fff; border-radius: 22px; box-shadow: 0 18px 40px rgba(15,23,42,.08); padding: 26px; margin-bottom: 24px; }
    .section-title { font-size: 22px; margin: 0 0 18px; color: #111827; }
    .section-row { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .section-link { color: #0f172a; font-weight: 700; text-decoration: none; }
    .summary-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e5e7eb; font-size: 15px; }
    .summary-total { display: flex; justify-content: space-between; font-weight: 700; font-size: 20px; padding: 16px 0 0; }
    .product-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    .product-table th, .product-table td { padding: 16px 14px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
    .product-table th { text-align: left; color: #475569; font-weight: 700; }
    .product-cell { display: flex; align-items: center; gap: 14px; }
    .product-cell img { width: 58px; height: 58px; border-radius: 14px; object-fit: contain; background: #f8fafc; }
    .product-name { font-weight: 700; color: #0f172a; }
    .empty-state, .error-state, .loading-state { text-align: center; padding: 56px 24px; color: #475569; font-size: 16px; }
    .checkout-button { width: 100%; max-width: 320px; min-height: 54px; border: none; border-radius: 14px; background: #0f172a; color: #fff; font-size: 16px; font-weight: 700; cursor: pointer; }
    .checkout-button:disabled { opacity: .6; cursor: not-allowed; }
    @media (max-width: 860px) { .section-row { flex-direction: column; align-items: stretch; } }
  </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="checkout-summary">
  <div id="pageMessage" class="loading-state">Đang tải thông tin đơn hàng...</div>
  <div id="checkoutContent" style="display:none;">
    <div class="section-card">
      <div class="section-row">
        <h2 class="section-title">Địa chỉ giao hàng</h2>
        <a class="section-link" href="../customer/address.php">Thay đổi</a>
      </div>
      <div id="shippingInfo"></div>
    </div>

    <div class="section-card">
      <div class="section-row">
        <h2 class="section-title">Phương thức thanh toán</h2>
        <a class="section-link" href="payment_method.php">Thay đổi</a>
      </div>
      <div id="paymentInfo"></div>
    </div>

    <div class="section-card">
      <div class="section-row">
        <h2 class="section-title">Sản phẩm trong giỏ</h2>
      </div>
      <div style="overflow-x:auto;">
        <table class="product-table">
          <thead>
            <tr>
              <th>Sản phẩm</th>
              <th>Giá</th>
              <th>Số lượng</th>
              <th>Tổng</th>
            </tr>
          </thead>
          <tbody id="cartItems"></tbody>
        </table>
      </div>
    </div>

    <div class="section-card">
      <h2 class="section-title">Chi tiết thanh toán</h2>
      <div class="summary-item"><span>Tạm tính</span><span id="subtotalText"></span></div>
      <div class="summary-item"><span>Phí vận chuyển</span><span id="shippingFeeText"></span></div>
      <div class="summary-item" id="promoRow" style="display:none;"><span id="promoLabel"></span><span id="discountText"></span></div>
      <div class="summary-total"><span>Tổng</span><span id="totalText"></span></div>
      <div class="summary-item" id="summaryWarning" style="display:none; color:#b91c1c;"></div>
      <button class="checkout-button" id="confirmOrderBtn">XÁC NHẬN ĐẶT HÀNG</button>
    </div>
  </div>
</div>
<script>
  const API_BASE_URL = window.API_BASE_URL || (window.location.origin + <?= json_encode(app_base_url()) ?>);
  const pageMessage = document.getElementById('pageMessage');
  const checkoutContent = document.getElementById('checkoutContent');
  const shippingInfo = document.getElementById('shippingInfo');
  const paymentInfo = document.getElementById('paymentInfo');
  const cartItems = document.getElementById('cartItems');
  const subtotalText = document.getElementById('subtotalText');
  const shippingFeeText = document.getElementById('shippingFeeText');
  const promoRow = document.getElementById('promoRow');
  const promoLabel = document.getElementById('promoLabel');
  const discountText = document.getElementById('discountText');
  const totalText = document.getElementById('totalText');
  const summaryWarning = document.getElementById('summaryWarning');
  const confirmOrderBtn = document.getElementById('confirmOrderBtn');
  const summaryUrl = `${API_BASE_URL}/checkout/api/summary.php`;
  const checkoutUrl = `${API_BASE_URL}/checkout/api/checkout.php`;

  function formatMoney(value) {
    return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
  }

  function productImage(product) {
    if (product.image_path) return product.image_path;
    const brand = (product.company_name || '').toLowerCase();
    const series = (product.company_series || '').toLowerCase();
    let model = (product.model_no || '').toLowerCase();
    if (series === 'iphone' && model.startsWith('iphone')) model = model.slice(6);
    model = model.replace(/(\d+)g\b/g, '$1gb').replace(/\s|\+|-/g, '');
    const base = `${series.replace(/\s|\+|-/g, '')}_${model}`;
    return brand === 'samsung' ? `../images/${base}.png` : `../images/${base}.png`;
  }

  async function showMessage(text, isError = false) {
    pageMessage.innerHTML = text;
    pageMessage.style.display = 'block';
    pageMessage.style.color = isError ? '#b91c1c' : '#475569';
    checkoutContent.style.display = 'none';
  }

  async function loadSummary() {
    try {
      const response = await fetch(summaryUrl, { credentials: 'include' });
      if (response.status === 401) {
        window.location.href = '../auth/login.php';
        return;
      }
      const result = await response.json();
      if (!result.success) {
        await showMessage(result.message || 'Không thể tải dữ liệu đơn hàng.', true);
        return;
      }

      const data = result.data;
      if (!data.shipping_info) {
        await showMessage('Vui lòng nhập địa chỉ giao hàng trước khi tiếp tục. <a href="../customer/address.php">Đi tới địa chỉ</a>');
        return;
      }

      shippingInfo.innerHTML = `
        <div style="font-weight:700; margin-bottom:10px;">${data.shipping_info.fullname || ''} (${data.shipping_info.country_code || ''} ${data.shipping_info.phone || ''})</div>
        <div>Email: ${data.shipping_info.email || 'Chưa có'}</div>
        <div>Giới tính: ${data.shipping_info.gender === 'male' ? 'Nam' : data.shipping_info.gender === 'female' ? 'Nữ' : 'Chưa rõ'}</div>
        <div>Địa chỉ: ${data.shipping_info.detail || ''}, ${data.shipping_info.city || ''}, ${data.shipping_info.country || ''}</div>
      `;
      paymentInfo.innerHTML = `<div style="font-weight:700; margin-bottom:10px;">${data.payment_method || 'Chưa chọn phương thức'}</div>`;

      cartItems.innerHTML = '';
      data.cart_items.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>
            <div class="product-cell">
              ${productImage(item) ? `<img src="${productImage(item)}" alt="">` : '<div style="width:58px;height:58px;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:11px;text-align:center;">Chưa có ảnh</div>'}
              <div class="product-name">${item.company_name || ''} ${item.company_series || ''} ${item.model_no || ''}</div>
            </div>
          </td>
          <td>${formatMoney(item.price)}</td>
          <td>${item.quantity}</td>
          <td>${formatMoney(item.line_total)}</td>
        `;
        cartItems.appendChild(row);
      });

      subtotalText.textContent = formatMoney(data.subtotal);
      shippingFeeText.textContent = formatMoney(data.shipping_fee);
      totalText.textContent = formatMoney(data.total);

      if (data.discount_amount > 0) {
        promoRow.style.display = 'flex';
        promoLabel.textContent = `Khuyến mãi (${data.promo_description || 'Giảm giá'})`;
        discountText.textContent = `- ${formatMoney(data.discount_amount)}`;
      } else {
        promoRow.style.display = 'none';
      }

      if (!data.cart_items.length) {
        summaryWarning.textContent = 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi đặt hàng.';
        confirmOrderBtn.disabled = true;
      } else {
        summaryWarning.textContent = '';
        confirmOrderBtn.disabled = false;
      }

      pageMessage.style.display = 'none';
      checkoutContent.style.display = 'block';
    } catch (error) {
      await showMessage('Lỗi kết nối máy chủ. Vui lòng thử lại.', true);
    }
  }

  confirmOrderBtn.addEventListener('click', async () => {
    confirmOrderBtn.disabled = true;
    confirmOrderBtn.textContent = 'Đang xử lý...';

    try {
      const response = await fetch(checkoutUrl, {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({})
      });
      if (response.status === 401) {
        window.location.href = '../auth/login.php';
        return;
      }
      const result = await response.json();
      if (!result.success) {
        await showMessage(result.message || 'Thanh toán không thành công.', true);
        return;
      }
      window.location.href = 'success.php';
    } catch (error) {
      await showMessage('Lỗi máy chủ. Vui lòng thử lại.', true);
    } finally {
      confirmOrderBtn.disabled = false;
      confirmOrderBtn.textContent = 'XÁC NHẬN ĐẶT HÀNG';
    }
  });

  loadSummary();
</script>
</body>
</html>






