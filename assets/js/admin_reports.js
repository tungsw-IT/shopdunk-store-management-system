(() => {
  const yearSelect = document.getElementById('yearSelect');
  const brandSelect = document.getElementById('brandSelect');
  const charts = new Map();
  const requests = new Map();
  const numberFormat = new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 0 });

  async function fetchData(type, filters = {}) {
    const query = new URLSearchParams({ type, ...filters });
    const response = await fetch(`chart_api.php?${query}`, { credentials: 'same-origin' });
    if (!response.ok || response.redirected) throw new Error('Report request failed');
    const data = await response.json();
    if (!Array.isArray(data)) throw new Error('Invalid report response');
    return data;
  }

  function renderTable(id, labels, values, heading) {
    const table = document.getElementById(`${id}Table`);
    table.replaceChildren();
    const header = table.createTHead().insertRow();
    for (const label of [heading, id === 'revenueChart' ? 'Giá trị (VNĐ)' : 'Số lượng']) {
      const cell = document.createElement('th');
      cell.scope = 'col';
      cell.textContent = label;
      header.appendChild(cell);
    }
    const body = table.createTBody();
    labels.forEach((label, index) => {
      const row = body.insertRow();
      row.insertCell().textContent = label;
      row.insertCell().textContent = numberFormat.format(values[index]);
    });
  }

  async function loadChart(id, type, filters, labelFor, valueKey, heading, chartType = 'bar') {
    const request = (requests.get(id) || 0) + 1;
    requests.set(id, request);
    const status = document.getElementById(`${id}Status`);
    const canvas = document.getElementById(id);
    charts.get(id)?.destroy();
    charts.delete(id);
    canvas.parentElement.hidden = true;
    document.getElementById(`${id}Table`).replaceChildren();
    status.className = 'chart-status';
    status.textContent = 'Đang tải dữ liệu...';
    try {
      const rows = await fetchData(type, filters);
      if (requests.get(id) !== request) return;
      const labels = rows.map(labelFor);
      const values = rows.map(row => Number(row[valueKey]) || 0);
      renderTable(id, labels, values, heading);
      status.textContent = rows.length ? '' : 'Chưa có dữ liệu phù hợp với bộ lọc.';
      // Tables also work when the chart library cannot load.
      if (!rows.length || typeof Chart === 'undefined') return;
      canvas.parentElement.hidden = false;
      const options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: chartType === 'pie' } }
      };
      if (chartType === 'bar') options.scales = { y: { beginAtZero: true, ticks: { precision: 0 } } };
      charts.set(id, new Chart(canvas, {
        type: chartType,
        data: {
          labels,
          datasets: [{
            label: id === 'revenueChart' ? 'Giá trị hóa đơn (VNĐ)' : 'Số lượng',
            data: values,
            backgroundColor: chartType === 'pie'
              ? ['#3b82f6', '#ec4899', '#94a3b8', '#8b5cf6']
              : id === 'revenueChart' ? '#3b82f6' : '#f59e0b'
          }]
        },
        options
      }));
    } catch (error) {
      if (requests.get(id) !== request) return;
      status.className = 'chart-status error';
      status.textContent = 'Không tải được báo cáo. Vui lòng đăng nhập lại hoặc tải lại trang.';
    }
  }

  function updateInvoices() {
    const filters = { year: yearSelect.value, brand: brandSelect.value };
    loadChart('revenueChart', 'revenue', filters, row => row.brand || 'Chưa cập nhật', 'revenue', 'Hãng');
    loadChart('soldChart', 'sold_by_brand', filters, row => row.brand || 'Chưa cập nhật', 'sold', 'Hãng');
  }

  async function initializeFilters() {
    yearSelect.disabled = true;
    brandSelect.disabled = true;
    try {
      const [years, brands] = await Promise.all([fetchData('years'), fetchData('brands')]);
      for (const year of years) yearSelect.add(new Option(String(year), String(year)));
      for (const brand of brands) brandSelect.add(new Option(brand, brand));
      yearSelect.disabled = false;
      brandSelect.disabled = false;
      updateInvoices();
    } catch (error) {
      for (const id of ['revenueChart', 'soldChart']) {
        const status = document.getElementById(`${id}Status`);
        status.className = 'chart-status error';
        status.textContent = 'Không tải được bộ lọc báo cáo. Vui lòng tải lại trang.';
      }
    }
  }

  yearSelect.addEventListener('change', updateInvoices);
  brandSelect.addEventListener('change', updateInvoices);
  initializeFilters();
  loadChart('genderChart', 'gender', {}, row => {
    const gender = String(row.gender || '').toLowerCase();
    return ({ male: 'Nam', female: 'Nữ', other: 'Khác' })[gender] || row.gender || 'Chưa cập nhật';
  }, 'count', 'Giới tính', 'pie');
  loadChart('reviewChart', 'reviews', {}, row => `${row.rating} sao`, 'count', 'Đánh giá');
})();
