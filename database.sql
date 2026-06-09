-- eSC - Chess Club Manager
-- Script complet pentru crearea bazei de date SQLite de la zero

PRAGMA foreign_keys = ON;

-- Roluri utilizatori sistem
CREATE TABLE roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    role_name TEXT NOT NULL UNIQUE
);

-- Utilizatori (autentificare)
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    role_id INTEGER NOT NULL,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Antrenori si colaboratori
CREATE TABLE coaches (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nume TEXT NOT NULL,
    email TEXT NOT NULL,
    telefon TEXT,
    specializare TEXT,
    disponibilitate TEXT,
    rol TEXT NOT NULL CHECK(rol IN ('antrenor', 'colaborator'))
);

-- Membri club
CREATE TABLE members (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nume TEXT NOT NULL,
    prenume TEXT NOT NULL,
    data_nasterii DATE NOT NULL,
    email TEXT NOT NULL,
    telefon TEXT,
    categorie TEXT NOT NULL CHECK(categorie IN ('junior', 'senior', 'amator', 'profesionist')),
    rating INTEGER DEFAULT 0,
    adresa TEXT,
    coach_id INTEGER,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE SET NULL
);

-- Grupe
CREATE TABLE groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    denumire TEXT NOT NULL,
    nivel TEXT NOT NULL,
    coach_id INTEGER,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE SET NULL
);

-- Membri in grupe (many-to-many)
CREATE TABLE group_members (
    group_id INTEGER NOT NULL,
    member_id INTEGER NOT NULL,
    PRIMARY KEY (group_id, member_id),
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Sali
CREATE TABLE halls (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    denumire TEXT NOT NULL,
    capacitate INTEGER NOT NULL,
    dotari TEXT
);

-- Intervale orare disponibile per sala (time slots)
CREATE TABLE hall_slots (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    hall_id INTEGER NOT NULL,
    zi_saptamana TEXT NOT NULL,
    ora_start TEXT NOT NULL,
    ora_end TEXT NOT NULL,
    FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE
);

-- Activitati
CREATE TABLE activities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titlu TEXT NOT NULL,
    tip TEXT NOT NULL CHECK(tip IN ('antrenament', 'curs', 'workshop', 'simultan')),
    data_start DATETIME NOT NULL,
    data_end DATETIME NOT NULL,
    hall_id INTEGER NOT NULL,
    coach_id INTEGER NOT NULL,
    FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE
);

-- Concursuri
CREATE TABLE competitions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nume TEXT NOT NULL,
    locatie TEXT NOT NULL,
    data DATE NOT NULL,
    tip TEXT NOT NULL CHECK(tip IN ('online', 'fizic')),
    domeniu TEXT NOT NULL CHECK(domeniu IN ('local', 'international')) DEFAULT 'local'
);

-- Echipe (performante de echipa)
CREATE TABLE teams (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    denumire TEXT NOT NULL,
    descriere TEXT
);

CREATE TABLE team_members (
    team_id INTEGER NOT NULL,
    member_id INTEGER NOT NULL,
    PRIMARY KEY (team_id, member_id),
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

CREATE TABLE team_results (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    team_id INTEGER NOT NULL,
    competition_id INTEGER NOT NULL,
    punctaj_total REAL DEFAULT 0,
    loc_obtinut INTEGER,
    observatii TEXT,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    UNIQUE(team_id, competition_id)
);

-- Participari la concursuri
CREATE TABLE participations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    member_id INTEGER NOT NULL,
    competition_id INTEGER NOT NULL,
    punctaj REAL DEFAULT 0,
    loc_obtinut INTEGER,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    UNIQUE(member_id, competition_id)
);

-- Premii
CREATE TABLE prizes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titlu TEXT NOT NULL,
    descriere TEXT,
    member_id INTEGER NOT NULL,
    competition_id INTEGER,
    data_acordare DATE NOT NULL,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE SET NULL
);

