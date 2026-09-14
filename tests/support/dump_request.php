<?php
header('Content-Type: text/plain');
$body = file_get_contents('php://input');
echo "BODY=[{$body}]\n";
$headers = function_exists('getallheaders') ? getallheaders() : [];
echo "HEADERS:\n";
print_r($headers);






