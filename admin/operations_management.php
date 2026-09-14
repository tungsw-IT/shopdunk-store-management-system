<?php
require_once __DIR__ . '/module_tools.php';
render_module_page([
    'page' => 'operations_management.php',
    'permission' => 'operations_management',
    'title' => 'Quan ly van hanh',
    'description' => 'Lap va theo doi cac cong viec van hanh giua cac bo phan trong cua hang.',
    'table' => 'operations_tasks',
    'primary_key' => 'id',
    'fields' => [
        ['name' => 'title', 'label' => 'Cong viec', 'required' => true],
        ['name' => 'description', 'label' => 'Mo ta', 'type' => 'textarea', 'required' => true],
        ['name' => 'status', 'label' => 'Trang thai', 'type' => 'select', 'options' => ['pending' => 'Cho xu ly', 'in_progress' => 'Dang xu ly', 'done' => 'Hoan tat'], 'required' => true],
        ['name' => 'assigned_role', 'label' => 'Bo phan phu trach', 'type' => 'select', 'options' => ['quan_ly' => 'Quan ly', 'ky_thuat_vien' => 'Ky thuat vien', 'thu_ngan_cskh' => 'Thu ngan - CSKH', 'nhan_vien_ban_hang' => 'Nhan vien ban hang', 'to_truong_ban_hang' => 'To truong ban hang'], 'required' => true],
        ['name' => 'due_date', 'label' => 'Han xu ly', 'type' => 'date'],
    ],
    'list_columns' => ['title' => 'Cong viec', 'assigned_role' => 'Bo phan', 'status' => 'Trang thai', 'due_date' => 'Han xu ly', 'updated_at' => 'Cap nhat'],
    'formatters' => [
        'assigned_role' => fn ($value) => htmlspecialchars(role_label($value)),
    ],
]);
?>





