<?php
require_once __DIR__ . '/auth.php';
require_permission('reviews');
include __DIR__ . '/sidebar.php';

// Xử lý POST: chỉ còn xóa đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['review_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id && $action === 'delete') {
        mysqli_query($conn, "DELETE FROM reviews WHERE review_id=$id");
    }

    header("Location: reviews.php");
    exit;
}

// Lọc và sắp xếp
$search = $_GET['search'] ?? '';
$brand_filter = $_GET['brand'] ?? '';
$model_filter = $_GET['model'] ?? '';
$rating_filter = $_GET['rating'] ?? '';
$allowedSorts = ['review_id', 'created_at', 'rating'];
$sort_by = $_GET['sort_by'] ?? 'review_id';
if (!in_array($sort_by, $allowedSorts, true)) {
    $sort_by = 'review_id';
}
$order = strtoupper($_GET['order'] ?? 'DESC');
if (!in_array($order, ['ASC', 'DESC'], true)) {
    $order = 'DESC';
}

$where_clauses = [];
$params = [];

if ($search) {
    $search = trim($search);
    $where_clauses[] = "(CAST(account_id AS CHAR) LIKE ? OR review_text LIKE ? OR model_no LIKE ? OR company_series LIKE ?)";
    $like_search = "%$search%";
    $params[] = $like_search;
    $params[] = $like_search;
    $params[] = $like_search;
    $params[] = $like_search;
}

