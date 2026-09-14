<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Danh mục sản phẩm | ShopDunk</title>
  <style>
    body { margin: 0; font-family: Arial, sans-serif; background: #f4f6fa; color: #111; }
    .category-page { max-width: 1320px; margin: 0 auto; padding: 24px 18px 40px; }
    .category-header { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px; margin-bottom: 16px; }
    .category-header h1 { margin: 0; font-size: 28px; }
    .category-description { margin: 0; color: #555; font-size: 16px; }
    .category-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; }
    .category-card { background: #fff; border: 1px solid #e8edf5; border-radius: 20px; padding: 18px; text-decoration: none; color: inherit; box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08); transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .category-card:hover { transform: translateY(-4px); box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12); }
    .category-card img { width: 100%; height: 240px; object-fit: contain; border-radius: 16px; background: #f8f9fd; }
    .category-card h2 { margin: 18px 0 10px; font-size: 18px; }
    .category-card .price { color: #0a65cc; font-weight: 700; margin-top: 6px; }
    .empty-state { padding: 20px; background: #fff; border-radius: 18px; text-align: center; color: #54616f; font-size: 16px; }
    @media (max-width: 940px) { .category-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 620px) { .category-grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<div class="category-page">
  <div class="category-header">
    <div>
      <h1 id="categoryTitle">Danh mục sản phẩm</h1>
      <p id="categorySubtitle" class="category-description">Tìm các sản phẩm phù hợp với nhu cầu của bạn.</p>
    </div>
  </div>
  <div id="categoryContent">
    <div class="empty-state">Đang tải sản phẩm...</div>
  </div>
</div>
<script>
  window.API_BASE_URL = window.API_BASE_URL || (window.location.origin + <?= json_encode(app_base_url()) ?>);
  const searchParams = new URLSearchParams(window.location.search);
  const companyName = searchParams.get('company_name') || '';
  const companySeries = searchParams.get('company_series') || '';
  const query = searchParams.get('query') || '';

  const titleParts = [];
  if (companyName) titleParts.push(companyName);
  if (companySeries) titleParts.push(companySeries);
  document.getElementById('categoryTitle').textContent = titleParts.length ? titleParts.join(' ') : 'Tất cả sản phẩm';
  if (query) {
    document.getElementById('categorySubtitle').textContent = `Kết quả tìm kiếm cho “${query}”`;
  } else if (companyName || companySeries) {
    document.getElementById('categorySubtitle').textContent = 'Danh sách sản phẩm được lấy từ backend.';
  }

  const categoryContent = document.getElementById('categoryContent');

  function productImage(product) {
    const brand = (product.company_name || '').toLowerCase();
    const folder = brand === 'samsung' ? '../../images/' : '../../images/';
    const base = (product.company_series || '').replace(/\s|\+|-/g, '').toLowerCase() + '_' +
                 (product.model_no || '').replace(/\s|\+|-/g, '').toLowerCase();
    const png = folder + base + '.png';
    const jpg = folder + base + '.jpg';
    if (document.querySelector(`img[src="${png}"]`) || document.querySelector(`img[src="${jpg}"]`)) {
      return png;
    }
    return folder + 'no-image.png';
  }

  function renderProducts(products) {
    if (!products || products.length === 0) {
      categoryContent.innerHTML = '<div class="empty-state">Không tìm thấy sản phẩm nào.</div>';
      return;
    }
    const grid = document.createElement('div');
    grid.className = 'category-grid';
    products.forEach(product => {
      const link = document.createElement('a');
      link.href = `../detail/product-detail.php?model_no=${encodeURIComponent(product.model_no)}`;
      link.className = 'category-card';
      link.innerHTML = `
        <img src="${productImage(product)}" alt="${(product.company_series || '') + ' ' + (product.model_no || '')}" />
        <h2>${product.company_series || ''} ${product.model_no || ''}</h2>
        <div class="price">${Number(product.price || 0).toLocaleString('vi-VN')} đ</div>
      `;
      grid.appendChild(link);
    });
    categoryContent.innerHTML = '';
    categoryContent.appendChild(grid);
  }

  async function loadCategory() {
    let url = `${window.API_BASE_URL}/products/api/products.php?limit=100`;
    if (query) {
      url += `&search=${encodeURIComponent(query)}`;
    }
    if (companyName) {
      url += `&company_name=${encodeURIComponent(companyName)}`;
    }
    if (companySeries) {
      url += `&company_series=${encodeURIComponent(companySeries)}`;
    }

    try {
      const response = await fetch(url, { credentials: 'include' });
      const result = await response.json();
      if (result.success && Array.isArray(result.data)) {
        renderProducts(result.data);
      } else {
        categoryContent.innerHTML = '<div class="empty-state">Lỗi khi tải dữ liệu.</div>';
      }
    } catch (error) {
      categoryContent.innerHTML = '<div class="empty-state">Không thể kết nối đến máy chủ.</div>';
    }
  }

  loadCategory();
</script>
</body>
</html>