-- Deplasari echipa reprezentativa
CREATE TABLE trips (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    destinatie TEXT NOT NULL,
    data_plecare DATE NOT NULL,
    data_intoarcere DATE NOT NULL,
    scop TEXT,
    team_id INTEGER,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL
);

-- Membri participanti la deplasare
CREATE TABLE trip_members (
    trip_id INTEGER NOT NULL,
    member_id INTEGER NOT NULL,
    PRIMARY KEY (trip_id, member_id),
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Cheltuieli deplasari
CREATE TABLE expenses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    trip_id INTEGER NOT NULL,
    tip TEXT NOT NULL CHECK(tip IN ('transport', 'cazare', 'masa')),
    suma REAL NOT NULL,
    observatii TEXT,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE
);

-- Date initiale: roluri
INSERT INTO roles (role_name) VALUES
('administrator'),
('antrenor'),
('responsabil_financiar');

-- Singur cont administrator (username: admin, parola: admin pass)
INSERT INTO users (role_id, username, email, password_hash) VALUES
(1, 'admin', 'admin@esc.ro', '$2y$10$WyTSiRXzHvj8Z1oJEIua/eeHTiZE7YluTHt2OmJG1kYK4QH8HULO2');

-- Antrenori si colaboratori demo
INSERT INTO coaches (nume, email, telefon, specializare, disponibilitate, rol) VALUES
('Popescu Ion', 'ion.popescu@esc.ro', '0721000001', 'Deschidere', 'Luni-Vineri 10-18', 'antrenor'),
('Ionescu Maria', 'maria.ionescu@esc.ro', '0721000002', 'Mijloc', 'Marti, Joi, Sambata', 'antrenor'),
('Georgescu Andrei', 'andrei.georgescu@esc.ro', '0721000003', 'Final', 'Luni, Miercuri, Vineri', 'antrenor'),
('Dumitrescu Ana', 'ana.dumitrescu@esc.ro', '0721000004', 'Arbitraj', 'Weekend', 'colaborator'),
('Radu Mihai', 'mihai.radu@esc.ro', '0721000005', 'Organizare', 'Flexibil', 'colaborator');

-- Membri (toate categoriile: junior, senior, amator, profesionist)
INSERT INTO members (nume, prenume, data_nasterii, email, telefon, categorie, rating, adresa, coach_id) VALUES
('Vasilescu', 'Alexandru', '2012-03-15', 'alex.v@email.ro', '0731000001', 'junior', 1450, 'Str. Stejarului 5, Bucuresti', 1),
('Marinescu', 'Elena', '2008-07-22', 'elena.m@email.ro', '0731000002', 'junior', 1580, 'Str. Florilor 12, Bucuresti', 1),
('Nistor', 'Diana', '2010-05-12', 'diana.n@email.ro', '0731000006', 'junior', 1520, 'Str. Lalelelor 3, Iasi', 1),
('Enache', 'Mihai', '2011-09-08', 'mihai.e@email.ro', '0731000009', 'junior', 1380, 'Str. Castanilor 7, Constanta', 1),
('Stan', 'Victor', '1995-11-03', 'victor.s@email.ro', '0731000003', 'senior', 1720, 'Bd. Unirii 45, Bucuresti', 2),
('Munteanu', 'Radu', '1992-12-25', 'radu.m@email.ro', '0731000007', 'senior', 1850, 'Str. Independentei 20, Timisoara', 3),
('Constantinescu', 'Ioana', '1988-01-18', 'ioana.c@email.ro', '0731000004', 'amator', 1650, 'Str. Pacii 8, Cluj', 2),
('Florea', 'Andreea', '1980-04-07', 'andreea.f@email.ro', '0731000008', 'amator', 1700, 'Str. Primaverii 15, Brasov', 2),
('Dobre', 'Paul', '1978-06-14', 'paul.d@email.ro', '0731000010', 'amator', 1620, 'Str. Libertatii 22, Craiova', 2),
('Barbu', 'Cristian', '1975-09-30', 'cristian.b@email.ro', '0731000005', 'profesionist', 2100, 'Str. Victoriei 100, Bucuresti', 3),
('Iliescu', 'Gabriel', '1982-02-28', 'gabriel.i@email.ro', '0731000011', 'profesionist', 2250, 'Bd. Magheru 50, Bucuresti', 3);

