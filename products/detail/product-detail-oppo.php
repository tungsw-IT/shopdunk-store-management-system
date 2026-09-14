<?php
include __DIR__ . '/../../config/db.php';
session_start();
$isLoggedIn = isset($_SESSION['account_id']) && $_SESSION['account_id'] > 0 ? 1 : 0;

$modelNo = $_GET['model_no'] ?? '';
if (!$modelNo) die("<h2>Thiếu mã sản phẩm</h2>");


// Lấy sản phẩm (cho OPPO, đổi hãng tùy ý)
$stmt = $pdo->prepare("SELECT * FROM mobile WHERE model_no = ?");
$stmt->execute([$modelNo]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) die("<h2>Sản phẩm không tồn tại</h2>");

// Hàm lấy file ảnh
function getAlloppoImage($series, $model_no) {
    $img_base = 
        strtolower(str_replace([' ', '+', '-'], '', $series)) .
        '_' .
        strtolower(str_replace([' ', '+', '-'], '', $model_no));
    $img_path_png = '../../images/' . $img_base . '.png';
    $img_path_jpg = '../../images/' . $img_base . '.jpg';
    if (file_exists(__DIR__ . '/' . $img_path_png)) return $img_path_png;
    if (file_exists(__DIR__ . '/' . $img_path_jpg)) return $img_path_jpg;
    return '../../images/no-image.png'; // hoặc .jpg tùy bạn!
}



// Thông số
$specs = [
    "Kích thước màn hình" => $product['display_size(inchi)'] . ' inches',
    "Công nghệ màn hình"  => $product['display_quality'],
    "Chip"                => $product['processor'],
    "Dung lượng RAM"      => $product['ram(GB)'] . ' GB',
    "Bộ nhớ"              => $product['rom(GB)'] . ' GB',
    "Pin"                 => $product['battery_capacity(mah)'] . ' mAh',
];
$old_price = !empty($product['old_price']) ? $product['old_price'] : 0;
$discount = ($old_price && $old_price > $product['price']) ? round((($old_price - $product['price'])/$old_price)*100) : 0;

// Mô tả mẫu
$description = "Màn hình {$product['display_quality']} {$product['display_size(inchi)']}\" – Hiển thị sắc nét, mượt mà, tiết kiệm pin. Chip {$product['processor']} – Hiệu năng mạnh mẽ, tối ưu AI, tiết kiệm năng lượng. Thiết kế cao cấp, bền bỉ. Pin khỏe dùng cả ngày.";

// Xử lý ĐÁNH GIÁ NGAY TRANG
$reviewMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['rating'], $_POST['comment'])) {
    $name = trim($_POST['name']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    if ($name && $rating && $comment) {
        $stmtR = $pdo->prepare("INSERT INTO reviews (company_series, model_no, name, comment, rating) VALUES (?, ?, ?, ?, ?)");
        $stmtR->execute([$product['company_series'], $product['model_no'], $name, $comment, $rating]);
        $reviewMsg = "<span style='color:#24b47e;font-weight:700;'>Gửi đánh giá thành công!</span>";
        header("Location: ".$_SERVER['REQUEST_URI']);
        exit;
    } else {
        $reviewMsg = "<span style='color:#e65c00;'>Vui lòng nhập đầy đủ thông tin!</span>";
    }
}
// XỬ LÝ THÊM VÀO GIỎ HÀNG
$cartMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $imei_number = $product['imei_number'];
    $quantity = 1;
    $account_id = $_SESSION['account_id'] ?? 0;
    if ($account_id == 0) {
        $cartMsg = "Bạn cần đăng nhập để thêm vào giỏ hàng!";
    } else {
        // Kiểm tra đã có sản phẩm này trong giỏ chưa
        $stmtCheck = $pdo->prepare("SELECT quantity FROM cart WHERE account_id = ? AND product_imei = ?");
        $stmtCheck->execute([$account_id, $imei_number]);
        $existCart = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if ($existCart) {
            // Nếu có rồi, tăng số lượng lên 1
            $stmtUpdate = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE account_id = ? AND product_imei = ?");
            $stmtUpdate->execute([$account_id, $imei_number]);
            $cartMsg = "Đã tăng số lượng sản phẩm trong giỏ!";
        } else {
            // Nếu chưa có, insert mới
            $stmtCart = $pdo->prepare("INSERT INTO cart (account_id, product_imei, quantity) VALUES (?, ?, ?)");
            $stmtCart->execute([$account_id, $imei_number, $quantity]);
            $cartMsg = "Đã thêm vào giỏ hàng!";
        }
    }
}



