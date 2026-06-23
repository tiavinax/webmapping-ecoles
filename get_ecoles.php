<?php
// ============================================================
//  Fichier  : get_ecoles.php
//  Rôle     : Retourne TOUTES les écoles en JSON
//  Méthode  : GET
//  URL ex.  : /api/get_ecoles.php
//             /api/get_ecoles.php?type=primaire
//             /api/get_ecoles.php?statut=public
//             /api/get_ecoles.php?type=college&statut=prive
// ============================================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/connexion.php';

try {
    $pdo = getConnexion();

    // ── Filtres optionnels ────────────────────────────────────
    $conditions = [];
    $params     = [];

    $type   = $_GET['type']   ?? null;
    $statut = $_GET['statut'] ?? null;

    $typesValides   = ['primaire', 'college', 'lycee'];
    $statutsValides = ['public', 'prive'];

    if ($type !== null && in_array($type, $typesValides, true)) {
        $conditions[] = 'type = :type';
        $params[':type'] = $type;
    }

    if ($statut !== null && in_array($statut, $statutsValides, true)) {
        $conditions[] = 'statut = :statut';
        $params[':statut'] = $statut;
    }

    // ── Construction de la requête ────────────────────────────
    $sql = 'SELECT id, nom, type, statut, fokontany, telephone, nb_eleves,
                   latitude, longitude
            FROM ecoles';

    if (!empty($conditions)) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY nom ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $ecoles = $stmt->fetchAll();

    // Caster les types numériques
    foreach ($ecoles as &$e) {
        $e['id']        = (int)   $e['id'];
        $e['nb_eleves'] = (int)   $e['nb_eleves'];
        $e['latitude']  = (float) $e['latitude'];
        $e['longitude'] = (float) $e['longitude'];
    }
    unset($e);

    echo json_encode([
        'success' => true,
        'count'   => count($ecoles),
        'data'    => $ecoles,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la récupération des écoles.',
    ]);
}
