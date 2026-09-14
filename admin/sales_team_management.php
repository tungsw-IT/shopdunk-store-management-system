<?php
require_once __DIR__ . '/module_tools.php';
render_module_page([
    'page' => 'sales_team_management.php',
    'permission' => 'sales_team_management',
    'title' => 'Quan ly doi ngu ban hang',
    'description' => 'Phan cong dau viec cho tung nhom ban hang va theo doi tinh trang xu ly.',
    'table' => 'sales_team_tasks',
    'primary_key' => 'id',
    'fields' => [
        ['name' => 'title', 'label' => 'Nhiem vu', 'required' => true],
        ['name' => 'team_name', 'label' => 'Nhom ban hang', 'required' => true],
        ['name' => 'assignee_username', 'label' => 'Phu trach'],
        ['name' => 'note', 'label' => 'Ghi chu', 'type' => 'textarea'],
        ['name' => 'status', 'label' => 'Trang thai', 'type' => 'select', 'options' => ['open' => 'Moi giao', 'in_progress' => 'Dang xu ly', 'done' => 'Hoan tat'], 'required' => true],
    ],
    'list_columns' => ['title' => 'Nhiem vu', 'team_name' => 'Nhom', 'assignee_username' => 'Phu trach', 'status' => 'Trang thai', 'updated_at' => 'Cap nhat'],
]);
?>





