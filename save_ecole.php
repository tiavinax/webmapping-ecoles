<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Données JSON invalides']);
    exit;
}

$required = ['nom','type','statut','latitude','longitude'];
foreach ($required as $f) {
    if (empty($data[$f]) && $data[$f] !== '0') {
        echo json_encode(['success' => false, 'error' => "Champ manquant: $f"]);
        exit;
    }
}

$file = __DIR__ . '/ecoles.json';
$list = [];
if (file_exists($file)) {
    $content = file_get_contents($file);
    $list = json_decode($content, true) ?: [];
}

$maxId = 0;
foreach ($list as $it) if (isset($it['id']) && $it['id'] > $maxId) $maxId = $it['id'];
$newId = $maxId + 1;

$entry = [
    'id' => $newId,
    'nom' => $data['nom'],
    'type' => $data['type'],
    'statut' => $data['statut'],
    'fokontany' => $data['fokontany'] ?? null,
    'telephone' => $data['telephone'] ?? null,
    'nb_eleves' => $data['nb_eleves'] ?? null,
    'latitude' => (float)$data['latitude'],
    'longitude' => (float)$data['longitude']
];

$list[] = $entry;
if (file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    echo json_encode(['success' => false, 'error' => 'Impossible d\'écrire le fichier']);
    exit;
}

echo json_encode(['success' => true, 'id' => $newId]);
exit;