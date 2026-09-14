<?php
require_once __DIR__ . '/../../includes/api_cors.php';
require_once __DIR__ . '/../../config/api_db.php';
require_once __DIR__ . '/../product_images_helper.php';
header('Content-Type: application/json');

$modelNo = trim((string)($_GET['model_no'] ?? ''));
$companySeries = trim((string)($_GET['company_series'] ?? ''));

if ($modelNo === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'model_no is required.']);
    exit;
}

if (!$pdo) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'Database unavailable.']);
    exit;
}

try {
    $query = 'SELECT * FROM mobile WHERE model_no = ?';
    $params = [$modelNo];
    if ($companySeries !== '') {
        $query .= ' AND company_series = ?';
        $params[] = $companySeries;
    }
    $query .= ' LIMIT 1';

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }

    $fallback = app_url('images/no-image.png');
    $primaryImage = resolve_primary_product_image_pdo($pdo, $product, $fallback);

    $stmtImages = $pdo->prepare('SELECT image_path FROM product_images WHERE imei_number = ? ORDER BY sort_order ASC, id ASC');
    $stmtImages->execute([$product['imei_number'] ?? '']);
    $images = array_map('product_image_url', $stmtImages->fetchAll(PDO::FETCH_COLUMN)) ?: [$primaryImage];

    $relatedStmt = $pdo->prepare(
        'SELECT imei_number, company_name, company_series, model_no, price
         FROM mobile
         WHERE company_name = ? AND company_series = ? AND model_no <> ?
         ORDER BY model_no ASC
         LIMIT 4'
    );
    $relatedStmt->execute([$product['company_name'], $product['company_series'], $product['model_no']]);
    $related = $relatedStmt->fetchAll();

    $reviewsStmt = $pdo->prepare(
        'SELECT r.rating, r.review_text, r.created_at, a.accounts_name
         FROM reviews r
         LEFT JOIN accounts a ON r.account_id = a.account_id
         WHERE r.model_no = ?' . ($companySeries !== '' ? ' AND r.company_series = ?' : '') . '
         ORDER BY r.created_at DESC'
    );
    if ($companySeries !== '') {
        $reviewsStmt->execute([$product['model_no'], $companySeries]);
    } else {
        $reviewsStmt->execute([$product['model_no']]);
    }
    $reviews = $reviewsStmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => [
            'product' => $product,
            'images' => $images,
            'related' => $related,
            'reviews' => $reviews,
        ],
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error.']);
}





