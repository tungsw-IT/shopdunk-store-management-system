<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách yêu thích</title>
    <link rel="stylesheet" href="../assets/css/style_cart.css"/>
    <style>
        .wishlist-section { max-width: 980px; margin: 38px auto; padding: 36px 28px 32px; background: #fff; border-radius: 16px; box-shadow: 0 2px 18px rgba(0,0,0,0.07); }
        .wishlist-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; gap: 16px; }
        .wishlist-header h2 { margin: 0; font-size: 24px; color: #111827; }
        .wishlist-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 22px; }
        .wishlist-card { background: #fafbfc; border-radius: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); padding: 22px 18px 18px; display: flex; flex-direction: column; align-items: center; position: relative; transition: transform .16s ease, box-shadow .16s ease; }
        .wishlist-card:hover { transform: translateY(-2px); box-shadow: 0 6px 22px rgba(44,160,240,0.16); }
        .wishlist-card img { width: 98px; height: 98px; object-fit: contain; border-radius: 10px; margin-bottom: 12px; border: 1px solid #e5e9ef; background: #fff; }
        .wishlist-name { font-weight: 700; font-size: 15.5px; margin-bottom: 10px; text-align: center; color: #111827; }
        .wishlist-price { color: #1565c0; font-size: 16px; font-weight: 700; margin-bottom: 14px; }
        .wishlist-actions { width: 100%; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .wishlist-actions a,
        .wishlist-actions button { padding: 10px 14px; border-radius: 10px; border: none; cursor: pointer; font-size: 14px; text-decoration: none; }
        .wishlist-actions a { background: #1d4ed8; color: #fff; }
        .wishlist-actions button { background: #ef4444; color: #fff; }
        .empty-state { margin: 26px 0; text-align: center; color: #6b7280; font-size: 18px; }
        .status-message { margin: 24px 0 0; padding: 18px 22px; border-radius: 14px; background: #f8fafc; color: #334155; }
        @media (max-width: 700px) { .wishlist-section { padding: 20px 14px; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="wishlist-section">
    <div class="wishlist-header">
        <h2>Danh sách yêu thích của bạn</h2>
        <a href="../index.php" style="text-decoration:none;font-size:16px;color:#1d4ed8;">Tiếp tục mua sắm →</a>
    </div>

    <div id="wishlistGrid" class="wishlist-grid"></div>
    <div id="emptyState" class="empty-state" style="display:none;">Bạn chưa có sản phẩm yêu thích nào.</div>
    <div id="statusMessage" class="status-message">Đang tải danh sách yêu thích...</div>
</div>

<script>
const API_BASE_URL = window.API_BASE_URL || (window.location.origin + <?= json_encode(app_base_url()) ?>);
const wishlistGrid = document.getElementById('wishlistGrid');
const emptyState = document.getElementById('emptyState');
const statusMessage = document.getElementById('statusMessage');

function formatMoney(value) {
    return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
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

function getProductImage(product) {
    const brand = (product.company_name || '').toLowerCase();
    const baseName = `${(product.company_series || '').replace(/\s|\+|-/g, '').toLowerCase()}_${(product.model_no || '').replace(/\s|\+|-/g, '').toLowerCase()}`;
    if (brand === 'samsung') return `../images/${baseName}.png`;
    return `../images/${baseName}.png`;
}

function renderWishlistItem(product) {
    const card = document.createElement('div');
    card.className = 'wishlist-card';
    const productLink = getProductLink(product);
    card.innerHTML = `
        <img src="${getProductImage(product)}" alt="${product.company_name || ''} ${product.company_series || ''} ${product.model_no || ''}">
        <div class="wishlist-name">${product.company_name || ''} ${product.company_series || ''} ${product.model_no || ''}</div>
        <div class="wishlist-price">${formatMoney(product.price || 0)}</div>
        <div class="wishlist-actions">
            <a href="${productLink}">Xem chi tiết</a>
            <button type="button" data-imei="${product.imei_number}" onclick="removeItem(this)">Xóa</button>
        </div>
    `;
    return card;
}

async function loadWishlist() {
    try {
        const response = await fetch(`${API_BASE_URL}/cart/api/wishlist.php`, {
            credentials: 'include'
        });
        if (response.status === 401) {
            window.location.href = '../auth/login.php';
            return;
        }
        const data = await response.json();
        if (!data.success) throw new Error(data.message || 'Không lấy được danh sách yêu thích.');

        wishlistGrid.innerHTML = '';
        if (!Array.isArray(data.data) || data.data.length === 0) {
            emptyState.style.display = 'block';
            statusMessage.textContent = 'Bạn chưa có sản phẩm yêu thích nào.';
            return;
        }

        emptyState.style.display = 'none';
        data.data.forEach(product => wishlistGrid.appendChild(renderWishlistItem(product)));
        statusMessage.textContent = `Đã tải ${data.data.length} sản phẩm yêu thích.`;
    } catch (error) {
        wishlistGrid.innerHTML = '';
        emptyState.style.display = 'none';
        statusMessage.textContent = error.message;
        statusMessage.style.color = '#b91c1c';
    }
}

async function removeItem(button) {
    const imei = button.dataset.imei;
    if (!imei || !confirm('Xóa sản phẩm này khỏi danh sách yêu thích?')) return;
    button.disabled = true;
    button.textContent = 'Đang xóa...';

    try {
        const response = await fetch(`${API_BASE_URL}/cart/api/wishlist.php`, {
            method: 'DELETE',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ imei_number: imei })
        });
        const data = await response.json();
        if (!data.success) throw new Error(data.message || 'Xóa thất bại.');
        await loadWishlist();
    } catch (error) {
        statusMessage.textContent = error.message;
        statusMessage.style.color = '#b91c1c';
        button.disabled = false;
        button.textContent = 'Xóa';
    }
}

loadWishlist();
</script>
</body>
</html>