-- Grupe demo
INSERT INTO groups (denumire, nivel, coach_id) VALUES
('Grupa Juniori A', 'incepatori', 1),
('Grupa Juniori B', 'intermediar', 1),
('Grupa Seniori', 'avansat', 2),
('Grupa Profesionisti', 'competitie', 3);

INSERT INTO group_members (group_id, member_id) VALUES
(1, 1), (1, 6), (1, 9),
(2, 2), (2, 4),
(3, 3), (3, 7), (3, 8),
(4, 5), (4, 11);

-- Sali demo
INSERT INTO halls (denumire, capacitate, dotari) VALUES
('Sala Principala', 40, '30 table, ceasuri DGT, proiector'),
('Sala Training', 20, '20 table, ceasuri analogice'),
('Sala Cursuri', 15, 'Tabla alba, laptop, 10 table');

INSERT INTO hall_slots (hall_id, zi_saptamana, ora_start, ora_end) VALUES
(1, 'Luni', '10:00', '12:00'),
(1, 'Luni', '14:00', '18:00'),
(1, 'Miercuri', '10:00', '12:00'),
(1, 'Miercuri', '16:00', '20:00'),
(1, 'Sambata', '09:00', '13:00'),
(2, 'Marti', '09:00', '11:00'),
(2, 'Joi', '16:00', '18:00'),
(2, 'Vineri', '10:00', '12:00'),
(3, 'Vineri', '14:00', '16:00'),
(3, 'Duminica', '11:00', '13:00');

-- Activitati (toate tipurile: antrenament, curs, workshop, simultan)
INSERT INTO activities (titlu, tip, data_start, data_end, hall_id, coach_id) VALUES
('Antrenament Juniori', 'antrenament', '2026-06-10 10:00:00', '2026-06-10 12:00:00', 1, 1),
('Antrenament Seniori', 'antrenament', '2026-06-10 16:00:00', '2026-06-10 18:00:00', 2, 2),
('Curs Tactica Mijloc', 'curs', '2026-06-11 14:00:00', '2026-06-11 16:00:00', 3, 2),
('Curs Deschidere', 'curs', '2026-06-13 10:00:00', '2026-06-13 12:00:00', 3, 1),
('Workshop Final', 'workshop', '2026-06-12 09:00:00', '2026-06-12 11:00:00', 2, 3),
('Workshop Final Avansat', 'workshop', '2026-06-14 09:00:00', '2026-06-14 11:00:00', 2, 3),
('Simultan GM', 'simultan', '2026-06-15 18:00:00', '2026-06-15 20:00:00', 1, 3),
('Simultan Club', 'simultan', '2026-06-22 17:00:00', '2026-06-22 19:00:00', 1, 1);

-- Echipe demo
INSERT INTO teams (denumire, descriere) VALUES
('Echipa Reprezentativa ESC', 'Echipa oficiala a clubului pentru competitii externe'),
('Echipa Juniori ESC', 'Echipa de juniori pentru competitiile locale');

INSERT INTO team_members (team_id, member_id) VALUES
(1, 3), (1, 5), (1, 7), (1, 11),
(2, 1), (2, 2), (2, 6), (2, 4);

-- Concursuri (local/international, online/fizic)
INSERT INTO competitions (nume, locatie, data, tip, domeniu) VALUES
('Campionatul National Juniori', 'Bucuresti', '2026-07-05', 'fizic', 'local'),
('Turneu Rapid Online', 'lichess.org', '2026-06-20', 'online', 'international'),
('Open de Vara ESC', 'Brasov', '2026-08-10', 'fizic', 'local'),
('Memorialul Popescu', 'Cluj-Napoca', '2026-09-15', 'fizic', 'international'),
('Cupa Orasului', 'Bucuresti', '2026-05-18', 'fizic', 'local'),
('Olimpiada Online Europa', 'chess.com', '2026-10-01', 'online', 'international');

