
<?php
require_once __DIR__ . '/auth.php';
require_permission('brands');
include __DIR__ . '/sidebar.php';

$id = $_GET['id'] ?? '';
$result = mysqli_query($conn, "SELECT * FROM brands WHERE id = $id");
$brand = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $note = $_POST['note'];
  $sql = "UPDATE brands SET brand_name='$name', note='$note' WHERE id=$id";
  mysqli_query($conn, $sql);
  header("Location: brand.php");
  exit();
}
?>
<div style="margin-left: 220px; padding: 40px;">
  <h2>Sửa thương hiệu</h2>
  <div style="background: white; border-radius: 12px; padding: 30px; max-width: 600px;">
    <form method="post">
      <label>Tên thương hiệu</label><br>
      <input name="name" value="<?= $brand['brand_name'] ?>" style="width: 100%; padding: 10px; margin-bottom: 20px;"><br>
      <label>Ghi chú</label><br>
      <textarea name="note" style="width: 100%; padding: 10px;"><?= $brand['note'] ?></textarea><br><br>
      <div style="text-align: right;"><button type="submit" style="padding: 10px 25px; border: 2px solid #4c2bb6; color: #4c2bb6; border-radius: 20px;">Lưu lại</button></div>
    </form>
  </div>
</div>
    





