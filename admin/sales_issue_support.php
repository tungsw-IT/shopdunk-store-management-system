<?php
require_once __DIR__ . '/module_tools.php';
render_module_page([
    'page' => 'sales_issue_support.php',
    'permission' => 'sales_issue_support',
    'title' => 'Ho tro su co ban hang',
    'description' => 'Ghi nhan va xu ly cac su co tai quay ban hang, giao nham may, loi thanh toan hoac khiem khuyet quy trinh.',
    'table' => 'sales_incidents',
    'primary_key' => 'id',
    'fields' => [
        ['name' => 'title', 'label' => 'Su co', 'required' => true],
        ['name' => 'detail', 'label' => 'Chi tiet', 'type' => 'textarea', 'required' => true],
        ['name' => 'priority', 'label' => 'Muc do', 'type' => 'select', 'options' => ['low' => 'Thap', 'medium' => 'Trung binh', 'high' => 'Cao'], 'required' => true],
        ['name' => 'status', 'label' => 'Trang thai', 'type' => 'select', 'options' => ['open' => 'Moi ghi nhan', 'in_progress' => 'Dang xu ly', 'resolved' => 'Da khac phuc'], 'required' => true],
        ['name' => 'assignee_username', 'label' => 'Nguoi xu ly'],
    ],
    'list_columns' => ['title' => 'Su co', 'priority' => 'Muc do', 'status' => 'Trang thai', 'assignee_username' => 'Nguoi xu ly', 'created_at' => 'Ngay tao'],
]);
?>