// Lấy đánh giá
$reviewsStmt = $pdo->prepare("SELECT * FROM reviews WHERE company_series = ? AND model_no = ? ORDER BY created_at DESC");
$reviewsStmt->execute([$product['company_series'], $product['model_no']]);
$reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

// Tính trung bình rating
$avgRating = 0;
if (count($reviews)) {
    $sum = 0;
    foreach ($reviews as $r) $sum += $r['rating'];
    $avgRating = round($sum / count($reviews), 1);
}
function renderStars($rating) {
    $rating = round($rating);
    return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
}

// Sản phẩm liên quan (OPPO, khác model)
$relatedStmt = $pdo->prepare("SELECT * FROM mobile WHERE company_name = ? AND model_no != ? ORDER BY RAND() LIMIT 4");
$relatedStmt->execute([$product['company_name'], $product['model_no']]);
$relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
include __DIR__ . '/../../includes/header.php';
?>
<script>var IS_LOGGED_IN = <?= $isLoggedIn ?>;</script>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8" />
<title><?= htmlspecialchars($product['company_series'].' '.$product['model_no']) ?></title>
<link rel="stylesheet" href="../../assets/css/style.css" />
<style>
body { background:#f5f5f7; }
.container-detail {
    max-inline-size: 1100px; margin: 40px auto; background: #fff; border-radius: 18px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.10); padding: 36px 36px 22px 36px;
}
@media(max-width: 900px){ .container-detail {padding: 10px;} .info-block {flex-direction:column; align-items:stretch;} .image-block{justify-content:center;} }
.info-block { display:flex; gap:40px; align-items:stretch; }
.image-block { flex: 1.2; display: flex; align-items:flex-start; justify-content:flex-start;}
.image-block img { max-inline-size: 320px; inline-size:100%; border-radius: 18px; background:#f6f6f6; box-shadow:0 4px 24px #f0f0f0;}
.detail-content { flex: 2; min-inline-size:290px;}
.detail-content h1 { font-size: 1.7rem; font-weight: bold; margin-block-end: 12px; color: #222; }
.detail-content .product-price { font-size: 2rem; font-weight: 700; color: #e53935; margin-block-end: 6px; }
.detail-content .old-price { text-decoration:line-through; color: #999; margin-inline-start: 12px; font-size: 1.1rem;}
.detail-content .discount { color: #2e7d32; margin-inline-start:10px; font-weight: 600; }
.detail-content .color { color:#333; margin-block-end: 18px; font-size:1.12rem; }
.detail-content .btn-buy, .detail-content .btn-cart {
    display: block; inline-size:100%; font-size:1.17rem; font-weight:700; border:none; border-radius:10px; padding:18px 0; margin-block-end:18px; cursor:pointer; transition:background .15s;
}
.detail-content .btn-buy { background:#e53935; color:#fff;}
.detail-content .btn-buy:hover { background:#b71c1c; }
.detail-content .btn-cart { background:#1877f2; color:#fff; display:flex; align-items:center; justify-content:center;}
.detail-content .btn-cart:hover { background:#0d47a1; }
.detail-content .btn-cart i { margin-inline-end:9px; font-size:1.35rem; }
.spec-box { background: #f8f9fa; border-radius: 12px; box-shadow:0 2px 8px #eee; padding: 18px 26px; margin-block-end: 28px; }
.spec-box h2 { font-size:1.11rem; color:#111; margin-block-end:12px; font-weight:600;}
.spec-table { inline-size:100%; border-collapse:collapse;}
.spec-table th, .spec-table td { text-align:start; padding:7px 0; font-size:1.04rem; }
.spec-table th { inline-size: 42%; color:#111; font-weight:500;}
.spec-table td { color:#222;}
.spec-table tr:not(:last-child) td, .spec-table tr:not(:last-child) th { border-block-end:1px solid #ececec;}
@media(max-width:650px){ .spec-box{padding:8px;} .image-block img{max-width:95vw;} }
.detail-content .desc-title { font-size:1.2rem; font-weight:700; margin:38px 0 10px 0;}
.detail-content .desc-content { font-size:1.05rem; color:#222;}

/* Bình luận, đánh giá, liên quan */
.section { max-inline-size:950px; margin:38px auto 0; }
.section-title { font-size:1.22rem; font-weight:700; color:#24b47e; margin-block-end:14px;}
.review-form { background:#eafaf3; border-radius:16px; padding:22px 28px; margin-block-end: 30px;}
.review-form label { display:block; margin:9px 0 2px 0; font-weight:600; }
.review-form input, .review-form textarea, .review-form select {
    inline-size:100%; padding:8px; margin-block-end:10px; border-radius:8px; border:1px solid #ccc;
}
.review-form button {
    margin-block-start:12px; background:#24b47e; color:#fff; padding:12px 28px;
    font-weight:700; border:none; border-radius:28px; cursor:pointer; transition: background 0.3s;
}
.review-form button:hover { background:#198a5e; }
.review-list .review-item {
    background:#f8f8f8; border-radius:10px; margin-block-end:13px; padding:12px 18px;
    box-shadow:0 2px 10px #eef; color:#212;
}
.review-item .name { font-weight:700; color:#198a5e; }
.review-item .rating { color:#fbc02d; font-size:1.11rem;}
.review-item .date { color:#888; font-size:.97rem; margin-inline-start:12px;}
.related-products .grid {display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:22px;}
.related-products .card {
    background:#fff; border-radius:14px; box-shadow:0 4px 16px #e9ffe6;
    padding:13px; text-align:center; transition:.18s;
}
.related-products .card:hover { box-shadow:0 10px 24px #bae6b8; transform:translateY(-5px);}
.related-products img {max-inline-size:120px; border-radius:8px; margin-block-end:7px;}
.related-products .prod-name{font-weight:600;font-size:1.03rem;margin-block-end:3px;}
.related-products .prod-price{color:#e53935;font-weight:700;}
.related-products .old-price{color:#999;text-decoration:line-through;margin-inline-start:6px;}
.related-products .discount{color:#2e7d32;font-weight:700;margin-inline-start:7px;}
.back-link {
  display: inline-block;
  margin: 22px 0 12px 36px;
  color: #198a5e;
  background: #eafaf3;
  font-weight: 700;
  padding: 10px 28px 10px 20px;
  border-radius: 22px;
  text-decoration: none;
  box-shadow: 0 2px 10px #d8ffea;
  transition: background 0.18s, color 0.18s;
  font-size: 1.08rem;
}
.back-link:hover {
  background: #24b47e;
  color: #fff;
}
.back-link i {
  margin-inline-end: 8px;
}
@media (max-width: 700px) {
  .back-link { margin: 12px 0 8px 10px; font-size:1rem;}
}
.rating-stars {
  display: flex;
  gap: 7px;
  cursor: pointer;
}
.rating-stars .star {
  color: #ccc;
  transition: color 0.15s;
  user-select: none;
}
.rating-stars .star.selected,
.rating-stars .star.hovered {
  color: #fbc02d;
}
.cart-msg {
    background: #e0ffe8;
    color: #198a5e;
    font-weight: 700;
    padding: 10px 20px;
    border-radius: 8px;
    margin-block-end: 10px;
    box-shadow: 0 2px 10px #d0ffe0;
    text-align: center;
}
.popup-cart-msg {
    display: none;
    position: fixed;
    inset-block-start: 50px;
    inset-inline-end: 50px;
    z-index: 9999;
    background: #24b47e;
    color: #fff;
    padding: 18px 36px 18px 20px;
    border-radius: 12px;
    box-shadow: 0 8px 36px #b6fbe3;
    font-size: 1.12rem;
    font-weight: 600;
    min-inline-size: 210px;
    animation: popShow .5s;
}
.popup-cart-close {
    background: none;
    border: none;
    color: #fff;
    font-size: 1.3rem;
    position: absolute;
    inset-block-start: 6px;
    inset-inline-end: 14px;
    cursor: pointer;
    font-weight: bold;
}
@keyframes popShow {
    from {opacity:0; transform: translateY(-30px);}
    to   {opacity:1; transform: translateY(0);}
}
@media (max-width:700px) {
    .popup-cart-msg { inset-block-start:10px; inset-inline-end:10px; inset-inline-start:10px; min-inline-size: 0;}
}
.related-products .card,
.related-products .card:visited,
.related-products .card:active,
.related-products .card:hover,
.related-products .card .prod-name,
.related-products .card .prod-name:hover {
    text-decoration: none !important;
    color: inherit !important;
    box-shadow: none !important;
}


</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body>

<div class="container-detail">

  <div class="info-block">
    <div class="image-block">
      <img src="<?= $imageFile = getAlloppoImage($product['company_series'], $product['model_no']); ?>" />
    </div>
    <div class="detail-content">
      <h1><?= htmlspecialchars($product['company_series'].' '.$product['model_no'].' '.$product['ram(GB)'].'GB '.$product['rom(GB)'].'GB') ?></h1>
      <div class="product-price"><?= number_format($product['price'],0,',','.') ?>đ
        <?php if ($old_price): ?>
            <span class="old-price"><?= number_format($old_price,0,',','.') ?>đ</span>
        <?php endif; ?>
        <?php if ($discount): ?>
            <span class="discount">-<?= $discount ?>%</span>
        <?php endif; ?>
      </div>
      <div class="avg-rating" style="margin-block-end:10px; font-size:1.28rem; color:#fbc02d;">
    <?php if ($avgRating > 0): ?>
        <?= renderStars($avgRating) ?>
        <span style="font-size:1.07rem; color:#444; font-weight:400; margin-inline-start:5px;"><?= $avgRating ?>/5</span>
    <?php else: ?>
        <span style="font-size:1.07rem; color:#888;">Chưa có đánh giá</span>
    <?php endif; ?>
</div>

      <div class="color"><b>Màu sắc:</b> <?= htmlspecialchars($product['color']) ?></div>
<!-- Form MUA NGAY -->
<form method="post" action="../../checkout/buy_now.php" id="buy-now-form" style="margin-block-end:12px;">
    <input type="hidden" name="imei_number" value="<?= htmlspecialchars($product['imei_number']) ?>">
    <input type="hidden" name="quantity" value="1">
    <button type="submit" class="btn-buy" id="btnBuyNow" aria-label="Mua sản phẩm ngay">MUA NGAY</button>
</form>

<!-- Form THÊM VÀO GIỎ -->
<form method="post" action="" id="cart-form">
    <input type="hidden" name="add_to_cart" value="1">
    <input type="hidden" name="product_imei" value="<?= htmlspecialchars($product['imei_number']) ?>">
    <button type="submit" class="btn-cart" id="btnAddCart" style="display:flex;align-items:center;justify-content:center;">
        <i class="fa fa-cart-plus"></i>THÊM VÀO GIỎ HÀNG
    </button>
</form>


      <div class="spec-box">
        <h2>Thông số kỹ thuật</h2>
        <table class="spec-table">
        <?php foreach($specs as $k => $v): ?>
            <tr>
                <th><?= htmlspecialchars($k) ?></th>
                <td><?= htmlspecialchars($v) ?></td>
            </tr>
        <?php endforeach; ?>
        </table>
      </div>
    </div>
  </div>
  
  <div class="desc-title">Mô tả sản phẩm</div>
  <div class="desc-content"><?= htmlspecialchars($description) ?></div>

</div>

<!-- Đánh giá, bình luận -->
<div class="section">
    <div class="section-title">Đánh giá & Nhận xét (<?=count($reviews)?>)</div>
    <form class="review-form" method="POST" id="reviewForm">
        <input type="hidden" name="review_submit" value="1">
        <?php if ($reviewMsg) echo '<div style="margin-block-end:10px">'.$reviewMsg.'</div>'; ?>
        <label>Tên của bạn *</label>
        <input type="text" name="name" required maxlength="100">

        <label>Đánh giá *</label>
        <div class="rating-stars" style="font-size:2rem;margin-block-end:12px;">
            <?php for($i=1;$i<=5;$i++): ?>
                <span class="star" data-value="<?=$i?>">&#9733;</span>
            <?php endfor; ?>
            <input type="hidden" name="rating" id="ratingInput" required>
        </div>

        <label>Bình luận *</label>
        <textarea name="comment" rows="3" required maxlength="300"></textarea>
        <button type="submit">Gửi đánh giá</button>
    </form>
    <div class="review-list">
        <?php if (count($reviews)): foreach($reviews as $r): ?>
            <div class="review-item">
                <span class="name"><?=htmlspecialchars($r['name'])?></span>
                <span class="rating"><?=renderStars($r['rating'])?></span>
                <span class="date"><?=date('d/m/Y H:i',strtotime($r['created_at']))?></span>
                <div><?=nl2br(htmlspecialchars($r['comment']))?></div>
            </div>
        <?php endforeach; else: ?>
            <div style="color:#888;">Chưa có nhận xét nào cho sản phẩm này.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Sản phẩm OPPO liên quan -->
<div class="section related-products">
    <div class="section-title">Sản phẩm OPPO liên quan</div>
    <div class="grid">
        <?php foreach($relatedProducts as $item): 
            $img = getAlloppoImage($item['company_series'], $item['model_no']);
            $rel_old = !empty($item['old_price']) ? $item['old_price'] : 0;
            $rel_discount = ($rel_old && $rel_old > $item['price']) ? round((($rel_old - $item['price'])/$rel_old)*100) : 0;
        ?>
        <a href="?model_no=<?=urlencode($item['model_no'])?>" class="card">
            <img src="<?=$img?>" alt="<?=htmlspecialchars($item['company_series'].' '.$item['model_no'])?>">
            <div class="prod-name"><?=htmlspecialchars($item['company_series'].' '.$item['model_no'])?></div>
            <div class="prod-price"><?=number_format($item['price'],0,',','.')?>đ
                <?php if($rel_old): ?><span class="old-price"><?=number_format($rel_old,0,',','.')?>đ</span><?php endif; ?>
                <?php if($rel_discount): ?><span class="discount">-<?=$rel_discount?>%</span><?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.rating-stars .star');
    const ratingInput = document.getElementById('ratingInput');
    let selected = 0;

    stars.forEach(star => {
        // Hover hiệu ứng
        star.addEventListener('mouseenter', function() {
            const val = parseInt(this.dataset.value);
            stars.forEach((s, i) => {
                s.classList.toggle('hovered', i < val);
            });
        });
        // Out hiệu ứng
        star.addEventListener('mouseleave', function() {
            stars.forEach((s, i) => {
                s.classList.remove('hovered');
                s.classList.toggle('selected', i < selected);
            });
        });
        // Click chọn sao
        star.addEventListener('click', function() {
            selected = parseInt(this.dataset.value);
            ratingInput.value = selected;
            stars.forEach((s, i) => {
                s.classList.toggle('selected', i < selected);
            });
        });
    });

    // Nếu form submit mà chưa chọn sao thì báo lỗi nhẹ
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        if (!ratingInput.value) {
            alert('Vui lòng chọn số sao đánh giá!');
            e.preventDefault();
        }
    });
});
</script>
<!-- Popup thông báo thêm vào giỏ hàng -->
<div id="popup-cart-msg" class="popup-cart-msg">
    <span id="popup-cart-msg-text"></span>
    <button onclick="closeCartMsg()" class="popup-cart-close">&times;</button>
</div>
<script>
function showCartMsg(msg) {
    var box = document.getElementById('popup-cart-msg');
    var txt = document.getElementById('popup-cart-msg-text');
    txt.innerText = msg;
    box.style.display = 'block';
    setTimeout(function() { box.style.display = 'none'; }, 2500);
}
function closeCartMsg() {
    document.getElementById('popup-cart-msg').style.display = 'none';
}
// Chỉ hiện popup khi có thông báo thêm giỏ hàng (submit thành công)
<?php if ($cartMsg): ?>
    showCartMsg("<?= $cartMsg ?>");
<?php endif; ?>
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script>
function showLoginPopup() {
    if (document.getElementById('login-required-popup')) return;
    const popup = document.createElement('div');
    popup.id = 'login-required-popup';
    popup.innerHTML = `
        <div style="
            position: fixed; inset-block-start: 24px; inset-inline-end: 38px; z-index: 10001;
            background: #2196f3; color: #fff; padding: 23px 34px; border-radius: 13px;
            box-shadow: 0 8px 24px rgba(30,60,180,0.18);
            font-size: 18px; font-weight: 500; display: flex; align-items: center; gap: 18px;
            animation: popUpIn 0.3s cubic-bezier(.49,1.51,.67,.92);
        ">
            <span style="font-size:24px; margin-inline-end:12px;">&#128274;</span>
            <span>Bạn cần <b>đăng nhập</b> để thực hiện chức năng này!</span>
            <button id="btn-login-now" style="
                margin-inline-start: 20px; background: #fff; color: #1976d2;
                font-weight: 700; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; box-shadow: 0 2px 8px #1890ff30;">
                Đăng nhập ngay
            </button>
            <button onclick="document.getElementById('login-required-popup').remove()" style="
                margin-inline-start:10px; background: transparent; color: #fff; font-size: 22px; border: none; cursor:pointer;">
                &times;
            </button>
        </div>
        <style>
            @keyframes popUpIn {
                0% { opacity:0; transform:translateY(-30px) scale(0.8);}
                100% { opacity:1; transform:translateY(0) scale(1);}
            }
        </style>
    `;
    document.body.appendChild(popup);
    document.getElementById('btn-login-now').onclick = function () {
        window.location.href = '../../auth/login.php';
    };
}

// Mua ngay
document.getElementById('buy-now-form').onsubmit = function(e) {
    if (!IS_LOGGED_IN) {
        showLoginPopup();
        e.preventDefault();
        return false;
    }
};

document.getElementById('cart-form').onsubmit = function(e) {
    if (!IS_LOGGED_IN) {
        showLoginPopup();
        e.preventDefault();
        return false;
    }
};



// Thêm vào giỏ hàng
document.getElementById('btnAddCart').onclick = function() {
    if (!IS_LOGGED_IN) {
        showLoginPopup();
        return false;
    }
    document.getElementById('cart-buy-form').submit();
};

// Bình luận/đánh giá
document.getElementById('reviewForm').onsubmit = function(e) {
    if (!IS_LOGGED_IN) {
        showLoginPopup();
        e.preventDefault();
        return false;
    }
    if (!document.getElementById('ratingInput').value) {
        alert('Vui lòng chọn số sao đánh giá!');
        e.preventDefault();
        return false;
    }
};
</script>

</body>
</html>







