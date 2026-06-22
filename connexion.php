<?php
// ============================================================
//  Projet Webmapping — Écoles à Andoharanofotsy
//  Fichier  : connexion.php
//  Rôle     : Connexion PDO PostgreSQL — retourne $pdo
//  Partie   : API PHP (Célina)
// ============================================================

// ── Configuration ────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'ecoles_ando');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres');
define('DB_CHARSET', 'utf8');

// ── Connexion PDO ────────────────────────────────────────────
function getConnexion(): PDO
{
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        DB_HOST, DB_PORT, DB_NAME
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        $pdo->exec("SET NAMES 'UTF8'");
        return $pdo;
    } catch (PDOException $e) {
        // Ne jamais exposer les détails de connexion en production
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Erreur de connexion à la base de données.',
        ]);
        exit;
    }
}
