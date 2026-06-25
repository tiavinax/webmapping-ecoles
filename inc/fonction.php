<?php
require("connexion.php");

// Retourne toutes les écoles
function get_ecoles()
{
    $pdo = getConnexion();
    try {
        $sql = "SELECT id, nom, type, statut, fokontany, telephone, nb_eleves, latitude, longitude 
                FROM ecoles";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return ['erreur' => $e->getMessage()];
    }
}

// Retourne une école par son id
function findById($id)
{
    $pdo = getConnexion();
    try {
        $sql = "SELECT id, nom, type, statut, fokontany, telephone, nb_eleves, latitude, longitude 
                FROM ecoles WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return ['erreur' => $e->getMessage()];
    }
}

// Insère une école et retourne son id
function save($ecole)
{
    $pdo = getConnexion();
    try {
        $sql = "INSERT INTO ecoles(nom, type, statut, fokontany, telephone, nb_eleves, latitude, longitude)
                VALUES(:nom, :type, :statut, :fokontany, :telephone, :nb_eleves, :latitude, :longitude)
                RETURNING id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nom'       => $ecole->nom,
            'type'      => $ecole->type,
            'statut'    => $ecole->statut,
            'fokontany' => $ecole->fokontany,
            'telephone' => $ecole->telephone,
            'nb_eleves' => $ecole->nb_eleves,
            'latitude'  => $ecole->latitude,
            'longitude' => $ecole->longitude,
        ]);
        $row = $stmt->fetch();
        return (int) $row['id'];
    } catch (Exception $e) {
        return ['erreur' => $e->getMessage()];
    }
}

function update($id, $ecole)
{
    $pdo = getConnexion();
    try {
        $sql = "UPDATE ecoles 
        SET nom=:nom, type=:type, statut=:statut, fokontany=:fokontany,
            telephone=:telephone, nb_eleves=:nb_eleves, latitude=:latitude, longitude=:longitude
        WHERE id = :id
        RETURNING id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nom'       => $ecole->nom,
            'type'      => $ecole->type,
            'statut'    => $ecole->statut,
            'fokontany' => $ecole->fokontany,
            'telephone' => $ecole->telephone,
            'nb_eleves' => $ecole->nb_eleves,
            'latitude'  => $ecole->latitude,
            'longitude' => $ecole->longitude,
            'id' => $id
        ]);
        $row = $stmt->fetch();
        return (int) $row['id'];
    } catch (Exception $e) {
        return ['erreur' => $e->getMessage()];
    }
}

function delete($id)
{
    $pdo = getConnexion();
    try {
        $sql = "DELETE FROM ecoles WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute(['id' => $id]);
    } catch (Exception $e) {
        return ['erreur' => $e->getMessage()];
    }
}

function findByNom($nom)
{
    $pdo = getConnexion();
    try {
        $sql = "SELECT id, nom, type, statut, fokontany, telephone, nb_eleves, latitude, longitude 
                FROM ecoles WHERE nom = :nom";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nom' => $nom]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return ['erreur' => $e->getMessage()];
    }
}

function rechercher($nom, $type, $statut)
{
    $pdo = getConnexion();
    try {
        $sql = "SELECT id, nom, type, statut, fokontany, telephone, nb_eleves, latitude, longitude 
                FROM ecoles WHERE 1=1";
        $params = [];

        if (!empty($nom)) {
            $sql .= " AND nom ILIKE :nom";        // ILIKE = LIKE insensible à la casse en PostgreSQL
            $params['nom'] = '%' . $nom . '%';
        }
        if (!empty($type)) {
            $sql .= " AND type = :type";           // type exact, pas de LIKE
            $params['type'] = $type;
        }
        if (!empty($statut)) {
            $sql .= " AND statut = :statut";       // statut exact aussi
            $params['statut'] = $statut;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return ['erreur' => $e->getMessage()];
    }
}