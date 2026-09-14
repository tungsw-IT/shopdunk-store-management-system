<?php
require_once __DIR__ . '/auth.php';
require_permission('products');
require_once __DIR__ . '/../products/product_images_helper.php';

ensure_product_images_table_mysqli($conn);

function ensure_stock_quantity_column(mysqli $conn): void
{
    $result = $conn->query("SHOW COLUMNS FROM mobile LIKE 'stock_quantity'");
    if (!$result || $result->num_rows === 0) {
        $conn->query("ALTER TABLE mobile ADD COLUMN stock_quantity INT NOT NULL DEFAULT 0");
    }
}

ensure_stock_quantity_column($conn);

$ram_options = [4, 6, 8];
$rom_options = [
    128 => '128G',
    256 => '256G',
    512 => '512G',
    1024 => '1T',
    2048 => '2T',
];

$brand_series = [];
$res = $conn->query("SELECT company_name, company_series FROM mobile WHERE company_name IS NOT NULL AND company_series IS NOT NULL");
while ($row = $res->fetch_assoc()) {
    $brand = $row['company_name'];
    $series = $row['company_series'];
    if (!isset($brand_series[$brand])) {
        $brand_series[$brand] = [];
    }
    if (!in_array($series, $brand_series[$brand], true)) {
        $brand_series[$brand][] = $series;
    }
}

$default_brand_series = [
    'Apple' => ['iPhone', 'iPad', 'MacBook', 'iMac', 'Watch'],
];

foreach ($default_brand_series as $brand => $series_list) {
    if (!isset($brand_series[$brand])) {
        $brand_series[$brand] = [];
    }
    foreach ($series_list as $series) {
        if (!in_array($series, $brand_series[$brand], true)) {
            $brand_series[$brand][] = $series;
        }
    }
}

$imei = $_GET['imei_number'] ?? '';
if (!$imei) {
    echo '<h2>Không có mã IMEI được cung cấp.</h2>';
    exit;
}

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = trim($_POST['company_name'] ?? '');
    $company_series = trim($_POST['company_series'] ?? '');
    $model_no = trim($_POST['model_no'] ?? '');
    $ram = (float) ($_POST['ram'] ?? 0);
    $rom = (float) ($_POST['rom'] ?? 0);
    $display_size = (float) ($_POST['display_size'] ?? 0);
    $display_quality = trim($_POST['display_quality'] ?? '');
    $processor = trim($_POST['processor'] ?? '');
    $battery = (float) ($_POST['battery'] ?? 0);
    $color = trim($_POST['color'] ?? '');
    $stock_quantity = (int) ($_POST['stock_quantity'] ?? 0);
    $price = (float) ($_POST['price'] ?? 0);
    $old_price = (float) ($_POST['old_price'] ?? 0);

    if ($ram < 0 || $rom < 0 || $display_size < 0 || $battery < 0 || $stock_quantity < 0 || $price < 0 || $old_price < 0) {
        $err = 'Không được nhập số âm cho trường số!';
    } else {
        $stmt = $conn->prepare("UPDATE mobile SET
            company_name = ?,
            company_series = ?,
            model_no = ?,
            `ram(GB)` = ?,
            `rom(GB)` = ?,
            `display_size(inchi)` = ?,
            display_quality = ?,
            processor = ?,
            `battery_capacity(mah)` = ?,
            color = ?,
            stock_quantity = ?,
            price = ?,
            old_price = ?
            WHERE imei_number = ?");
        $stmt->bind_param(
            'sssiidssisidds',
            $company_name,
            $company_series,
            $model_no,
            $ram,
            $rom,
            $display_size,
            $display_quality,
            $processor,
            $battery,
            $color,
            $stock_quantity,
            $price,
            $old_price,
            $imei
        );
        $stmt->execute();
        $stmt->close();

        $deleteImageIds = array_map('intval', $_POST['delete_image_ids'] ?? []);
        delete_product_images_mysqli($conn, $deleteImageIds);

        $imagePaths = save_uploaded_product_images($_FILES['product_images'] ?? [], $imei);
        insert_product_images_mysqli($conn, $imei, $imagePaths);

        header("Location: product.php?new=$imei");
        exit;
    }
}

