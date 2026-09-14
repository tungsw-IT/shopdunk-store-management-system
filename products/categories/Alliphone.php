<?php
session_start(); // Khởi động session 
// Kết nối CSDL
include __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../product_images_helper.php';

// Hàm tạo tên file ảnh từ model_no (chỉ dùng cho iPhone, theo quy tắc: iphone_model.png)
function toImageFileName($model_no) {
    $str = strtolower($model_no);      // Đưa về chữ thường         
    $str = preg_replace('/[^a-z0-9_]/', '', $str); //  Loại bỏ ký tự đặc biệt, Chỉ giữ a-z, 0-9, _
    return 'iphone_' . $str . '.png';
}
// Hàm hiển thị số sao dựa trên rating trung bình (sử dụng icon FontAwesome)
function renderStars($rating) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5 ? true : false;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    for ($i = 0; $i < $fullStars; $i++) {
        echo '<i class="fa-solid fa-star" style="color: #fbc02d; font-size: 18px;"></i>'; // sao vàng đầy
    }

    if ($halfStar) {
        echo '<i class="fa-solid fa-star-half-stroke" style="color: #fbc02d; font-size: 18px;"></i>'; // nửa sao
    }

    for ($i = 0; $i < $emptyStars; $i++) {
        echo '<i class="fa-regular fa-star" style="color: #ccc; font-size: 18px;"></i>'; // sao xám rỗng
    }
}
// Lấy danh sách các sản phẩm đã có trong wishlist của user hiện tại (nếu đã đăng nhập)
$userWishlist = [];
if (isset($_SESSION['account_id'])) {
    $stmt = $pdo->prepare("SELECT imei_number FROM wishlist WHERE account_id = ?");
    $stmt->execute([$_SESSION['account_id']]);
    $userWishlist = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
// Xử lý khi có AJAX request thêm sản phẩm vào wishlist (bấm trái tim)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wishlist_imei'])) {
    header('Content-Type: application/json');
    if (!isset($_SESSION['account_id'])) {
        echo json_encode(['status' => 'login']);
        exit;
    }
    include __DIR__ . '/../../config/db.php';
    $account_id = $_SESSION['account_id'];
    $imei_number = $_POST['wishlist_imei'];

    // Check đã có chưa
    $stmt = $pdo->prepare("SELECT 1 FROM wishlist WHERE account_id = ? AND imei_number = ?");
    $stmt->execute([$account_id, $imei_number]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'exist']);
        exit;
    }
    // Thêm vào wishlist
    $stmt = $pdo->prepare("INSERT INTO wishlist (account_id, imei_number) VALUES (?, ?)");
    if ($stmt->execute([$account_id, $imei_number])) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'err']);
    }
    exit;
}
// Lấy toàn bộ sản phẩm iPhone
$stmt = $pdo->prepare("SELECT *, CONCAT(company_series, model_no) AS img FROM mobile WHERE company_name = 'Apple' AND company_series = 'iPhone'");
$stmt->execute();
$products = $stmt->fetchAll();

// Tính rating trung bình
$ratingStmt = $pdo->query("SELECT company_series, model_no, AVG(rating) AS avg_rating FROM reviews GROUP BY company_series, model_no");
$ratingResults = $ratingStmt->fetchAll();

$ratings = [];
foreach ($ratingResults as $row) {
    $ratings[$row['company_series']][$row['model_no']] = $row['avg_rating'];
}

// Tính phần trăm giảm giá cho từng sản phẩm (nếu có old_price và giá nhỏ hơn old_price)
foreach ($products as &$product) {
    $product['discount'] = (!empty($product['old_price']) && $product['old_price'] > $product['price'])
        ? round((($product['old_price'] - $product['price']) / $product['old_price']) * 100)
        : 0;
}
unset($product);

