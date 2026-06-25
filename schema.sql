-- ============================================================
--  Projet Webmapping — Écoles à Andoharanofotsy
--  Partie : Base de données (Célina)
--  SGBD    : PostgreSQL
-- ============================================================

-- 1. Créer la base de données (à exécuter en dehors de psql ou via pgAdmin)
-- CREATE DATABASE ecoles_map;;

-- 2. Se connecter à la base puis exécuter la suite :
-- \c ecoles_map;

-- 3. Créer la table écoles
CREATE TABLE IF NOT EXISTS ecoles (
    id          SERIAL PRIMARY KEY,
    nom         VARCHAR(150)     NOT NULL,
    type        VARCHAR(50)      NOT NULL CHECK (type IN ('primaire', 'college', 'lycee')),
    statut      VARCHAR(20)      NOT NULL CHECK (statut IN ('public', 'prive')),
    fokontany   VARCHAR(100),
    telephone   VARCHAR(30),
    nb_eleves   INTEGER          DEFAULT 0,
    latitude    DOUBLE PRECISION NOT NULL,
    longitude   DOUBLE PRECISION NOT NULL,
    created_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
--  4. Données initiales — écoles recensées à Andoharanofotsy
--     (coordonnées vérifiées sur Google Maps)
-- ============================================================

TRUNCATE TABLE ecoles; 

INSERT INTO ecoles (nom, type, statut, fokontany, telephone, nb_eleves, latitude, longitude) VALUES
-- Fokontany : Andoharanofotsy
('EPP Andoharanofotsy', 'primaire', 'public', 'Andoharanofotsy', NULL, 450, -18.97574, 47.53310),
('CEG Andoharanofotsy', 'college', 'public', 'Andoharanofotsy', NULL, 650, -18.97495, 47.53021),
('Andrian School', 'primaire', 'prive', 'Andoharanofotsy', '+261 34 08 940 94', 180, -18.97650, 47.53420),

-- Fokontany : Mahabo
('École Sweet First Years', 'primaire', 'prive', 'Mahabo', '+261 34 52 623 00', 120, -18.97822, 47.53235),
('EPP Mahabo', 'primaire', 'public', 'Mahabo', NULL, 210, -18.98012, 47.53401),

-- Fokontany : Mahalavolona
('Complexe Scolaire Fukuzawa', 'lycee', 'prive', 'Mahalavolona', '+261 34 53 648 71', 250, -18.97710, 47.53180),
('Collège Privé Mahalavolona', 'college', 'prive', 'Mahalavolona', NULL, 140, -18.97940, 47.53610),

-- Fokontany : Ivoloha (Iavoloha)
('Lycée Andoharanofotsy', 'lycee', 'public', 'Ivoloha', NULL, 800, -18.99291, 47.53571),

-- Fokontany : Ambohimanala
('EPP Ambohimanala', 'primaire', 'public', 'Ambohimanala', NULL, 310, -18.96840, 47.54010),

-- Fokontany : Belambanana
('École Privée Belambanana', 'primaire', 'prive', 'Belambanana', NULL, 95, -18.96910, 47.52180),

-- Fokontany : Morarano
('EPP Morarano', 'primaire', 'public', 'Morarano', NULL, 280, -18.98320, 47.54120),

-- Fokontany : Volotara
('Collège Saint Pierre Malaza', 'college', 'prive', 'Volotara', NULL, 350, -18.98140, 47.52550);

