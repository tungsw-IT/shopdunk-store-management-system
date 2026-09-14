<?php
require_once __DIR__ . '/auth.php';
require_permission('products');
include __DIR__ . '/sidebar.php';

$stockColumnCheck = mysqli_query($conn, "SHOW COLUMNS FROM mobile LIKE 'stock_quantity'");
if (!$stockColumnCheck || mysqli_num_rows($stockColumnCheck) === 0) {
    mysqli_query($conn, "ALTER TABLE mobile ADD COLUMN stock_quantity INT NOT NULL DEFAULT 0");
}

$result = mysqli_query($conn, "SELECT * FROM mobile ORDER BY company_name ASC, company_series ASC, model_no ASC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý sản phẩm</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { margin: 0; font-family: system-ui, sans-serif; background: #f4f4fc; }
    .main-content { margin-left: 220px; padding: 30px 40px; }
    h2 { color: #333; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .top-bar input[type="text"] { padding: 10px 15px; border-radius: 20px; border: 1px solid #ccc; width: 250px; }
    .top-bar button { padding: 10px 18px; border-radius: 15px; border: none; background: linear-gradient(to right, #7c58e6, #6e44d9); color: white; cursor: pointer; margin-left: 10px; }
    .table-container { background: white; border-radius: 10px; overflow-x: auto; margin-bottom: 40px; }
    table { width: 100%; border-collapse: collapse; text-align: center; font-size: 15px; min-width: 1520px; }
    thead { background: #0c2a61; color: white; }
    th, td { padding: 8px 10px; border: 1px solid #ddd; white-space: nowrap; }
    .action-buttons a .btn-edit { background: #0d6efd; color: #fff; border: none; padding: 5px 10px; border-radius: 6px; margin: 0 3px; font-size: 13px; cursor: pointer; }
    .action-buttons a .btn-delete { background: #e63946; color: #fff; border: none; padding: 5px 10px; border-radius: 6px; margin: 0 3px; font-size: 13px; cursor: pointer; }
    .action-buttons a .btn-edit:hover, .action-buttons a .btn-delete:hover { opacity: 0.9; }
    @media (max-width:1100px) { .main-content { padding:10px; } }
    @media (max-width:800px) { table, .table-container { font-size:12px; } }
  </style>
</head>
<body>
<div class="main-content">
  <h2>Sản phẩm</h2>

  <div class="top-bar">
    <input type="text" id="search" placeholder="Tìm kiếm ở đây..." onkeyup="searchProduct()">
    <div>
      <a href="addproduct.php"><button>Thêm mới</button></a>
    </div>
  </div>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>STT</th>
          <th>Thương hiệu</th>
          <th>Dòng sản phẩm</th>
          <th>Model</th>
          <th>IMEI</th>
          <th>RAM</th>
          <th>ROM</th>
          <th>Kích thước (inch)</th>
          <th>Chất lượng màn</th>
          <th>Vi xử lý</th>
          <th>Pin (mAh)</th>
          <th>Màu sắc</th>
          <th>Số lượng</th>
          <th>Giá bán</th>
          <th>Giá cũ</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody id="productTable">
        <?php if (!$result): ?>
          <tr><td colspan="16">Lỗi truy vấn: <?= htmlspecialchars(mysqli_error($conn)) ?></td></tr>
        <?php else: ?>
          <?php $i = 1; while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?= $i ?></td>
              <td><?= htmlspecialchars((string) $row['company_name']) ?></td>
              <td><?= htmlspecialchars((string) $row['company_series']) ?></td>
              <td><?= htmlspecialchars((string) $row['model_no']) ?></td>
              <td><?= htmlspecialchars((string) $row['imei_number']) ?></td>
              <td><?= htmlspecialchars((string) $row['ram(GB)']) ?> GB</td>
              <td><?= htmlspecialchars((string) $row['rom(GB)']) ?> GB</td>
              <td><?= htmlspecialchars((string) ($row['display_size(inchi)'] ?? '-')) ?></td>
              <td><?= htmlspecialchars((string) ($row['display_quality'] ?? '-')) ?></td>
              <td><?= htmlspecialchars((string) ($row['processor'] ?? '-')) ?></td>
              <td><?= htmlspecialchars((string) ($row['battery_capacity(mah)'] ?? '-')) ?></td>
              <td><?= htmlspecialchars((string) ($row['color'] ?? '-')) ?></td>
              <td><?= (int) ($row['stock_quantity'] ?? 0) ?></td>
              <td><?= isset($row['price']) ? number_format((float) $row['price'], 0, ',', '.') . ' ₫' : '-' ?></td>
              <td><?= isset($row['old_price']) ? number_format((float) $row['old_price'], 0, ',', '.') . ' ₫' : '-' ?></td>
              <td class="action-buttons">
                <a href="editproduct.php?imei_number=<?= urlencode((string) $row['imei_number']) ?>"><button class="btn-edit">Sửa</button></a>
                <a href="deleteproduct.php?imei_number=<?= urlencode((string) $row['imei_number']) ?>" onclick="return confirm('Bạn có chắc muốn xóa không?');"><button class="btn-delete">Xóa</button></a>
              </td>
            </tr>
            <?php $i++; endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function searchProduct() {
  var input = document.getElementById('search');
  var filter = input.value.toUpperCase();
  var table = document.getElementById('productTable');
  var tr = table.getElementsByTagName('tr');
  for (var i = 0; i < tr.length; i++) {
    var txt = '';
    for (var j = 1; j < tr[i].cells.length - 1; j++) txt += tr[i].cells[j].textContent + ' ';
    tr[i].style.display = txt.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
  }
}
</script>
</body>
</html>





