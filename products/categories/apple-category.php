<?php
session_start();
include __DIR__ . '/../../config/db.php';

$categories = [
    'ipad' => [
        'title' => 'iPad',
        'banner' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?auto=format&fit=crop&w=1600&q=80',
        'keywords' => ['iPad'],
    ],
    'mac' => [
        'title' => 'Mac',
        'banner' => 'https://images.unsplash.com/photo-1517336714739-489689fd1ca8?auto=format&fit=crop&w=1600&q=80',
        'keywords' => ['MacBook', 'Mac'],
    ],
    'watch' => [
        'title' => 'Watch',
        'banner' => 'https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?auto=format&fit=crop&w=1600&q=80',
        'keywords' => ['Watch', 'Apple Watch'],
    ],
];

$categoryKey = strtolower($_GET['category'] ?? 'ipad');
$category = $categories[$categoryKey] ?? $categories['ipad'];
$ipadSlides = [
    '../../images/ipad-banner-1.png',
    '../../images/ipad-banner-2.png',
    '../../images/ipad-banner-3.png',
    '../../images/ipad-banner-4.png',
];

$conditions = [];
$params = [];
foreach ($category['keywords'] as $keyword) {
    $conditions[] = "company_series LIKE ?";
    $params[] = '%' . $keyword . '%';
}

$sql = "SELECT * FROM mobile WHERE company_name = 'Apple'";
if ($conditions) {
    $sql .= ' AND (' . implode(' OR ', $conditions) . ')';
}
$sql .= ' ORDER BY price ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

