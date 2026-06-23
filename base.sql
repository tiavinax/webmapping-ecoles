-- Créer la base de données (à exécuter en tant que superuser PostgreSQL)
-- psql -U postgres -c "CREATE DATABASE ecoles_ando;"

-- Ensuite se connecter à la base :
-- psql -U postgres -d ecoles_ando -f create_db.sql

-- ============================================================
-- TABLE ECOLES
-- ============================================================
CREATE TABLE IF NOT EXISTS ecoles (
    id         SERIAL PRIMARY KEY,
    nom        VARCHAR(150)   NOT NULL,
    type       VARCHAR(50)    NOT NULL CHECK (type IN ('primaire', 'collège', 'lycée')),
    statut     VARCHAR(20)    NOT NULL CHECK (statut IN ('public', 'privé')),
    fokontany  VARCHAR(100),
    telephone  VARCHAR(30),
    nb_eleves  INTEGER,
    latitude   NUMERIC(10, 6) NOT NULL,
    longitude  NUMERIC(10, 6) NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

-- ============================================================
-- DONNÉES DE TEST — écoles fictives à Andoharanofotsy
-- (à remplacer par les vraies données recensées)
-- ============================================================
INSERT INTO ecoles (nom, type, statut, fokontany, telephone, nb_eleves, latitude, longitude) VALUES
    ('EPP Andoharanofotsy I',     'primaire', 'public', 'Andoharanofotsy I',   '034 00 000 01', 320,  -18.9495, 47.5398),
    ('EPP Andoharanofotsy II',    'primaire', 'public', 'Andoharanofotsy II',  '034 00 000 02', 280,  -18.9512, 47.5421),
    ('Collège Saint-Joseph',      'collège',  'privé',  'Andoharanofotsy II',  '034 00 000 03', 480,  -18.9523, 47.5435),
    ('Collège Public Ando',       'collège',  'public', 'Andoharanofotsy',     '034 00 000 04', 520,  -18.9480, 47.5375),
    ('Lycée Andoharanofotsy',     'lycée',    'public', 'Andoharanofotsy',     '034 00 000 05', 600,  -18.9468, 47.5362),
    ('Lycée Privé Sainte-Marie',  'lycée',    'privé',  'Andoharanofotsy I',   '034 00 000 06', 350,  -18.9502, 47.5410);