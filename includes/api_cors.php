<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$defaultOrigin = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost');
if ($origin !== '') {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
} else {
    header('Access-Control-Allow-Origin: ' . $defaultOrigin);
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept');
header('Access-Control-Allow-Credentials: true');

// Ensure backend session cookie can be used by frontend on another host.
$secureCookie = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
$sameSite = $secureCookie ? 'None' : 'Lax';
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', $sameSite);
ini_set('session.cookie_secure', $secureCookie ? '1' : '0');
ini_set('session.cookie_path', '/');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
?>





