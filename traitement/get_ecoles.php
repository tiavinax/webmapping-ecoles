<?php 
include("../inc/fonction.php");

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// $ecoles = get_ecoles();
// echo json_encode($ecoles);

$nom    = $_GET['nom']    ?? null;
$type   = $_GET['type']   ?? null;
$statut = $_GET['statut'] ?? null;

$ecoles = rechercher($nom,$type,$statut);
echo json_encode($ecoles);