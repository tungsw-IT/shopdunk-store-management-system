<?php session_start();
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../config/db.php';

// 1. Lấy danh sách sản phẩm yêu thích của user (nếu đã đăng nhập)
$wishlistImeis = [];
if (isset($_SESSION['account_id'])) {
    $stmtWish = $pdo->prepare("SELECT imei_number FROM wishlist WHERE account_id = ?");
    $stmtWish->execute([$_SESSION['account_id']]);
    $wishlistImeis = $stmtWish->fetchAll(PDO::FETCH_COLUMN);
    // Debug thử:
    // echo '<pre>'; print_r($wishlistImeis); echo '</pre>';
}
// 2. Hiện thông báo khi thêm vào wishlist (KHÔNG echo trực tiếp nữa!)
$wishlistMsg = '';
if (isset($_GET['wishlist_msg'])) {
    if ($_GET['wishlist_msg'] == 'added') {
        $wishlistMsg = 'Đã thêm vào wishlist!';
    } elseif ($_GET['wishlist_msg'] == 'exists') {
        $wishlistMsg = 'Sản phẩm này đã có trong wishlist!';
    }
}
// 3. Lấy filter từ URL
$series = $_GET['series'] ?? '';
$ram = $_GET['ram'] ?? '';
$rom = $_GET['rom'] ?? '';
$price = $_GET['price'] ?? '';
$sort = $_GET['sort'] ?? '';
$discountMin = (int)($_GET['discount'] ?? 0);

// 4. Xây WHERE filter
$where = "company_name = 'OPPO'";
$params = [];
if ($series) { $where .= " AND company_series = ?"; $params[] = $series; }
if ($ram)    { $where .= " AND `ram(GB)` = ?";      $params[] = (int)str_replace('GB', '', $ram); }
if ($rom)    { $where .= " AND `rom(GB)` = ?";      $params[] = (int)str_replace('GB', '', $rom); }
if ($price) {
    if ($price === 'under-7')      $where .= " AND price < 7000000";
    elseif ($price === '7-10')     $where .= " AND price >= 7000000 AND price <= 10000000";
    elseif ($price === '10-15')    $where .= " AND price > 10000000 AND price <= 15000000";
    elseif ($price === 'above-15') $where .= " AND price > 15000000";
}

/// Lấy filter rating từ GET
$ratingFilter = isset($_GET['rating']) && in_array($_GET['rating'], ['1','2','3','4','5']) ? (int)$_GET['rating'] : 0;

// Query sản phẩm JOIN với reviews để tính trung bình rating và lọc rating
$sql = "
    SELECT m.*, AVG(r.rating) AS avg_rating
    FROM mobile m
    LEFT JOIN reviews r
      ON r.company_series COLLATE utf8mb4_general_ci = m.company_series COLLATE utf8mb4_general_ci
     AND r.model_no COLLATE utf8mb4_general_ci = m.model_no COLLATE utf8mb4_general_ci
    WHERE $where
    GROUP BY m.company_series, m.model_no
";

if ($ratingFilter > 0) {
    $sql .= " HAVING avg_rating >= ?";
    $params[] = $ratingFilter;
}




// Sắp xếp
if ($sort === 'price-asc')      $sql .= " ORDER BY m.price ASC";
elseif ($sort === 'price-desc') $sql .= " ORDER BY m.price DESC";
else                           $sql .= " ORDER BY m.company_series, m.price DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Các loại sản phẩm OPPO</title>
<!-- Link CSS chung -->
<link rel="stylesheet" href="../../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
<style>
/* Style cho phần hiển thị sao rating */
.rating {
    color: #f5b600; /* vàng */
    font-size: 18px;
    margin-block-start: 8px;
}
</style>
</head> 
<body>

<!-- Banner slide ảnh OPPO -->
<div class="banner-container oppo-banner">
    <div class="carousel-container">
        <div class="carousel-slide">
            <img src="../../images/anh_chup_man_hinh_2024-07-08_luc_19.38.04.png" alt="OPPO Reno12 Series">
            <img src="../../images/8546243_Oppo-Find-X8.webp" alt="OPPO Find X8 Night">
            <img src="../../images/can-canh-oppo-find-x8-ultra-1.jpg" alt="OPPO Find X8 Ultra">
        </div>
        <button type="button" class="prev" aria-label="Prev">❮</button>
        <button type="button" class="next" aria-label="Next">❯</button>
    </div>
</div>


<style>
@keyframes popUpWishlist {
    0% { opacity:0; transform:translateY(-30px) scale(0.85);}
    100% { opacity:1; transform:translateY(0) scale(1);}
}
.btn-reset-filter {
    color: #555;
    cursor: pointer;
    transition: color 0.3s ease;
}

.btn-reset-filter:hover {
    color: #f5b600; /* màu vàng nổi bật */
}

</style>

