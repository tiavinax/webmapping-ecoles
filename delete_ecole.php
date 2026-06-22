<?php
// ============================================================
//  Fichier  : delete_ecole.php
//  Rôle     : DELETE une école par son id (POST JSON)
//  Méthode  : POST
//  Body ex. : { "id": 5 }
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

$id = isset($data['id']) ? (int) $data['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Le champ "id" est obligatoire et doit être un entier positif.',
    ]);
    exit;
}

// ── Suppression ──────────────────────────────────────────────
try {
    $pdo = getConnexion();

    $stmt = $pdo->prepare('DELETE FROM ecoles WHERE id = :id');
    $stmt->execute([':id' => $id]);

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
        'message' => "École $id supprimée avec succès.",
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la suppression de l\'école.',
    ]);
}