// Lấy thông tin lọc/sắp xếp từ URL (GET)
$model = $_GET['model'] ?? '';
$storage = $_GET['storage'] ?? '';
$ram = $_GET['ram'] ?? '';
$price = $_GET['price'] ?? '';
$discountMin = (int)($_GET['discount'] ?? 0);
$rating = (int)($_GET['rating'] ?? 0);
$sort = $_GET['sort'] ?? '';
// Hàm lọc sản phẩm dựa trên các tiêu chí (model, bộ nhớ, RAM, giá, giảm giá, rating)
$filtered = array_filter($products, function ($p) use ($model, $storage, $ram, $price, $discountMin, $rating, $ratings) {
    $config = $p['ram(GB)'] . 'GB + ' . $p['rom(GB)'] . 'GB';
    if ($model && stripos($p['model_no'], $model) === false) return false;
    if ($storage && stripos($config, $storage) === false) return false;
    if ($ram && $p['ram(GB)'] . 'GB' !== $ram) return false;
   // Lọc theo khoảng giá
    if ($price) {
        $pr = $p['price'];
        if ($price === 'under-20' && $pr >= 20000000) return false;
        if ($price === '20-30' && ($pr < 20000000 || $pr > 30000000)) return false;
        if ($price === '30-40' && ($pr < 30000000 || $pr > 40000000)) return false;
        if ($price === 'above-40' && $pr <= 40000000) return false;
    }
  // Lọc theo phần trăm giảm giá tối thiểu
    if ($p['discount'] < $discountMin) return false;

    if ($rating > 0) {
        $avg = $ratings[$p['company_series']][$p['model_no']] ?? 5;
        if (floor($avg) < $rating) return false;
    }

    return true;
});

// Sắp xếp mảng sản phẩm theo tiêu chí đã chọn
if ($sort === 'price-asc') {
    usort($filtered, fn($a, $b) => $a['price'] <=> $b['price']);
} elseif ($sort === 'price-desc') {
    usort($filtered, fn($a, $b) => $b['price'] <=> $a['price']);
} elseif ($sort === 'discount-desc') {
    usort($filtered, fn($a, $b) => $b['discount'] <=> $a['discount']);
}
 include __DIR__ . '/../../includes/header.php'; 
$displayedProducts = $filtered; // Danh sách sản phẩm cuối cùng sẽ hiển thị
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>iPhone Series - Sản phẩm</title>
    <link rel="stylesheet" href="../../assets/css/style_iphone.css">
    <style>
      .filter-sort-form {
  display: flex;
  flex-wrap: nowrap;       
  gap: 8px;                 
  justify-content: center; 
  align-items: center;
  margin: 18px auto 10px auto;
  background: #f5f5f5;
  border-radius: 8px;
  padding: 10px 18px;     
  max-inline-size: 1500px;    
  min-inline-size: 700px;         
  box-sizing: border-box;
}
      .filter-sort-form > div {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
      }
      .filter-select {
        padding: 6px 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
      }
      .filter-sort-form button {
        background-color: orange;
        color: white;
        padding: 6px 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
      }
      .filter-sort-form button:hover {
        background-color: #cc6600;
      }
    </style>
</head>
<body>
    <!-- Font Awesome để hiển thị icon sao -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<!-- Banner iPhone -->
<header class="iphone-header">
 
    <div class="banner-container">
        <img src="../../images/iphone.gif" alt="iPhone Banner" class="banner-gif">
        <div class="banner-text">
            <h1>iPhone</h1>
        </div>
    </div>
</header>
<!-- Thanh tab điều hướng các đời iPhone -->
<nav class="filter-bar">
    <a href="Alliphone.php" class="tab active">Tất cả</a>
    <a href="../detail/iphone14.php" class="tab">iPhone 14</a>
    <a href="../detail/iphone15.php" class="tab">iPhone 15</a>
    <a href="../detail/iphone16.php" class="tab">iPhone 16</a>
    <a href="../detail/iphone17.php" class="tab">iPhone 17</a>
