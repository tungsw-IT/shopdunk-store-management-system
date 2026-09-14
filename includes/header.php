<?php require_once __DIR__ . '/../config/paths.php'; ?>
<header>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }


    body {
      font-family: system-ui, sans-serif;
    }


    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 30px;
      background: #fff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }


    .logo img {
      height: 52px;
      width: auto;
      display: block;
    }


    .nav-links {
      display: flex;
      gap: 15px;
    }


    .nav-links a {
      text-decoration: none;
      color: #333;
      font-weight: 400;
      padding: 10px;
    }


    .actions {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
      justify-content: flex-end;
    }


    .icon-button.white {
      background: #fff;
      border: 1px solid #d1d5db;
      border-radius: 12px;
      width: 44px;
      height: 44px;
      display: inline-flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      padding: 0;
      transition: transform .2s ease, border-color .2s ease, background .2s ease;
    }


    .icon-button.white:hover {
      background: #f8fafc;
      border-color: #9ca3af;
      transform: translateY(-1px);
    }


    .icon-button.white img {
      width: 20px;
      height: 20px;
    }


    .login-button {
      background: #000;
      color: #fff;
      padding: 10px 18px;
      border-radius: 9999px;
      border: none;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.16);
      white-space: nowrap;
      min-height: 44px;
    }


    .hamburger {
      display: none;
    }


    .search-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.95);
      z-index: 9999;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }


    .search-overlay.active {
      display: flex;
    }


    .search-overlay input {
      width: 90%;
      max-width: 400px;
      padding: 15px 20px;
      font-size: 16px;
      border: none;
      border-radius: 30px;
      background-color: #1c1c1e;
      color: #fff;
      outline: none;
      background-image: url('<?= app_url('icons/search.svg') ?>');
      background-repeat: no-repeat;
      background-position: 15px center;
      background-size: 20px;
      padding-left: 50px;
    }


    .search-overlay input::placeholder {
      color: #aaa;
    }


    .search-overlay .close-btn {
      color: #fff;
      font-size: 24px;
      position: absolute;
      top: 20px;
      right: 20px;
      cursor: pointer;
    }


    .search-overlay form {
      width: 100%;
      display: flex;
      justify-content: center;
    }


   
   
  </style>


  <!-- Hamburger -->
  <div class="hamburger" onclick="toggleMenu()">☰</div>


  <!-- Logo -->
  <div class="logo">
    <a href="<?= app_url('index.php') ?>">
      <img src="<?= app_url('images/logoshopdunk.png') ?>" alt="ShopDunk Logo">
    </a>
  </div>


  <!-- Navigation -->
  <nav class="nav-links">
    <a href="<?= app_url('products/categories/Alliphone.php') ?>">iPhone</a>
    <a href="<?= app_url('products/categories/ipad.php') ?>">iPad</a>
    <a href="<?= app_url('products/categories/mac.php') ?>">Mac</a>
    <a href="<?= app_url('products/categories/watch.php') ?>">Watch</a>
    <a href="<?= app_url('customer/repair_request.php') ?>">Sửa chữa</a>
    <a href="<?= app_url('pages/blog.php') ?>">Blog</a>
  </nav>


  <!-- Action Buttons -->
  <div class="actions">
    <!-- Search -->
    <button class="icon-button white" onclick="openSearch()">
      <img src="<?= app_url('icons/search.svg') ?>" alt="Search" />
    </button>


  <!-- Cart -->
<button class="icon-button white" onclick="checkLogin('<?= app_url('cart/cart.php') ?>')">
  <img src="<?= app_url('icons/cart.svg') ?>" alt="Cart" />
</button>


<!-- Favourite -->
<button class="icon-button white" onclick="checkLogin('<?= app_url('cart/wishlist.php') ?>')">
  <img src="<?= app_url('icons/heart.svg') ?>" alt="Yêu thích" />
</button>


<!-- Nút Thông tin cá nhân với kiểm tra đăng nhập -->
<button class="icon-button white" onclick="checkLogin('<?= app_url('customer/profile.php') ?>')">
  <img src="<?= app_url('icons/user.svg') ?>" alt="Thông tin cá nhân" />
