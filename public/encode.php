<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../classes/UrlShortener.php';

$input = json_decode(file_get_contents('php://input'), true);
$longUrl = $input['url'] ?? '';

if (empty($longUrl)) {
    echo json_encode(['error' => 'URL is required']);
    exit;
}

$shortener = new UrlShortener();
echo json_encode($shortener->encode($longUrl));