</nav>
<!-- Form lọc và sắp xếp sản phẩm -->
<form method="GET" class="filter-sort-form">
   <!-- Bộ lọc đánh giá -->
  <div>
    <select name="rating" class="filter-select" >
      <option value="">-- Mức độ đánh giá --</option>
      <option value="5" <?= (isset($_GET['rating']) && $_GET['rating']=='5')?'selected':'' ?>> = 5 sao</option>
      <option value="4" <?= (isset($_GET['rating']) && $_GET['rating']=='4')?'selected':'' ?>>>= 4 sao</option>
      <option value="3" <?= (isset($_GET['rating']) && $_GET['rating']=='3')?'selected':'' ?>>>= 3 sao</option>
      <option value="2" <?= (isset($_GET['rating']) && $_GET['rating']=='2')?'selected':'' ?>>>= 2 sao</option>
      <option value="1" <?= (isset($_GET['rating']) && $_GET['rating']=='1')?'selected':'' ?>>>= 1 sao</option>
    </select>
       <!-- Bộ lọc bộ nhớ -->
    <select name="storage" class="filter-select" >
      <option value="">-- Bộ nhớ --</option>
      <option value="128GB" <?= (isset($_GET['storage']) && $_GET['storage']=='128GB')?'selected':'' ?>>128GB</option>
      <option value="256GB" <?= (isset($_GET['storage']) && $_GET['storage']=='256GB')?'selected':'' ?>>256GB</option>
      <option value="512GB" <?= (isset($_GET['storage']) && $_GET['storage']=='512GB')?'selected':'' ?>>512GB</option>
      <option value="1024GB" <?= (isset($_GET['storage']) && $_GET['storage']=='1024GB')?'selected':'' ?>>1TB</option>
    </select>
        <!-- Bộ lọc RAM -->
    <select name="ram" class="filter-select" >
      <option value="">-- RAM --</option>
      <option value="6GB" <?= (isset($_GET['ram']) && $_GET['ram']=='6GB')?'selected':'' ?>> 6GB</option>
      <option value="8GB" <?= (isset($_GET['ram']) && $_GET['ram']=='8GB')?'selected':'' ?>> 8GB</option>
    </select>
       <!-- Bộ lọc giá -->
    <select name="price" class="filter-select" >
      <option value="">-- Khoảng giá --</option>
      <option value="under-20" <?= (isset($_GET['price']) && $_GET['price']=='under-20')?'selected':'' ?>>Dưới 20 triệu</option>
      <option value="20-30" <?= (isset($_GET['price']) && $_GET['price']=='20-30')?'selected':'' ?>>20 - 30 triệu</option>
      <option value="30-40" <?= (isset($_GET['price']) && $_GET['price']=='30-40')?'selected':'' ?>>30 - 40 triệu</option>
      <option value="above-40" <?= (isset($_GET['price']) && $_GET['price']=='above-40')?'selected':'' ?>>Trên 40 triệu</option>
    </select>
    <!-- Bộ lọc giảm giá -->
    <select name="discount" class="filter-select" >
      <option value="">-- Giảm giá --</option>
     <option value="10" <?= (isset($_GET['discount']) && $_GET['discount']=='10')?'selected':'' ?>>Từ 10%</option>
      <option value="20" <?= (isset($_GET['discount']) && $_GET['discount']=='20')?'selected':'' ?>>Từ 20%</option>
      <option value="30" <?= (isset($_GET['discount']) && $_GET['discount']=='30')?'selected':'' ?>>Từ 30%</option>
    </select>
    
  </div>
   <!-- Sắp xếp -->
  <div style="display:flex; align-items:center; gap:10px;">
  <label for="sort" style="font-weight:bold;">Sắp xếp:</label>
  <select name="sort" id="sort" class="filter-select">
    <option value="">Mặc định</option>
    <option value="price-asc" <?= (isset($_GET['sort']) && $_GET['sort']=='price-asc')?'selected':'' ?>>Giá tăng dần</option>
    <option value="price-desc" <?= (isset($_GET['sort']) && $_GET['sort']=='price-desc')?'selected':'' ?>>Giá giảm dần</option>
    <option value="discount-desc" <?= (isset($_GET['sort']) && $_GET['sort']=='discount-desc')?'selected':'' ?>>Giảm giá nhiều nhất</option>
  </select>
  <button type="submit" style="background-color: orange; color: white; padding: 6px 14px; border: none; border-radius: 5px; cursor: pointer;">Áp dụng</button>
  <button type="button" onclick="window.location.href='Alliphone.php'" style="background-color: #888; color: white; padding: 6px 14px; border: none; border-radius: 5px; cursor: pointer;">Reload</button>
