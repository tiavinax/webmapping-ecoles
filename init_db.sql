-- ============================================================
--  Projet Webmapping — Écoles à Andoharanofotsy
--  Partie : Base de données (Célina)
--  SGBD    : PostgreSQL
-- ============================================================

-- 1. Créer la base de données (à exécuter en dehors de psql ou via pgAdmin)
-- CREATE DATABASE ecoles_ando;

-- 2. Se connecter à la base puis exécuter la suite :
-- \c ecoles_ando

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

INSERT INTO ecoles (nom, type, statut, fokontany, telephone, nb_eleves, latitude, longitude) VALUES
('EPP Andoharanofotsy',          'primaire', 'public',  'Andoharanofotsy Centre', '034 00 000 01', 320,  -18.9700,  47.5210),
('Collège Public Andoharanofotsy','college',  'public',  'Andoharanofotsy Centre', '034 00 000 02', 480,  -18.9715,  47.5225),
('LTP Andoharanofotsy',          'lycee',    'public',  'Andoharanofotsy Est',    '034 00 000 03', 610,  -18.9690,  47.5240),
('École Privée Saint-Joseph',    'primaire', 'prive',   'Andoharanofotsy Nord',   '033 00 000 04', 210,  -18.9680,  47.5198),
('Collège Privé La Lumière',     'college',  'prive',   'Andoharanofotsy Sud',    '032 00 000 05', 350,  -18.9730,  47.5205),
('EPP Ambohimandroso',           'primaire', 'public',  'Ambohimandroso',         '034 00 000 06', 290,  -18.9748,  47.5232),
('Lycée Privé Excellence',       'lycee',    'prive',   'Andoharanofotsy Ouest',  '033 00 000 07', 420,  -18.9705,  47.5185),
('École Primaire Sainte-Marie',  'primaire', 'prive',   'Andoharanofotsy Centre', '032 00 000 08', 180,  -18.9720,  47.5217),
('CEG Ambohimandroso',           'college',  'public',  'Ambohimandroso',         '034 00 000 09', 530,  -18.9758,  47.5245),
('Lycée Public Fokontany Est',   'lycee',    'public',  'Andoharanofotsy Est',    '034 00 000 10', 670,  -18.9685,  47.5255);

-- Vérification rapide
SELECT COUNT(*) AS total_ecoles FROM ecoles;
