<?php
require_once __DIR__ . '/module_tools.php';
render_module_page([
    'page' => 'repair_management.php',
    'permission' => 'repair_management',
    'title' => 'Quan ly sua chua',
    'description' => 'Tiep nhan yeu cau sua chua, phan cong ky thuat vien va theo doi tien do.',
    'table' => 'repair_requests',
    'primary_key' => 'id',
    'fields' => [
        ['name' => 'customer_name', 'label' => 'Khach hang', 'required' => true],
        ['name' => 'contact_phone', 'label' => 'So dien thoai'],
        ['name' => 'device_model', 'label' => 'Thiet bi', 'required' => true],
        ['name' => 'issue_description', 'label' => 'Loi bao sua', 'type' => 'textarea', 'required' => true],
        ['name' => 'status', 'label' => 'Trang thai', 'type' => 'select', 'options' => ['pending' => 'Moi tiep nhan', 'in_progress' => 'Dang sua', 'completed' => 'Hoan tat'], 'required' => true],
        ['name' => 'technician_username', 'label' => 'Ky thuat vien'],
        ['name' => 'request_date', 'label' => 'Ngay nhan', 'type' => 'date', 'required' => true, 'default' => date('Y-m-d')],
        ['name' => 'completed_date', 'label' => 'Ngay hoan tat', 'type' => 'date'],
    ],
    'list_columns' => ['customer_name' => 'Khach hang', 'device_model' => 'Thiet bi', 'status' => 'Trang thai', 'technician_username' => 'Ky thuat vien', 'request_date' => 'Ngay nhan'],
]);
?>





