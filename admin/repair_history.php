<?php
require_once __DIR__ . '/module_tools.php';
render_module_page([
    'page' => 'repair_history.php',
    'permission' => 'repair_history',
    'title' => 'Lich su sua chua',
    'description' => 'Tong hop cac phieu sua chua da hoan tat de doi ky thuat tra cuu nhanh.',
    'table' => 'repair_requests',
    'primary_key' => 'id',
    'fields' => [
        ['name' => 'customer_name', 'label' => 'Khach hang'],
    ],
    'list_columns' => ['customer_name' => 'Khach hang', 'device_model' => 'Thiet bi', 'issue_description' => 'Noi dung', 'technician_username' => 'Ky thuat vien', 'completed_date' => 'Ngay hoan tat'],
    'where' => "status = 'completed'",
    'order_by' => "completed_date DESC, id DESC",
    'read_only' => true,
]);
?>





