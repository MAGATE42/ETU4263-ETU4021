CREATE TABLE prefixes (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe      VARCHAR(10)  NOT NULL,
    description  VARCHAR(100),
    actif        INTEGER      NOT NULL DEFAULT 1,
    created_at   DATETIME,
    updated_at   DATETIME
);


CREATE TABLE types_operations (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    code         VARCHAR(20)  NOT NULL UNIQUE,
    libelle      VARCHAR(100) NOT NULL,
    description  TEXT,
    actif        INTEGER      NOT NULL DEFAULT 1,
    created_at   DATETIME,
    updated_at   DATETIME
);


CREATE TABLE baremes_frais (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id   INTEGER NOT NULL,
    montant_min         REAL    NOT NULL DEFAULT 0,
    montant_max         REAL    NOT NULL,
    frais               REAL    NOT NULL DEFAULT 0,
    created_at          DATETIME,
    updated_at          DATETIME,
    FOREIGN KEY (type_operation_id) REFERENCES types_operations (id)
        ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE comptes (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    telephone    VARCHAR(20)  NOT NULL UNIQUE,
    nom          VARCHAR(100),
    prenom       VARCHAR(100),
    solde        REAL         NOT NULL DEFAULT 0,
    statut       VARCHAR(20)  NOT NULL DEFAULT 'actif'
                 CHECK (statut IN ('actif', 'suspendu', 'fermé')),
    created_at   DATETIME,
    updated_at   DATETIME
);


CREATE TABLE transactions (
    id                       INTEGER PRIMARY KEY AUTOINCREMENT,
    reference                VARCHAR(30) NOT NULL UNIQUE,
    compte_id                INTEGER     NOT NULL,
    type_operation_id        INTEGER     NOT NULL,
    montant                  REAL        NOT NULL,
    frais                    REAL        NOT NULL DEFAULT 0,
    compte_destinataire_id   INTEGER,
    statut                   VARCHAR(20) NOT NULL DEFAULT 'success'
                              CHECK (statut IN ('success', 'failed')),
    note                     TEXT,
    created_at               DATETIME,
    updated_at               DATETIME,
    FOREIGN KEY (compte_id)              REFERENCES comptes (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (type_operation_id)      REFERENCES types_operations (id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (compte_destinataire_id) REFERENCES comptes (id)
        ON DELETE SET NULL ON UPDATE CASCADE
);


CREATE INDEX idx_baremes_type            ON baremes_frais (type_operation_id);
CREATE INDEX idx_transactions_compte     ON transactions (compte_id);
CREATE INDEX idx_transactions_type       ON transactions (type_operation_id);
CREATE INDEX idx_transactions_dest       ON transactions (compte_destinataire_id);
CREATE INDEX idx_transactions_created_at ON transactions (created_at);


INSERT INTO prefixes (prefixe, description, actif, created_at) VALUES
('032', 'Orange Madagascar - 032', 1, CURRENT_TIMESTAMP),
('033', 'Orange Madagascar - 033', 1, CURRENT_TIMESTAMP),
('037', 'Orange Madagascar - 037', 1, CURRENT_TIMESTAMP);


INSERT INTO types_operations (code, libelle, description, actif, created_at) VALUES
('DEPOT',     'Dépôt',     'Dépôt d''argent sur le compte',              1, CURRENT_TIMESTAMP),
('RETRAIT',   'Retrait',   'Retrait d''argent du compte',                1, CURRENT_TIMESTAMP),
('TRANSFERT', 'Transfert', 'Transfert d''argent vers un autre compte',   1, CURRENT_TIMESTAMP);

-- ------------------------------------------------------------
-- Barèmes de frais - RETRAIT
-- ------------------------------------------------------------
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 100,      1000,     50,   CURRENT_TIMESTAMP FROM types_operations WHERE code = 'RETRAIT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 1001,     5000,     50,   CURRENT_TIMESTAMP FROM types_operations WHERE code = 'RETRAIT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 5001,     10000,    100,  CURRENT_TIMESTAMP FROM types_operations WHERE code = 'RETRAIT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 10001,    25000,    200,  CURRENT_TIMESTAMP FROM types_operations WHERE code = 'RETRAIT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 25001,    50000,    400,  CURRENT_TIMESTAMP FROM types_operations WHERE code = 'RETRAIT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 50001,    100000,   800,  CURRENT_TIMESTAMP FROM types_operations WHERE code = 'RETRAIT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 100001,   250000,   1500, CURRENT_TIMESTAMP FROM types_operations WHERE code = 'RETRAIT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 250001,   500000,   1500, CURRENT_TIMESTAMP FROM types_operations WHERE code = 'RETRAIT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 500001,   1000000,  2500, CURRENT_TIMESTAMP FROM types_operations WHERE code = 'RETRAIT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 1000001,  2000000,  3000, CURRENT_TIMESTAMP FROM types_operations WHERE code = 'RETRAIT';

-- ------------------------------------------------------------
-- Barèmes de frais - TRANSFERT
-- ------------------------------------------------------------
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 100,      1000,     50,   CURRENT_TIMESTAMP FROM types_operations WHERE code = 'TRANSFERT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 1001,     5000,     50,   CURRENT_TIMESTAMP FROM types_operations WHERE code = 'TRANSFERT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 5001,     10000,    100,  CURRENT_TIMESTAMP FROM types_operations WHERE code = 'TRANSFERT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 10001,    25000,    200,  CURRENT_TIMESTAMP FROM types_operations WHERE code = 'TRANSFERT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 25001,    50000,    400,  CURRENT_TIMESTAMP FROM types_operations WHERE code = 'TRANSFERT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 50001,    100000,   800,  CURRENT_TIMESTAMP FROM types_operations WHERE code = 'TRANSFERT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 100001,   250000,   1500, CURRENT_TIMESTAMP FROM types_operations WHERE code = 'TRANSFERT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 250001,   500000,   1500, CURRENT_TIMESTAMP FROM types_operations WHERE code = 'TRANSFERT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 500001,   1000000,  2500, CURRENT_TIMESTAMP FROM types_operations WHERE code = 'TRANSFERT';
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais, created_at)
SELECT id, 1000001,  2000000,  3000, CURRENT_TIMESTAMP FROM types_operations WHERE code = 'TRANSFERT';

-- ------------------------------------------------------------
-- Comptes clients test
-- ------------------------------------------------------------
INSERT INTO comptes (telephone, nom, prenom, solde, statut, created_at) VALUES
('0334263', 'RAKOTO', 'Jean',  150000.00, 'actif', CURRENT_TIMESTAMP),
('0334021', 'RABE',   'Marie', 75000.00,  'actif', CURRENT_TIMESTAMP);
