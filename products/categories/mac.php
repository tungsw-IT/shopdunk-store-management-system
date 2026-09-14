<?php
session_start();
include __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../product_images_helper.php';

$slides = [
    '../../images/mac-banner-1.png',
    '../../images/mac-banner-2.png',
    '../../images/mac-banner-3.png',
    '../../images/mac-banner-4.png',
    '../../images/mac-banner-5.png',
];

function renderStars($rating) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
    for ($i = 0; $i < $fullStars; $i++) echo '<i class="fa-solid fa-star" style="color:#fbc02d;font-size:18px;"></i>';
    if ($halfStar) echo '<i class="fa-solid fa-star-half-stroke" style="color:#fbc02d;font-size:18px;"></i>';
    for ($i = 0; $i < $emptyStars; $i++) echo '<i class="fa-regular fa-star" style="color:#ccc;font-size:18px;"></i>';
}

function getMacProductImage($product) {
    $folder = '../../images/';
    $imgBase = strtolower(str_replace([' ', '+', '-'], '', $product['company_series'])) . '_' . strtolower(str_replace([' ', '+', '-'], '', $product['model_no']));
    $png = $folder . $imgBase . '.png';
    $jpg = $folder . $imgBase . '.jpg';
    if (file_exists(__DIR__ . '/' . $png)) return resolve_primary_product_image_pdo($GLOBALS['pdo'], $product, $png);
    if (file_exists(__DIR__ . '/' . $jpg)) return resolve_primary_product_image_pdo($GLOBALS['pdo'], $product, $jpg);
    return resolve_primary_product_image_pdo($GLOBALS['pdo'], $product, $folder . 'no-image.png');
}

$ratingStmt = $pdo->query("SELECT company_series, model_no, AVG(rating) AS avg_rating FROM reviews GROUP BY company_series, model_no");
$ratingResults = $ratingStmt->fetchAll();
$ratings = [];
foreach ($ratingResults as $row) $ratings[$row['company_series']][$row['model_no']] = $row['avg_rating'];