function getCategoryProductImage($product) {
    $folder = '../../images/';
    $imgBase = strtolower(str_replace([' ', '+', '-'], '', $product['company_series'])) . '_' .
        strtolower(str_replace([' ', '+', '-'], '', $product['model_no']));
    $png = $folder . $imgBase . '.png';
    $jpg = $folder . $imgBase . '.jpg';

    if (file_exists(__DIR__ . '/' . $png)) {
        return $png;
    }

    if (file_exists(__DIR__ . '/' . $jpg)) {
        return $jpg;
    }

    return $folder . 'no-image.png';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($category['title']) ?> | ShopDunk</title>
  <style>
    body {
      margin: 0;
      font-family: system-ui, sans-serif;
      background: #f6f6f8;
      color: #111;
    }

    .page-shell {
      padding: 0 20px 48px;
    }

    .hero {
      max-width: 1200px;
      min-height: 280px;
      margin: 24px auto 32px;
      border-radius: 24px;
      overflow: hidden;
      position: relative;
      background: #111;
    }

    .hero img {
      width: 100%;
      height: 280px;
      object-fit: cover;
      opacity: 0.45;
      display: block;
    }

    .hero-copy {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 32px 40px;
      color: #fff;
    }

    .hero-copy p {
      max-width: 520px;
      margin: 0 0 10px;
      font-size: 15px;
      color: rgba(255, 255, 255, 0.78);
    }

    .hero-copy h1 {
      margin: 0;
      font-size: 46px;
      line-height: 1.05;
    }

    .ipad-slider-shell {
      max-width: 1500px;
      margin: 24px auto 32px;
      position: relative;
    }

    .ipad-slider {
      background: #fff;
      border-radius: 28px;
      box-shadow: 0 18px 48px rgba(15, 23, 42, 0.08);
      overflow: hidden;
      position: relative;
    }

    .ipad-track {
      display: flex;
      transition: transform 0.65s ease;
    }

    .ipad-slide {
      min-width: 100%;
      padding: 18px;
      box-sizing: border-box;
      background: #f4f6fb;
    }

    .ipad-slide img {
      width: 100%;
      height: auto;
      display: block;
      border-radius: 22px;
    }

    .ipad-arrow {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 58px;
      height: 58px;
      border: none;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.88);
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.14);
      font-size: 34px;
      color: #9aa3b2;
      cursor: pointer;
      z-index: 2;
      transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .ipad-arrow:hover {
      background: #ffffff;
      color: #1f2937;
      transform: translateY(-50%) scale(1.04);
    }

    .ipad-arrow.prev {
      left: 18px;
    }

    .ipad-arrow.next {
      right: 18px;
    }

    .ipad-dots {
      display: flex;
      justify-content: center;
      gap: 10px;
      padding: 18px 0 4px;
    }

    .ipad-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      border: none;
      background: #c7ced9;
      cursor: pointer;
      transition: transform 0.2s ease, background 0.2s ease;
    }

    .ipad-dot.active {
      background: #3b3f47;
      transform: scale(1.18);
    }

    .product-grid {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 24px;
    }

    .product-card {
      background: #000;
      color: #fff;
      border-radius: 20px;
      overflow: hidden;
      text-decoration: none;
      box-shadow: 0 16px 30px rgba(0, 0, 0, 0.12);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 34px rgba(0, 0, 0, 0.16);
    }

    .product-card img {
      width: 100%;
      height: 240px;
      object-fit: contain;
      background: linear-gradient(180deg, #111 0%, #1e1e1e 100%);
      display: block;
    }

    .product-body {
      padding: 18px 18px 22px;
    }

    .product-body h3 {
      margin: 0 0 10px;
      font-size: 18px;
    }

    .price {
      font-weight: 700;
      color: #f4b03e;
      margin: 0;
    }

    .old-price {
      color: #999;
      text-decoration: line-through;
      margin-left: 8px;
      font-size: 13px;
    }

    .empty-state {
      max-width: 1200px;
      margin: 0 auto;
      background: #fff;
      border-radius: 20px;
      padding: 32px;
      text-align: center;
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
    }

    .empty-state h2 {
      margin: 0 0 8px;
      font-size: 28px;
    }

    .empty-state p {
      margin: 0;
      color: #666;
    }

    @media (max-width: 768px) {
      .ipad-slider-shell {
        margin: 18px auto 28px;
      }

      .ipad-slide {
        padding: 10px;
      }

      .ipad-arrow {
        width: 44px;
        height: 44px;
        font-size: 26px;
      }

      .ipad-arrow.prev {
        left: 10px;
      }

      .ipad-arrow.next {
        right: 10px;
      }
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/../../includes/header.php'; ?>

  <div class="page-shell">
    <section class="hero">
      <img src="<?= htmlspecialchars($category['banner']) ?>" alt="<?= htmlspecialchars($category['title']) ?>">
      <div class="hero-copy">
        <p>Danh mục Apple trên ShopDunk</p>
        <h1><?= htmlspecialchars($category['title']) ?></h1>
      </div>
    </section>

    <?php if ($products): ?>
      <section class="product-grid">
        <?php foreach ($products as $product): ?>
          <?php
            $price = number_format($product['price'], 0, ',', '.') . 'đ';
            $oldPrice = (!empty($product['old_price']) && $product['old_price'] > $product['price'])
                ? number_format($product['old_price'], 0, ',', '.') . 'đ'
                : '';
          ?>
          <a class="product-card" href="../detail/product-detail.php?model_no=<?= urlencode($product['model_no']) ?>">
            <img src="<?= htmlspecialchars(getCategoryProductImage($product)) ?>" alt="<?= htmlspecialchars($product['company_series'] . ' ' . $product['model_no']) ?>">
            <div class="product-body">
              <h3><?= htmlspecialchars($product['company_series'] . ' ' . $product['model_no']) ?></h3>
              <p class="price">
                <?= htmlspecialchars($price) ?>
                <?php if ($oldPrice): ?>
                  <span class="old-price"><?= htmlspecialchars($oldPrice) ?></span>
                <?php endif; ?>
              </p>
            </div>
          </a>
        <?php endforeach; ?>
      </section>
    <?php else: ?>
      <section class="empty-state">
        <h2>Chưa có sản phẩm <?= htmlspecialchars($category['title']) ?></h2>
        <p>Phần này đã có trên giao diện. Khi bạn thêm dữ liệu sản phẩm tương ứng trong bảng mobile, danh sách sẽ hiện ở đây.</p>
      </section>
    <?php endif; ?>
  </div>

  <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>








