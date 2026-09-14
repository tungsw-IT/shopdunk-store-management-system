<?php
session_start();
include __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../product_images_helper.php';

ensure_product_images_table_pdo($pdo);

$isLoggedIn = isset($_SESSION['account_id']) && (int) $_SESSION['account_id'] > 0;
$modelNo = trim((string) ($_GET['model_no'] ?? ''));

if ($modelNo === '') {
    die('<h2>Thiếu mã sản phẩm</h2>');
}

$stmt = $pdo->prepare('SELECT * FROM mobile WHERE model_no = ? LIMIT 1');
$stmt->execute([$modelNo]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die('<h2>Sản phẩm không tồn tại</h2>');
}

function generic_detail_fallback_image(array $product): string
{
    $brand = strtolower((string) ($product['company_name'] ?? ''));
    return $brand === 'samsung' ? '../../images/no-image.png' : '../../images/no-image.png';
}

function generic_detail_product_name(array $product): string
{
    $parts = array_filter([
        $product['company_name'] ?? '',
        $product['company_series'] ?? '',
        $product['model_no'] ?? '',
    ]);
    return trim(implode(' ', $parts));
}

function generic_detail_description(array $product): string
{
    $segments = [];
    $name = generic_detail_product_name($product);

    if ($name !== '') {
        $segments[] = $name . ' mang đến trải nghiệm ổn định cho nhu cầu học tập, làm việc và giải trí.';
    }
    if (!empty($product['display_size(inchi)']) || !empty($product['display_quality'])) {
        $displayParts = [];
        if (!empty($product['display_size(inchi)'])) {
            $displayParts[] = $product['display_size(inchi)'] . ' inch';
        }
        if (!empty($product['display_quality'])) {
            $displayParts[] = $product['display_quality'];
        }
        $segments[] = 'Màn hình ' . implode(', ', $displayParts) . ' hiển thị hình ảnh rõ ràng và sắc nét.';
    }
    if (!empty($product['processor'])) {
        $segments[] = 'Máy sử dụng chip ' . $product['processor'] . ' cho khả năng xử lý mượt mà và ổn định.';
    }
    if (!empty($product['ram(GB)']) || !empty($product['rom(GB)'])) {
        $memoryParts = [];
        if (!empty($product['ram(GB)'])) {
            $memoryParts[] = 'RAM ' . $product['ram(GB)'] . 'GB';
        }
        if (!empty($product['rom(GB)'])) {
            $memoryParts[] = 'bộ nhớ ' . $product['rom(GB)'] . 'GB';
        }
        $segments[] = 'Cấu hình với ' . implode(', ', $memoryParts) . ' đáp ứng tốt nhiều nhu cầu sử dụng khác nhau.';
    }
    if (!empty($product['battery_capacity(mah)'])) {
        $segments[] = 'Pin ' . $product['battery_capacity(mah)'] . 'mAh giúp kéo dài thời gian sử dụng trong ngày.';
    }

    return $segments ? implode(' ', $segments) : 'Mô tả sản phẩm đang được cập nhật.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!$isLoggedIn) {
        header('Location: ../../auth/login.php');
        exit;
    }

    $productImei = trim((string) ($_POST['product_imei'] ?? ''));
    $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
    $accountId = (int) $_SESSION['account_id'];

    if ($productImei !== '') {
        $stmtCheck = $pdo->prepare('SELECT quantity FROM cart WHERE product_imei = ? AND account_id = ?');
        $stmtCheck->execute([$productImei, $accountId]);
        $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $stmtUpdate = $pdo->prepare('UPDATE cart SET quantity = quantity + ? WHERE product_imei = ? AND account_id = ?');
            $stmtUpdate->execute([$quantity, $productImei, $accountId]);
        } else {
            $stmtInsert = $pdo->prepare('INSERT INTO cart (product_imei, quantity, account_id) VALUES (?, ?, ?)');
            $stmtInsert->execute([$productImei, $quantity, $accountId]);
        }

        header('Location: product-detail.php?model_no=' . urlencode($modelNo) . '&added=1');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!$isLoggedIn) {
        header('Location: ../../auth/login.php');
        exit;
    }

    $rating = max(1, min(5, (int) ($_POST['rating'] ?? 0)));
    $reviewText = trim((string) ($_POST['comment'] ?? ''));
    $accountId = (int) $_SESSION['account_id'];

    if ($rating > 0) {
        $stmtReview = $pdo->prepare('INSERT INTO reviews (account_id, company_series, model_no, rating, review_text) VALUES (?, ?, ?, ?, ?)');
        $stmtReview->execute([$accountId, $product['company_series'], $product['model_no'], $rating, $reviewText !== '' ? $reviewText : null]);
        header('Location: product-detail.php?model_no=' . urlencode($modelNo) . '&reviewed=1');
        exit;
    }
}

