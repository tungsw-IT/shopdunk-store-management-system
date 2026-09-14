<?php
session_start();
include __DIR__ . '/config/db.php';
require_once __DIR__ . '/products/product_images_helper.php';
?>

<?php
function getFeaturedProducts($pdo, $brand, $limit = 4) {
    $stmt = $pdo->prepare("SELECT * FROM mobile WHERE company_name = ? LIMIT ?");
    $stmt->bindParam(1, $brand);
    $stmt->bindParam(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
function getFeaturedProductsBySeries($pdo, array $keywords, $limit = 4) {
    if (!$keywords) {
        return [];
    }

    $conditions = [];
    foreach ($keywords as $keyword) {
        $conditions[] = "company_series LIKE ?";
    }

    $sql = "SELECT * FROM mobile WHERE company_name = 'Apple' AND (" . implode(' OR ', $conditions) . ") LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $index = 1;
    foreach ($keywords as $keyword) {
        $stmt->bindValue($index++, '%' . $keyword . '%', PDO::PARAM_STR);
    }
    $stmt->bindValue($index, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
function displayProducts($products) {
    foreach ($products as $product) {
        // Lấy ảnh đúng quy tắc với hàm getProductImage
        $img = getProductImage($product);

        echo '<div class="product-card">';
        if ($img !== '') {
          echo '<img src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($product['model_no']) . '">';
        } else {
          echo '<div style="height:220px;display:flex;align-items:center;justify-content:center;color:#64748b;">Chưa có ảnh</div>';
        }
        echo '<h3>' . htmlspecialchars($product['company_series'] . ' ' . $product['model_no']) . '</h3>';
        echo '<p class="price">' . number_format($product['price'], 0, ",", ".") . 'đ</p>';
        if (!empty($product['old_price']) && $product['old_price'] > $product['price']) {
            $discount = round(100 - ($product['price'] / $product['old_price']) * 100);
            echo '<p class="old-price">' . number_format($product['old_price'], 0, ",", ".") . 'đ</p>';
            echo '<span class="discount">-' . $discount . '%</span>';
        }
        echo '</div>';
    }
}

function getFlashSaleProducts($pdo, $limit = 9) {
    $stmt = $pdo->query("SELECT * FROM mobile WHERE old_price > price AND price > 0 ORDER BY (old_price - price) DESC LIMIT $limit");
    return $stmt->fetchAll();
}
function getProductImage($product) {
    $folder = 'images/';
    if (strtolower($product['company_name']) == 'samsung') $folder = 'images/';

  $series = strtolower((string) $product['company_series']);
  $model = strtolower((string) $product['model_no']);
  if ($series === 'iphone' && str_starts_with($model, 'iphone')) {
    $model = substr($model, 6);
  }
  $model = preg_replace('/(\d+)g\b/', '$1gb', $model);
  $img_base = preg_replace('/[^a-z0-9]/', '', $series) . '_' .
        preg_replace('/[^a-z0-9]/', '', $model);
    $img_path_png = $folder . $img_base . '.png';
    $img_path_jpg = $folder . $img_base . '.jpg';
  if (file_exists(__DIR__ . '/' . $img_path_png)) {
    $fallback = $img_path_png;
  } elseif (file_exists(__DIR__ . '/' . $img_path_jpg)) {
    $fallback = $img_path_jpg;
  } else {
    $fallback = '';
  }
    $img_show = resolve_primary_product_image_pdo($GLOBALS['pdo'], $product, $fallback);
    return $img_show;
}
function getProductDetailLink($product) {
    $brand = strtolower($product['company_name']);
    $model = urlencode($product['model_no']);
    $series = urlencode($product['company_series']);

    switch ($brand) {
        case 'samsung':
            return "products/detail/product-detail.php?model_no=$model&series=$series";
        case 'oppo':
            return "products/detail/product-detail-oppo.php?model_no=$model";
        case 'xiaomi':
            return "products/detail/product-detail.php?model_no=$model";
        case 'apple':
        case 'iphone':
            return "products/detail/product-detail-iphone.php?model_no=$model";
        default:
            return "products/detail/product-detail.php?model_no=$model";
    }
}

function getHomepageFallbackProducts(): array {
    return [
        'iphone' => [
            [
                'title' => 'iPhone 16 Pro Max 256GB',
                'image' => 'images/iphone_16promax256GB.png',
                'price' => 36790000,
                'old_price' => 37990000,
                'badge_left' => 'Giảm 3%',
                'badge_right' => 'Mới',
                'link' => 'products/categories/Alliphone.php',
            ],
            [
                'title' => 'iPhone 16 Pro 128GB',
                'image' => 'images/iphone_16pro128GB.png',
                'price' => 33690000,
                'old_price' => 34990000,
                'badge_left' => 'Giảm 3%',
                'badge_right' => 'Mới',
                'link' => 'products/categories/Alliphone.php',
            ],
            [
                'title' => 'iPhone 16 256GB',
                'image' => 'images/iphone_16256GB.png',
                'price' => 17590000,
                'old_price' => 17990000,
                'badge_left' => 'Giảm 2%',
                'badge_right' => 'Mới',
                'link' => 'products/categories/Alliphone.php',
            ],
            [
                'title' => 'iPhone 16 512GB',
                'image' => 'images/iphone_16512GB.png',
                'price' => 23690000,
                'old_price' => 24990000,
                'badge_left' => 'Giảm 5%',
                'badge_right' => 'Mới',
                'link' => 'products/categories/Alliphone.php',
            ],
        ],
        'ipad' => [
            [
                'title' => 'iPad Pro M5 11 inch Wi‑Fi 256GB',
                'image' => 'images/home-ipad.png',
                'price' => 28990000,
                'old_price' => 29990000,
                'badge_left' => 'Giảm 3%',
                'badge_right' => 'Trả góp 0%',
                'link' => 'products/categories/ipad.php',
            ],
            [
                'title' => 'iPad (A16) 11 inch Wi‑Fi',
                'image' => 'images/ipad-banner-2.png',
                'price' => 9390000,
                'old_price' => 9990000,
                'badge_left' => 'Giảm 6%',
                'badge_right' => 'Trả góp 0%',
                'link' => 'products/categories/ipad.php',
            ],
            [
                'title' => 'iPad Air (M3) 11 inch Wi‑Fi',
                'image' => 'images/ipad-banner-1.png',
                'price' => 14990000,
                'old_price' => 16990000,
                'badge_left' => 'Giảm 11%',
                'badge_right' => 'Trả góp 0%',
                'link' => 'products/categories/ipad.php',
            ],
            [
                'title' => 'iPad mini (A17 Pro) Wi‑Fi 128GB',
                'image' => 'images/ipad-banner-4.png',
                'price' => 13390000,
                'old_price' => 13990000,
                'badge_left' => 'Giảm 4%',
                'badge_right' => 'Trả góp 0%',
                'link' => 'products/categories/ipad.php',
            ],
        ],
        'mac' => [
            [
                'title' => 'MacBook Neo 13-inch A18 Pro',
                'image' => 'images/mac-banner-1.png',
                'price' => 16499000,
                'old_price' => 0,
                'badge_left' => '',
                'badge_right' => 'Mới',
                'link' => 'products/categories/mac.php',
            ],
            [
                'title' => 'MacBook Air M5 13-inch',
                'image' => 'images/mac-banner-2.png',
                'price' => 29999000,
                'old_price' => 0,
                'badge_left' => '',
                'badge_right' => 'Mới',
                'link' => 'products/categories/mac.php',
            ],
            [
                'title' => 'MacBook Pro M5 Pro 14-inch',
                'image' => 'images/mac-banner-3.png',
                'price' => 70990000,
                'old_price' => 0,
                'badge_left' => '',
                'badge_right' => 'Mới',
                'link' => 'products/categories/mac.php',
            ],
            [
                'title' => 'iMac M4 24-inch',
                'image' => 'images/mac-banner-5.png',
                'price' => 97990000,
                'old_price' => 0,
                'badge_left' => '',
                'badge_right' => 'Mới',
                'link' => 'products/categories/mac.php',
            ],
        ],
        'watch' => [
            [
                'title' => 'Apple Watch Series 11 42mm',
                'image' => 'images/watch-banner-1.png',
                'price' => 10990000,
                'old_price' => 11490000,
                'badge_left' => 'Giảm 4%',
                'badge_right' => 'Mới',
                'link' => 'products/categories/watch.php',
            ],
            [
                'title' => 'Apple Watch Ultra 3 GPS + Cellular',
                'image' => 'images/watch-banner-2.png',
                'price' => 22990000,
                'old_price' => 23990000,
                'badge_left' => 'Giảm 4%',
                'badge_right' => 'Mới',
                'link' => 'products/categories/watch.php',
            ],
            [
                'title' => 'Apple Watch SE 3 40mm',
                'image' => 'images/home-watch.png',
                'price' => 6490000,
                'old_price' => 6990000,
                'badge_left' => 'Giảm 7%',
                'badge_right' => 'Mới',
                'link' => 'products/categories/watch.php',
            ],
            [
                'title' => 'Apple Watch Series 10 42mm',
                'image' => 'images/watch-banner-3.png',
                'price' => 10090000,
                'old_price' => 10990000,
                'badge_left' => 'Giảm 8%',
                'badge_right' => 'Trả góp 0%',
                'link' => 'products/categories/watch.php',
            ],
        ],
    ];
}

function mapHomepageProducts(array $products): array {
    $mapped = [];
    foreach ($products as $product) {
        $mapped[] = [
            'title' => trim($product['company_series'] . ' ' . $product['model_no']),
            'image' => getProductImage($product),
            'price' => (float) $product['price'],
            'old_price' => (float) ($product['old_price'] ?? 0),
            'badge_left' => (!empty($product['old_price']) && $product['old_price'] > $product['price'])
                ? 'Giảm ' . round(100 - ($product['price'] / $product['old_price']) * 100) . '%'
                : '',
            'badge_right' => 'Mới',
            'link' => getProductDetailLink($product),
        ];
    }
    return $mapped;
}

function renderHomepageProductSection(string $title, array $items, string $viewAllLink, string $viewAllLabel): void {
    echo '<section class="home-product-section">';
    echo '<h2 class="section-title">' . htmlspecialchars($title) . '</h2>';
    echo '<div class="home-product-grid">';
    foreach ($items as $item) {
        echo '<a href="' . htmlspecialchars($item['link']) . '" class="home-product-card">';
        if (!empty($item['badge_left'])) {
            echo '<span class="badge badge-left">' . htmlspecialchars($item['badge_left']) . '</span>';
        }
        if (!empty($item['badge_right'])) {
            $badgeClass = stripos($item['badge_right'], 'trả góp') !== false ? 'badge-blue' : 'badge-green';
            echo '<span class="badge badge-right ' . $badgeClass . '">' . htmlspecialchars($item['badge_right']) . '</span>';
        }
        echo '<div class="home-product-image"><img src="' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['title']) . '"></div>';
        echo '<h3>' . htmlspecialchars($item['title']) . '</h3>';
        echo '<div class="home-price-row">';
        echo '<span class="home-price">' . number_format($item['price'], 0, ',', '.') . 'đ</span>';
        if (!empty($item['old_price']) && $item['old_price'] > $item['price']) {
            echo '<span class="home-old-price">' . number_format($item['old_price'], 0, ',', '.') . 'đ</span>';
        }
        echo '</div>';
        echo '</a>';
    }
    echo '</div>';
    echo '<div class="view-all-wrap"><a class="view-all-btn" href="' . htmlspecialchars($viewAllLink) . '">' . htmlspecialchars($viewAllLabel) . '</a></div>';
    echo '</section>';
}


?>

 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopDunk Homepage</title>
  <style>
      .brand-container {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
    padding: 40px 50px;
  }

  .brand-card {
    display: inline-block;
    background: #fff;
    color: #111;
    border-radius: 15px;
    inline-size: 200px;
    block-size: 280px;
    overflow: hidden;
    text-align: center;
    box-shadow: 0 12px 24px rgba(0,0,0,0.12);
    transition: transform 0.3s;
    text-decoration: none;
    border: 1px solid #ececec;
  }

  .brand-card:visited,
  .brand-card:hover,
  .brand-card:active,
  .brand-card:focus {
    color: #111;
    text-decoration: none;
  }

  .brand-card:hover {
    transform: translateY(-5px);
  }

  .brand-card img {
    inline-size: 100%;
    block-size: 220px;
    object-fit: contain;
    background: #fff;
    padding: 10px 10px 0;
    display: block;
  }

  .brand-name {
    color: #111 !important;
    font-size: 16px;
    margin-block-start: 0;
    font-weight: 600;
    padding: 18px 12px 20px;
    background: #fff;
  }

  .empty-products {
    max-inline-size: 1100px;
    margin: 0 auto 20px;
    padding: 18px 22px;
    border-radius: 16px;
    background: #f4f4f6;
    color: #555;
    text-align: center;
  }

  .home-product-section {
    max-inline-size: 1320px;
    margin: 0 auto;
    padding: 20px 20px 10px;
  }

  .home-product-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 20px;
  }

  .home-product-card {
    position: relative;
    background: #fff;
    border: 1px solid #e9edf5;
    border-radius: 18px;
    padding: 18px 18px 22px;
    text-decoration: none;
    color: #111827;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    min-block-size: 440px;
    font-family: "SF Pro Display", "SF Pro Text", "Segoe UI", Arial, sans-serif;
  }

  .home-product-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12);
  }

  .home-product-image {
    block-size: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-block-start: 26px;
  }

  .home-product-image img {
    max-inline-size: 100%;
    max-block-size: 100%;
    object-fit: contain;
  }

  .home-product-card h3 {
    margin: 18px 0 14px;
    font-size: 16px;
    line-height: 1.4;
    min-block-size: 68px;
    color: #1d1d1f;
    font-weight: 600;
    letter-spacing: -0.01em;
    text-align: center;
  }

  .home-price-row {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .home-price {
    font-size: 17px;
    font-weight: 700;
    color: #0a65cc;
    letter-spacing: -0.01em;
  }

  .home-old-price {
    color: #8b8b90;
    text-decoration: line-through;
    font-size: 13px;
    font-weight: 400;
  }

  .badge {
    position: absolute;
    inset-block-start: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-block-size: 34px;
    padding: 0 14px;
    border-radius: 11px;
    font-size: 14px;
    font-weight: 700;
  }

  .badge-left {
    inset-inline-start: 14px;
    background: #eb1c24;
    color: #fff;
  }

  .badge-right {
    inset-inline-end: 14px;
    background: #fff;
    border: 2px solid #4caf50;
    color: #2f8f2f;
  }

  .badge-blue {
    border-color: #60a5fa;
    color: #2563eb;
  }

  .view-all-wrap {
    display: flex;
    justify-content: center;
    margin: 26px 0 10px;
  }

  .view-all-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-inline-size: 220px;
    min-block-size: 52px;
    border-radius: 16px;
    border: 1.5px solid #2f80ed;
    color: #2f80ed;
    text-decoration: none;
    font-size: 18px;
    font-weight: 600;
    background: #fff;
  }

  .view-all-btn:hover {
    background: #f3f8ff;
  }

  .section-title {
    text-align: center;
    font-size: 28px;
    margin-block-start: 40px;
  }
    body {
      margin: 0;
      font-family: system-ui, sans-serif;
      background: #fff;
    }
     .container {
      max-inline-size: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 50px;
      background: #fff;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .nav-links a {
      margin: 0 15px;
      text-decoration: none;
      color: #000;
      font-weight: 500;
    }
    .actions button {
      padding: 10px 20px;
      background: #000;
      color: #fff;
      border: none;
      border-radius: 20px;
      cursor: pointer;
    }

 .slider {
  inline-size: 100%;
  max-inline-size: 100%;
  block-size: 500px;
  margin: 0 auto;
  position: relative;
  overflow: hidden;
  border-radius: 12px;
}

