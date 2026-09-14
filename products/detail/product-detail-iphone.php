<?php
// Khởi động session để lưu thông tin đăng nhập và giỏ hàng
session_start();

include __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../product_images_helper.php';

ensure_product_images_table_pdo($pdo);
// XỬ LÝ THÊM GIỎ HÀNG
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
   // Kiểm tra đăng nhập. Nếu chưa, chuyển đến trang đăng nhập
    if (!isset($_SESSION['account_id'])) {
        header("Location: ../../auth/login.php"); // Chưa đăng nhập chuyển tới trang đăng nhập
        exit;
    }
    $productImei = $_POST['product_imei'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 1);
    $customerId = $_SESSION['account_id'];
 // Nếu có mã sản phẩm, kiểm tra đã có trong giỏ chưa
    if ($productImei) {
        $stmtCheck = $pdo->prepare("SELECT * FROM cart WHERE product_imei = ? AND account_id = ?");
        $stmtCheck->execute([$productImei, $customerId]);
        $existCart = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existCart) {
            // Nếu đã có, tăng số lượng
            $stmtUpdate = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE product_imei = ? AND account_id = ?");
            $stmtUpdate->execute([$quantity, $productImei, $customerId]);
        } else {
           // Nếu chưa có, thêm mới vào giỏ
            $stmtInsert = $pdo->prepare("INSERT INTO cart (product_imei, quantity, account_id) VALUES (?, ?, ?)");
            $stmtInsert->execute([$productImei, $quantity, $customerId]);
        }
          // Sau khi thêm giỏ hàng xong, reload lại trang (có thêm tham số added=1 để hiện popup)
        header("Location: product-detail-iphone.php?model_no=" . urlencode($_GET['model_no']) . "&added=1");
        exit;
    }
}

// XỬ LÝ GỬI ĐÁNH GIÁ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['rating'])) {
    $name = trim($_POST['name']);
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment'] ?? '');
    $modelNo = $_GET['model_no'] ?? '';
 // Kiểm tra hợp lệ và đủ thông tin
    if ($name && $rating >= 1 && $rating <= 5 && $modelNo) {
        // Lấy company_series từ sản phẩm để ghi vào reviews
        $stmtProduct = $pdo->prepare("SELECT company_series FROM mobile WHERE model_no = ?");
        $stmtProduct->execute([$modelNo]);
        $productInfo = $stmtProduct->fetch(PDO::FETCH_ASSOC);

        if ($productInfo) {
           // Lưu đánh giá vào DB
            $reviewStmt = $pdo->prepare("INSERT INTO reviews (company_series, model_no, name, rating, comment) VALUES (?, ?, ?, ?, ?)");
            $reviewStmt->execute([
                $productInfo['company_series'],
                $modelNo,
                $name,
                $rating,
                $comment !== '' ? $comment : null
            ]);
        }
 // Reload lại trang để hiển thị review vừa gửi
        header("Location: product-detail-iphone.php?model_no=" . urlencode($modelNo));
        exit;
    }
}
// HIỂN THỊ THÔNG TIN SẢN PHẨM 
include __DIR__ . '/../../includes/header.php';

$modelNo = $_GET['model_no'] ?? '';
if (!$modelNo) {
    die("<h2>Thiếu mã sản phẩm</h2>");
}

// Lấy thông tin sản phẩm theo model_no
$stmt = $pdo->prepare("SELECT * FROM mobile WHERE model_no = ?");
$stmt->execute([$modelNo]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    die("<h2>Sản phẩm không tồn tại</h2>");
}

// Hàm tạo tên file ảnh theo model_no 
function toImageFileName($model_no) {
  $str = strtolower((string) $model_no);
  if (str_starts_with($str, 'iphone')) {
    $str = substr($str, 6);
  }
  $str = preg_replace('/(\d+)g\b/', '$1gb', $str);
  $str = preg_replace('/[^a-z0-9]/', '', $str);
  return 'iphone_' . $str . '.png';
}
// Hàm mô tả chi tiết theo model_no (bạn thêm sửa nội dung theo ý)
$galleryImages = fetch_product_images_pdo($pdo, $product['imei_number']);
$galleryImages = array_values(array_filter($galleryImages, function ($imagePath) {
  return is_file(product_image_file($imagePath));
}));
$galleryImages = array_map('product_image_url', $galleryImages);
if (!$galleryImages) {
  $primaryImage = '../../images/' . toImageFileName($product['model_no']);
  $galleryImages[] = is_file(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $primaryImage))
    ? $primaryImage
    : '';
}

