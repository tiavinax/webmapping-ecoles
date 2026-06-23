<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID manquant']);
    exit;
}

$id = intval($data['id']);
if ($id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID invalide']);
    exit;
}

$file = __DIR__ . '/ecoles.json';
if (!file_exists($file)) {
    echo json_encode(['success' => false, 'error' => 'Fichier de données introuvable']);
    exit;
}

$content = file_get_contents($file);
$list = json_decode($content, true);
if ($list === null) {
    echo json_encode(['success' => false, 'error' => 'Données corrompues']);
    exit;
}

$newList = array_filter($list, function($item) use ($id) {
    return intval($item['id']) !== $id;
});

if (count($newList) === count($list)) {
    echo json_encode(['success' => false, 'error' => 'École introuvable']);
    exit;
}

// Réindexer le tableau (évite les trous dans le JSON)
$newList = array_values($newList);

if (file_put_contents($file, json_encode($newList, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    echo json_encode(['success' => false, 'error' => "Impossible d'écrire le fichier"]);
    exit;
}

echo json_encode(['success' => true]);
exit;