$stmt = $conn->prepare('SELECT * FROM mobile WHERE imei_number = ?');
$stmt->bind_param('s', $imei);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    echo '<h2>Sản phẩm không tồn tại.</h2>';
    exit;
}

$existingImages = fetch_product_images_mysqli($conn, $imei);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sửa sản phẩm</title>
  <style>
    body { margin: 0; font-family: system-ui, sans-serif; background: #f4f4fc; }
    .main-content { margin-left: 220px; padding: 40px; }
    .form-container { background: #fff; border-radius: 12px; padding: 30px; max-width: 800px; }
    h2 { color: #333; }
    .form-container h3 { background: #4c2bb6; color: white; padding: 10px 15px; border-radius: 8px 8px 0 0; margin: 0; }
    form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 0; }
    label { font-weight: 500; color: #4c2bb6; margin-bottom: 2px; }
    input, select { padding: 10px; border: 1px solid #ccc; border-radius: 6px; width: 100%; }
    input[type="file"] { background: #fff; }
    .form-footer { grid-column: 1 / 3; text-align: right; }
    .form-footer button { padding: 10px 25px; background: white; border: 2px solid #4c2bb6; color: #4c2bb6; border-radius: 20px; font-weight: bold; cursor: pointer; }
    .form-footer button:hover { background: #4c2bb6; color: white; }
    .error-msg { color: #e63946; font-size: 14px; margin: 0 0 10px 0; grid-column: 1 / 3; }
    .full-width { grid-column: 1 / 3; }
    .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 14px; margin-top: 10px; }
    .gallery-item { border: 1px solid #ddd; border-radius: 10px; padding: 10px; text-align: center; }
    .gallery-item img { width: 100%; height: 90px; object-fit: contain; margin-bottom: 8px; }
    .gallery-item label { color: #333; font-size: 13px; font-weight: 500; }
  </style>
  <script>
    const brandSeries = <?= json_encode($brand_series, JSON_UNESCAPED_UNICODE) ?>;

    window.addEventListener('DOMContentLoaded', function() {
      var brandSelect = document.getElementById('company_name');
      var seriesSelect = document.getElementById('company_series');

      function fillSeries(brand, selectedSeries) {
        seriesSelect.innerHTML = '<option value="">-- Chọn dòng sản phẩm --</option>';
        if (brandSeries[brand]) {
          brandSeries[brand].forEach(function(series) {
            var option = document.createElement('option');
            option.value = series;
            option.textContent = series;
            if (series === selectedSeries) {
              option.selected = true;
            }
            seriesSelect.appendChild(option);
          });
        }
      }

      brandSelect.addEventListener('change', function() {
        fillSeries(this.value, '');
      });

      fillSeries(brandSelect.value, <?= json_encode($product['company_series'], JSON_UNESCAPED_UNICODE) ?>);

      document.querySelector('form').addEventListener('submit', function(e) {
        document.querySelectorAll('.error-msg').forEach(function(el) { el.remove(); });
        var hasError = false;
        var fields = [
          { id: 'ram', label: 'RAM' },
          { id: 'rom', label: 'ROM' },
          { id: 'display_size', label: 'Kích thước màn' },
          { id: 'battery', label: 'Pin' },
          { id: 'stock_quantity', label: 'Số lượng' },
          { id: 'price', label: 'Giá' },
          { id: 'old_price', label: 'Giá cũ' }
        ];

        fields.forEach(function(field) {
          var input = document.getElementById(field.id);
          if (input && input.value !== '' && Number(input.value) < 0) {
            hasError = true;
            var error = document.createElement('div');
            error.className = 'error-msg';
            error.innerText = field.label + ' không được âm!';
            input.parentNode.appendChild(error);
          }
        });

        if (hasError) {
          e.preventDefault();
        }
      });
    });
  </script>
</head>
<body>
<?php include __DIR__ . '/sidebar.php'; ?>
<div class="main-content">
  <h2>Sửa sản phẩm</h2>
  <div class="form-container">
    <h3>Chi tiết sản phẩm</h3>
    <?php if (!empty($err)): ?>
      <div class="error-msg"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" autocomplete="off">
      <div class="form-group">
        <label for="company_name">Thương hiệu:</label>
        <select name="company_name" id="company_name" required>
          <option value="">-- Chọn thương hiệu --</option>
          <?php foreach (array_keys($brand_series) as $brand): ?>
            <option value="<?= htmlspecialchars($brand) ?>" <?= ($product['company_name'] === $brand) ? 'selected' : '' ?>>
              <?= htmlspecialchars($brand) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="company_series">Dòng sản phẩm:</label>
        <select name="company_series" id="company_series" required>
          <option value="">-- Chọn dòng sản phẩm --</option>
        </select>
      </div>
      <div class="form-group">
        <label for="model_no">Model:</label>
        <input id="model_no" name="model_no" value="<?= htmlspecialchars($product['model_no']) ?>" required>
      </div>
      <div class="form-group">
        <label for="imei_number">IMEI Number:</label>
        <input id="imei_number" name="imei_number" value="<?= htmlspecialchars($product['imei_number']) ?>" readonly style="background:#f2f2f2;">
      </div>
      <div class="form-group">
        <label for="ram">RAM (GB):</label>
        <select id="ram" name="ram" required>
          <option value="">-- Chọn RAM --</option>
          <?php foreach ($ram_options as $ram_option): ?>
            <option value="<?= $ram_option ?>" <?= (string) $ram_option === (string) $product['ram(GB)'] ? 'selected' : '' ?>><?= $ram_option ?>GB</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="rom">ROM (GB):</label>
        <select id="rom" name="rom" required>
          <option value="">-- Chọn ROM --</option>
          <?php foreach ($rom_options as $rom_value => $rom_label): ?>
            <option value="<?= $rom_value ?>" <?= (string) $rom_value === (string) $product['rom(GB)'] ? 'selected' : '' ?>><?= htmlspecialchars($rom_label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="display_size">Kích thước màn (inchi):</label>
        <input id="display_size" name="display_size" type="number" step="0.01" min="0" value="<?= htmlspecialchars($product['display_size(inchi)']) ?>" required>
      </div>
      <div class="form-group">
        <label for="display_quality">Chất lượng màn hình:</label>
        <input id="display_quality" name="display_quality" value="<?= htmlspecialchars($product['display_quality']) ?>" required>
      </div>
      <div class="form-group">
        <label for="processor">Vi xử lý:</label>
        <input id="processor" name="processor" value="<?= htmlspecialchars($product['processor']) ?>" required>
      </div>
      <div class="form-group">
        <label for="battery">Pin (mAh):</label>
        <input id="battery" name="battery" type="number" min="0" value="<?= htmlspecialchars($product['battery_capacity(mah)']) ?>" required>
      </div>
      <div class="form-group">
        <label for="color">Màu sắc:</label>
        <input id="color" name="color" value="<?= htmlspecialchars($product['color']) ?>" required>
      </div>
      <div class="form-group">
        <label for="stock_quantity">Số lượng:</label>
        <input id="stock_quantity" name="stock_quantity" type="number" min="0" value="<?= htmlspecialchars($product['stock_quantity'] ?? '0') ?>" required>
      </div>
      <div class="form-group">
        <label for="price">Giá:</label>
        <input id="price" name="price" type="number" step="10000" min="0" value="<?= htmlspecialchars($product['price']) ?>" required>
      </div>
      <div class="form-group">
        <label for="old_price">Giá cũ (nếu có):</label>
        <input id="old_price" name="old_price" type="number" step="10000" min="0" value="<?= htmlspecialchars($product['old_price']) ?>">
      </div>
      <div class="form-group full-width">
        <label for="product_images">Thêm ảnh sản phẩm:</label>
        <input id="product_images" name="product_images[]" type="file" accept="image/*" multiple>
      </div>
      <div class="form-group full-width">
        <label>Ảnh hiện có:</label>
        <?php if (!$existingImages): ?>
          <div>Chưa có ảnh nào.</div>
        <?php else: ?>
          <div class="gallery-grid">
            <?php foreach ($existingImages as $image): ?>
              <div class="gallery-item">
                <img src="<?= htmlspecialchars(product_image_url($image['image_path'])) ?>" alt="Ảnh sản phẩm">
                <label>
                  <input type="checkbox" name="delete_image_ids[]" value="<?= (int) $image['id'] ?>">
                  Xóa ảnh này
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="form-footer">
        <button type="submit">Lưu lại</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>




