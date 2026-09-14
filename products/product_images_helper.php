<?php

require_once __DIR__ . '/../config/paths.php';

function normalize_product_image_path(string $path): string
{
    $path = ltrim(str_replace('\\', '/', $path), '/');
    // Keep existing database records usable after moving assets/images to images.
    return preg_replace('#^assets/images/#', 'images/', $path);
}

function product_image_file(string $path): string
{
    return dirname(__DIR__) . '/' . normalize_product_image_path($path);
}

function product_image_url(string $path): string
{
    return $path === '' ? '' : app_url(normalize_product_image_path($path));
}

function product_gallery_directory(): string
{
    $directory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'product_gallery';
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    return $directory;
}

function ensure_product_images_table_mysqli(mysqli $conn): void
{
    mysqli_query(
        $conn,
        "CREATE TABLE IF NOT EXISTS product_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            imei_number VARCHAR(100) NOT NULL,
            image_path VARCHAR(255) NOT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_product_images_imei (imei_number)
        )"
    );
}

function ensure_product_images_table_pdo(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS product_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            imei_number VARCHAR(100) NOT NULL,
            image_path VARCHAR(255) NOT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_product_images_imei (imei_number)
        )"
    );
}

function save_uploaded_product_images(array $files, string $imeiNumber): array
{
    $saved = [];
    $directory = product_gallery_directory();
    $safeImei = preg_replace('/[^a-zA-Z0-9_-]/', '', $imeiNumber);

    if (!isset($files['name']) || !is_array($files['name'])) {
        return $saved;
    }

    foreach ($files['name'] as $index => $originalName) {
        $tmpName = $files['tmp_name'][$index] ?? '';
        $error = $files['error'][$index] ?? UPLOAD_ERR_NO_FILE;

        if ($error !== UPLOAD_ERR_OK || $tmpName === '') {
            continue;
        }

        $extension = strtolower(pathinfo((string) $originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            continue;
        }

        $fileName = $safeImei . '_' . date('YmdHis') . '_' . $index . '.' . $extension;
        $targetPath = $directory . DIRECTORY_SEPARATOR . $fileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $saved[] = 'images/product_gallery/' . $fileName;
        }
    }

    return $saved;
}

function insert_product_images_mysqli(mysqli $conn, string $imeiNumber, array $imagePaths): void
{
    if (!$imagePaths) {
        return;
    }

    $stmt = $conn->prepare("INSERT INTO product_images (imei_number, image_path, sort_order) VALUES (?, ?, ?)");
    if (!$stmt) {
        return;
    }

    foreach (array_values($imagePaths) as $index => $imagePath) {
        $sortOrder = $index + 1;
        $stmt->bind_param('ssi', $imeiNumber, $imagePath, $sortOrder);
        $stmt->execute();
    }

    $stmt->close();
}

function fetch_product_images_pdo(PDO $pdo, string $imeiNumber): array
{
    ensure_product_images_table_pdo($pdo);

    $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE imei_number = ? ORDER BY sort_order ASC, id ASC");
    $stmt->execute([$imeiNumber]);

    return array_map('normalize_product_image_path', $stmt->fetchAll(PDO::FETCH_COLUMN) ?: []);
}

function fetch_product_images_mysqli(mysqli $conn, string $imeiNumber): array
{
    ensure_product_images_table_mysqli($conn);

    $stmt = $conn->prepare("SELECT id, image_path FROM product_images WHERE imei_number = ? ORDER BY sort_order ASC, id ASC");
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param('s', $imeiNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();

    return $rows;
}

function delete_product_images_mysqli(mysqli $conn, array $imageIds): void
{
    if (!$imageIds) {
        return;
    }

    $placeholders = implode(',', array_fill(0, count($imageIds), '?'));
    $types = str_repeat('i', count($imageIds));

    $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE id IN ($placeholders)");
    if (!$stmt) {
        return;
    }

    $stmt->bind_param($types, ...$imageIds);
    $stmt->execute();
    $result = $stmt->get_result();
    $paths = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM product_images WHERE id IN ($placeholders)");
    if (!$stmt) {
        return;
    }

    $stmt->bind_param($types, ...$imageIds);
    $stmt->execute();
    $stmt->close();

    foreach ($paths as $row) {
        $fullPath = realpath(product_image_file($row['image_path']));
        $galleryPath = realpath(product_gallery_directory()) . DIRECTORY_SEPARATOR;
        if ($fullPath !== false && str_starts_with($fullPath, $galleryPath) && is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}

function resolve_primary_product_image_pdo(PDO $pdo, array $product, string $fallbackPath): string
{
    if (!empty($product['imei_number'])) {
        $images = fetch_product_images_pdo($pdo, (string) $product['imei_number']);
        if (!empty($images[0])) {
            $imagePath = product_image_file($images[0]);
            if (is_file($imagePath)) {
                return product_image_url($images[0]);
            }
        }
    }

    return $fallbackPath;
}





