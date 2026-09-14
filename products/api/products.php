<?php
require_once __DIR__ . '/../../includes/api_cors.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/api_db.php';

if (!$pdo) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'Database unavailable.']);
    exit;
}

$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 20;
$limit = max(1, min(200, $limit));
$companyName = trim((string) ($_GET['company_name'] ?? ''));
$companySeries = trim((string) ($_GET['company_series'] ?? ''));
$search = trim((string) ($_GET['search'] ?? ''));
$searchTerm = '%' . str_replace(' ', '%', $search) . '%';

try {
    $sql = 'SELECT imei_number, company_name, company_series, model_no, price, old_price, color, `display_size(inchi)`, display_quality, `ram(GB)`, `rom(GB)`, processor, `battery_capacity(mah)` FROM mobile';
    $conditions = [];
    $params = [];

    if ($companyName !== '') {
        $conditions[] = 'company_name = ?';
        $params[] = $companyName;
    }
    if ($companySeries !== '') {
        $conditions[] = 'company_series = ?';
        $params[] = $companySeries;
    }
    if ($search !== '') {
        $conditions[] = '(company_name LIKE ? OR company_series LIKE ? OR model_no LIKE ?)';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    if (!empty($conditions)) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY price ASC LIMIT ?';
    $stmt = $pdo->prepare($sql);
    foreach ($params as $index => $value) {
        $stmt->bindValue($index + 1, $value);
    }
    $stmt->bindValue(count($params) + 1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $products]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to load products.']);
}
?>





