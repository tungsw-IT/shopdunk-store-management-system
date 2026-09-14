<?php
require_once __DIR__ . '/auth.php';
require_permission('customers');
include __DIR__ . '/sidebar.php';


// Xử lý tìm kiếm
$search = trim($_GET['search'] ?? '');
$search_sql = '';
$params = [];
// Phân trang
$limit = 10; // Số khách hàng mỗi trang, chỉnh tùy ý
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

if ($search) {
    $search_sql = "WHERE customer_name LIKE ? OR customer_email LIKE ? OR customer_contact_no LIKE ?";
    $like_search = "%$search%";
    $params = [$like_search, $like_search, $like_search];
}
// Đếm tổng số khách hàng (theo điều kiện tìm kiếm nếu có)
$count_sql = "SELECT COUNT(*) as total FROM customer $search_sql";
$count_stmt = $conn->prepare($count_sql);
if ($search) {
    $count_stmt->bind_param("sss", ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_customers = $count_result->fetch_assoc()['total'] ?? 0;
$total_pages = ceil($total_customers / $limit);

// Chuẩn bị truy vấn
$sql = "SELECT * FROM customer $search_sql ORDER BY customer_id ASC LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);

if ($search) {
    $stmt->bind_param("sss", ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8" />
<title>Quản lý Khách hàng</title>
<style>
  /* Reset và font chung */
  * {
    box-sizing: border-box;
  }
  body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f5f7fa;
  }

  /* Container chính bên phải */
  .main-content {
    margin-left: 220px; /* tương ứng sidebar */
    padding: 30px 40px;
    max-width: 1200px;
    margin-right: auto;
  }

  h2 {
    color: #222;
    font-weight: 700;
    margin-bottom: 25px;
  }

  form {
    margin-bottom: 20px;
  }

  input[type="text"] {
    padding: 10px 15px;
    width: 320px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 1rem;
    transition: border-color 0.3s ease;
  }
  input[type="text"]:focus {
    border-color: #4c2bb6;
    outline: none;
  }

  button, a.add-btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
    text-decoration: none;
    display: inline-block;
    color: white;
  }
  button {
    background-color: #4c2bb6;
    margin-left: 10px;
  }
  button:hover {
    background-color: #3a2089;
  }
  a.add-btn {
    background-color: #28a745;
    margin-left: 20px;
  }
  a.add-btn:hover {
    background-color: #218838;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    min-width: 900px;
  }

  thead {
    background: #0c2a61;
    color: white;
    font-weight: 600;
  }

  th, td {
    padding: 14px 18px;
    border-bottom: 1px solid #e0e0e0;
    text-align: center;
    vertical-align: middle;
  }

  td a {
    color: #4c2bb6;
    font-weight: 600;
  }
  td a:hover {
    text-decoration: underline;
  }
  a.btn {
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
  text-decoration: none;
  display: inline-block;
  color: white;
}

.refresh-btn {
  background-color: #17a2b8; /* xanh ngọc */
  margin-left: 10px;
}

.refresh-btn:hover {
  background-color: #138496;
}

.pagination .page-num {
  background: #eee;
  color: #4c2bb6;
  margin: 0 2px;
  padding: 8px 15px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  transition: background 0.2s;
}
.pagination .page-num:hover {
  background: #d7e5ff;
  color: #4c2bb6;
}

  /* Responsive */
  @media (max-width: 768px) {
    .main-content {
      margin-left: 0;
      padding: 20px;
    }
    table {
      min-width: 700px;
    }
  }
</style>
</head>
<body>
<div id="toast-success" style="
  display:none;
  position: fixed;
  top: 30px; right: 40px;
  z-index: 9999;
  background: #28a745;
  color: #fff;
  padding: 16px 32px;
  border-radius: 8px;
  box-shadow: 0 4px 24px rgba(40,167,69,0.13);
  font-size: 1.1rem;
  font-weight: bold;
  animation: fadeInDown 0.5s;
">
  <span id="toast-msg"></span>
</div>
<style>
@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-24px);}
  to { opacity: 1; transform: translateY(0);}
}
</style>


<style>
@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-24px);}
  to { opacity: 1; transform: translateY(0);}
}
</style>

<div class="main-content">
  <h2>Danh sách Khách hàng</h2>

  <form method="get" action="">
    <input type="text" name="search" placeholder="Tìm kiếm tên, email, số điện thoại..." value="<?php echo htmlspecialchars($search); ?>" />
    <button type="submit">Tìm</button>
    <a href="addcustomer.php" class="add-btn">Thêm Khách hàng mới</a>
    <a href="customer.php" class="btn refresh-btn">Làm mới</a>

  </form>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Tên khách hàng</th>
        <th>Giới tính</th>
        <th>Email</th>
        <th>Số điện thoại</th>
        <th>Địa chỉ</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows === 0): ?>
      <tr>
        <td colspan="7" style="text-align:center; padding:20px; color:#666;">Không tìm thấy khách hàng nào.</td>
      </tr>
      <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['customer_id']; ?></td>
          <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
          <td><?php echo htmlspecialchars($row['customer_gender']); ?></td>
          <td><?php echo htmlspecialchars($row['customer_email']); ?></td>
          <td><?php echo htmlspecialchars($row['customer_contact_no']); ?></td>
          <td><?php echo htmlspecialchars($row['customer_address']); ?></td>
          <td>
            <a href="editcustomer.php?id=<?php echo $row['customer_id']; ?>">Sửa</a> |
            <a href="deletecustomer.php?id=<?php echo $row['customer_id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?');">Xóa</a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <?php if ($total_pages > 1): ?>
<div style="text-align:center; margin: 22px 0;">
  <nav class="pagination">
    <?php
    $query_params = $_GET; // Giữ search/page khi chuyển trang
    for ($p = 1; $p <= $total_pages; $p++) {
        $query_params['page'] = $p;
        $is_current = $p == $page ? 'style="background:#4c2bb6;color:#fff;border-radius:6px;"' : '';
        echo '<a href="?' . http_build_query($query_params) . "\" $is_current class=\"page-num\" style=\"display:inline-block;margin:0 4px;padding:8px 15px;text-decoration:none;\">" . $p . '</a>';
    }
    ?>
  </nav>
</div>
<?php endif; ?>

</div>
<script>
function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}

window.addEventListener('DOMContentLoaded', function() {
  const successType = getQueryParam('success');
  if (successType === '1' || successType === '2' || successType === '3') {
    const toast = document.getElementById('toast-success');
    const msg = document.getElementById('toast-msg');
    msg.innerText =
      (successType === '1') ? "Đã thêm khách hàng thành công!" :
      (successType === '2') ? "Đã xóa khách hàng thành công!" :
      "Đã sửa khách hàng thành công!";
    toast.style.display = 'block';
    setTimeout(() => {
      toast.style.display = 'none';
      if (window.history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url.pathname + url.search);
      }
    }, 2500);
  }
});
</script>

</body>
</html>





