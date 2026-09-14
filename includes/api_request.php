<?php
function read_request_input(): array
{
    $body = file_get_contents('php://input');
    if ($body !== false && trim($body) !== '') {
        $decoded = json_decode($body, true);
        if (is_array($decoded)) {
            return $decoded;
        }
        parse_str($body, $parsed);
        if (is_array($parsed) && count($parsed) > 0) {
            return $parsed;
        }
    }
    if (!empty($_POST)) {
        return $_POST;
    }
    return [];
}
?>