<!-- Tabs lọc theo series -->
<nav class="filter-bar">
    <a href="alloppo.php" class="tab<?= !$series ? ' active' : '' ?>">Tất cả</a>
    <a href="alloppo.php?series=Reno" class="tab<?= $series == 'Reno' ? ' active' : '' ?>">Reno Series</a>
    <a href="alloppo.php?series=Find" class="tab<?= $series == 'Find' ? ' active' : '' ?>">Find Series</a>
    <a href="alloppo.php?series=A" class="tab<?= $series == 'A' ? ' active' : '' ?>">A Series</a>
</nav>

<!-- Form bộ lọc nâng cao -->
<form method="GET" class="filter-sort-form">
    <div class="filters">
        <!-- Các select filter -->
       <select name="rating">
        <option value="">-- Đánh giá --</option>
        <option value="5" <?= (isset($_GET['rating']) && $_GET['rating']=='5') ? 'selected' : '' ?>>5 sao</option>
        <option value="4" <?= (isset($_GET['rating']) && $_GET['rating']=='4') ? 'selected' : '' ?>>từ 4 sao</option>
        <option value="3" <?= (isset($_GET['rating']) && $_GET['rating']=='3') ? 'selected' : '' ?>>từ 3 sao</option>
        <option value="2" <?= (isset($_GET['rating']) && $_GET['rating']=='2') ? 'selected' : '' ?>>từ 2 sao</option>
        <option value="1" <?= (isset($_GET['rating']) && $_GET['rating']=='1') ? 'selected' : '' ?>>từ 1 sao</option>
</select>


        <select name="rom">
            <option value="">-- Bộ nhớ --</option>
            <option value="256GB" <?= $rom=='256GB'?'selected':''; ?>>256GB</option>
            <option value="512GB" <?= $rom=='512GB'?'selected':''; ?>>512GB</option>
        </select>
        <select name="ram">
            <option value="">-- RAM --</option>
            <option value="8GB" <?= $ram=='8GB'?'selected':''; ?>>8GB</option>
            <option value="12GB" <?= $ram=='12GB'?'selected':''; ?>>12GB</option>
            <option value="16GB" <?= $ram=='16GB'?'selected':''; ?>>16GB</option>
        </select>
        <select name="price">
            <option value="">-- Khoảng giá --</option>
            <option value="under-7" <?= $price=='under-7'?'selected':''; ?>>Dưới 7 triệu</option>
            <option value="7-10" <?= $price=='7-10'?'selected':''; ?>>7 - 10 triệu</option>
            <option value="10-15" <?= $price=='10-15'?'selected':''; ?>>10 - 15 triệu</option>
            <option value="above-15" <?= $price=='above-15'?'selected':''; ?>>Trên 15 triệu</option>
        </select>
        <select name="discount">
            <option value="">-- Giảm giá --</option>
            <option value="10" <?= $discountMin==10?'selected':''; ?>>Từ 10%</option>
            <option value="20" <?= $discountMin==20?'selected':''; ?>>Từ 20%</option>
            <option value="30" <?= $discountMin==30?'selected':''; ?>>Từ 30%</option>
        </select>
    </div>
    <div class="sort-box">
        <label>Sắp xếp:</label>
        <select name="sort">
            <option value="">Mặc định</option>
            <option value="price-asc" <?= $sort=='price-asc'?'selected':''; ?>>Giá tăng dần</option>
            <option value="price-desc" <?= $sort=='price-desc'?'selected':''; ?>>Giá giảm dần</option>
        </select>
        <button type="submit">Áp dụng</button>
            <!-- Nút reset (chỉ icon) -->
    <a href="alloppo.php" title="Reset bộ lọc" class="btn-reset-filter" style="margin-left:10px; font-size:18px; text-decoration:none;">
        &#x21bb; <!-- biểu tượng vòng xoay reset -->
    </a>
    </div>
</form>

