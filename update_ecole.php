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
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
    // DEBUG: journaliser la requête entrante pour diagnostic
    @file_put_contents(__DIR__ . '/update_debug.log', date('c') . " RAW: " . $raw . "\n", FILE_APPEND);
    @file_put_contents(__DIR__ . '/update_debug.log', date('c') . " PARSED: " . json_encode($data) . "\n", FILE_APPEND);
}

$file = __DIR__ . '/ecoles.json';
$list = [];
if (file_exists($file)) {
    $content = file_get_contents($file);
    $list = json_decode($content, true) ?: [];
}

$found = false;
foreach ($list as &$it) {
    @file_put_contents(__DIR__ . '/update_debug.log', date('c') . " CHECK ID: " . json_encode($it['id']) . "\n", FILE_APPEND);
    if (intval($it['id']) === intval($data['id'])) {
        $found = true;
        // Mettre à jour les champs présents
        foreach (['nom','type','statut','fokontany','telephone','nb_eleves','latitude','longitude'] as $f) {
            if (isset($data[$f])) $it[$f] = $data[$f];
        }
        break;
    }
}
unset($it);

if (!$found) {
    // Fournir des informations de debug utiles localement
    $available = array_map(function($i){ return isset($i['id']) ? $i['id'] : null; }, $list);
    echo json_encode(['success' => false, 'error' => 'École introuvable', 'received_id' => $data['id'], 'available_ids' => $available]);
    exit;
}

if (file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    echo json_encode(['success' => false, 'error' => 'Impossible d\'écrire le fichier']);
    exit;
}

echo json_encode(['success' => true]);
exit;