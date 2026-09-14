<?php
require_once __DIR__ . '/auth.php';

function module_table_exists(mysqli $conn, string $table): bool
{
    $escaped = mysqli_real_escape_string($conn, $table);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '{$escaped}'");

    return $result instanceof mysqli_result && $result->num_rows > 0;
}

function module_handle_crud(array $config): array
{
    global $conn;

    $table = $config['table'];
    $primaryKey = $config['primary_key'] ?? 'id';
    $fields = $config['fields'];
    $redirect = $config['page'];
    $editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
    $editRecord = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        if ($action === 'create' || $action === 'update') {
            $values = [];
            foreach ($fields as $field) {
                $name = $field['name'];
                $value = $_POST[$name] ?? ($field['default'] ?? '');
                if (($field['type'] ?? 'text') === 'number') {
                    $value = (string) ((int) $value);
                } else {
                    $value = trim((string) $value);
                }

                if (($field['required'] ?? false) && $value === '') {
                    set_flash('error', 'Vui long nhap day du thong tin.');
                    header("Location: {$redirect}" . ($action === 'update' ? '?edit=' . (int) ($_POST[$primaryKey] ?? 0) : ''));
                    exit;
                }

                $values[$name] = $value;
            }

            if ($action === 'create') {
                $columns = array_keys($values);
                $placeholders = implode(', ', array_fill(0, count($columns), '?'));
                $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES ({$placeholders})";
                $stmt = $conn->prepare($sql);
                $types = str_repeat('s', count($columns));
                $stmt->bind_param($types, ...array_values($values));
                $ok = $stmt->execute();
                $stmt->close();

                set_flash($ok ? 'success' : 'error', $ok ? 'Da tao ban ghi moi.' : 'Khong the tao ban ghi.');
                header("Location: {$redirect}");
                exit;
            }

            $recordId = (int) ($_POST[$primaryKey] ?? 0);
            $setParts = [];
            foreach (array_keys($values) as $column) {
                $setParts[] = "{$column} = ?";
            }
            $sql = "UPDATE {$table} SET " . implode(', ', $setParts) . " WHERE {$primaryKey} = ?";
            $stmt = $conn->prepare($sql);
            $bindValues = array_values($values);
            $bindValues[] = $recordId;
            $types = str_repeat('s', count($values)) . 'i';
            $stmt->bind_param($types, ...$bindValues);
            $ok = $stmt->execute();
            $stmt->close();

            set_flash($ok ? 'success' : 'error', $ok ? 'Da cap nhat ban ghi.' : 'Khong the cap nhat ban ghi.');
            header("Location: {$redirect}?edit={$recordId}");
            exit;
        }

        if ($action === 'delete') {
            $recordId = (int) ($_POST[$primaryKey] ?? 0);
            $stmt = $conn->prepare("DELETE FROM {$table} WHERE {$primaryKey} = ?");
            $stmt->bind_param('i', $recordId);
            $ok = $stmt->execute();
            $stmt->close();

            set_flash($ok ? 'success' : 'error', $ok ? 'Da xoa ban ghi.' : 'Khong the xoa ban ghi.');
            header("Location: {$redirect}");
            exit;
        }
    }

    if ($editId > 0) {
        $stmt = $conn->prepare("SELECT * FROM {$table} WHERE {$primaryKey} = ? LIMIT 1");
        $stmt->bind_param('i', $editId);
        $stmt->execute();
        $editRecord = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }

    $where = $config['where'] ?? '1=1';
    $orderBy = $config['order_by'] ?? "{$primaryKey} DESC";
    $rows = mysqli_query($conn, "SELECT * FROM {$table} WHERE {$where} ORDER BY {$orderBy}");

    return [$editRecord, $rows];
}

