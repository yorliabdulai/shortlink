<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../classes/UrlShortener.php';

$input = json_decode(file_get_contents('php://input'), true);
$shortUrl = $input['url'] ?? '';

if (empty($shortUrl)) {
    echo json_encode(['error' => 'Short URL is required']);
    exit;
}

$shortener = new UrlShortener();
echo json_encode($shortener->decode($shortUrl));