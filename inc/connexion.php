<?php
// ============================================================
//  Projet Webmapping — Écoles à Andoharanofotsy
//  Fichier  : connexion.php
//  Rôle     : Connexion PDO PostgreSQL — retourne $pdo
//  Partie   : API PHP (Célina)
// ============================================================

// ── Connexion PDO ────────────────────────────────────────────

function getConnexion()
{
    $host = 'localhost';
    $dbname = 'ecoles_map';
    $user = 'postgres';
    $pass = 'postgres';
    $port = '5432';

    $dsn = 'pgsql:host='.$host.';port='.$port.';dbname='.$dbname;

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        // Optionnel : Forcer l'encodage en UTF8 si nécessaire pour PostgreSQL
        $pdo->exec("SET NAMES 'UTF8'");

        return $pdo; 

    } catch (PDOException $e) {
        // En production, il vaut mieux lever une exception ou logger l'erreur 
        // plutôt que d'afficher le message en clair à l'utilisateur
        die('Connexion échouée : ' . $e->getMessage());
    }
}

