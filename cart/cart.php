<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="../assets/css/cart.css">
    <style>
        .container { max-width: 1180px; margin: 36px auto; padding: 0 16px; }
        .cart-section { background: #fff; border-radius: 18px; box-shadow: 0 6px 28px rgba(15,23,42,.08); padding: 28px; margin-bottom: 28px; }
        .cart-section h1 { margin: 0 0 18px; font-size: 2rem; color: #111827; }
        .cart-empty { padding: 40px 0; text-align: center; color: #64748b; font-size: 18px; }
        .status-message { margin-top: 22px; padding: 18px 20px; border-radius: 16px; background: #f8fafc; color: #334155; }
        .cart-table { width: 100%; border-collapse: collapse; font-size: 15px; }
        .cart-table th, .cart-table td { padding: 16px 12px; text-align: left; vertical-align: middle; }
        .cart-table th { color: #475569; font-weight: 600; border-bottom: 1px solid #e2e8f0; }
        .cart-table tbody tr { border-bottom: 1px solid #e5e7eb; }
        .product-cell { display: flex; align-items: center; gap: 16px; }
        .product-cell img { width: 92px; height: 92px; object-fit: contain; border-radius: 14px; background: #fff; border: 1px solid #e2e8f0; padding: 10px; }
        .product-name { font-weight: 700; color: #0f172a; line-height: 1.3; }
        .price-value, .total-value { font-weight: 700; color: #1d4ed8; }
        .qty-controls { display: inline-flex; align-items: center; gap: 8px; }
        .qty-controls button { width: 34px; height: 34px; border: 1px solid #cbd5e1; background: #fff; color: #475569; border-radius: 10px; cursor: pointer; }
        .qty-controls span { min-width: 32px; text-align: center; display: inline-block; font-weight: 600; }
        .remove-button { border: none; background: transparent; color: #dc2626; font-size: 20px; cursor: pointer; }
        .cart-summary { display: grid; grid-template-columns: minmax(280px, 360px); gap: 18px; margin-bottom: 36px; }
        .summary-box { background: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 6px 24px rgba(15,23,42,.06); }
        .summary-title { font-size: 1.15rem; font-weight: 700; color: #111827; margin-bottom: 18px; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 14px; color: #475569; }
        .summary-total { display: flex; justify-content: space-between; margin-top: 18px; font-size: 1.05rem; font-weight: 700; color: #111827; }
        .summary-actions { display: flex; flex-direction: column; gap: 12px; margin-top: 20px; }
        .btn { display: inline-flex; justify-content: center; align-items: center; padding: 14px 18px; border-radius: 12px; border: none; cursor: pointer; font-weight: 700; }
        .btn.checkout { background: #1d4ed8; color: #fff; }
        .btn.continue { background: #f8fafc; color: #0f172a; border: 1px solid #cbd5e1; }
        .btn:disabled { opacity: .55; cursor: not-allowed; }
        @media (max-width: 900px) { .cart-summary { grid-template-columns: 1fr; } .product-cell { flex-wrap: wrap; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="container">
    <section class="cart-section">
        <h1>Giỏ hàng</h1>
        <div id="cartContent"></div>
        <div id="cartEmpty" class="cart-empty" style="display:none;">Giỏ hàng của bạn đang trống.</div>
        <div id="cartStatus" class="status-message">Đang tải giỏ hàng...</div>
    </section>

    <aside id="cartSummary" class="cart-summary" style="display:none;">
        <div class="summary-box">
            <div class="summary-title">Chi tiết thanh toán</div>
            <div class="summary-item"><span>Tạm tính</span><span id="subTotalText">0 đ</span></div>
            <div class="summary-item"><span>Phí vận chuyển</span><span id="shippingFeeText">0 đ</span></div>
            <div class="summary-total"><span>TỔNG CỘNG</span><span id="totalText">0 đ</span></div>
            <div class="summary-actions">
                <button class="btn checkout" id="checkoutBtn" type="button" disabled>THANH TOÁN</button>
                <a class="btn continue" href="../index.php">TIẾP TỤC MUA SẮM</a>
            </div>
        </div>
    </aside>
</div>

<script>
const API_BASE_URL = window.API_BASE_URL || (window.location.origin + <?= json_encode(app_base_url()) ?>);
const cartContent = document.getElementById('cartContent');
const cartEmpty = document.getElementById('cartEmpty');
const cartStatus = document.getElementById('cartStatus');
const cartSummary = document.getElementById('cartSummary');
const subTotalText = document.getElementById('subTotalText');
const shippingFeeText = document.getElementById('shippingFeeText');
const totalText = document.getElementById('totalText');
const checkoutBtn = document.getElementById('checkoutBtn');

function formatMoney(value) {
    return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
}

function getProductName(product) {
    return [product.company_name, product.company_series, product.model_no].filter(Boolean).join(' ');
}

function getProductImage(product) {
    if (product.image_path) return product.image_path;
    const brand = (product.company_name || '').toLowerCase();
    const series = (product.company_series || '').toLowerCase();
    let model = (product.model_no || '').toLowerCase();
    if (series === 'iphone' && model.startsWith('iphone')) model = model.slice(6);
    model = model.replace(/(\d+)g\b/g, '$1gb').replace(/\s|\+|-/g, '');
    const baseName = `${series.replace(/\s|\+|-/g, '')}_${model}`;
    return brand === 'samsung' ? `../images/${baseName}.png` : `../images/${baseName}.png`;
}

function renderProductImage(product) {
    const image = getProductImage(product);
    return image
        ? `<img src="${image}" alt="${getProductName(product)}">`
        : '<div style="width:92px;height:92px;display:flex;align-items:center;justify-content:center;border:1px solid #e2e8f0;border-radius:14px;color:#64748b;font-size:12px;text-align:center;">Chưa có ảnh</div>';
}

function getProductLink(product) {
    const brand = (product.company_name || '').toLowerCase();
    const model = encodeURIComponent(product.model_no || '');
    const series = encodeURIComponent(product.company_series || '');
    if (brand === 'samsung') return `../products/detail/product-detail.php?model_no=${model}&series=${series}`;
    if (brand === 'oppo') return `../products/detail/product-detail-oppo.php?model_no=${model}`;
    if (brand === 'xiaomi') return `../products/detail/product-detail.php?model_no=${model}`;
    if (brand === 'apple' || brand === 'iphone') return `../products/detail/product-detail-iphone.php?model_no=${model}`;
    return `../products/detail/product-detail.php?model_no=${model}`;
}

function renderCart(items) {
    if (!items || items.length === 0) {
        cartContent.innerHTML = '';
        cartEmpty.style.display = 'block';
        cartSummary.style.display = 'none';
        checkoutBtn.disabled = true;
        cartStatus.textContent = 'Giỏ hàng của bạn đang trống.';
        return;
    }

    cartEmpty.style.display = 'none';
    cartSummary.style.display = 'block';
    cartContent.innerHTML = `
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                ${items.map(item => `
                    <tr>
                        <td>
                            <div class="product-cell">
                                ${renderProductImage(item)}
                                <div>
                                    <div class="product-name"><a href="${getProductLink(item)}" style="color:#111827;text-decoration:none;">${getProductName(item)}</a></div>
                                </div>
                            </div>
                        </td>
                        <td class="price-value">${formatMoney(item.price || 0)}</td>
                        <td>
                            <div class="qty-controls" data-imei="${item.product_imei}">
                                <button type="button" class="qty-decrease" aria-label="Giảm">-</button>
                                <span>${item.quantity}</span>
                                <button type="button" class="qty-increase" aria-label="Tăng">+</button>
                            </div>
                        </td>
                        <td class="total-value">${formatMoney((item.quantity || 1) * (item.price || 0))}</td>
                        <td><button type="button" class="remove-button" data-imei="${item.product_imei}" title="Xóa sản phẩm">✕</button></td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;

    const subTotal = items.reduce((sum, item) => sum + ((item.quantity || 1) * (item.price || 0)), 0);
    const shippingFee = 0;
    const total = subTotal + shippingFee;

    subTotalText.textContent = formatMoney(subTotal);
    shippingFeeText.textContent = formatMoney(shippingFee);
    totalText.textContent = formatMoney(total);
    checkoutBtn.disabled = subTotal <= 0;
    cartStatus.textContent = `Đã tải ${items.length} sản phẩm trong giỏ hàng.`;

    cartContent.querySelectorAll('.qty-decrease').forEach(button => {
        button.addEventListener('click', () => changeQuantity(button.closest('.qty-controls').dataset.imei, -1));
    });
    cartContent.querySelectorAll('.qty-increase').forEach(button => {
        button.addEventListener('click', () => changeQuantity(button.closest('.qty-controls').dataset.imei, 1));
    });
    cartContent.querySelectorAll('.remove-button').forEach(button => {
        button.addEventListener('click', () => removeItem(button.dataset.imei));
    });
}

async function requestCart(payload) {
    try {
        const response = await fetch(`${API_BASE_URL}/cart/api/cart.php`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        if (response.status === 401) {
            window.location.href = '../auth/login.php';
            return null;
        }
        return await response.json();
    } catch (error) {
        throw new Error('Lỗi kết nối đến server.');
    }
}

async function loadCart() {
    try {
        const response = await fetch(`${API_BASE_URL}/cart/api/cart.php`, { credentials: 'include' });
        if (response.status === 401) {
            window.location.href = '../auth/login.php';
            return;
        }
        const data = await response.json();
        if (!data.success) throw new Error(data.message || 'Không thể tải giỏ hàng.');
        renderCart(data.data || []);
    } catch (error) {
        cartContent.innerHTML = '';
        cartEmpty.style.display = 'none';
        cartSummary.style.display = 'none';
        cartStatus.textContent = error.message;
        cartStatus.style.color = '#b91c1c';
    }
}

async function changeQuantity(imei, delta) {
    if (!imei) return;
    const success = await requestCart({ action: 'update', product_imei: imei, quantity: delta });
    if (success?.success) await loadCart();
}

async function removeItem(imei) {
    if (!imei || !confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')) return;
    const success = await requestCart({ action: 'remove', product_imei: imei });
    if (success?.success) await loadCart();
}

checkoutBtn.addEventListener('click', () => {
    if (typeof checkLogin === 'function') {
        checkLogin('../customer/address.php');
    } else {
        window.location.href = '../customer/address.php';
    }
});

loadCart();
</script>
</body>
</html>






