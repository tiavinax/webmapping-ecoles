<?php
header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . '/ecoles.json';
if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

$content = file_get_contents($file);
$data = json_decode($content, true);
if ($data === null) {
    echo json_encode([]);
    exit;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
exit;