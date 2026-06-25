<?php
include("../inc/fonction.php");

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Lire le body JSON envoyé par fetch()
$body = file_get_contents('php://input');
$ecole = json_decode($body); // objet PHP avec $ecole->nom, $ecole->latitude...

// Vérification minimale
if (!isset($ecole->nom) || !isset($ecole->latitude) || !isset($ecole->longitude)) {
    http_response_code(400);
    echo json_encode(['erreur' => 'données manquantes']);
    exit;
}

$id = save($ecole);
echo json_encode(['succes' => true, 'id' => $id]);