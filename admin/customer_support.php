<?php
require_once __DIR__ . '/module_tools.php';
render_module_page([
    'page' => 'customer_support.php',
    'permission' => 'customer_support',
    'title' => 'Ho tro khach hang',
    'description' => 'Quan ly ticket cham soc khach hang, yeu cau tu van va phan hoi sau mua hang.',
    'table' => 'support_tickets',
    'primary_key' => 'id',
    'fields' => [
        ['name' => 'customer_name', 'label' => 'Khach hang', 'required' => true],
        ['name' => 'contact_phone', 'label' => 'So dien thoai'],
        ['name' => 'topic', 'label' => 'Chu de', 'required' => true],
        ['name' => 'message', 'label' => 'Noi dung', 'type' => 'textarea', 'required' => true],
        ['name' => 'status', 'label' => 'Trang thai', 'type' => 'select', 'options' => ['open' => 'Moi', 'processing' => 'Dang xu ly', 'closed' => 'Da dong'], 'required' => true],
        ['name' => 'assigned_to', 'label' => 'Nguoi phu trach'],
    ],
    'list_columns' => ['customer_name' => 'Khach hang', 'topic' => 'Chu de', 'status' => 'Trang thai', 'assigned_to' => 'Phu trach', 'created_at' => 'Ngay tao'],
]);
?>