</div>
</form>
<!-- Hiển thị sản phẩm dạng lưới -->
<div class="product-area-center">
<div class="product-grid">
 <?php foreach ($displayedProducts as $product): 
    $config = $product['ram(GB)'] . 'GB + ' . $product['rom(GB)'] . 'GB';
    $modelNo = urlencode($product['model_no']);
    $imgName = toImageFileName($product['model_no']); 
    $imagePath = resolve_primary_product_image_pdo($pdo, $product, '../../images/' . $imgName);
     // Kiểm tra sản phẩm này đã có trong wishlist user chưa (để hiện tim đầy)
    $isWishlisted = in_array($product['imei_number'], $userWishlist);
    $heartClass = $isWishlisted ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
    $heartColor = $isWishlisted ? '#e53935' : '#ff4444';
?>
<div class="product-card hover-hand" style="position:relative;">
    <!-- Nút trái tim: nếu đã thích sẽ là tim đầy (solid), chưa thích thì tim rỗng (regular) -->
    <button type="button" class="wishlist-btn"
        data-imei="<?= htmlspecialchars($product['imei_number']) ?>"
        style="position:absolute;inset-block-start:14px;inset-inline-end:16px;background:transparent;border:none;cursor:pointer;z-index:3;">
        <i class="<?= $heartClass ?>" style="font-size:22px;color:<?= $heartColor ?>"></i>
    </button>
    <a href="../detail/product-detail-iphone.php?model_no=<?= $modelNo ?>" class="product-card-link">
        <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($product['model_no']) ?>">
        <h3><?= htmlspecialchars($product['model_no']) ?><br><span>(<?= $config ?>)</span></h3>
        <div class="rating-stars" style="margin: 5px 0;">
          <?php renderStars($ratings[$product['company_series']][$product['model_no']] ?? 5); ?>
        </div>
        <div class="price-row">
          <span class="price"><?= number_format($product['price'], 0, '.', '.') ?>đ</span>
          <?php if ($product['discount'] > 0): ?>
            <span class="old-price"><?= number_format($product['old_price'], 0, '.', '.') ?>đ</span>
            <span class="discount">-<?= $product['discount'] ?>%</span>
          <?php endif; ?>
        </div>
        <p class="installment">0% Lãi suất 12 tháng</p>
    </a>
</div>
<?php endforeach; ?>
</div>
</div>
  <!-- Script bắt sự kiện bấm vào trái tim -->
<script>
document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.onclick = function(e) {
        e.stopPropagation();// Không kích hoạt sự kiện click ở cha (card)
        var imei = this.dataset.imei;// Lấy mã imei sản phẩm
        fetch(window.location.href, {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'wishlist_imei='+encodeURIComponent(imei)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'login') {
                alert('Bạn cần đăng nhập để thêm sản phẩm vào mục yêu thích');
            } else if (data.status === 'ok') {
                alert('Đã thêm vào mục yêu thích!');
                 // Đổi icon tim thành đầy (solid)
                this.querySelector('i').classList.remove('fa-regular');
                this.querySelector('i').classList.add('fa-solid');
                this.querySelector('i').style.color = '#e53935';
            } else if (data.status === 'exist') {
                alert('Sản phẩm đã có trong mục yêu thích!');
            } else {
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            }
        })
        .catch(err => {
            alert('Lỗi kết nối đến server!');
            console.error(err);
        });
    };
});
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>













