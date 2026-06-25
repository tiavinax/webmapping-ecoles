<?php 
include("../inc/fonction.php");

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$body = file_get_contents('php://input');
$data = json_decode($body);
$id = $data->id;

if (!isset($id)) {
    http_response_code(400);
    echo json_encode(['erreur' => 'données manquantes']);
    exit;
}

delete($id);
echo json_encode(['succes' => true]);

