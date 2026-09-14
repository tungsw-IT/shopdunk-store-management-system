<?php
require_once __DIR__ . '/module_tools.php';
render_module_page([
    'page' => 'system_management.php',
    'permission' => 'system_management',
    'title' => 'Quan ly he thong',
    'description' => 'Theo doi cac dau viec quan tri he thong, cau hinh va cong viec can xu ly trong noi bo.',
    'table' => 'system_tasks',
    'primary_key' => 'id',
    'fields' => [
        ['name' => 'title', 'label' => 'Tieu de', 'required' => true],
        ['name' => 'description', 'label' => 'Mo ta', 'type' => 'textarea', 'required' => true],
        ['name' => 'status', 'label' => 'Trang thai', 'type' => 'select', 'options' => ['pending' => 'Cho xu ly', 'in_progress' => 'Dang xu ly', 'done' => 'Hoan tat'], 'required' => true],
        ['name' => 'priority', 'label' => 'Muc uu tien', 'type' => 'select', 'options' => ['low' => 'Thap', 'medium' => 'Trung binh', 'high' => 'Cao'], 'required' => true],
        ['name' => 'owner_username', 'label' => 'Nguoi phu trach'],
    ],
    'list_columns' => ['title' => 'Tieu de', 'status' => 'Trang thai', 'priority' => 'Uu tien', 'owner_username' => 'Phu trach', 'updated_at' => 'Cap nhat'],
]);
?>





