<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin tài khoản</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6fa; margin: 0; }
        .profile-wrap { max-width: 1050px; margin: 40px auto; display: flex; gap: 38px; }
        .profile-left { background: #fff; border-radius: 16px; box-shadow: 0 4px 18px #0002; padding: 32px 28px; width: 330px; flex-shrink: 0; display: flex; flex-direction: column; }
        .profile-title { font-size: 2rem; font-weight: 700; color: #1565c0; margin-bottom: 20px; }
        .profile-info-row { margin-bottom: 14px; color: #1f2937; line-height: 1.5; }
        .profile-info-row span { font-weight: 600; color: #234; min-width: 110px; display: inline-block; }
        .edit-link { color: #1976d2; text-decoration: underline; margin-left: 12px; font-size: 15px; }
        .profile-right { flex: 1; display: flex; flex-direction: column; gap: 18px; }
        .profile-block { background: #fff; border-radius: 16px; box-shadow: 0 4px 18px #0002; padding: 24px 24px 14px; }
        .profile-action-link { display:inline-flex; align-items:center; justify-content:center; padding:12px 18px; border-radius:12px; background:#2563eb; color:#fff; text-decoration:none; font-weight:600; margin-top:12px; }
        .profile-section-title { font-size: 1.13rem; color:#1976d2; font-weight:600; margin-bottom:10px; }
        .profile-list { display:flex; gap:18px; flex-wrap:wrap; min-height:118px; }
        .profile-product { width:130px; background:#f8fafb; border-radius: 12px; padding:10px 10px 14px; text-align:center; box-shadow:0 2px 12px #0001; transition:.18s; }
        .profile-product:hover { box-shadow:0 6px 20px #1976d22a; }
        .profile-product img { width:88px; height:88px; object-fit:contain; border-radius:8px; background:#fff; border:1px solid #e5e7eb; }
        .profile-product-name { font-size: 13.5px; margin:7px 0 8px; min-height:38px; color:#111827; }
        .profile-product-price { color:#1565c0; font-size:13px; font-weight:600; }
        .nodata { font-size: 15px; color: #94a3b8; font-style: italic; margin: 12px 0; }
        .status-message { padding: 18px 24px; background: #fff; border-radius: 16px; box-shadow: 0 4px 18px rgba(0,0,0,.08); color: #475569; margin-bottom: 24px; }
        @media (max-width: 1050px) { .profile-wrap { flex-direction: column; gap: 24px; padding: 0 10px; } .profile-left { width: 100%; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<div class="profile-wrap">
    <div class="profile-left">
        <div class="profile-title">Thông tin tài khoản</div>
        <div class="status-message" id="statusMessage">Đang tải thông tin tài khoản...</div>
        <div class="profile-info-row"><span>Họ và tên:</span> <span id="profileName">...</span></div>
        <div class="profile-info-row"><span>Email:</span> <span id="profileEmail">...</span></div>
        <div class="profile-info-row"><span>SĐT:</span> <span id="profilePhone">...</span></div>
        <div class="profile-info-row"><span>Giới tính:</span> <span id="profileGender">...</span></div>
        <div class="profile-info-row"><span>Quốc gia:</span> <span id="profileCountry">...</span></div>
        <div class="profile-info-row"><span>Mã quốc gia:</span> <span id="profileCountryCode">...</span></div>
        <div class="profile-info-row"><span>Tỉnh/Thành:</span> <span id="profileCity">...</span></div>
        <div class="profile-info-row"><span>Địa chỉ chi tiết:</span> <span id="profileAddress">...</span></div>
        <a href="editprofile.php" class="edit-link">Chỉnh sửa</a>
    </div>

    <div class="profile-right">
        <div class="profile-block">
            <div class="profile-section-title">Dịch vụ sửa chữa</div>
            <div>Gửi yêu cầu để bộ phận kỹ thuật tiếp nhận và xử lý.</div>
            <a class="profile-action-link" href="repair_request.php">Gửi yêu cầu sửa chữa</a>
        </div>

        <div class="profile-block">
            <div class="profile-section-title">Danh sách yêu thích</div>
            <div class="profile-list" id="wishlistList"></div>
            <div class="nodata" id="wishlistEmpty" style="display:none;">Bạn chưa có sản phẩm yêu thích nào.</div>
            <a class="profile-action-link" href="../cart/wishlist.php">Xem toàn bộ yêu thích</a>
        </div>

        <div class="profile-block">
            <div class="profile-section-title">Sản phẩm đã mua</div>
            <div class="profile-list" id="purchasedList"></div>
            <div class="nodata" id="purchasedEmpty" style="display:none;">Bạn chưa có đơn hàng nào.</div>
        </div>
    </div>
</div>
<script>
const API_BASE_URL = window.API_BASE_URL || (window.location.origin + <?= json_encode(app_base_url()) ?>);
const statusMessage = document.getElementById('statusMessage');
const profileName = document.getElementById('profileName');
const profileEmail = document.getElementById('profileEmail');
const profilePhone = document.getElementById('profilePhone');
const profileGender = document.getElementById('profileGender');
const profileCountry = document.getElementById('profileCountry');
const profileCountryCode = document.getElementById('profileCountryCode');
const profileCity = document.getElementById('profileCity');
const profileAddress = document.getElementById('profileAddress');
const wishlistList = document.getElementById('wishlistList');
const wishlistEmpty = document.getElementById('wishlistEmpty');
const purchasedList = document.getElementById('purchasedList');
const purchasedEmpty = document.getElementById('purchasedEmpty');

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

function buildProductItem(item) {
    const baseName = `${(item.company_series || '').replace(/\s|\+|-/g,'').toLowerCase()}_${(item.model_no || '').replace(/\s|\+|-/g,'').toLowerCase()}`;
    const image = (item.company_name || '').toLowerCase() === 'samsung'
        ? `../images/${baseName}.png`
        : `../images/${baseName}.png`;
    return {
        image,
        link: getProductLink(item),
        ...item
    };
}

function renderProductCard(item) {
    const card = document.createElement('div');
    card.className = 'profile-product';
    card.innerHTML = `
        <a href="${item.link}" style="text-decoration:none; color:inherit;">
            <img src="${item.image}" alt="${item.company_name || ''} ${item.company_series || ''} ${item.model_no || ''}">
            <div class="profile-product-name">${item.company_name || ''} ${item.company_series || ''} ${item.model_no || ''}</div>
            <div class="profile-product-price">${formatMoney(item.price || 0)}</div>
        </a>
    `;
    return card;
}

async function loadProfile() {
    try {
        const response = await fetch(`${API_BASE_URL}/customer/api/user.php`, {
            credentials: 'include'
        });
        if (response.status === 401) {
            window.location.href = '../auth/login.php';
            return;
        }
        const data = await response.json();
        if (!data.success) throw new Error(data.message || 'Không lấy được dữ liệu.');

        const user = data.data.user || {};
        const shipping = data.data.shipping_info || {};
        const wishlist = data.data.wishlist || [];
        const purchased = data.data.purchased || [];

        profileName.textContent = user.accounts_name || 'Chưa cập nhật';
        profileEmail.textContent = user.email || 'Chưa cập nhật';
        profilePhone.textContent = user.phone || 'Chưa cập nhật';
        profileGender.textContent = shipping.gender === 'male' ? 'Nam' : shipping.gender === 'female' ? 'Nữ' : 'Chưa cập nhật';
        profileCountry.textContent = shipping.country || 'Việt Nam';
        profileCountryCode.textContent = shipping.country_code || '+84';
        profileCity.textContent = shipping.city || 'TP. Hồ Chí Minh';
        profileAddress.textContent = shipping.detail || 'Chưa cập nhật';

        wishlistList.innerHTML = '';
        if (wishlist.length === 0) {
            wishlistEmpty.style.display = 'block';
        } else {
            wishlistEmpty.style.display = 'none';
            wishlist.forEach(item => wishlistList.appendChild(renderProductCard(buildProductItem(item))));
        }

        purchasedList.innerHTML = '';
        if (purchased.length === 0) {
            purchasedEmpty.style.display = 'block';
        } else {
            purchasedEmpty.style.display = 'none';
            purchased.forEach(item => purchasedList.appendChild(renderProductCard(buildProductItem(item))));
        }

        statusMessage.textContent = 'Thông tin tài khoản đã được tải.';
    } catch (error) {
        statusMessage.textContent = error.message;
        statusMessage.style.color = '#b91c1c';
    }
}

loadProfile();
</script>
</body>
</html>






