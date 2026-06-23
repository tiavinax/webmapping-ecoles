<?php
// ============================================================
//  Fichier  : get_ecole.php
//  Rôle     : Retourne UNE école par son id en JSON
//  Méthode  : GET
//  URL ex.  : /api/get_ecole.php?id=3
// ============================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/connexion.php';

// ── Validation de l'id ────────────────────────────────────────
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Paramètre id manquant ou invalide.',
    ]);
    exit;
}

try {
    $pdo = getConnexion();

    $stmt = $pdo->prepare(
        'SELECT id, nom, type, statut, fokontany, telephone, nb_eleves,
                latitude, longitude, created_at
         FROM ecoles
         WHERE id = :id'
    );
    $stmt->execute([':id' => $id]);
    $ecole = $stmt->fetch();

    if (!$ecole) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => "Aucune école trouvée avec l'id $id.",
        ]);
        exit;
    }

    // Caster les types
    $ecole['id']        = (int)   $ecole['id'];
    $ecole['nb_eleves'] = (int)   $ecole['nb_eleves'];
    $ecole['latitude']  = (float) $ecole['latitude'];
    $ecole['longitude'] = (float) $ecole['longitude'];

    echo json_encode([
        'success' => true,
        'data'    => $ecole,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération de l\'école.',
    ]);
}
