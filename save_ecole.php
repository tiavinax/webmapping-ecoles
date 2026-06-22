<?php
// ============================================================
//  Fichier  : save_ecole.php
//  Rôle     : INSERT une nouvelle école (POST JSON)
//  Méthode  : POST
//  Body ex. :
//  {
//    "nom"       : "École Privée Horizon",
//    "type"      : "primaire",
//    "statut"    : "prive",
//    "fokontany" : "Andoharanofotsy Nord",
//    "telephone" : "034 12 345 67",
//    "nb_eleves" : 150,
//    "latitude"  : -18.9695,
//    "longitude" : 47.5202
//  }
// ============================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Pré-vol CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

require_once __DIR__ . '/connexion.php';

// ── Lecture et décodage du JSON ──────────────────────────────
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Corps JSON invalide.']);
    exit;
}

// ── Validation des champs obligatoires ───────────────────────
$typesValides   = ['primaire', 'college', 'lycee'];
$statutsValides = ['public', 'prive'];

$errors = [];

$nom       = trim($data['nom']       ?? '');
$type      = trim($data['type']      ?? '');
$statut    = trim($data['statut']    ?? '');
$fokontany = trim($data['fokontany'] ?? '');
$telephone = trim($data['telephone'] ?? '');
$nb_eleves = isset($data['nb_eleves']) ? (int) $data['nb_eleves'] : 0;
$latitude  = $data['latitude']  ?? null;
$longitude = $data['longitude'] ?? null;

if ($nom === '')                                         $errors[] = 'Le champ "nom" est obligatoire.';
if (!in_array($type,   $typesValides,   true))           $errors[] = 'Le champ "type" doit être : primaire, college ou lycee.';
if (!in_array($statut, $statutsValides, true))           $errors[] = 'Le champ "statut" doit être : public ou prive.';
if ($latitude  === null || !is_numeric($latitude))       $errors[] = 'Le champ "latitude" est obligatoire et doit être numérique.';
if ($longitude === null || !is_numeric($longitude))      $errors[] = 'Le champ "longitude" est obligatoire et doit être numérique.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// ── Insertion ────────────────────────────────────────────────
try {
    $pdo = getConnexion();

    $stmt = $pdo->prepare(
        'INSERT INTO ecoles (nom, type, statut, fokontany, telephone, nb_eleves, latitude, longitude)
         VALUES (:nom, :type, :statut, :fokontany, :telephone, :nb_eleves, :latitude, :longitude)
         RETURNING id'
    );

    $stmt->execute([
        ':nom'       => $nom,
        ':type'      => $type,
        ':statut'    => $statut,
        ':fokontany' => $fokontany,
        ':telephone' => $telephone,
        ':nb_eleves' => $nb_eleves,
        ':latitude'  => (float) $latitude,
        ':longitude' => (float) $longitude,
    ]);

    $row = $stmt->fetch();

    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'École ajoutée avec succès.',
        'id'      => (int) $row['id'],
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'insertion de l\'école.',
    ]);
}
