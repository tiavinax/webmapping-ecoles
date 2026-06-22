# Projet Webmapping — Écoles à Andoharanofotsy

Technologie : 
    persistecence = postgres 

## Base de données (Célina)

- [ ] Créer la base de données PostgreSQL `ecoles_ando`
- [ ] Créer la table `ecoles` (id, nom, type, statut, fokontany, telephone, nb_eleves, latitude, longitude)
- [ ] Recenser les écoles sur Google Maps et insérer les données en SQL

## API PHP (Célina)

- [ ] `connexion.php` — connexion PDO PostgreSQL
- [ ] `get_ecoles.php` — retourne toutes les écoles en JSON
- [ ] `get_ecole.php?id=` — retourne une école par id en JSON
- [ ] `save_ecole.php` — INSERT nouvelle école (POST JSON)
- [ ] `update_ecole.php` — UPDATE école existante (POST JSON)
- [ ] `delete_ecole.php` — DELETE école par id (POST JSON)

## Carte Leaflet (Tiavina)

- [ ] Initialiser la carte centrée sur Andoharanofotsy
- [ ] Charger et afficher les markers des écoles depuis `get_ecoles.php`
- [ ] Différencier les markers par couleur selon le type (primaire / collège / lycée)
- [ ] Afficher popup sur chaque marker (nom, type, statut, téléphone)
- [ ] Routing OSRM depuis position actuelle vers école cliquée
- [ ] Filtrer les markers par type sur la carte
- [ ] Filtrer les markers par statut (public / privé) sur la carte

## Interface admin (Yrielle)

- [ ] Page login admin (simple mot de passe en session PHP)
- [ ] Formulaire ajout école — clic sur carte pour récupérer lat/lon + saisie infos
- [ ] Formulaire modification école — pré-remplir les champs avec données existantes
- [ ] Bouton suppression école avec confirmation
- [ ] Masquer les boutons admin si non connecté

## Intégration finale (Tiavina)

- [ ] Tester tous les PHP avec les données réelles
- [ ] Vérifier le routing OSRM sur les vraies positions
- [ ] Tester les filtres avec toutes les écoles insérées
- [ ] Préparer la démo pour la soutenance