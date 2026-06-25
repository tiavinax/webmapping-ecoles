CREATE TABLE IF NOT EXISTS ecoles (
    id          SERIAL PRIMARY KEY,
    nom         VARCHAR(150)     NOT NULL,
    type        VARCHAR(50)      NOT NULL CHECK (type IN ('primaire', 'college', 'lycee', 'mixte', 'universite', 'autre')),
    statut      VARCHAR(20)      NOT NULL CHECK (statut IN ('public', 'prive')),
    fokontany   VARCHAR(100),
    telephone   VARCHAR(30),
    nb_eleves   INTEGER          DEFAULT 0,
    latitude    DOUBLE PRECISION NOT NULL,
    longitude   DOUBLE PRECISION NOT NULL,
    created_at  TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO ecoles (nom, type, statut, fokontany, telephone, nb_eleves, latitude, longitude) VALUES
-- --- ÉCOLES PUBLIQUES ---
('EPP Andoharanofotsy',
    'primaire', 'public', 'Andoharanofotsy Centre', NULL, 320, -18.9700, 47.5210),
('School Andoharanofotsy',
    'lycee', 'public', 'Andoharanofotsy Centre', '032 48 343 02', 610, -18.9715, 47.5225),

-- --- ÉCOLES PRIVÉES ---
('School La Fontaine',
    'mixte', 'prive', 'Andoharanofotsy', NULL, 250, -18.9690, 47.5240),
('Ecole Platoni Academy',
    'lycee', 'prive', 'Andoharanofotsy', '038 48 129 25', 180, -18.9720, 47.5217),
('Private School Les Meilleurs',
    'lycee', 'prive', 'Andoharanofotsy', '033 03 897 98', 420, -18.9705, 47.5185),
('Sunshine Montessori School - Madagascar',
    'primaire', 'prive', 'Imerimanjaka', '034 92 382 97', 150, -18.9748, 47.5232),
('Akany Fanantenana - Enfant de l''Espoir de Madagascar - Ecole Primaire',
    'primaire', 'prive', 'Andoharanofotsy', NULL, 130, -18.9710, 47.5190),
('Private School La Flèche',
    'lycee', 'prive', 'Andoharanofotsy', NULL, 310, -18.9680, 47.5198),
('College Bird',
    'mixte', 'prive', 'Andoharanofotsy', '034 25 001 55', 520, -18.9645, 47.5135),
('Lycée Peter Pan',
    'lycee', 'prive', 'Andoharanofotsy', '034 15 215 98', 480, -18.9630, 47.5120),
('Au P''tit Pré',
    'lycee', 'prive', 'Mahabo', '034 23 750 05', 195, -18.9758, 47.5245),
('Ecole La Francophonie',
    'mixte', 'prive', 'Andoharanofotsy', '034 88 229 96', 280, -18.9665, 47.5142),
('School Saint Pierre Malaza',
    'lycee', 'prive', 'Malaza', NULL, 550, -18.9730, 47.5205),
('Private Middle School - Savoir',
    'college', 'prive', 'Andoharanofotsy Centre', NULL, 210, -18.9685, 47.5255),
('School Bout D''chou',
    'primaire', 'prive', 'Andoharanofotsy', '034 20 329 22', 115, -18.9712, 47.5270),
('Elementary School of Antananarivo',
    'primaire', 'prive', 'Andoharanofotsy', '034 19 302 87', 160, -18.9678, 47.5115),

-- --- UNIVERSITÉS PRIVÉES ---
('ISTS - Institut Supérieur de Travail Social',
    'universite', 'prive', 'Andoharanofotsy', '020 22 460 34', 400, -18.9650, 47.5150),
('UTB - University of Technology and Business',
    'universite', 'prive', 'Mandrimena Iavoloha', '020 22 572 60', 350, -18.9800, 47.5300),
('IT University',
    'universite', 'prive', 'Andoharanofotsy', '033 03 300 32', 800, -18.9660, 47.5160);