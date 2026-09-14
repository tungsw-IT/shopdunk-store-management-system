<?php
require_once __DIR__ . '/auth.php';
require_permission('reports');
header('Content-Type: application/json; charset=utf-8');

foreach (['type', 'year', 'brand'] as $parameter) {
    if (isset($_GET[$parameter]) && !is_string($_GET[$parameter])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid report filter']);
        exit;
    }
}
$type = $_GET['type'] ?? '';
$yearParam = trim((string) ($_GET['year'] ?? ''));
$brandParam = trim((string) ($_GET['brand'] ?? ''));
$currentYear = (int) date('Y');
$year = preg_match('/^\d{4}$/', $yearParam) ? (int) $yearParam : null;
$brand = $brandParam !== '' ? $brandParam : null;

if ($yearParam !== '' && ($year === null || $year < 1000)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid report year']);
    exit;
}
require_once __DIR__ . '/report_dates.php';
$pbYearExpr = report_year_expression();

if ($type === 'revenue') {
    $conditions = [];
    $effectiveYear = $year ?? $currentYear;
    $conditions[] = "{$pbYearExpr} = {$effectiveYear}";
    if ($brand !== null) {
        $escapedBrand = mysqli_real_escape_string($conn, $brand);
        $conditions[] = "purchase_mobile_company = '{$escapedBrand}'";
    }

    $whereClause = '';
    if (!empty($conditions)) {
        $whereClause = 'WHERE ' . implode(' AND ', $conditions);
    }

    $sql = "SELECT purchase_mobile_company AS brand, COALESCE(SUM(total_paid_amount), 0) AS revenue 
            FROM paymentbill 
            $whereClause
            GROUP BY brand";

    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['brand' => $row['brand'], 'revenue' => (float)$row['revenue']];
    }
    echo json_encode($data);

} elseif ($type === 'gender') {
    $sql = "SELECT customer_gender AS gender, COUNT(*) AS count 
            FROM customer 
            GROUP BY gender";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['gender' => ucfirst((string) $row['gender']), 'count' => (int)$row['count']];
    }
    echo json_encode($data);


} elseif ($type === 'years') {
    $sql = "SELECT DISTINCT {$pbYearExpr} AS year
            FROM paymentbill
            WHERE {$pbYearExpr} BETWEEN 1000 AND 9999
            ORDER BY year DESC";
    $result = $conn->query($sql);
    $data = [];
    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row['year'];
        }
    }
    if (count($data) === 0) {
        $data = [$currentYear];
    }
    echo json_encode($data);
}

elseif ($type === 'revenue_by_series') {
    $conditions = [];
    $effectiveYear = $year ?? $currentYear;
    $conditions[] = "{$pbYearExpr} = {$effectiveYear}";
    if ($brand !== null) {
        $escapedBrand = mysqli_real_escape_string($conn, $brand);
        $conditions[] = "purchase_mobile_company = '{$escapedBrand}'";
    }

    $whereClause = '';
    if (!empty($conditions)) {
        $whereClause = 'WHERE ' . implode(' AND ', $conditions);
    }

    $sql = "SELECT purchase_mobile_series AS series, COALESCE(SUM(total_paid_amount), 0) AS revenue
            FROM paymentbill
            $whereClause
            GROUP BY series
            ORDER BY revenue DESC";

    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['series' => (string) $row['series'], 'revenue' => (float) $row['revenue']];
    }
    echo json_encode($data);
}


elseif ($type === 'reviews') {
    $sql = "SELECT rating, COUNT(*) AS count 
            FROM reviews 
            GROUP BY rating 
            ORDER BY rating";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['rating' => (int)$row['rating'], 'count' => (int)$row['count']];
    }
    echo json_encode($data);

} elseif ($type === 'brands') {
    $sql = "SELECT DISTINCT purchase_mobile_company AS brand FROM paymentbill WHERE purchase_mobile_company IS NOT NULL AND purchase_mobile_company <> '' ORDER BY brand";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row['brand'];
    }
    echo json_encode($data);
}
elseif ($type === 'sold_by_brand') {
    $conditions = [];
    $effectiveYear = $year ?? $currentYear;
    $conditions[] = "{$pbYearExpr} = {$effectiveYear}";
    if ($brand !== null) {
        $escapedBrand = mysqli_real_escape_string($conn, $brand);
        $conditions[] = "purchase_mobile_company = '{$escapedBrand}'";
    }
    $whereClause = '';
    if (!empty($conditions)) {
        $whereClause = 'WHERE ' . implode(' AND ', $conditions);
    }
    $sql = "SELECT purchase_mobile_company AS brand, COUNT(*) AS sold
            FROM paymentbill
            $whereClause
            GROUP BY brand";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['brand' => $row['brand'], 'sold' => (int)$row['sold']];
    }
    echo json_encode($data);
}

elseif ($type === 'sold_by_series') {
    $conditions = [];
    $effectiveYear = $year ?? $currentYear;
    $conditions[] = "{$pbYearExpr} = {$effectiveYear}";
    if ($brand !== null) {
        $escapedBrand = mysqli_real_escape_string($conn, $brand);
        $conditions[] = "purchase_mobile_company = '{$escapedBrand}'";
    }

    $whereClause = '';
    if (!empty($conditions)) {
        $whereClause = 'WHERE ' . implode(' AND ', $conditions);
    }

    $sql = "SELECT purchase_mobile_series AS series, COUNT(*) AS sold
            FROM paymentbill
            $whereClause
            GROUP BY series
            ORDER BY sold DESC";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = ['series' => (string) $row['series'], 'sold' => (int) $row['sold']];
    }
    echo json_encode($data);
}


else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid chart type']);
}