if ($brand_filter) {
    $where_clauses[] = "company_series = ?";
    $params[] = $brand_filter;
}
if ($model_filter) {
    $where_clauses[] = "model_no = ?";
    $params[] = $model_filter;
}
if ($rating_filter) {
    $where_clauses[] = "rating = ?";
    $params[] = $rating_filter;
}
$where_sql = count($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";


$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
// Đếm tổng số đánh giá theo điều kiện lọc
$count_sql = "SELECT COUNT(*) as total FROM reviews $where_sql";
$count_stmt = $conn->prepare($count_sql);
if ($count_stmt === false) {
    die("Lỗi prepare SQL: " . $conn->error . "<br>SQL: <pre>" . htmlspecialchars($count_sql) . "</pre>");
}

if (count($params) > 0) {
    $count_stmt->bind_param(str_repeat("s", count($params)), ...$params); 
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_reviews = $count_result->fetch_assoc()['total'] ?? 0;
$total_pages = ceil($total_reviews / $limit);

// Chuẩn bị truy vấn với lọc và sắp xếp
$sql = "SELECT * FROM reviews $where_sql ORDER BY $sort_by $order LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);

// Kiểm tra có tham số hay không, nếu có mới bind
if (count($params) > 0) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Lấy rating trung bình theo sản phẩm
$avg_sql = "SELECT company_series, model_no, ROUND(AVG(rating),2) AS avg_rating, COUNT(*) AS total_reviews
            FROM reviews GROUP BY company_series, model_no";
$avg_result = mysqli_query($conn, $avg_sql);

$avg_ratings = [];
while ($row = mysqli_fetch_assoc($avg_result)) {
    $key = $row['company_series'] . '|' . $row['model_no'];
    $avg_ratings[$key] = ['avg' => $row['avg_rating'], 'count' => $row['total_reviews']];
}

$low_rating_threshold = 3;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Quản lý Đánh giá Sản phẩm</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body { margin: 0; font-family: system-ui, sans-serif; background: #f4f4fc; }
    .main-content { margin-left: 220px; padding: 30px 40px; }
    h2 { color: #333; }
    .table-container { background: white; border-radius: 10px; overflow: hidden; margin-bottom: 40px; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: #0c2a61; color: white; }
    th, td { padding: 12px; border-bottom: 1px solid #ddd; vertical-align: top; }
    td.comment-col { max-width: 250px; word-wrap: break-word; text-align: left; }
    .action-buttons form { display: inline-block; margin: 0 3px; }
    .action-buttons button { padding: 5px 12px; border-radius: 6px; border: none; background: #e63946; color: white; cursor: pointer; }
    .rating-stars {
      color: #f4c150;
      font-size: 1.1em;
      user-select: none;
    }
    .low-rating {
      background: #fddede;
    }
    /* --- Bộ lọc & sắp xếp --- */
form {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 25px;
  align-items: center;
}

form input[type="text"],
form select {
  padding: 10px 14px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #fff;
  transition: border-color 0.3s ease;
  width: 200px;
}

form input[type="text"]:focus,
form select:focus {
  border-color: #0c2a61;
  outline: none;
}

form button[type="submit"] {
  padding: 10px 18px;
  background-color: #0c2a61;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

form button[type="submit"]:hover {
  background-color: #143a85;
}
/* Form lọc/sắp xếp */
.filter-form {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 25px;
  align-items: center;
}

.filter-form input[type="text"],
.filter-form select {
  padding: 10px 14px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #fff;
  transition: border-color 0.3s ease;
  width: 200px;
}

.filter-form input[type="text"]:focus,
.filter-form select:focus {
  border-color: #0c2a61;
  outline: none;
}

.filter-form .btn-search {
  padding: 10px 18px;
  background-color: #0c2a61;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.filter-form .btn-search:hover {
  background-color: #143a85;
}

.filter-form .btn-refresh {
  padding: 10px 18px;
  background-color: #17a2b8;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.filter-form .btn-refresh:hover {
  background-color: #138496;
}

.pagination .page-num {
  background: #eee;
  color: #0c2a61;
  margin: 0 2px;
  padding: 8px 15px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  transition: background 0.2s;
}
.pagination .page-num:hover {
  background: #d7e5ff;
  color: #0c2a61;
}

/* Responsive layout */
@media (max-width: 768px) {
  .filter-form {
    flex-direction: column;
    align-items: stretch;
  }
  .filter-form input,
  .filter-form select,
  .filter-form .btn-search,
  .filter-form .btn-refresh {
    width: 100%;
  }
}


  </style>
</head>
<body>
<div class="main-content">
  <h2>Danh sách đánh giá & bình luận</h2>

  <!-- Form lọc và sắp xếp -->
  <form method="get" action="" class="filter-form">
  <input type="text" name="search" placeholder="Tìm kiếm " value="<?= htmlspecialchars($search); ?>" />

  <select name="rating">
    <option value="">Chọn Rating</option>
    <?php for ($i = 5; $i >= 1; $i--): ?>
      <option value="<?= $i ?>" <?= $rating_filter == $i ? 'selected' : '' ?>><?= $i ?></option>
    <?php endfor; ?>
  </select>

  <select name="sort_by">
    <option value="review_id" <?= $sort_by == 'review_id' ? 'selected' : '' ?>>Sắp xếp theo ID</option>
    <option value="created_at" <?= $sort_by == 'created_at' ? 'selected' : '' ?>>Sắp xếp theo Ngày</option>
    <option value="rating" <?= $sort_by == 'rating' ? 'selected' : '' ?>>Sắp xếp theo Đánh giá</option>
  </select>

  <select name="order">
    <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Giảm dần</option>
    <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Tăng dần</option>
  </select>

  <button type="submit" class="btn-search">Tìm</button>
  <a href="reviews.php" class="btn-refresh">Làm mới</a>
</form>


  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Thương hiệu</th>
          <th>Model</th>
          <th>Tên người đánh giá</th>
          <th>Bình luận</th>
          <th>Đánh giá</th>
          <th>Ngày tạo</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
      <?php
      while ($row = mysqli_fetch_assoc($result)) {
          $key = $row['company_series'] . '|' . $row['model_no'];
          $is_low_rating = isset($avg_ratings[$key]) && $avg_ratings[$key]['avg'] < $low_rating_threshold;

          // Hiển thị sao
          $stars = '';
          for ($s = 1; $s <= 5; $s++) {
              $stars .= $s <= $row['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
          }

          $row_class = $is_low_rating ? 'low-rating' : '';

          echo "<tr class='{$row_class}'>
              <td>{$row['review_id']}</td>
              <td>{$row['company_series']}</td>
              <td>{$row['model_no']}</td>
              <td>{$row['account_id']}</td>
              <td class='comment-col'>" . htmlspecialchars($row['review_text']) . "</td>
              <td class='rating-stars'>{$stars}</td>
              <td>{$row['created_at']}</td>
              <td class='action-buttons'>
                <form method='post' onsubmit=\"return confirm('Bạn có chắc muốn xóa đánh giá này không?');\">
                  <input type='hidden' name='review_id' value='{$row['review_id']}'>
                  <button type='submit' name='action' value='delete'>Xóa</button>
                </form>
              </td>
            </tr>";
      }
      ?>
      </tbody>
    </table>
    <?php if ($total_pages > 1): ?>
<div style="text-align:center; margin: 22px 0;">
  <nav class="pagination">
    <?php
    // Giữ các tham số lọc khi chuyển trang
    $query_params = $_GET;
    for ($p = 1; $p <= $total_pages; $p++) {
        $query_params['page'] = $p;
        $class = $p == $page ? 'style="background:#0c2a61;color:#fff;border-radius:6px;"' : '';
        echo '<a href="?' . http_build_query($query_params) . "\" $class class=\"page-num\" style=\"display:inline-block;margin:0 4px;padding:8px 15px;text-decoration:none;\">" . $p . '</a>';
    }
    ?>
  </nav>
</div>
<?php endif; ?>

  </div>

  <h2>Báo cáo sản phẩm cần cải thiện (rating trung bình < <?php echo $low_rating_threshold; ?>)</h2>
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Thương hiệu</th>
          <th>Model sản phẩm</th>
          <th>Rating trung bình</th>
          <th>Số lượng đánh giá</th>
        </tr>
      </thead>
      <tbody>
      <?php
     $has_low_rating = false;
foreach ($avg_ratings as $key => $data) {
    $parts = explode('|', $key);
    if (count($parts) === 2) {
        list($brand, $model) = $parts;

        // Debug
        echo "<!-- DEBUG: $brand - $model | AVG = {$data['avg']} -->";

        if ((float)$data['avg'] < $low_rating_threshold) {
            $has_low_rating = true;
            echo "<tr>
                    <td>" . htmlspecialchars($brand) . "</td>
                    <td>" . htmlspecialchars($model) . "</td>
                    <td>" . number_format($data['avg'], 2) . "</td>
                    <td>" . intval($data['count']) . "</td>
                  </tr>";
        }
    }
}

      if (!$has_low_rating) {
          echo '<tr><td colspan="4" style="text-align:center;">Không có sản phẩm nào cần cải thiện.</td></tr>';
      }
      ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>