function render_module_page(array $config): void
{
    $currentAdmin = require_permission($config['permission']);
    [$editRecord, $rows] = module_handle_crud($config);
    $flash = get_flash();
    require_once __DIR__ . '/sidebar.php';
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
      <meta charset="UTF-8">
      <title><?= htmlspecialchars($config['title']) ?></title>
      <style>
        body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif; background: #f4f7fb; }
        .main-content { margin-left: 220px; padding: 28px 36px; }
        .layout { display: grid; grid-template-columns: 1.05fr 1.6fr; gap: 20px; align-items: start; }
        .card { background: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06); border: 1px solid #dbe6f4; }
        h1, h2 { margin-top: 0; color: #0f172a; }
        p { color: #475569; line-height: 1.5; }
        label { display: block; margin: 14px 0 6px; color: #334155; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; box-sizing: border-box; font-size: 14px; }
        textarea { min-height: 110px; resize: vertical; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; vertical-align: top; }
        th { color: #334155; font-size: 14px; }
        td { color: #475569; }
        .toolbar { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 18px; }
        .btn, button { border: none; border-radius: 10px; padding: 10px 14px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-secondary { background: #e2e8f0; color: #0f172a; }
        .btn-danger { background: #dc2626; color: #fff; }
        .flash { margin-bottom: 18px; padding: 14px 16px; border-radius: 12px; font-weight: 600; }
        .flash.success { background: #dcfce7; color: #166534; }
        .flash.error { background: #fee2e2; color: #991b1b; }
        .pill { display: inline-block; padding: 6px 10px; border-radius: 999px; background: #e0f2fe; color: #0369a1; font-size: 12px; font-weight: 700; }
        @media (max-width: 1100px) { .layout { grid-template-columns: 1fr; } .main-content { margin-left: 0; padding: 20px; } }
      </style>
    </head>
    <body>
      <div class="main-content">
        <?php if ($flash): ?>
          <div class="flash <?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>
        <div class="layout">
          <div class="card">
            <h1><?= htmlspecialchars($config['title']) ?></h1>
            <p><?= htmlspecialchars($config['description']) ?></p>
            <?php if (!($config['read_only'] ?? false)): ?>
            <form method="post">
              <input type="hidden" name="action" value="<?= $editRecord ? 'update' : 'create' ?>">
              <?php if ($editRecord): ?>
                <input type="hidden" name="<?= htmlspecialchars($config['primary_key'] ?? 'id') ?>" value="<?= (int) $editRecord[$config['primary_key'] ?? 'id'] ?>">
              <?php endif; ?>
              <?php foreach ($config['fields'] as $field): ?>
                <?php
                $name = $field['name'];
                $type = $field['type'] ?? 'text';
                $value = $editRecord[$name] ?? ($field['default'] ?? '');
                ?>
                <label for="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($field['label']) ?></label>
                <?php if ($type === 'textarea'): ?>
                  <textarea id="<?= htmlspecialchars($name) ?>" name="<?= htmlspecialchars($name) ?>" <?= ($field['required'] ?? false) ? 'required' : '' ?>><?= htmlspecialchars((string) $value) ?></textarea>
                <?php elseif ($type === 'select'): ?>
                  <select id="<?= htmlspecialchars($name) ?>" name="<?= htmlspecialchars($name) ?>" <?= ($field['required'] ?? false) ? 'required' : '' ?>>
                    <?php foreach (($field['options'] ?? []) as $optionValue => $optionLabel): ?>
                      <option value="<?= htmlspecialchars((string) $optionValue) ?>" <?= (string) $value === (string) $optionValue ? 'selected' : '' ?>><?= htmlspecialchars((string) $optionLabel) ?></option>
                    <?php endforeach; ?>
                  </select>
                <?php else: ?>
                  <input id="<?= htmlspecialchars($name) ?>" type="<?= htmlspecialchars($type) ?>" name="<?= htmlspecialchars($name) ?>" value="<?= htmlspecialchars((string) $value) ?>" <?= ($field['required'] ?? false) ? 'required' : '' ?>>
                <?php endif; ?>
              <?php endforeach; ?>
              <div class="toolbar">
                <button class="btn-primary" type="submit"><?= $editRecord ? 'Luu thay doi' : 'Tao moi' ?></button>
                <?php if ($editRecord): ?>
                  <a class="btn btn-secondary" href="<?= htmlspecialchars($config['page']) ?>">Dong</a>
                <?php endif; ?>
              </div>
            </form>
            <?php else: ?>
              <div class="pill">Module chi xem du lieu lich su</div>
            <?php endif; ?>
          </div>
          <div class="card">
            <h2>Danh sach</h2>
            <table>
              <thead>
                <tr>
                  <?php foreach ($config['list_columns'] as $column => $label): ?>
                    <th><?= htmlspecialchars($label) ?></th>
                  <?php endforeach; ?>
                  <th>Thao tac</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $rows->fetch_assoc()): ?>
                  <tr>
                    <?php foreach ($config['list_columns'] as $column => $label): ?>
                      <td>
                        <?php if (isset($config['formatters'][$column])): ?>
                          <?= $config['formatters'][$column]($row[$column] ?? '', $row) ?>
                        <?php else: ?>
                          <?= htmlspecialchars((string) ($row[$column] ?? '')) ?>
                        <?php endif; ?>
                      </td>
                    <?php endforeach; ?>
                    <td>
                      <div class="toolbar" style="margin-top:0;">
                        <?php if (!($config['read_only'] ?? false)): ?>
                          <a class="btn btn-secondary" href="<?= htmlspecialchars($config['page']) ?>?edit=<?= (int) $row[$config['primary_key'] ?? 'id'] ?>">Sua</a>
                          <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="<?= htmlspecialchars($config['primary_key'] ?? 'id') ?>" value="<?= (int) $row[$config['primary_key'] ?? 'id'] ?>">
                            <button class="btn-danger" type="submit">Xoa</button>
                          </form>
                        <?php else: ?>
                          <span class="pill">Chi xem</span>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </body>
    </html>
    <?php
}
?>





