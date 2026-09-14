<?php
require_once __DIR__ . '/auth.php';
require_permission('brands');
include __DIR__ . '/sidebar.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $code = $_POST['code'];

  $sql = "INSERT INTO brands (brand_name, brand_code) VALUES ('$name', '$code')";
  mysqli_query($conn, $sql);
  header("Location: brand.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thêm thương hiệu</title>
  <style>
    body {
      margin: 0;
      font-family: system-ui, sans-serif;
      background: #f4f4fc;
    }

    .main-content {
      margin-left: 220px;
      padding: 40px;
    }

    h2 {
      color: #333;
    }

    .form-container {
      background: white;
      border-radius: 12px;
      padding: 30px;
      max-width: 800px;
    }

    .form-container h3 {
      background: #4c2bb6;
      color: white;
      padding: 10px 15px;
      border-radius: 8px 8px 0 0;
      margin: 0;
    }

    form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      padding-top: 20px;
    }

    form input {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      width: 100%;
    }

    .form-footer {
      grid-column: 1 / 3;
      text-align: right;
    }

    .form-footer button {
      padding: 10px 25px;
      background: white;
      border: 2px solid #4c2bb6;
      color: #4c2bb6;
      border-radius: 20px;
      font-weight: bold;
      cursor: pointer;
    }

    .form-footer button:hover {
      background: #4c2bb6;
      color: white;
    }
  </style>
</head>
<body>

<div class="main-content">
  <h2>Thêm thương hiệu</h2>
  <div class="form-container">
    <h3>Chi tiết thương hiệu</h3>
    <form method="post">
      <input name="name" placeholder="Tên thương hiệu">
      <input name="code" placeholder="Mã thương hiệu">
      <div class="form-footer">
        <button type="submit">Lưu lại</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>





