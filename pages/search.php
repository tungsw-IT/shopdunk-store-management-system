<?php
include __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../products/product_images_helper.php';
include __DIR__ . '/../includes/header.php';

$query = trim((string) ($_GET['query'] ?? ''));

function search_product_name(array $product): string
{
    $company = trim((string) ($product['company_name'] ?? ''));
    $series = trim((string) ($product['company_series'] ?? ''));
    $model = trim((string) ($product['model_no'] ?? ''));

    if ($series !== '' && stripos($series, $company) === 0) {
        return trim($series . ' ' . $model);
    }

    return trim($company . ' ' . $series . ' ' . $model);
}

function search_product_link(array $product): string
{
    $company = strtolower((string) ($product['company_name'] ?? ''));
    $modelNo = urlencode((string) ($product['model_no'] ?? ''));
    $series = urlencode((string) ($product['company_series'] ?? ''));

    if ($company === 'samsung') {
        return "../products/detail/product-detail.php?series={$series}&model_no={$modelNo}";
    }

    if ($company === 'oppo') {
        return "../products/detail/product-detail-oppo.php?model_no={$modelNo}";
    }

    if ($company === 'xiaomi') {
        return "../products/detail/product-detail.php?model_no={$modelNo}";
    }

    if ($company === 'apple' || $company === 'iphone') {
        return "../products/detail/product-detail-iphone.php?model_no={$modelNo}";
    }

    return "../products/detail/product-detail.php?model_no={$modelNo}";
}

function search_product_image(PDO $pdo, array $product): string
{
    $brand = strtolower((string) ($product['company_name'] ?? ''));
    $folder = $brand === 'samsung' ? '../images/' : '../images/';
    $imgBase = strtolower(str_replace([' ', '+', '-'], '', (string) ($product['company_series'] ?? '')))
        . '_'
        . strtolower(str_replace([' ', '+', '-'], '', (string) ($product['model_no'] ?? '')));

    $fallback = file_exists(__DIR__ . '/' . $folder . $imgBase . '.png')
        ? $folder . $imgBase . '.png'
        : (file_exists(__DIR__ . '/' . $folder . $imgBase . '.jpg') ? $folder . $imgBase . '.jpg' : $folder . 'no-image.png');

    return resolve_primary_product_image_pdo($pdo, $product, $fallback);
}

$results = [];
if ($query !== '') {
    $stmt = $pdo->prepare("
        SELECT * FROM mobile
        WHERE company_name LIKE ?
           OR company_series LIKE ?
           OR model_no LIKE ?
        ORDER BY company_name ASC, company_series ASC, model_no ASC
    ");
    $searchTerm = "%{$query}%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $results = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kết quả tìm kiếm | ShopDunk</title>
  <style>
    body {
      font-family: "Segoe UI", Arial, sans-serif;
      margin: 0;
      background: #f6f8fc;
      color: #0f172a;
    }

    .search-page {
      max-width: 1500px;
      margin: 0 auto;
      padding: 18px 28px 40px;
    }

    .search-title {
      margin: 8px 0 20px;
      font-size: 22px;
      font-weight: 800;
    }

    .search-title em {
      font-style: italic;
    }

    .empty-state,
    .search-item {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
      border: 1px solid #e8edf5;
    }

    .empty-state {
      padding: 26px;
      color: #64748b;
      font-size: 17px;
    }

    .search-item {
      margin-bottom: 18px;
      overflow: hidden;
    }

    .search-item-link {
      display: grid;
      grid-template-columns: 150px 1fr;
      gap: 18px;
      align-items: center;
      padding: 16px;
      text-decoration: none;
      color: inherit;
    }

    .search-item-link:hover {
      background: #f8fbff;
    }

    .search-thumb {
      width: 124px;
      height: 124px;
      border-radius: 12px;
      background: #f8fafc;
      object-fit: contain;
      border: 1px solid #edf2f7;
      padding: 8px;
    }

    .search-product-name {
      font-size: 18px;
      font-weight: 800;
      margin-bottom: 10px;
      color: #111827;
    }

    .search-meta {
      color: #334155;
      line-height: 1.7;
      font-size: 15px;
    }

    .search-price {
      margin-top: 6px;
      font-size: 17px;
      font-weight: 800;
      color: #0a65cc;
    }

    @media (max-width: 720px) {
      .search-page {
        padding: 16px;
      }

      .search-item-link {
        grid-template-columns: 1fr;
      }

      .search-thumb {
        width: 100%;
        height: 220px;
        justify-self: center;
      }
    }
  </style>
</head>
<body>
<div class="search-page">
  <?php if ($query === ''): ?>
    <div class="empty-state">Vui lòng nhập từ khóa để tìm kiếm.</div>
  <?php else: ?>
    <h2 class="search-title">Kết quả tìm kiếm cho: <em><?= htmlspecialchars($query) ?></em></h2>

    <?php if (count($results) > 0): ?>
      <?php foreach ($results as $row): ?>
        <div class="search-item">
          <a class="search-item-link" href="<?= htmlspecialchars(search_product_link($row)) ?>">
            <img
              class="search-thumb"
              src="<?= htmlspecialchars(search_product_image($pdo, $row)) ?>"
              alt="<?= htmlspecialchars(search_product_name($row)) ?>"
            >
            <div>
              <div class="search-product-name"><?= htmlspecialchars(search_product_name($row)) ?></div>
              <div class="search-meta">
                Màu: <?= htmlspecialchars((string) ($row['color'] ?? '')) ?> |
                RAM: <?= htmlspecialchars((string) ($row['ram(GB)'] ?? '')) ?>GB |
                ROM: <?= htmlspecialchars((string) ($row['rom(GB)'] ?? '')) ?>GB
                <br>
                Màn hình: <?= htmlspecialchars((string) ($row['display_size(inchi)'] ?? '')) ?>\" <?= htmlspecialchars((string) ($row['display_quality'] ?? '')) ?> |
                Pin: <?= htmlspecialchars((string) ($row['battery_capacity(mah)'] ?? '')) ?>mAh
                <br>
                Vi xử lý: <?= htmlspecialchars((string) ($row['processor'] ?? '')) ?>
              </div>
              <div class="search-price">Giá: <?= number_format((float) ($row['price'] ?? 0), 0, ',', '.') ?>đ</div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty-state">Không tìm thấy sản phẩm nào phù hợp.</div>
    <?php endif; ?>
  <?php endif; ?>
</div>
</body>
</html>







