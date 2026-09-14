<?php
require_once __DIR__ . '/auth.php';
require_permission('orders');
include __DIR__ . '/sidebar.php';

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? ''; // Lấy tham số sort từ URL

$sql = "SELECT * FROM paymentbill WHERE 1";

if ($search) {
    $search_esc = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (pb_id LIKE '%$search_esc%' OR customer_name LIKE '%$search_esc%')";
}

// Xử lý sắp xếp
$allowedSorts = ['pb_date_asc', 'pb_date_desc', 'pb_id_asc', 'pb_id_desc'];
if (!in_array($sort, $allowedSorts)) {
    $sort = 'pb_date_desc'; // Mặc định sắp xếp theo ngày giảm dần
}

switch ($sort) {
    case 'pb_date_asc':
        $sql .= " ORDER BY pb_date ASC";
        break;
    case 'pb_date_desc':
        $sql .= " ORDER BY pb_date DESC";
        break;
    case 'pb_id_asc':
        $sql .= " ORDER BY pb_id ASC";
        break;
    case 'pb_id_desc':
        $sql .= " ORDER BY pb_id DESC";
        break;
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}

// Hàm hiển thị icon sort
function sort_icon($field, $currentSort) {
    if (strpos($currentSort, $field) === false) {
        return ''; // Không phải cột đang sort
    }
    if (substr($currentSort, -3) === 'asc') {
        return '▲';
    } else {
        return '▼';
    }
}

// Hàm tạo URL đổi chiều sort
function sort_url($field, $currentSort) {
    $baseSort = strpos($currentSort, $field) === 0 ? $currentSort : '';
    if ($baseSort === $field . '_asc') {
        return $field . '_desc';
    }
    return $field . '_asc';
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Danh sách đơn hàng</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body { margin: 0; font-family: system-ui, sans-serif; background: #f4f4fc; }
    .main-content { margin-inline-start: 220px; padding: 30px 40px; }
    h2 { color: #333; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; margin-block-end: 20px; }
    .top-bar input[type="text"] { padding: 10px 15px; border-radius: 20px; border: 1px solid #ccc; inline-size: 250px; }
    .top-bar button { padding: 10px 18px; border-radius: 15px; border: none; background: linear-gradient(to right, #7c58e6, #6e44d9); color: white; cursor: pointer; margin-inline-start: 10px; }
    .table-container { background: white; border-radius: 10px; overflow: hidden; }
    table { inline-size: 100%; border-collapse: collapse; text-align: center; }
    thead { background: #0c2a61; color: white; }
    th, td { padding: 12px; border-block-end: 1px solid #ddd; cursor: pointer; user-select: none; }
    th a { color: white; text-decoration: none; }
    th a:hover { text-decoration: underline; }
    .action-buttons a button { margin: 0 5px; padding: 5px 12px; border-radius: 6px; border: none; background: #4c2bb6; color: white; cursor: pointer; }
    .action-buttons a button:hover { opacity: 0.9; }
    .btn-reload {
      background: #ddd !important;
      color:#333 !important;
      border: none;
      border-radius: 15px;
      padding: 10px 18px;
      margin-inline-start:4px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      font-weight: 500;
      font-size: 1rem;
      box-shadow: 0 2px 8px #e6e6e6;
      transition: background .15s;
    }
    .btn-reload:hover {
      background: #bde5ff !important;
      color: #1a237e !important;
    }
  </style>
</head>
<body>
<div class="main-content">
  <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
  <div style="background: #cceee3; color: #1d7762; padding: 10px 20px; border-radius: 6px; margin-block-end: 18px; font-weight: bold;">
    Đã xóa đơn hàng thành công!
  </div>
<?php endif; ?>

  <h2>Đơn hàng</h2>

  <div class="top-bar">
    <form method="GET" action="order.php" style="display:flex; gap:10px; align-items:center;">
  <input type="text" name="search" placeholder="Tìm mã đơn hoặc khách hàng" value="<?= htmlspecialchars($search) ?>" />
  <button type="submit">Tìm kiếm</button>
  <button type="button" class="btn-reload" title="Làm mới trang" onclick="window.location.href='order.php'">
    <i class="fa fa-rotate-right"></i>
  </button>
</form>

    <div><a href="addorder.php"><button>Thêm mới</button></a></div>
  </div>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>
            <a href="?search=<?=urlencode($search)?>&sort=<?=sort_url('pb_id', $sort)?>">
              Số thứ tự <?=sort_icon('pb_id', $sort)?>
            </a>
          </th>
          <th>
            <a href="?search=<?=urlencode($search)?>&sort=<?=sort_url('pb_id', $sort)?>">
              Mã đơn <?=sort_icon('pb_id', $sort)?>
            </a>
          </th>
          <th>Tên khách hàng</th>
          <th>
            <a href="?search=<?=urlencode($search)?>&sort=<?=sort_url('pb_date', $sort)?>">
              Ngày đặt <?=sort_icon('pb_date', $sort)?>
            </a>
          </th>
          <th>Tổng tiền</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i = 1;
        if (mysqli_num_rows($result) == 0) {
            echo "<tr><td colspan='6'>Không có đơn hàng nào</td></tr>";
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                  <td>{$i}</td>
                  <td>" . htmlspecialchars($row['pb_id']) . "</td>
                  <td>" . htmlspecialchars($row['customer_name']) . "</td>
                  <td>" . htmlspecialchars($row['pb_date']) . "</td>
                  <td>" . number_format($row['total_paid_amount'], 0, ',', '.') . " đ</td>
                  <td class='action-buttons'>
                    <a href='editorder.php?id=" . urlencode($row['pb_id']) . "'><button>Sửa</button></a>
                    <a href='deleteorder.php?id=" . urlencode($row['pb_id']) . "' onclick=\"return confirm('Bạn có chắc muốn xóa không?');\"><button style='background:#e63946;'>Xóa</button></a>
                  </td>
                </tr>";
                $i++;
            }
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>