$galleryImages = array_map('product_image_url', fetch_product_images_pdo($pdo, (string) $product['imei_number']));
if (!$galleryImages) {
    $galleryImages[] = generic_detail_fallback_image($product);
}

$relatedStmt = $pdo->prepare('
    SELECT * FROM mobile
    WHERE company_name = ?
      AND company_series = ?
      AND imei_number <> ?
    ORDER BY model_no ASC
    LIMIT 4
');
$relatedStmt->execute([
    $product['company_name'],
    $product['company_series'],
    $product['imei_number'],
]);
$relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

$reviewsStmt = $pdo->prepare('
    SELECT r.*, a.accounts_name
    FROM reviews r
    LEFT JOIN accounts a ON r.account_id = a.account_id
    WHERE r.model_no = ? AND r.company_series = ?
    ORDER BY r.created_at DESC
');
$reviewsStmt->execute([$product['model_no'], $product['company_series']]);
$reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

$description = generic_detail_description($product);
$stockQuantity = (int) ($product['stock_quantity'] ?? 1);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars(generic_detail_product_name($product)) ?> - Chi tiết sản phẩm</title>
  <link rel="stylesheet" href="../../assets/css/style_iphone.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 0;
    }
    .product-detail-container {
      max-width: 1100px;
      margin: 50px auto;
      background: #fff;
      padding: 30px;
      border-radius: 14px;
      box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    }
    .product-top-layout {
      display: flex;
      gap: 40px;
      margin-bottom: 40px;
      align-items: flex-start;
    }
    .left-column { flex: 1.15; }
    .right-column { flex: 0.95; }
    .product-gallery-main {
      background: #f7f7fb;
      border: 1px solid #ececf3;
      border-radius: 16px;
      padding: 18px;
      margin-bottom: 14px;
    }
    .product-gallery-main img {
      width: 100%;
      max-width: 560px;
      height: 520px;
      object-fit: contain;
      display: block;
      margin: 0 auto;
      background: #fff;
      border-radius: 12px;
    }
    .product-gallery-thumbs {
      display: flex;
      gap: 10px;
      overflow-x: auto;
      padding-bottom: 6px;
    }
    .product-gallery-thumb {
      width: 88px;
      height: 88px;
      border: 1.5px solid #d9dce5;
      border-radius: 12px;
      background: #fff;
      padding: 6px;
      cursor: pointer;
      flex: 0 0 auto;
    }
    .product-gallery-thumb.active {
      border-color: #0a65cc;
      box-shadow: 0 0 0 3px rgba(10, 101, 204, 0.12);
    }
    .product-gallery-thumb img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 8px;
    }
    .price {
      color: #e60012;
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 18px;
    }
    .button-column {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin: 22px 0;
    }
    .buy-button,
    .add-to-cart-button {
      width: 100%;
      border: none;
      border-radius: 8px;
      padding: 16px 18px;
      font-size: 18px;
      font-weight: 700;
      color: #fff;
      cursor: pointer;
    }
    .buy-button { background: #ff1f0f; }
    .add-to-cart-button {
      background: #1677f2;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    .buy-button:disabled,
    .add-to-cart-button:disabled {
      opacity: 0.55;
      cursor: not-allowed;
    }
    .stock-warning {
      margin: 0;
      color: #dc2626;
      font-weight: 700;
    }
    .specs {
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 18px;
      background: #fafafa;
    }
    .specs table {
      width: 100%;
      border-collapse: collapse;
    }
    .specs td {
      padding: 10px 8px;
      border-bottom: 1px solid #e5e7eb;
      vertical-align: top;
    }
    .specs td:first-child {
      width: 42%;
      font-weight: 700;
    }
    .detail-section-title {
      margin: 28px 0 16px;
      font-size: 20px;
      font-weight: 700;
      color: #111827;
    }
    .description-copy {
      line-height: 1.65;
      color: #1f2937;
      margin-bottom: 8px;
    }
    .review-section {
      margin-top: 34px;
    }
    .review-form label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
      color: #111827;
    }
    .review-form input,
    .review-form textarea {
      width: 100%;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      padding: 12px 14px;
      font-size: 15px;
      margin-bottom: 14px;
      box-sizing: border-box;
    }
    .review-form button {
      background: #ff7a00;
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 10px 18px;
      font-weight: 700;
      cursor: pointer;
    }
    .star-rating {
      font-size: 28px;
      color: #d1d5db;
      margin-bottom: 12px;
      user-select: none;
    }
    .star-rating .star {
      cursor: pointer;
      margin-right: 4px;
    }
    .star-rating .star.selected,
    .star-rating .star.hovered {
      color: #fbbf24;
    }
    .review-item {
      border-top: 1px solid #e5e7eb;
      padding: 16px 0;
    }
    .review-item:first-of-type {
      border-top: none;
    }
    .review-name {
      font-weight: 700;
      color: #111827;
    }
    .review-time {
      color: #6b7280;
      font-size: 13px;
    }
    .related-row {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      padding-bottom: 10px;
    }
    .product-link {
      text-decoration: none;
      color: inherit;
      flex: 0 0 220px;
    }
    .related-product {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      padding: 14px 12px 18px;
      text-align: center;
      box-shadow: 0 4px 16px rgba(0,0,0,0.06);
      min-height: 285px;
    }
    .related-product img {
      width: 100%;
      height: 170px;
      object-fit: contain;
      margin-bottom: 10px;
      background: #fff;
      border-radius: 12px;
    }
    .related-product h4 {
      margin: 8px 0 6px;
      font-size: 16px;
      line-height: 1.45;
      color: #111827;
    }
    .related-config {
      font-size: 13px;
      color: #6b7280;
    }
    .related-price {
      margin: 8px 0 0;
      color: #e60012;
      font-weight: 700;
    }
    #loginPopup,
    #cartPopup,
    #reviewPopup {
      display: none;
      position: fixed;
      left: 50%;
      transform: translateX(-50%);
      padding: 18px 28px;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.15);
      font-size: 18px;
      z-index: 9999;
      font-weight: 600;
      color: #fff;
    }
    #loginPopup { top: 24px; background: #004aad; }
    #cartPopup { top: 24px; background: #0b8d3b; }
    #reviewPopup { top: 24px; background: #0a65cc; }
    @media (max-width: 900px) {
      .product-top-layout { flex-direction: column; }
      .product-gallery-main img { height: 360px; }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<div id="loginPopup">Vui lòng đăng nhập để tiếp tục.</div>
<div id="cartPopup">Đã thêm sản phẩm vào giỏ hàng.</div>
<div id="reviewPopup">Đã gửi đánh giá thành công.</div>

<div class="product-detail-container">
  <div class="product-top-layout">
    <div class="left-column">
      <div class="product-gallery-main">
        <img id="mainProductImage" src="<?= htmlspecialchars($galleryImages[0]) ?>" alt="<?= htmlspecialchars($product['model_no']) ?>">
      </div>
      <div class="product-gallery-thumbs">
        <?php foreach ($galleryImages as $index => $imagePath): ?>
          <button type="button" class="product-gallery-thumb <?= $index === 0 ? 'active' : '' ?>" data-image="<?= htmlspecialchars($imagePath) ?>">
            <img src="<?= htmlspecialchars($imagePath) ?>" alt="Ảnh sản phẩm">
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="right-column">
      <h2><?= htmlspecialchars(generic_detail_product_name($product)) ?></h2>
      <p class="price"><?= number_format((float) $product['price'], 0, '.', '.') ?>đ</p>
      <p><strong>Màu sắc:</strong> <?= htmlspecialchars((string) $product['color']) ?></p>

      <div class="button-column">
        <form action="../../checkout/buy_now.php" method="post" id="buyNowForm" style="margin:0;">
          <input type="hidden" name="imei_number" value="<?= htmlspecialchars((string) $product['imei_number']) ?>">
          <input type="hidden" name="quantity" value="1">
          <button type="submit" class="buy-button" <?= $stockQuantity <= 0 ? 'disabled' : '' ?>>MUA NGAY</button>
        </form>

        <form method="post" id="addToCartForm" style="margin:0;">
          <input type="hidden" name="add_to_cart" value="1">
          <input type="hidden" name="product_imei" value="<?= htmlspecialchars((string) $product['imei_number']) ?>">
          <input type="hidden" name="quantity" value="1">
          <button type="submit" class="add-to-cart-button" <?= $stockQuantity <= 0 ? 'disabled' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;" width="28" height="28" fill="white" viewBox="0 0 24 24">
              <path d="M7 18c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm10 0c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm-12.826-3l1.587-6h13.049l-1.334 5.337c-.17.682-.833 1.163-1.53 1.163H8.308l-.134.5H19v2H7v-2l-1.826-1zM7 9h12V7H7l-1-4H1v2h3.272l3.126 8.188L6.174 9z"/>
            </svg>
            THÊM VÀO GIỎ HÀNG
          </button>
        </form>

        <?php if ($stockQuantity <= 0): ?>
          <p class="stock-warning">Sản phẩm hiện đang hết hàng.</p>
        <?php endif; ?>
      </div>

      <div class="specs">
        <h3>Thông số kỹ thuật</h3>
        <table>
          <tbody>
            <?php if (!empty($product['display_size(inchi)'])): ?><tr><td>Kích thước màn hình</td><td><?= htmlspecialchars((string) $product['display_size(inchi)']) ?> inches</td></tr><?php endif; ?>
            <?php if (!empty($product['display_quality'])): ?><tr><td>Công nghệ màn hình</td><td><?= htmlspecialchars((string) $product['display_quality']) ?></td></tr><?php endif; ?>
            <?php if (!empty($product['processor'])): ?><tr><td>Chip</td><td><?= htmlspecialchars((string) $product['processor']) ?></td></tr><?php endif; ?>
            <?php if (!empty($product['ram(GB)'])): ?><tr><td>Dung lượng RAM</td><td><?= htmlspecialchars((string) $product['ram(GB)']) ?> GB</td></tr><?php endif; ?>
            <?php if (!empty($product['rom(GB)'])): ?><tr><td>Bộ nhớ</td><td><?= htmlspecialchars((string) $product['rom(GB)']) ?> GB</td></tr><?php endif; ?>
            <?php if (!empty($product['battery_capacity(mah)'])): ?><tr><td>Pin</td><td><?= htmlspecialchars((string) $product['battery_capacity(mah)']) ?> mAh</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <h2 class="detail-section-title">Mô tả sản phẩm</h2>
  <p class="description-copy"><?= nl2br(htmlspecialchars($description)) ?></p>

  <div class="review-section">
    <h3 class="detail-section-title">Đánh giá và nhận xét</h3>
    <form method="post" id="reviewForm" class="review-form">
      <label for="reviewerName">Tên của bạn:</label>
      <input type="text" id="reviewerName" value="<?= $isLoggedIn ? htmlspecialchars((string) ($_SESSION['accounts_name'] ?? 'Khách hàng')) : '' ?>" <?= $isLoggedIn ? 'readonly' : 'placeholder="Đăng nhập để đánh giá"' ?>>

      <label>Đánh giá (1-5 sao):</label>
      <div class="star-rating" id="starRating">
        <span data-value="1" class="star">★</span>
        <span data-value="2" class="star">★</span>
        <span data-value="3" class="star">★</span>
        <span data-value="4" class="star">★</span>
        <span data-value="5" class="star">★</span>
        <input type="hidden" name="rating" id="ratingValue" required>
      </div>

      <label for="comment">Bình luận (tùy chọn):</label>
      <textarea name="comment" id="comment" rows="4" placeholder="Viết nhận xét của bạn..."></textarea>
      <input type="hidden" name="submit_review" value="1">
      <button type="submit">Gửi đánh giá</button>
    </form>

    <div class="all-reviews">
      <h4 style="margin-top:28px;">Các đánh giá gần đây:</h4>
      <?php if (!$reviews): ?>
        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
      <?php else: ?>
        <?php foreach ($reviews as $review): ?>
          <div class="review-item">
            <div class="review-name"><?= htmlspecialchars((string) ($review['accounts_name'] ?: 'Khách hàng')) ?></div>
            <div style="color:#f39c12; margin:6px 0;"><?= str_repeat('★', (int) $review['rating']) . str_repeat('☆', 5 - (int) $review['rating']) ?></div>
            <?php if (!empty($review['review_text'])): ?>
              <p><?= nl2br(htmlspecialchars((string) $review['review_text'])) ?></p>
            <?php endif; ?>
            <div class="review-time">Gửi lúc: <?= date('d/m/Y H:i', strtotime((string) $review['created_at'])) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <h3 class="detail-section-title">Sản phẩm liên quan</h3>
  <div class="related-row">
    <?php foreach ($relatedProducts as $related): ?>
      <?php $relatedImage = resolve_primary_product_image_pdo($pdo, $related, generic_detail_fallback_image($related)); ?>
      <a href="product-detail.php?model_no=<?= urlencode((string) $related['model_no']) ?>" class="product-link">
        <div class="related-product">
          <img src="<?= htmlspecialchars($relatedImage) ?>" alt="<?= htmlspecialchars((string) $related['model_no']) ?>">
          <h4><?= htmlspecialchars(generic_detail_product_name($related)) ?></h4>
          <div class="related-config">(<?= htmlspecialchars((string) $related['ram(GB)']) ?>GB + <?= htmlspecialchars((string) $related['rom(GB)']) ?>GB)</div>
          <p class="related-price"><?= number_format((float) $related['price'], 0, '.', '.') ?>đ</p>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<script>
const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

function showTimedPopup(id) {
  const popup = document.getElementById(id);
  if (!popup) return;
  popup.style.display = 'block';
  setTimeout(() => {
    popup.style.display = 'none';
    if (window.history.replaceState) {
      const url = new URL(window.location.href);
      url.searchParams.delete('added');
      url.searchParams.delete('reviewed');
      window.history.replaceState({}, document.title, url.pathname + url.search);
    }
  }, 1700);
}

document.addEventListener('DOMContentLoaded', function () {
  const mainImage = document.getElementById('mainProductImage');
  const thumbs = document.querySelectorAll('.product-gallery-thumb');
  thumbs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (!mainImage) return;
      mainImage.src = this.dataset.image;
      thumbs.forEach(item => item.classList.remove('active'));
      this.classList.add('active');
    });
  });

  if (window.location.search.indexOf('added=1') !== -1) {
    showTimedPopup('cartPopup');
  }
  if (window.location.search.indexOf('reviewed=1') !== -1) {
    showTimedPopup('reviewPopup');
  }

  const loginGuardForms = [document.getElementById('buyNowForm'), document.getElementById('addToCartForm'), document.getElementById('reviewForm')];
  loginGuardForms.forEach(function (form) {
    if (!form) return;
    form.addEventListener('submit', function (event) {
      if (!isLoggedIn) {
        event.preventDefault();
        showTimedPopup('loginPopup');
      }
    });
  });

  const stars = document.querySelectorAll('#starRating .star');
  const ratingInput = document.getElementById('ratingValue');
  stars.forEach((star, index) => {
    star.addEventListener('mouseenter', () => {
      stars.forEach((item, i) => item.classList.toggle('hovered', i <= index));
    });
    star.addEventListener('mouseleave', () => {
      stars.forEach(item => item.classList.remove('hovered'));
    });
    star.addEventListener('click', () => {
      const value = star.dataset.value;
      ratingInput.value = value;
      stars.forEach((item, i) => item.classList.toggle('selected', i < value));
    });
  });
});
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>