$userWishlist = [];
if (isset($_SESSION['account_id'])) {
    $stmt = $pdo->prepare("SELECT imei_number FROM wishlist WHERE account_id = ?");
    $stmt->execute([$_SESSION['account_id']]);
    $userWishlist = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wishlist_imei'])) {
    header('Content-Type: application/json');
    if (!isset($_SESSION['account_id'])) { echo json_encode(['status' => 'login']); exit; }
    $account_id = $_SESSION['account_id'];
    $imei_number = $_POST['wishlist_imei'];
    $stmt = $pdo->prepare("SELECT 1 FROM wishlist WHERE account_id = ? AND imei_number = ?");
    $stmt->execute([$account_id, $imei_number]);
    if ($stmt->fetch()) { echo json_encode(['status' => 'exist']); exit; }
    $stmt = $pdo->prepare("INSERT INTO wishlist (account_id, imei_number) VALUES (?, ?)");
    echo json_encode(['status' => $stmt->execute([$account_id, $imei_number]) ? 'ok' : 'err']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM mobile WHERE company_name = 'Apple' AND (company_series LIKE ? OR company_series LIKE ?) ORDER BY price ASC");
$stmt->execute(['%MacBook%', '%Mac%']);
$products = $stmt->fetchAll();
foreach ($products as &$product) {
    $product['discount'] = (!empty($product['old_price']) && $product['old_price'] > $product['price']) ? round((($product['old_price'] - $product['price']) / $product['old_price']) * 100) : 0;
}
unset($product);

$storage = $_GET['storage'] ?? '';
$ram = $_GET['ram'] ?? '';
$price = $_GET['price'] ?? '';
$discountMin = (int)($_GET['discount'] ?? 0);
$rating = (int)($_GET['rating'] ?? 0);
$sort = $_GET['sort'] ?? '';
$displayedProducts = array_filter($products, function ($p) use ($storage, $ram, $price, $discountMin, $rating, $ratings) {
    if ($storage && ($p['rom(GB)'] . 'GB' !== $storage)) return false;
    if ($ram && ($p['ram(GB)'] . 'GB' !== $ram)) return false;
    if ($price) {
        $pr = $p['price'];
        if ($price === 'under-20' && $pr >= 20000000) return false;
        if ($price === '20-30' && ($pr < 20000000 || $pr > 30000000)) return false;
        if ($price === '30-40' && ($pr < 30000000 || $pr > 40000000)) return false;
        if ($price === 'above-40' && $pr <= 40000000) return false;
    }
    if ($discountMin && $p['discount'] < $discountMin) return false;
    if ($rating > 0) { $avg = $ratings[$p['company_series']][$p['model_no']] ?? 5; if (floor($avg) < $rating) return false; }
    return true;
});
if ($sort === 'price-asc') usort($displayedProducts, fn($a, $b) => $a['price'] <=> $b['price']);
elseif ($sort === 'price-desc') usort($displayedProducts, fn($a, $b) => $b['price'] <=> $a['price']);
elseif ($sort === 'discount-desc') usort($displayedProducts, fn($a, $b) => $b['discount'] <=> $a['discount']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mac | ShopDunk</title>
  <link rel="stylesheet" href="../../assets/css/style_iphone.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    .page-shell { padding: 0 20px 48px; }
    .slider-shell { max-width: 1500px; margin: 24px auto 20px; position: relative; }
    .slider { background:#fff; border-radius:28px; box-shadow:0 18px 48px rgba(15,23,42,.08); overflow:hidden; position:relative; }
    .track { display:flex; transition:transform .65s ease; width:100%; }
    .slide { flex:0 0 100%; min-width:100%; padding:18px; box-sizing:border-box; background:#f4f6fb; }
    .slide img { width:100%; height:auto; display:block; border-radius:22px; }
    .arrow { position:absolute; top:50%; transform:translateY(-50%); width:58px; height:58px; border:none; border-radius:50%; background:rgba(255,255,255,.9); box-shadow:0 10px 24px rgba(15,23,42,.14); font-size:34px; color:#9aa3b2; cursor:pointer; z-index:2; }
    .arrow.prev { left:18px; } .arrow.next { right:18px; }
    .dots { display:flex; justify-content:center; gap:10px; padding:18px 0 4px; }
    .dot { width:10px; height:10px; border-radius:50%; border:none; background:#c7ced9; cursor:pointer; }
    .dot.active { background:#3b3f47; transform:scale(1.18); }
    .filter-sort-form { display:flex; flex-wrap:nowrap; gap:8px; justify-content:center; align-items:center; margin:18px auto 18px; background:#f5f5f5; border-radius:8px; padding:10px 18px; max-inline-size:1500px; min-inline-size:700px; box-sizing:border-box; }
    .filter-sort-form select, .filter-sort-form button { padding:6px 10px; border-radius:5px; border:1px solid #ccc; font-size:14px; }
    .filter-sort-form button { background:orange; color:#fff; border:none; cursor:pointer; }
    .filter-sort-form button:hover { background:#cc6600; }
  </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<div class="page-shell">
  <section class="slider-shell">
    <div class="slider" id="pageSlider">
      <button class="arrow prev" type="button" data-direction="-1">&#8249;</button>
      <div class="track" id="pageTrack">
        <?php foreach ($slides as $index => $slide): ?><div class="slide"><img src="<?= htmlspecialchars($slide) ?>" alt="Mac banner <?= $index + 1 ?>"></div><?php endforeach; ?>
      </div>
      <button class="arrow next" type="button" data-direction="1">&#8250;</button>
    </div>
    <div class="dots"><?php foreach ($slides as $index => $slide): ?><button class="dot<?= $index === 0 ? ' active' : '' ?>" type="button" data-slide="<?= $index ?>"></button><?php endforeach; ?></div>
  </section>

  <nav class="filter-bar"><a href="mac.php" class="tab active">Tất cả</a></nav>
  <form method="GET" class="filter-sort-form">
    <div style="display:flex; gap:10px;">
      <select name="rating"><option value="">-- Mức độ đánh giá --</option><option value="5" <?= (isset($_GET['rating']) && $_GET['rating']=='5')?'selected':'' ?>>= 5 sao</option><option value="4" <?= (isset($_GET['rating']) && $_GET['rating']=='4')?'selected':'' ?>>>= 4 sao</option><option value="3" <?= (isset($_GET['rating']) && $_GET['rating']=='3')?'selected':'' ?>>>= 3 sao</option><option value="2" <?= (isset($_GET['rating']) && $_GET['rating']=='2')?'selected':'' ?>>>= 2 sao</option><option value="1" <?= (isset($_GET['rating']) && $_GET['rating']=='1')?'selected':'' ?>>>= 1 sao</option></select>
      <select name="storage"><option value="">-- Bộ nhớ --</option><option value="128GB" <?= (isset($_GET['storage']) && $_GET['storage']=='128GB')?'selected':'' ?>>128GB</option><option value="256GB" <?= (isset($_GET['storage']) && $_GET['storage']=='256GB')?'selected':'' ?>>256GB</option><option value="512GB" <?= (isset($_GET['storage']) && $_GET['storage']=='512GB')?'selected':'' ?>>512GB</option><option value="1024GB" <?= (isset($_GET['storage']) && $_GET['storage']=='1024GB')?'selected':'' ?>>1TB</option><option value="2048GB" <?= (isset($_GET['storage']) && $_GET['storage']=='2048GB')?'selected':'' ?>>2TB</option></select>
      <select name="ram"><option value="">-- RAM --</option><option value="4GB" <?= (isset($_GET['ram']) && $_GET['ram']=='4GB')?'selected':'' ?>>4GB</option><option value="6GB" <?= (isset($_GET['ram']) && $_GET['ram']=='6GB')?'selected':'' ?>>6GB</option><option value="8GB" <?= (isset($_GET['ram']) && $_GET['ram']=='8GB')?'selected':'' ?>>8GB</option><option value="12GB" <?= (isset($_GET['ram']) && $_GET['ram']=='12GB')?'selected':'' ?>>12GB</option></select>
      <select name="price"><option value="">-- Khoảng giá --</option><option value="under-20" <?= (isset($_GET['price']) && $_GET['price']=='under-20')?'selected':'' ?>>Dưới 20 triệu</option><option value="20-30" <?= (isset($_GET['price']) && $_GET['price']=='20-30')?'selected':'' ?>>20 - 30 triệu</option><option value="30-40" <?= (isset($_GET['price']) && $_GET['price']=='30-40')?'selected':'' ?>>30 - 40 triệu</option><option value="above-40" <?= (isset($_GET['price']) && $_GET['price']=='above-40')?'selected':'' ?>>Trên 40 triệu</option></select>
      <select name="discount"><option value="">-- Giảm giá --</option><option value="10" <?= (isset($_GET['discount']) && $_GET['discount']=='10')?'selected':'' ?>>Từ 10%</option><option value="20" <?= (isset($_GET['discount']) && $_GET['discount']=='20')?'selected':'' ?>>Từ 20%</option><option value="30" <?= (isset($_GET['discount']) && $_GET['discount']=='30')?'selected':'' ?>>Từ 30%</option></select>
    </div>
    <div style="display:flex; align-items:center; gap:10px;"><label for="sort" style="font-weight:bold;">Sắp xếp:</label><select name="sort" id="sort" class="filter-select"><option value="">Mặc định</option><option value="price-asc" <?= (isset($_GET['sort']) && $_GET['sort']=='price-asc')?'selected':'' ?>>Giá tăng dần</option><option value="price-desc" <?= (isset($_GET['sort']) && $_GET['sort']=='price-desc')?'selected':'' ?>>Giá giảm dần</option><option value="discount-desc" <?= (isset($_GET['sort']) && $_GET['sort']=='discount-desc')?'selected':'' ?>>Giảm giá nhiều nhất</option></select><button type="submit">Áp dụng</button><button type="button" onclick="window.location.href='mac.php'">Reload</button></div>
  </form>

  <div class="product-grid">
    <?php if (empty($displayedProducts)): ?><div style="padding:30px 0;font-size:18px;color:#777;">Không tìm thấy sản phẩm phù hợp.</div><?php endif; ?>
    <?php foreach ($displayedProducts as $product): $slug = urlencode($product['model_no']); $config = $product['ram(GB)'] . 'GB + ' . $product['rom(GB)'] . 'GB'; $isWishlisted = in_array($product['imei_number'], $userWishlist); $heartClass = $isWishlisted ? 'fa-solid fa-heart' : 'fa-regular fa-heart'; $heartColor = $isWishlisted ? '#e53935' : '#ff4444'; ?>
      <div class="product-card hover-hand" style="position:relative;">
        <button type="button" class="wishlist-btn" data-imei="<?= htmlspecialchars($product['imei_number']) ?>" style="position:absolute;inset-block-start:14px;inset-inline-end:16px;background:transparent;border:none;cursor:pointer;z-index:3;"><i class="<?= $heartClass ?>" style="font-size:22px;color:<?= $heartColor ?>"></i></button>
        <a href="../detail/product-detail.php?model_no=<?= $slug ?>" class="product-card-link">
          <img src="<?= htmlspecialchars(getMacProductImage($product)) ?>" alt="<?= htmlspecialchars($product['company_series'] . ' ' . $product['model_no']) ?>">
          <h3><?= htmlspecialchars($product['company_series'] . ' ' . $product['model_no']) ?><br><span>(<?= htmlspecialchars($config) ?>)</span></h3>
          <div class="rating-stars" style="margin: 5px 0;"><?php renderStars($ratings[$product['company_series']][$product['model_no']] ?? 5); ?></div>
          <div class="price-row"><span class="price"><?= number_format($product['price'], 0, '.', '.') ?>đ</span><?php if ($product['discount'] > 0): ?><span class="old-price"><?= number_format($product['old_price'], 0, '.', '.') ?>đ</span><?php endif; ?></div>
          <p class="installment">0% Lãi suất 12 tháng</p>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script>
window.addEventListener('load', () => {
  const track = document.getElementById('pageTrack');
  const dots = Array.from(document.querySelectorAll('.dot'));
  const slider = document.getElementById('pageSlider');
  const arrows = Array.from(document.querySelectorAll('.arrow'));
  if (!track || dots.length === 0) return;
  let currentSlide = 0; let autoSlideTimer = null;
  function renderSlide(index) { const total = dots.length; currentSlide = (index + total) % total; track.style.transform = `translate3d(-${currentSlide * 100}%, 0, 0)`; dots.forEach((dot, i) => dot.classList.toggle('active', i === currentSlide)); }
  function startAutoSlide() { clearInterval(autoSlideTimer); autoSlideTimer = window.setInterval(() => renderSlide(currentSlide + 1), 3500); }
  dots.forEach(dot => dot.addEventListener('click', () => { renderSlide(Number(dot.dataset.slide || 0)); startAutoSlide(); }));
  arrows.forEach(arrow => arrow.addEventListener('click', () => { renderSlide(currentSlide + Number(arrow.dataset.direction || 1)); startAutoSlide(); }));
  if (slider) { slider.addEventListener('mouseenter', () => clearInterval(autoSlideTimer)); slider.addEventListener('mouseleave', startAutoSlide); }
  document.addEventListener('visibilitychange', () => { if (document.hidden) clearInterval(autoSlideTimer); else startAutoSlide(); });
  renderSlide(0); startAutoSlide();
});
document.querySelectorAll('.wishlist-btn').forEach(btn => {
  btn.onclick = function(e) {
    e.stopPropagation();
    var imei = this.dataset.imei;
    fetch(window.location.href, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'wishlist_imei=' + encodeURIComponent(imei) })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'login') alert('Bạn cần đăng nhập để thêm sản phẩm vào mục yêu thích');
        else if (data.status === 'ok') { alert('Đã thêm vào mục yêu thích!'); this.querySelector('i').classList.remove('fa-regular'); this.querySelector('i').classList.add('fa-solid'); this.querySelector('i').style.color = '#e53935'; }
        else if (data.status === 'exist') alert('Sản phẩm đã có trong mục yêu thích!');
        else alert('Có lỗi xảy ra, vui lòng thử lại!');
      });
  };
});
</script>
</body>
</html>