<!-- Lưới sản phẩm -->
<div class="product-grid">
<?php
foreach ($products as $product) {
    // 1. Kiểm tra sản phẩm đã có trong wishlist của user chưa
    $isWished = in_array($product['imei_number'], $wishlistImeis);

    // 2. Xử lý các biến giá, giảm giá, rating cho hiển thị
    $price = (float)$product['price'];
    $old_price = isset($product['old_price']) ? (float)$product['old_price'] : 0;
    if ($old_price < $price || $old_price == 0) $old_price = $price;
    $discount = ($old_price > $price && $price > 0) ? round(100 - ($price / $old_price) * 100) : 0;
    if ($discountMin && $discount < $discountMin) continue;
    $avgRating = floor($product['avg_rating'] ?? 0);



    // 3. Xử lý ảnh sản phẩm
    $img_base = 
        strtolower(str_replace([' ', '+', '-'], '', $product['company_series'])) .
        '_' .
        strtolower(str_replace([' ', '+', '-'], '', $product['model_no']));
    $img_path_png = '../../images/' . $img_base . '.png';
    $img_path_jpg = '../../images/' . $img_base . '.jpg';
    $img_show = file_exists(__DIR__ . '/' . $img_path_png) ? $img_path_png : (file_exists(__DIR__ . '/' . $img_path_jpg) ? $img_path_jpg : '../../images/no-image.png');

    // 4. Tạo link chi tiết sản phẩm
    $link = '../detail/product-detail-oppo.php?model_no=' . urlencode($product['model_no']);
    ?>
    <!-- 5. Bắt đầu in sản phẩm ra giao diện -->
    <a href="<?= $link ?>" class="product-card-link">
        <div class="product-card oppo-card">
            <!-- 6. Ảnh sản phẩm -->
            <img src="<?= $img_show ?>" alt="<?= htmlspecialchars($product['company_series'] . ' ' . $product['model_no']) ?>">
            
            <!-- 7. Tiêu đề sản phẩm -->
            <h3><?= htmlspecialchars($product['company_series'] . ' ' . $product['model_no']) ?>
                <br><span>(<?= $product['ram(GB)'] ?>GB + <?= $product['rom(GB)'] ?>GB)</span>
            </h3>
            
            <!-- 8. Hiển thị số sao rating -->
            <div class="rating">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?= $i <= $avgRating ? '★' : '☆' ?>
                <?php endfor; ?>
            </div>

            
            <!-- 9. Hiển thị giá, giá cũ và giảm giá -->
            <div class="price-row">
                <span class="price"><?= number_format($price, 0, ',', '.') ?>đ</span>
                <span class="old-price"><?= number_format($old_price, 0, ',', '.') ?>đ</span>
                <?php if ($discount > 0): ?>
                    <span class="discount">-<?= $discount ?>%</span>
                <?php endif; ?>
            </div>
            <p class="installment">0% Trả góp / Hỗ trợ đổi máy mới</p>
            
            <!-- 10. Nút wishlist: fill khi đã yêu thích -->
            <form method="post" action="../../cart/add_to_wishlist.php" class="wishlist-form">
                <input type="hidden" name="imei_number" value="<?= $product['imei_number'] ?>">
                <button type="submit" class="wishlist-btn<?= $isWished ? ' wished' : '' ?>" title="Thêm vào wishlist">
                    <span class="wishlist-icon">
                        <!-- Tim outline khi chưa yêu thích, fill khi đã thích -->
                        <i class="fa-regular fa-heart" style="<?= $isWished ? 'display:none;' : '' ?>"></i>
                        <i class="fa-solid fa-heart" style="<?= $isWished ? 'display:inline;' : 'display:none;' ?>"></i>
                    </span>
                </button>
            </form>
        </div>
    </a>
    <?php
}
?>
</div>


<!-- Script điều khiển slider banner -->
<script>
let slideIndex = 0;
const slide = document.querySelector('.carousel-slide');
const totalSlides = slide.children.length;

// Hiển thị slide theo index
function showSlide(index) {
    if (index >= totalSlides) slideIndex = 0;
    else if (index < 0) slideIndex = totalSlides - 1;
    else slideIndex = index;
    const offset = -slideIndex * 100;
    slide.style.transform = `translateX(${offset}vw)`;
}

// Nút next, pre
document.addEventListener('DOMContentLoaded', function() {
    let slideIndex = 0;
    const slide = document.querySelector('.oppo-banner .carousel-slide');
    const totalSlides = slide.children.length;
    function showSlide(index) {
        if (index >= totalSlides) slideIndex = 0;
        else if (index < 0) slideIndex = totalSlides - 1;
        else slideIndex = index;
        slide.style.transform = `translateX(${-slideIndex * 100}%)`;
    }
    document.querySelector('.oppo-banner .next').onclick = () => showSlide(slideIndex + 1);
    document.querySelector('.oppo-banner .prev').onclick = () => showSlide(slideIndex - 1);
    setInterval(() => showSlide(slideIndex + 1), 4000);
    showSlide(slideIndex);
});


// Auto chạy slider mỗi 5 giây
setInterval(() => { showSlide(slideIndex + 1); }, 5000);

// Hiển thị slide đầu tiên khi tải trang
showSlide(slideIndex);
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($wishlistMsg)): ?>
        var popup = document.getElementById('wishlist-popup');
        var msg = document.getElementById('wishlist-popup-msg');
        msg.innerText = "<?= addslashes($wishlistMsg) ?>";
        popup.style.display = "block";
        setTimeout(function() { popup.style.display = "none"; }, 3000);
    <?php endif; ?>
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let slideIndex = 0;
    const slide = document.querySelector('.oppo-banner .carousel-slide');
    const slides = slide.querySelectorAll('img');
    const totalSlides = slides.length;

    // Đặt chiều rộng cho .carousel-slide và từng img (slide ngang)
    slide.style.width = (totalSlides * 100) + '%';
    slides.forEach(img => img.style.width = (100 / totalSlides) + '%');

    function showSlide(index) {
        if (index >= totalSlides) slideIndex = 0;
        else if (index < 0) slideIndex = totalSlides - 1;
        else slideIndex = index;
        slide.style.transform = `translateX(${-slideIndex * (100 / totalSlides)}%)`;
    }

    document.querySelector('.oppo-banner .next').onclick = () => showSlide(slideIndex + 1);
    document.querySelector('.oppo-banner .prev').onclick = () => showSlide(slideIndex - 1);
    setInterval(() => showSlide(slideIndex + 1), 4000);
    showSlide(slideIndex);
});
</script>

</body>
</html>