function getProductDescription(array $product): string {
    $segments = [];

    if (!empty($product['model_no'])) {
        $segments[] = $product['company_series'] . ' ' . $product['model_no'] . ' mang đến trải nghiệm ổn định cho nhu cầu học tập, làm việc và giải trí hằng ngày.';
    }
    if (!empty($product['processor'])) {
        $segments[] = 'Máy sử dụng chip ' . $product['processor'] . ' cho khả năng xử lý mượt mà.';
    }
    if (!empty($product['display_size(inchi)']) || !empty($product['display_quality'])) {
        $displayParts = [];
        if (!empty($product['display_size(inchi)'])) {
            $displayParts[] = $product['display_size(inchi)'] . ' inch';
        }
        if (!empty($product['display_quality'])) {
            $displayParts[] = $product['display_quality'];
        }
        $segments[] = 'Màn hình ' . implode(', ', $displayParts) . ' hiển thị hình ảnh sắc nét và rõ ràng.';
    }
    if (!empty($product['ram(GB)']) || !empty($product['rom(GB)'])) {
        $memoryParts = [];
        if (!empty($product['ram(GB)'])) {
            $memoryParts[] = 'RAM ' . $product['ram(GB)'] . 'GB';
        }
        if (!empty($product['rom(GB)'])) {
            $memoryParts[] = 'bộ nhớ ' . $product['rom(GB)'] . 'GB';
        }
        $segments[] = 'Cấu hình với ' . implode(', ', $memoryParts) . ' đáp ứng tốt nhiều nhu cầu sử dụng.';
    }
    if (!empty($product['battery_capacity(mah)'])) {
        $segments[] = 'Pin ' . $product['battery_capacity(mah)'] . 'mAh giúp kéo dài thời gian sử dụng trong ngày.';
    }

    if (!$segments) {
        return 'Mô tả sản phẩm đang được cập nhật.';
    }

    return implode(' ', $segments);
}

$description = getProductDescription($product);



// Lấy sản phẩm liên quan (cùng hãng, cùng series, khác cấu hình RAM và ROM)
$relatedStmt = $pdo->prepare("
    SELECT * FROM mobile
    WHERE company_name = ?
      AND company_series = ?
      AND NOT (model_no = ? AND `ram(GB)` = ? AND `rom(GB)` = ?)
    LIMIT 10
");
$relatedStmt->execute([
    $product['company_name'],
    $product['company_series'],
    $product['model_no'],
    $product['ram(GB)'],
    $product['rom(GB)']
]);
$relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);



