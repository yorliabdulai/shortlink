<?php
header('Content-Type: application/json');

$requestUri = $_SERVER['REQUEST_URI'];

if ($requestUri === '/encode') {
    require_once __DIR__ . '/encode.php';
} elseif ($requestUri === '/decode') {
    require_once __DIR__ . '/decode.php';
} else {
    echo json_encode(['error' => 'Invalid endpoint']);
}