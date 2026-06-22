<?php
// ============================================================
//  Fichier  : update_ecole.php
//  Rôle     : UPDATE une école existante (POST JSON)
//  Méthode  : POST
//  Body ex. :
//  {
//    "id"        : 3,
//    "nom"       : "LTP Andoharanofotsy (rénové)",
//    "type"      : "lycee",
//    "statut"    : "public",
//    "fokontany" : "Andoharanofotsy Est",
//    "telephone" : "034 00 000 99",
//    "nb_eleves" : 650,
//    "latitude"  : -18.9690,
//    "longitude" : 47.5240
//  }
// ============================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

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

// ── Lecture JSON ─────────────────────────────────────────────
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Corps JSON invalide.']);
    exit;
}

// ── Validation ───────────────────────────────────────────────
$typesValides   = ['primaire', 'college', 'lycee'];
$statutsValides = ['public', 'prive'];
$errors = [];

$id        = isset($data['id']) ? (int) $data['id'] : 0;
$nom       = trim($data['nom']       ?? '');
$type      = trim($data['type']      ?? '');
$statut    = trim($data['statut']    ?? '');
$fokontany = trim($data['fokontany'] ?? '');
$telephone = trim($data['telephone'] ?? '');
$nb_eleves = isset($data['nb_eleves']) ? (int) $data['nb_eleves'] : 0;
$latitude  = $data['latitude']  ?? null;
$longitude = $data['longitude'] ?? null;

if ($id <= 0)                                            $errors[] = 'Le champ "id" est obligatoire et doit être un entier positif.';
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

// ── Mise à jour ──────────────────────────────────────────────
try {
    $pdo = getConnexion();

    $stmt = $pdo->prepare(
        'UPDATE ecoles
         SET nom = :nom, type = :type, statut = :statut,
             fokontany = :fokontany, telephone = :telephone,
             nb_eleves = :nb_eleves, latitude = :latitude, longitude = :longitude
         WHERE id = :id'
    );

    $stmt->execute([
        ':id'        => $id,
        ':nom'       => $nom,
        ':type'      => $type,
        ':statut'    => $statut,
        ':fokontany' => $fokontany,
        ':telephone' => $telephone,
        ':nb_eleves' => $nb_eleves,
        ':latitude'  => (float) $latitude,
        ':longitude' => (float) $longitude,
    ]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => "Aucune école trouvée avec l'id $id.",
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'École mise à jour avec succès.',
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la mise à jour de l\'école.',
    ]);
}
