
<?php
require_once __DIR__ . '/auth.php';
require_permission('brands');
include __DIR__ . '/sidebar.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý Thương hiệu</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { margin: 0; font-family: system-ui, sans-serif; background: #f4f4fc; }
    .main-content { margin-left: 220px; padding: 30px 40px; }
    h2 { color: #333; }
    .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .top-bar input[type="text"] { padding: 10px 15px; border-radius: 20px; border: 1px solid #ccc; width: 250px; }
    .top-bar button { padding: 10px 18px; border-radius: 15px; border: none; background: linear-gradient(to right, #7c58e6, #6e44d9); color: white; cursor: pointer; margin-left: 10px; }
    .table-container { background: white; border-radius: 10px; overflow: hidden; }
    table { width: 100%; border-collapse: collapse; text-align: center; }
    thead { background: #0c2a61; color: white; }
    th, td { padding: 12px; border-bottom: 1px solid #ddd; }
    .action-buttons a button { margin: 0 5px; padding: 5px 12px; border-radius: 6px; border: none; background: #4c2bb6; color: white; cursor: pointer; }
    .action-buttons a button:hover { opacity: 0.9; }
  </style>
</head>
<body>
<div class="main-content">
  <h2>Thương hiệu</h2>
  <div class="top-bar">
    <input type="text" id="search" placeholder="Tìm kiếm ở đây..." onkeyup="searchBrand()">
    <div><a href="addbrand.php"><button>Thêm mới</button></a></div>
  </div>
  <div class="table-container">
    <table>
      <thead>
        <tr><th>Số thứ tự</th><th>Tên thương hiệu</th><th>Ghi chú</th><th>Hành động</th></tr>
      </thead>
      <tbody id="brandTable">
        <?php
        $sql = "SELECT * FROM brands";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
          echo "<tr><td colspan='4'>Lỗi truy vấn: " . mysqli_error($conn) . "</td></tr>";
        } else {
          $i = 1;
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
              <td>{$i}</td>
              <td>{$row['brand_name']}</td>
              <td>{$row['note']}</td>
              <td class='action-buttons'>
                <a href='editbrand.php?id={$row['id']}'><button>Sửa</button></a>
                <a href='deletebrand.php?id={$row['id']}' onclick=\"return confirm('Bạn có chắc muốn xóa không?');\"><button style='background:#e63946;'>Xóa</button></a>
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
<script>
function searchBrand() {
  var input = document.getElementById("search");
  var filter = input.value.toUpperCase();
  var table = document.getElementById("brandTable");
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
    var td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      var txtValue = td.textContent || td.innerText;
      tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
    }
  }
}
</script>
</body>
</html>
    





