<?php
session_start();
require_once __DIR__ . '/data.php';
$page     = $_GET['page']     ?? 'home';
$category = $_GET['category'] ?? '';
if ($category) {
    $filtered = array_filter($data, fn($p) => $p['category'] === urldecode($category));
} else {
    $filtered = $data;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="../assets/css/blog.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?> 
    <div class="container">
        <!-- Phần tìm kiếm, bộ lọc -->
        <section class="tittle">
            <div class="tittle-content">
                <h1>Blog</h1>
                <p>Home / Blog</p>
            </div>
        </section>

        <div class="header-nav">
            <section class="blog-toolbar">
                <div class="filter-btn">
                    <img src="../icons/filter.png" alt="Lọc" style="width:20px;height:20px;">
                    <span>Lọc</span>
                </div>
                <div class="search-box">
                    <img src="../icons/search.svg" alt="Tìm kiếm" style="width:20px;height:20px;">
                    <input type="text" placeholder="Tìm kiếm">
                </div>
                <div class="sort-box">
                    <select>
                        <option selected>Đề xuất</option>
                        <option>Mới nhất</option>
                        <option>Phổ biến</option>
                    </select>
                </div>
            </section>
        </div>

        <!-- Thanh phân loại với poster-->
        <div class="content-layout">
            <aside class="sidebar">
            <h3>Phân loại</h3>
            <ul>
                <li><a href="?page=home">Tất cả</a></li>
                <li><a href="?page=home&category=So sánh">So sánh (1)</a></li>
                <li><a href="?page=home&category=Khuyến mãi">Khuyến mãi (2)</a></li>
                <li><a href="?page=home&category=Hướng dẫn">Hướng dẫn (2)</a></li>
                <li><a href="?page=home&category=Sản phẩm mới">Sản phẩm mới (3)</a></li>
            </ul>
            </aside>
            <div class="posts">
                <?php include __DIR__ . '/content.php';?>
            </div>
        </div>

        <!-- 1. Promotion Slider -->
        <section class="promo-slider">
        <div class="slider-window">
            <div class="slider-track">
            <div class="slide"><img src="../images/promo2.jpg" alt="Deal 3"></div>
            </div>
        </div>
        <div class="slider-nav">
            <button class="prev">&lsaquo;</button>
            <ul class="dots">
            <li class="active">1</li>
            <li>2</li>
            <li>3</li>
            </ul>
            <button class="next">&rsaquo;</button>
        </div>
        </section>

        <!-- 2. Follow on Instagram -->
        <section class="instagram-section">
        <h2>Theo dõi chúng tôi trên Instagram</h2>
        <div class="insta-grid">
            <a href="#"><img src="../images/insta1.jpg" alt=""></a>
            <a href="#"><img src="../images/insta2.jpg" alt=""></a>
            <a href="#"><img src="../images/insta3.jpg" alt=""></a>
        </div>
        <a href="#" class="insta-icon"><img src="../images/instagram.png" alt=""></a>
        </section>

        <!-- 3. Newsletter -->
        <section class="newsletter">
        <h2>Đăng kí để nhận thông tin của chúng tôi</h2>
        <form class="subscribe-form" action="#" method="post">
            <div class="input-group">
                <img src="../icons/mail.png" class="icon" alt="Email" style="width:20px;height:20px;">
                <input type="email" name="email" placeholder="Địa chỉ email" required>
            </div>
            <button type="submit">Đăng kí</button>
        </form>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?> 
    </div>

    <!-- Giúp thanh trượt slide chuyển động -->
    <script>
    (()=>{
        const track = document.querySelector('.slider-track');
        const dots  = document.querySelectorAll('.slider-nav .dots li');
        const prev  = document.querySelector('.slider-nav .prev');
        const next  = document.querySelector('.slider-nav .next');
        let idx = 0;

        function goTo(i){
        idx = (i + dots.length) % dots.length;
        track.style.transform = `translateX(-${100*idx}%)`;
        dots.forEach((d,j)=> d.classList.toggle('active', j===idx));
        }

        dots.forEach((d,i)=> d.addEventListener('click', ()=> goTo(i)));
        prev.addEventListener('click', ()=> goTo(idx-1));
        next.addEventListener('click', ()=> goTo(idx+1));
    })();
</script> 
 
</body>
</html>