INSERT INTO team_results (team_id, competition_id, punctaj_total, loc_obtinut, observatii) VALUES
(1, 2, 24.5, 1, 'Locul I la turneul rapid online'),
(1, 4, 18.0, 2, 'Locul II la memorial'),
(1, 6, 22.0, 1, 'Locul I la olimpiada online'),
(2, 1, 16.5, 3, 'Locul III la campionatul national juniori'),
(2, 5, 14.0, 2, 'Locul II la cupa orasului');

-- Participari (acopera toate competitiile)
INSERT INTO participations (member_id, competition_id, punctaj, loc_obtinut) VALUES
(1, 1, 5.5, 3),
(2, 1, 4.0, 8),
(4, 1, 3.5, 10),
(5, 1, 7.0, 1),
(6, 1, 4.5, 6),
(3, 2, 8.0, 2),
(7, 2, 9.0, 1),
(11, 2, 7.5, 3),
(5, 3, 6.5, 1),
(8, 3, 5.0, 4),
(10, 3, 4.0, 6),
(3, 4, 6.0, 4),
(11, 4, 7.0, 2),
(7, 5, 5.5, 1),
(8, 5, 4.5, 3),
(9, 5, 3.0, 8),
(5, 6, 8.5, 1),
(11, 6, 7.0, 2),
(7, 6, 6.5, 3);

-- Premii
INSERT INTO prizes (titlu, descriere, member_id, competition_id, data_acordare) VALUES
('Locul I Juniori', 'Trofeu si diploma', 5, 1, '2026-07-05'),
('Locul III Juniori', 'Medalie bronz', 1, 1, '2026-07-05'),
('Locul I Rapid Online', 'Premiu special', 7, 2, '2026-06-20'),
('Locul II Rapid Online', 'Medalie argint', 3, 2, '2026-06-20'),
('Locul I Open Vara', 'Trofeu', 5, 3, '2026-08-10'),
('Fair Play', 'Pentru spirit sportiv', 8, 3, '2026-08-10'),
('Locul II Memorial', 'Medalie argint', 11, 4, '2026-09-15'),
('Locul I Cupa Orasului', 'Cupa + diploma', 7, 5, '2026-05-18'),
('Locul I Olimpiada Online', 'Trofeu international', 5, 6, '2026-10-01');

-- Deplasari demo
INSERT INTO trips (destinatie, data_plecare, data_intoarcere, scop, team_id) VALUES
('Brasov - Open de Vara', '2026-08-09', '2026-08-11', 'Participare competitie', 1),
('Cluj-Napoca - Memorial', '2026-09-14', '2026-09-16', 'Participare competitie', 1),
('Bucuresti - Campionat National', '2026-07-04', '2026-07-06', 'Participare competitie', 2);

INSERT INTO trip_members (trip_id, member_id) VALUES
(1, 5), (1, 8), (1, 10),
(2, 3), (2, 7), (2, 5), (2, 11),
(3, 1), (3, 2), (3, 6), (3, 4);

-- Cheltuieli demo
INSERT INTO expenses (trip_id, tip, suma, observatii) VALUES
(1, 'transport', 450.00, 'Autocar dus-intors'),
(1, 'cazare', 600.00, '2 nopti hotel 3*'),
(1, 'masa', 280.00, 'Mese pentru 8 persoane'),
(2, 'transport', 380.00, 'Tren + taxi'),
(2, 'cazare', 500.00, '2 nopti pensiune'),
(2, 'masa', 220.00, 'Mese echipa'),
(3, 'transport', 150.00, 'Transport local'),
(3, 'cazare', 350.00, '1 noapte'),
(3, 'masa', 180.00, 'Mese participanti');
