<?php require_once __DIR__ . '/../config/paths.php'; ?>
<footer class="footer">
  <div class="footer-container">
    <div class="footer-col logo-col">
      <div class="brand-badge">
        <img src="<?= app_url('images/logoshopdunk.png') ?>" alt="ShopDunk" height="54">
        <div class="badge-divider"></div>
        <div class="apple-badge">
          <div class="apple-icon"></div>
          <div class="apple-copy">
            <span>Authorised</span>
            <span>Reseller</span>
          </div>
        </div>
      </div>
      <p class="brand-story">
        Năm 2020, ShopDunk trở thành đại lý ủy quyền của Apple. Chúng tôi phát triển chuỗi cửa hàng tiêu chuẩn và Apple Mono Store nhằm mang đến trải nghiệm tốt nhất về sản phẩm và dịch vụ của Apple cho người dùng Việt Nam.
      </p>
      <div class="social-icons">
        <a href="https://www.facebook.com/shopdunk.store" target="_blank" rel="noopener noreferrer"><img src="<?= app_url('icons/facebook.svg') ?>" alt="Facebook"></a>
        <a href="https://www.tiktok.com/@shopdunk_apple" target="_blank" rel="noopener noreferrer"><img src="<?= app_url('icons/tiktok.svg') ?>" alt="TikTok"></a>
        <a href="https://www.tiktok.com/@shopdunk_apple" target="_blank" rel="noopener noreferrer"><img src="<?= app_url('icons/youtube.svg') ?>" alt="TikTok"></a>
        <a href="https://www.instagram.com/_shopdunk/" target="_blank" rel="noopener noreferrer"><img src="<?= app_url('icons/instagram.svg') ?>" alt="Instagram"></a>
      </div>
    </div>

    <div class="footer-col">
      <h4>Liên hệ</h4>
      <p>Hotline: 0909 123 456 (6h - 24h)</p>
      <p>Zalo hỗ trợ: 0909 123 456 (8h - 21h)</p>
      <p>Email: cskh@shopdunk.vn</p>
    </div>

    <div class="footer-col">
      <h4>Về ShopDunk</h4>
      <p><a href="<?= app_url('index.php') ?>">Trang chủ</a></p>
    </div>

    <div class="footer-col">
      <h4>Sản phẩm</h4>
      <p><a href="<?= app_url('index.php') ?>#flashsale">Sản phẩm khuyến mãi</a></p>
      <p><a href="<?= app_url('products/categories/Alliphone.php') ?>">iPhone</a></p>
      <p><a href="<?= app_url('products/categories/ipad.php') ?>">iPad</a></p>
      <p><a href="<?= app_url('products/categories/mac.php') ?>">Mac</a></p>
      <p><a href="<?= app_url('products/categories/watch.php') ?>">Watch</a></p>
    </div>
  </div>
  <style>
    .footer {
      background: #fff;
      padding: 40px 30px;
      border-top: 1px solid #ccc;
      color: #333;
    }

    .footer-container {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      max-width: 1200px;
      margin: auto;
      gap: 30px;
    }

    .footer-col {
      flex: 1 1 160px;
      min-width: 150px;
    }

    .logo-col {
      flex: 1.6 1 330px;
    }

    .footer-col h4 {
      font-size: 16px;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .footer-col p,
    .footer-col a {
      font-size: 14px;
      margin: 4px 0;
      color: #333;
      text-decoration: none;
    }

    .brand-badge {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 18px;
    }

    .badge-divider {
      width: 1px;
      height: 42px;
      background: #cfcfcf;
    }

    .apple-badge {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #111;
    }

    .apple-icon {
      font-size: 30px;
      line-height: 1;
    }

    .apple-copy {
      display: flex;
      flex-direction: column;
      font-weight: 600;
      line-height: 1.1;
      font-size: 14px;
    }

    .brand-story {
      max-width: 420px;
      margin: 0 0 18px;
      font-size: 16px;
      line-height: 1.7;
    }

    .social-icons a {
      margin-right: 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 34px;
      height: 34px;
      border-radius: 50%;
      border: 1px solid #d8d8d8;
    }

    .social-icons img {
      width: 20px;
      height: 20px;
    }

    @media (max-width: 768px) {
      .footer {
        padding: 30px 15px;
      }

      .footer-col {
        flex: 1 1 100%;
      }

      .footer-col h4 {
        font-size: 15px;
      }

      .footer-col p,
      .footer-col a {
        font-size: 13px;
      }

      .brand-badge {
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 10px;
      }

      .badge-divider {
        display: none;
      }

      .brand-story {
        max-width: 100%;
        font-size: 14px;
      }

      .brand-badge img {
        height: 30px;
      }

      .social-icons img {
        width: 18px;
        height: 18px;
      }
    }
  </style>
</footer>