</button>






    <!-- Đăng nhập / Đăng xuất -->
    <span id="authAction">
      <button class="login-button" id="loginBtn" type="button">Đăng nhập</button>
    </span>
  </div>

  <script>
    window.API_BASE_URL = window.API_BASE_URL || (window.location.origin + <?= json_encode(app_base_url()) ?>);

    async function refreshAuthState() {
      const authAction = document.getElementById('authAction');
      const loginBtn = document.getElementById('loginBtn');
      if (!authAction || !loginBtn) return;

      try {
        const response = await fetch(`${window.API_BASE_URL}/auth/api/me.php`, {
          mode: 'cors',
          credentials: 'include'
        });
        const data = await response.json();
        if (data.success && data.logged_in) {
          authAction.innerHTML = `<button class="login-button" id="logoutBtn" type="button">Đăng xuất</button>`;
          document.getElementById('logoutBtn')?.addEventListener('click', logout);
          return;
        }
      } catch (error) {
        // Ignore and show login.
      }

      authAction.innerHTML = `<button class="login-button" id="loginBtn" type="button">Đăng nhập</button>`;
      document.getElementById('loginBtn')?.addEventListener('click', () => window.location.href = '<?= app_url('auth/login.php') ?>');
    }

    async function logout() {
      try {
        await fetch(`${window.API_BASE_URL}/auth/api/logout.php`, {
          method: 'POST',
          mode: 'cors',
          credentials: 'include'
        });
      } catch (error) {
        // ignore
      }
      window.location.reload();
    }

    async function checkLogin(targetUrl) {
      try {
        const response = await fetch(`${window.API_BASE_URL}/auth/api/me.php`, {
          mode: 'cors',
          credentials: 'include'
        });
        const data = await response.json();
        if (data.success && data.logged_in) {
          window.location.href = targetUrl;
          return;
        }
      } catch (error) {
        // ignore
      }
      showLoginPopup();
    }

    document.addEventListener('DOMContentLoaded', refreshAuthState);

    function toggleMenu() {
      const nav = document.querySelector('.nav-links');
      nav.classList.toggle('show');
    }


    function openSearch() {
      document.getElementById("searchOverlay").classList.add("active");
    }


    function closeSearch() {
      document.getElementById("searchOverlay").classList.remove("active");
    }


    document.addEventListener('keydown', function (event) {
      if (event.key === "Escape") closeSearch();
    });
  </script>
  <script>
  function showLoginPopup() {
    const popup = document.createElement('div');
    popup.id = 'loginPopup';
    popup.style.position = 'fixed';
    popup.style.top = '0';
    popup.style.left = '0';
    popup.style.width = '100%';
    popup.style.height = '100%';
    popup.style.background = 'rgba(0,0,0,0.7)';
    popup.style.display = 'flex';
    popup.style.justifyContent = 'center';
    popup.style.alignItems = 'center';
    popup.style.zIndex = '9999';


    popup.innerHTML = `
      <div style="background:#fff; padding:30px 40px; border-radius:10px; text-align:center; max-width:300px;">
        <h3 style="margin-bottom:15px;">Bạn cần đăng nhập</h3>
        <p style="margin-bottom:20px;">Vui lòng đăng nhập để tiếp tục.</p>
        <button onclick="window.location.href='<?= app_url('auth/login.php') ?>'" style="padding:10px 20px; background:#000; color:#fff; border:none; border-radius:6px; cursor:pointer;">Đăng nhập</button>
        <div style="margin-top:15px;">
          <button onclick="closeLoginPopup()" style="background:none; border:none; color:#888; cursor:pointer;">Đóng</button>
        </div>
      </div>
    `;
    document.body.appendChild(popup);
  }


  function closeLoginPopup() {
    const popup = document.getElementById('loginPopup');
    if (popup) popup.remove();
  }
</script>


</header>


<!-- Search Overlay -->
<div id="searchOverlay" class="search-overlay">
  <span class="close-btn" onclick="closeSearch()">×</span>
  <form action="<?= app_url('pages/search.php') ?>" method="get">
    <input type="text" name="query" placeholder="Tìm kiếm sản phẩm..." required>
  </form>
</div>