.slides {
  display: flex;
  transition: transform 1s ease-in-out;
  inline-size: 100%;
}

.slides img {
  flex: 0 0 100%;
  inline-size: 100%;
  block-size: 500px;
  object-fit: cover;
}


    @keyframes slide {
      0%, 20%   { transform: translateX(0); }
      25%, 45%  { transform: translateX(-100%); }
      50%, 70%  { transform: translateX(-200%); }
      75%, 95%  { transform: translateX(-300%); }
      100%      { transform: translateX(0); }
    }

      .section-title {
      text-align: center;
      font-size: 28px;
      margin: 40px 0 20px;
      font-weight: bold;
    }
    .product-slider-container {
      overflow-x: auto;
      white-space: nowrap;
      padding: 0 30px 20px;
    }
    .product-slider {
      display: inline-flex;
      gap: 20px;
    }
    .product-card {
      display: inline-block;
      inline-size: 200px;
      background: #000;
      color: #fff;
      border-radius: 10px;
      overflow: hidden;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .product-card img {
      inline-size: 100%;
      block-size: 200px;
      object-fit: cover;
    }
    .product-card h3 {
      margin: 10px 0 0;
    }
    .product-card .price {
      font-weight: bold;
      color: #fca120 !important;
      font-size: 16px;
    }
    .product-card .old-price {
      text-decoration: line-through;
      font-size: 12px;
      color: gray;
      margin-inline-start: 5px;
    }
    .product-card .discount {
      font-size: 12px;
      margin: 5px 0 10px;
    }
    .product-slider-container::-webkit-scrollbar {
      block-size: 6px;
    }
    .product-slider-container::-webkit-scrollbar-thumb {
      background: #aaa;
      border-radius: 3px;
    }
  .iphone-slider-wrapper {
  inline-size: 100%;
  max-inline-size: 1100px;         /* đủ chứa 4 sản phẩm và khoảng cách */
  margin: 30px auto;
  overflow: hidden;
  position: relative;
}


.iphone-slider-track {
  display: flex;
  transition: transform 0.6s ease-in-out;
}


.slide-group {
  display: flex;
  justify-content: space-between;
  gap: 40px;                /* Tăng khoảng cách giữa các sản phẩm */
  min-inline-size: 100%;          /* Đảm bảo đúng 4 sản phẩm trên 1 khung */
  padding: 20px 40px;
  box-sizing: border-box;
}



.product-card {
  inline-size: 220px;
  block-size: 320px;
  background: #000;
  color: #fff;
  border-radius: 12px;
  overflow: hidden;
  text-align: center;
  box-shadow: 0 6px 18px rgba(0,0,0,0.25);
  padding: 10px 0;
  text-decoration: none;
  color: inherit;
}
 
.product-card img {
  inline-size: 100%;
  block-size: 220px;
  object-fit: contain;
}


.product-card h3 {
  margin: 10px 0 0;
  font-size: 15px;
}

.product-card .price {
  font-weight: bold;
  color: #fca120 !important;
  font-size: 16px;
}

.product-card .old-price {
  text-decoration: line-through !important;
  font-size: 12px;
  color: gray !important;  
  margin-inline-start: 5px;
}

.product-card .discount {
  font-size: 12px;
  margin: 5px 0 10px;
}
.samsung-slider-wrapper {
  inline-size: 100%;
  max-inline-size: 1100px;
  margin: 30px auto;
  overflow: hidden;
  position: relative;
}

.samsung-slider-track {
  display: flex;
  transition: transform 0.6s ease-in-out;
}

.slide-group {
  display: flex;
  justify-content: space-between;
  gap: 40px;
  min-inline-size: 100%;
  padding: 20px 40px;
  box-sizing: border-box;
}

.product-card {
  inline-size: 220px;
  block-size: 320px;
  background: #000;
  color: #fff;
  border-radius: 12px;
  overflow: hidden;
  text-align: center;
  box-shadow: 0 6px 18px rgba(0,0,0,0.25);
  padding: 10px 0;
}

.product-card img {
  inline-size: 100%;
  block-size: 220px;
  object-fit: contain;
}

.product-card h3 {
  margin: 10px 0 0;
  font-size: 15px;
}

.product-card .price {
  font-weight: bold;
  color: #fca120 !important;
  font-size: 16px;
}

.product-card .old-price {
  text-decoration: line-through;
  font-size: 12px;
  color: gray;
  margin-inline-start: 5px;
}

.product-card .discount {
  font-size: 12px;
  margin: 5px 0 10px;
}
.oppo-product-grid {
  display: flex;
  justify-content: center;
  gap: 30px;
  padding: 40px 50px;
}

.oppo-card {
  background: #000;
  border-radius: 15px;
  inline-size: 220px;
  block-size: 320px;
  overflow: hidden;
  text-align: center;
  text-decoration: none;
  color: #fff;
  padding: 10px 0;
  box-shadow: 0 8px 16px rgba(0,0,0,0.2);
  transition: transform 0.3s;
}

.oppo-card:hover {
  transform: translateY(-5px);
}

.oppo-card img {
  inline-size: 100%;
  block-size: 180px;
  object-fit: contain;
  background: #000;
  border-radius: 8px;
}

.oppo-card h3 {
  font-size: 15px;
  margin: 10px 0 5px;
  font-weight: 500;
  color: #fff;
}

.oppo-card .price {
  font-weight: bold;
  color: #fca120;
  font-size: 16px;
}

.oppo-card .old-price {
  text-decoration: line-through;
  font-size: 12px;
  color: #aaa;
  margin-inline-start: 6px;
}

.oppo-card .discount {
  font-size: 13px;
  color: #ff4d4f;
  margin-block-start: 5px;
}

.xiaomi-product-grid {
  display: flex;
  justify-content: center;
  gap: 40px;
  flex-wrap: wrap;
  padding: 20px 50px;
}

.xiaomi-card {
  inline-size: 220px;
  block-size: 320px;
  background: #000;              
  color: #fff;                   
  border-radius: 12px;
  overflow: hidden;
  text-align: center;
  box-shadow: 0 6px 18px rgba(0,0,0,0.25);
  padding: 10px 0px;
  text-decoration: none;
}


.xiaomi-card img {
  inline-size: 100%;
  block-size: 220px;
  object-fit: contain;
}

.xiaomi-card h3 {
  margin: 10px 0 0;
  font-size: 15px;
  color: #fff !important;;
}

.xiaomi-card .price {
  font-weight: bold;
  color: #fca120;   /* màu cam */
  font-size: 16px;
}

.xiaomi-card .old-price {
  text-decoration: line-through;
  font-size: 12px;
  color: gray;
  margin-inline-start: 5px;
}

.xiaomi-card .discount {
  font-size: 12px;
  color: #fff;
  margin: 5px 0 0;
}

.blog-section {
  .blog-section {
  background: #333;
  color: #fff;
  padding: 20px 20px 80px 20px; /* top right bottom left */
  text-align: center;
}

}

.section-title {
  font-size: 32px;
  margin-block-end: 20px;
  font-weight: bold;
}

.blog-slider-container {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  max-inline-size: 1200px;
  margin: auto;
}

.blog-slider-wrapper {
  overflow: hidden;
  inline-size: 100%;
}

.blog-slider-track {
  display: flex;
  transition: transform 0.5s ease;
}

.blog-slide {
  display: flex;
  min-inline-size: 100%;
  justify-content: space-around;
}

.blog-card {
  background: #222;
  border-radius: 12px;
  overflow: hidden;
  text-align: start;
  inline-size: 30%;
  text-decoration: none;
  color: white;
}

.blog-card img {
  inline-size: 100%;
  block-size: 180px;
  object-fit: cover;
  display: block;
}

.blog-info {
  padding: 15px;
}

.blog-title {
  font-size: 16px;
  font-weight: 600;
  margin-block-end: 10px;
}

.blog-time {
  font-size: 12px;
  color: #aaa;
}

.nav-btn {
  background: #555;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 50%;
  cursor: pointer;
  z-index: 2;
  margin: 0 20px;
}
.service-features {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px 20px;
  background: #fff;
  gap: 60px;
  flex-wrap: wrap;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 12px;
  max-inline-size: 250px;
  text-align: start;
}

.feature-item img {
  inline-size: 36px;
  block-size: 36px;
}

.feature-item h4 {
  margin: 0;
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.feature-item p {
  margin: 4px 0 0;
  font-size: 14px;
  color: #666;
}

 .flashsale-slider,
    .blog-slider-container,
    .iphone-slider-wrapper,
    .samsung-slider-wrapper,
    .xiaomi-product-grid,
    .oppo-product-grid,
    .brand-container,
    .service-features {
      max-inline-size: 1200px;
      margin: auto;
    }

    .xiaomi-card:hover {
  transform: translateY(-5px);
}

    @media (max-width: 1100px) {
      .home-product-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 680px) {
      .home-product-grid {
        grid-template-columns: 1fr;
      }

      .home-product-card {
        min-block-size: auto;
      }
    }
   
</style>
   <div class="container">
    <?php include __DIR__ . '/includes/header.php'; ?>

  <div class="slider">
  <div class="slides" id="slideWrapper">
    <img src="https://startuppakistan.com.pk/wp-content/uploads/2024/01/iphone-16-rumors.webp" alt="Slide 1">
    <img src="https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?auto=format&fit=crop&w=1600&q=80" alt="Slide 2">
    <img src="images/home-slide-mac.png" alt="Slide 3">
    <img src="https://images.unsplash.com/photo-1434493789847-2f02dc6ca35d?auto=format&fit=crop&w=1600&q=80" alt="Slide 4">
  </div>
</div>
<script>
 const slideWrapper = document.getElementById('slideWrapper');
const slides = slideWrapper.children.length;
let index = 0;

setInterval(() => {
  index = (index + 1) % slides;
  slideWrapper.style.transform = `translateX(-${index * 100}%)`;
}, 3000);

</script>
  <!-- Brand Cards Section -->
<h2 class="section-title"></h2>

<div class="brand-container">
  <?php
  $brands = [
    ["name" => "iPhone", "img" => "images/home-iphone.png", "slug" => "products/categories/Alliphone.php"],
    ["name" => "iPad", "img" => "images/home-ipad.png", "slug" => "products/categories/ipad.php"],
    ["name" => "Mac", "img" => "images/home-mac.png", "slug" => "products/categories/mac.php"],
    ["name" => "Watch", "img" => "images/home-watch.png", "slug" => "products/categories/watch.php"]
  ];

  foreach ($brands as $brand) {
    echo '<a href="'.$brand['slug'].'" class="brand-card">';
    echo '<img src="'.$brand['img'].'" alt="'.$brand['name'].'">';
    echo '<div class="brand-name">'.$brand['name'].'</div>';
    echo '</a>';
  }
  ?>
</div>
<?php
$fallbackProducts = getHomepageFallbackProducts();

$iphoneProducts = getFeaturedProductsBySeries($pdo, ['iPhone'], 4);
$ipadProducts = getFeaturedProductsBySeries($pdo, ['iPad'], 4);
$macProducts = getFeaturedProductsBySeries($pdo, ['MacBook', 'Mac', 'iMac'], 4);
$watchProducts = getFeaturedProductsBySeries($pdo, ['Watch', 'Apple Watch'], 4);

renderHomepageProductSection(
    'iPhone',
    $iphoneProducts ? mapHomepageProducts($iphoneProducts) : $fallbackProducts['iphone'],
    'products/categories/Alliphone.php',
    'Xem tất cả iPhone'
);

renderHomepageProductSection(
    'iPad',
    $ipadProducts ? mapHomepageProducts($ipadProducts) : $fallbackProducts['ipad'],
    'products/categories/ipad.php',
    'Xem tất cả iPad'
);

renderHomepageProductSection(
    'Mac',
    $macProducts ? mapHomepageProducts($macProducts) : $fallbackProducts['mac'],
    'products/categories/mac.php',
    'Xem tất cả Mac'
);

renderHomepageProductSection(
    'Watch',
    $watchProducts ? mapHomepageProducts($watchProducts) : $fallbackProducts['watch'],
    'products/categories/watch.php',
    'Xem tất cả Watch'
);
?>

<!-- FLASH SALE SECTION -->
<section id="flashsale">
  <div style="display: flex; justify-content: space-between; align-items: center; max-inline-size: 1200px; margin: auto; padding: 0 20px;">
    <img src="https://www.creativefabrica.com/wp-content/uploads/2022/10/30/Flash-Sale-Vector-Graphics-43847109-1.png" alt="Flash Sale" style="block-size: 120px;">
    <div style="text-align: end;">
      <div style="font-size: 20px; font-weight: bold;">KẾT THÚC TRONG</div>
      <div style="font-size: 32px;">
        <span id="hours">12</span> :
        <span id="minutes">59</span> :
        <span id="seconds">59</span>
      </div>
    </div>
  </div>

  <div class="flashsale-slider">
    <div class="flashsale-track" id="flashsaleTrack">
      <?php
      $flashsales = getFlashSaleProducts($pdo, 9); // Lấy tối đa 9 sản phẩm giảm giá
      $chunks = array_chunk($flashsales, 3);
      foreach ($chunks as $group) {
        echo '<div class="flashsale-group">';
        foreach ($group as $item) {
          $price = number_format($item["price"], 0, ",", ".") . "đ";
          $old = number_format($item["old_price"], 0, ",", ".") . "đ";
          $discount = '-' . round(100 - ($item["price"] / $item["old_price"]) * 100) . '%';
          echo '<a href="' . getProductDetailLink($item) . '" class="flashsale-card">';
          echo '<img src="' . getProductImage($item) . '" alt="' . htmlspecialchars($item["model_no"]) . '">';

          echo '<h4>' . htmlspecialchars($item["company_series"] . ' ' . $item["model_no"]) . '</h4>';
          echo '<div><span class="price">' . $price . '</span><span class="old-price">' . $old . '</span></div>';
          echo '<div class="discount">' . $discount . '</div>';
          echo '</a>';
        }
        echo '</div>';
      }
      ?>
    </div>
  </div>
</section>

<style>
  
  .flashsale-slider {
  overflow: hidden;
  max-inline-size: 1100px;
  margin: 30px auto;
}
.flashsale-track {
  display: flex;
  transition: transform 0.6s ease-in-out;
}
.flashsale-group {
  display: flex;
  justify-content: space-between;
  gap: 40px;
  min-inline-size: 100%;
  padding: 20px 40px;
  box-sizing: border-box;
}
.flashsale-card {
  inline-size: 220px;
  block-size: 320px;
  background:rgb(241, 235, 235);
  color: #000;
  border-radius: 12px;
  overflow: hidden;
  text-align: center;
  box-shadow: 0 6px 18px rgba(0,0,0,0.25);
  padding: 10px 0;
  text-decoration: none;
  position: relative;
}
.flashsale-card img {
  inline-size: 100%;
  block-size: 220px;
  object-fit: contain;
}
.flashsale-card h4 {
  margin: 10px 0 0;
  font-size: 15px;
}
.flashsale-card .price {
  font-weight: bold;
  color: red;
  font-size: 16px;
}
.flashsale-card .old-price {
  text-decoration: line-through;
  font-size: 12px;
  color: gray;
  margin-inline-start: 5px;
}
.flashsale-card .discount {
  font-size: 12px;
  background: red;
  color: white;
  padding: 2px 6px;
  border-radius: 4px;
  position: absolute;
  inset-block-end: 10px;
  inset-inline-end: 10px;
}

</style>
<script>

window.addEventListener('load', () => {
  const flashsaleTrack = document.getElementById('flashsaleTrack');
  if (!flashsaleTrack || flashsaleTrack.children.length === 0) {
    return;
  }
  const groupWidth = flashsaleTrack.children[0].offsetWidth;
  let flashIndex = 0;
  setInterval(() => {
    flashIndex = (flashIndex + 1) % flashsaleTrack.children.length;
    flashsaleTrack.style.transform = `translateX(-${flashIndex * groupWidth}px)`;
  }, 4000);
});

const endTime = new Date();
endTime.setHours(endTime.getHours() + 1);

function updateCountdown() {
  const now = new Date();
  const diff = endTime - now;
  if (diff <= 0) {
    document.getElementById('hours').textContent = "00";
    document.getElementById('minutes').textContent = "00";
    document.getElementById('seconds').textContent = "00";
    return;
  }
  const hours = String(Math.floor(diff / (1000 * 60 * 60))).padStart(2, '0');
  const minutes = String(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
  const seconds = String(Math.floor((diff % (1000 * 60)) / 1000)).padStart(2, '0');
  document.getElementById('hours').textContent = hours;
  document.getElementById('minutes').textContent = minutes;
  document.getElementById('seconds').textContent = seconds;
}
setInterval(updateCountdown, 1000);
updateCountdown();
</script>

<section class="blog-section">
  <h2 class="section-title">Blog</h2>
  <div class="blog-slider-container">
    <button class="nav-btn prev" onclick="slideBlog(-1)">←</button>

    <div class="blog-slider-wrapper">
      <div class="blog-slider-track" id="blogSliderTrack">
        <?php
        $blogs = [
          [
            "title" => "Bảng giá iPhone 15 tháng 5: iPhone 15 Pro Max xả kho rẻ, iPhone 15 giá dễ mua hút khách",
            "img" => "https://media.techz.vn/resize_x700x/media2019/source/1-Tung/1-ngay%205%20thang%205/iphone-15-series-2.jpg",
            "link" => "https://www.techz.vn/189-525-1-bang-gia-iphone-15-thang-5-iphone-15-pro-max-xa-kho-re-nhu-beo-iphone-15-gia-de-mua-hut-khach-ylt650590.html",
            "time" => "21/05/25"
          ],
          [
            "title" => "iPhone kỷ niệm 20 năm sẽ là siêu phẩm tuyệt mỹ?",
            "img" => "https://vb.1cdn.vn/2025/05/21/static-images.vnncdn.net-vps_images_publish-000001-000003-2025-5-21-_iphone-pro-20-nam-26356.jpg",
            "link" => "https://vietbao.vn/iphone-ky-niem-20-nam-se-la-sieu-pham-tuyet-my-543446.html",
            "time" => "21/05/25"
          ],
          [
            "title" => "Lộ ảnh thực tế iPhone 17 Air so độ mỏng cùng iPhone 16 Plus",
            "img" => "https://images2.thanhnien.vn/528068263637045248/2025/5/20/air-1747751930996400838882.jpg",
            "link" => "https://thanhnien.vn/lo-anh-thuc-te-iphone-17-air-so-do-mong-cung-iphone-16-plus-185250520213558061.htm",
            "time" => "20/05/25"
          ]
        ];

        foreach (array_chunk($blogs, 3) as $slide) {
          echo '<div class="blog-slide">';
          foreach ($slide as $blog) {
            echo '<a class="blog-card" href="' . $blog["link"] . '" target="_blank">
                    <img src="' . $blog["img"] . '" alt="' . $blog["title"] . '">
                    <div class="blog-info">
                      <p class="blog-title">' . $blog["title"] . '</p>
                      <p class="blog-time">' . $blog["time"] . '</p>
                    </div>
                  </a>';
          }
          echo '</div>';
        }
        ?>
      </div>
    </div>

    <button class="nav-btn next" onclick="slideBlog(1)">→</button>
  </div>
</section>
<script>
let currentSlide = 0;
function slideBlog(direction) {
  const slides = document.querySelectorAll('.blog-slide');
  currentSlide += direction;
  if (currentSlide < 0) currentSlide = slides.length - 1;
  if (currentSlide >= slides.length) currentSlide = 0;
  document.getElementById('blogSliderTrack').style.transform = `translateX(-${100 * currentSlide}%)`;
}
</script>
<section class="service-features">
  <div class="feature-item">
    <img src="icons/quality.svg" alt="Chất lượng cao">
    <div>
      <h4>Chất lượng cao</h4>
      <p>Sản phẩm chính hãng</p>
    </div>
  </div>

  <div class="feature-item">
    <img src="icons/warranty.svg" alt="Chính sách bảo hành">
    <div>
      <h4>Chính sách bảo hành</h4>
      <p>1 năm hoàn toàn miễn phí</p>
    </div>
  </div>

  <div class="feature-item">
    <img src="icons/shipping.svg" alt="Miễn phí vận chuyển">
    <div>
      <h4>Miễn phí vận chuyển</h4>
      <p>Đơn hàng trên 5 triệu</p>
    </div>
  </div>

  <div class="feature-item">
    <img src="icons/support.svg" alt="Hỗ trợ 24/7">
    <div>
      <h4>Hỗ trợ 24/7</h4>
      <p>Hỗ trợ tận tâm</p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