// Lấy danh sách review sản phẩm
$reviewsStmt = $pdo->prepare("SELECT * FROM reviews WHERE model_no = ? ORDER BY created_at DESC");
$reviewsStmt->execute([$modelNo]);
$reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['model_no']) ?> - Chi tiết sản phẩm</title>
  <link rel="stylesheet" href="../../assets/css/style_iphone.css">
  <style>
    .star-rating {
      font-size: 2rem;
      color: #ccc;
      cursor: pointer;
      user-select: none;
    }
    .star-rating .star {
      transition: color 0.2s;
      margin-inline-end: 3px;
    }
    .star-rating .star.selected,
    .star-rating .star.hovered {
      color: #fbc02d;
    }
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0; padding: 0;
    }
    .product-detail-container {
      max-inline-size: 1100px;
      margin: 50px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .product-top-layout {
      display: flex;
      gap: 40px;
      margin-block-end: 40px;
    }
    .left-column {
      flex: 1.2;
    }
    .right-column {
      flex: 1;
    }
    .left-column img {
      inline-size: 100%;
      max-inline-size: 600px;
      block-size: auto;
      border-radius: 10px;
    }
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
      color: red;
      font-size: 24px;
      font-weight: bold;
      margin-block-end: 20px;
    }
    .buy-button {
      background-color: #ff0000;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      margin: 20px 0;
      font-size: 16px;
      cursor: pointer;
      display: flex;        
      justify-content: center; 
      align-items: center;   
      inline-size: 100%;         
      font-weight: bold;
      min-block-size: 46px;     
      letter-spacing: 1px;
      box-sizing: border-box;
    }
    .buy-button:hover {
      background-color:rgb(0, 204, 85);
    }
    .addcart-button {
      background-color:rgb(82, 45, 232);
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      margin: 20px 0;
      font-size: 16px;
      cursor: pointer;
      display: flex;        
      justify-content: center; 
      align-items: center;   
      inline-size: 100%;         
      font-weight: bold;
      min-block-size: 46px;     
      letter-spacing: 1px;
      box-sizing: border-box;
    }
    .addcart-button:hover {
      background-color:rgb(0, 189, 69);
    }
    .specs {
      border: 1px solid #eee;
      border-radius: 8px;
      padding: 20px;
      background: #fafafa;
    }
    .specs h3 {
      margin-block-start: 0;
    }
    .specs table {
      inline-size: 100%;
      border-collapse: collapse;
    }
    .specs td {
      padding: 8px;
      border-block-end: 1px solid #ddd;
    }
    .specs td:first-child {
      font-weight: bold;
      inline-size: 40%;
    }
    .review-section {
      margin-block-start: 40px;
    }
    .review { border-block-end: 1px solid #ddd; padding: 10px 0; }
    .review strong { display: block; margin-block-end: 5px; color: #333; }
    .review em { font-size: 0.85rem; color: #888; }
    textarea, input[type=text] {
      inline-size: 100%;
      padding: 10px;
      margin-block-end: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      background-color: #ff6700;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background-color: #e65a00;
    }
    a.product-link {
      text-decoration: none;
      color: inherit;
    }
    .related-product {
      background:#000;
      border:1px solid #ccc;
      border-radius:10px;
      padding:10px;
      box-shadow:0 2px 6px rgba(0,0,0,0.3);
      text-align:center;
      color: white;
      inline-size: 240px;
      flex-shrink: 0;
    }
    .related-product img {
      inline-size: 100%;
      block-size: auto;
      border-radius: 8px;
    }
    .related-product h4 {
      margin: 10px 0 5px;
      font-size: 16px;
      color: white;
    }
  </style>
    <!--  TRẠNG THÁI ĐĂNG NHẬP (truyền vào JS) -->
  <script>
  var isLoggedIn = <?= isset($_SESSION['account_id']) ? 'true' : 'false' ?>;
</script>
</head>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('#starRating .star');
    const ratingValue = document.getElementById('ratingValue');
    let selected = 0;

    stars.forEach((star, idx) => {
      star.addEventListener('mouseenter', function () {
        updateStars(idx + 1, true);
      });
      star.addEventListener('mouseleave', function () {
        updateStars(selected, false);
      });
      star.addEventListener('click', function () {
        selected = idx + 1;
        ratingValue.value = selected; // Gán value cho input hidden
        updateStars(selected, false);
      });
    });

    function updateStars(rating, hover) {
      stars.forEach((star, i) => {
        star.classList.remove('selected', 'hovered');
        if (hover && i < rating) {
          star.classList.add('hovered');
        } else if (!hover && i < rating) {
          star.classList.add('selected');
        }
      });
    }
  });
</script>
<body>
<div class="product-detail-container">

  <div class="product-top-layout">
    <div class="left-column">
      <div class="product-gallery-main">
        <?php if ($galleryImages[0]): ?>
          <img id="mainProductImage" src="<?= htmlspecialchars($galleryImages[0]) ?>" alt="<?= htmlspecialchars($product['model_no']) ?>">
        <?php else: ?>
          <div style="height:100%;display:flex;align-items:center;justify-content:center;color:#64748b;">Chưa có ảnh</div>
        <?php endif; ?>
      </div>
      <div class="product-gallery-thumbs">
        <?php foreach (array_filter($galleryImages) as $index => $imagePath): ?>
          <button type="button" class="product-gallery-thumb <?= $index === 0 ? 'active' : '' ?>" data-image="<?= htmlspecialchars($imagePath) ?>">
            <img src="<?= htmlspecialchars($imagePath) ?>" alt="Ảnh sản phẩm">
          </button>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="right-column">
      <h2><?= htmlspecialchars($product['company_name'] . ' ' . $product['company_series'] . ' ' . $product['model_no']) ?></h2>
      <p class="price"><?= number_format($product['price'], 0, '.', '.') ?>đ</p>
      <p><strong>Màu sắc:</strong> <?= htmlspecialchars($product['color']) ?></p>
      <!-- FORM MUA NGAY (submit đến ../../customer/address.php, yêu cầu đăng nhập) -->
      <form action="../../checkout/buy_now.php" method="post" style="margin-block-end: 12px;" id="buyNowForm">
        <input type="hidden" name="imei_number" value="<?= htmlspecialchars($product['imei_number']) ?>">
        <input type="hidden" name="quantity" value="1">
        <button type="submit" class="buy-button">MUA NGAY</button>
      </form>
      
      <div id="loginPopup" style="display:none; position:fixed; inset-block-start:20px; inset-inline-end:32px; background:#004aad; color:#fff; padding:22px 36px; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.19); z-index:99999; font-size:18px; font-weight:600;">
          Vui lòng đăng nhập.
          <button onclick="window.location.href='../../auth/login.php'" style="margin-inline-start:16px;background:#fff;color:#004aad;padding:7px 18px;border:none;border-radius:8px;font-weight:600;cursor:pointer;">Đăng nhập</button>
      </div>
     
      <div id="cartPopup" style="display:none; position:fixed; inset-block-start:30px; inset-inline-start:50%; transform:translateX(-50%); background:#0b8d3b; color:#fff; padding:18px 32px; border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,0.15); font-size:18px; z-index:9999; font-weight:600;">
        Đã thêm vào giỏ hàng!
      </div>
      
      <script>
        function showCartPopup() {
          var popup = document.getElementById("cartPopup");
          if (!popup) return;
          popup.style.display = "block";
          setTimeout(function(){
            popup.style.display = "none";
            // Xóa ?added=1 khỏi URL (giúp reload không bị hiện lại popup)
            if (window.history.replaceState) {
              const url = new URL(window.location);
              url.searchParams.delete('added');
              window.history.replaceState({}, document.title, url.pathname + url.search);
            }
          }, 1700);
        }
        window.addEventListener("DOMContentLoaded", function() {
          if (window.location.search.indexOf("added=1") !== -1) {
            showCartPopup();
          }
        });
      </script>
        <!-- FORM THÊM VÀO GIỎ HÀNG (submit POST về chính trang, yêu cầu đăng nhập) -->
     <form method="post" id="addToCartForm" style="margin-block-start: 12px;">
    <input type="hidden" name="add_to_cart" value="1">
    <input type="hidden" name="product_imei" value="<?= htmlspecialchars($product['imei_number']) ?>">
    <input type="hidden" name="quantity" value="1">
      <button type="submit" class="addcart-button">Thêm vào giỏ hàng</button>
</form>
      <div class="specs">
        <h3>Thông số kỹ thuật</h3>
        <table>
          <tbody>
            <?php if (!empty($product['display_size(inchi)'])): ?>
              <tr><td>Kích thước màn hình</td><td><?= htmlspecialchars($product['display_size(inchi)']) ?> inches</td></tr>
            <?php endif; ?>
            <?php if (!empty($product['display_quality'])): ?>
              <tr><td>Công nghệ màn hình</td><td><?= htmlspecialchars($product['display_quality']) ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($product['processor'])): ?>
              <tr><td>Chip</td><td><?= htmlspecialchars($product['processor']) ?></td></tr>
            <?php endif; ?>
            <?php if (!empty($product['ram(GB)'])): ?>
              <tr><td>Dung lượng RAM</td><td><?= htmlspecialchars($product['ram(GB)']) ?> GB</td></tr>
            <?php endif; ?>
            <?php if (!empty($product['rom(GB)'])): ?>
              <tr><td>Bộ nhớ</td><td><?= htmlspecialchars($product['rom(GB)']) ?> GB</td></tr>
            <?php endif; ?>
            <?php if (!empty($product['battery_capacity(mah)'])): ?>
              <tr><td>Pin</td><td><?= htmlspecialchars($product['battery_capacity(mah)']) ?> mAh</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <h2 style=" margin-block-end: 20px;">Mô tả sản phẩm</h2>
  <p><?= nl2br(htmlspecialchars($description)) ?></p>
 
  <div class="review-section">
    <h3 style="margin-block-start:40px; margin-block-end: 40px;">Đánh giá và nhận xét</h3>
  <form method="post" id="reviewForm">
      <label for="name">Tên của bạn:</label>
      <input type="text" name="name" id="name" required>
      <label>Đánh giá (1-5 sao):</label>
      <div class="star-rating" id="starRating">
        <span data-value="1" class="star">&#9733;</span>
        <span data-value="2" class="star">&#9733;</span>
        <span data-value="3" class="star">&#9733;</span>
        <span data-value="4" class="star">&#9733;</span>
        <span data-value="5" class="star">&#9733;</span>
        <input type="hidden" name="rating" id="ratingValue" required>
      </div>
      <label for="comment">Bình luận (tùy chọn):</label>
      <textarea name="comment" id="comment" rows="4" placeholder="Viết nhận xét của bạn..."></textarea>
      <button type="submit">Gửi đánh giá</button>
    </form>
   
    <div class="all-reviews">
      <h4 style="margin-block-start:40px;">Các đánh giá gần đây:</h4>
      <?php if (count($reviews) === 0): ?>
        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
      <?php else: ?>
        <?php foreach ($reviews as $r): ?>
          <div class="review">
            <strong><?= htmlspecialchars($r['name']) ?></strong>
            <div style="color: #f39c12;">
              <?= str_repeat('★', (int)$r['rating']) ?>
              <?= str_repeat('☆', 5 - (int)$r['rating']) ?>
            </div>
            <?php if (!empty($r['comment'])): ?>
              <p><?= nl2br(htmlspecialchars($r['comment'])) ?></p>
            <?php endif; ?>
            <em>Gửi lúc: <?= date("d/m/Y H:i", strtotime($r['created_at'])) ?></em>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
  <h3 style="margin-block-start:40px;">Sản phẩm liên quan</h3>
  <div style="display:flex; gap:20px; overflow-x:auto; padding-block-end:10px;">
    <?php foreach ($relatedProducts as $rp): 
      $rpModelNo = urlencode($rp['model_no']);
        $rpFallback = '../../images/' . toImageFileName($rp['model_no']);
          if (!is_file(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rpFallback))) {
            $rpFallback = '';
          }
        $rpImage = resolve_primary_product_image_pdo($pdo, $rp, $rpFallback);
      $config = $rp['ram(GB)'] . 'GB + ' . $rp['rom(GB)'] . 'GB';
    ?>
    <a href="product-detail-iphone.php?model_no=<?= $rpModelNo ?>" class="product-link">
      <div class="related-product">
        <?php if ($rpImage): ?>
          <img src="<?= htmlspecialchars($rpImage) ?>" alt="<?= htmlspecialchars($rp['model_no']) ?>">
        <?php else: ?>
          <div style="height:160px;display:flex;align-items:center;justify-content:center;color:#64748b;">Chưa có ảnh</div>
        <?php endif; ?>
        <h4><?= htmlspecialchars($rp['company_series'] . ' ' . $rp['model_no']) ?><br>
            <span style="font-size:13px; color:#ccc;">(<?= htmlspecialchars($config) ?>)</span>
        </h4>
        <p style="color:red; font-weight:bold; margin:0;">
          <?= number_format($rp['price'], 0, '.', '.') ?>đ
        </p>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  var mainImage = document.getElementById('mainProductImage');
  var thumbs = document.querySelectorAll('.product-gallery-thumb');
  thumbs.forEach(function(btn) {
    btn.addEventListener('click', function() {
      if (!mainImage) return;
      mainImage.src = this.dataset.image;
      thumbs.forEach(function(item) { item.classList.remove('active'); });
      this.classList.add('active');
    });
  });

  var buyNowForm = document.getElementById('buyNowForm');
  if (buyNowForm) {
    buyNowForm.addEventListener('submit', function(e) {
      if (typeof isLoggedIn !== "undefined" && !isLoggedIn) {
        e.preventDefault();
        var loginPopup = document.getElementById("loginPopup");
        if (loginPopup) {
          loginPopup.style.display = "block";
          setTimeout(() => { loginPopup.style.display = "none"; }, 4000);
        }
      }
    });
  }

  var reviewForm = document.getElementById('reviewForm');
  if (reviewForm) {
    reviewForm.addEventListener('submit', function(e) {
      if (typeof isLoggedIn !== "undefined" && !isLoggedIn) {
        e.preventDefault();
        var loginPopup = document.getElementById("loginPopup");
        if (loginPopup) {
          loginPopup.style.display = "block";
          setTimeout(() => { loginPopup.style.display = "none"; }, 4000);
        }
      }
    });
  }
  var addToCartForm = document.getElementById('addToCartForm');
  if (addToCartForm) {
    addToCartForm.addEventListener('submit', function(e) {
      if (typeof isLoggedIn !== "undefined" && !isLoggedIn) {
        e.preventDefault();
        var loginPopup = document.getElementById("loginPopup");
        if (loginPopup) {
          loginPopup.style.display = "block";
          setTimeout(() => { loginPopup.style.display = "none"; }, 4000);
        }
      }
    });
  }
});
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>







