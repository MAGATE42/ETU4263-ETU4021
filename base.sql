-- ─── TABLE : prefixes ───────────────────────────────────────────────────
-- Stocke les préfixes téléphoniques valides.
-- est_autre_operateur = 1 → Autre opérateur (commission applicable)
-- est_autre_operateur = 0 → Préfixe interne (même opérateur)
CREATE TABLE IF NOT EXISTS `prefixes` (
  `id`                   INTEGER PRIMARY KEY AUTOINCREMENT,
  `prefixe`              VARCHAR(10) NOT NULL,
  `description`          VARCHAR(100),
  `actif`                INTEGER DEFAULT 1,
  `est_autre_operateur`  INTEGER DEFAULT 0,   -- [V2] 0=interne, 1=autre opérateur
  `created_at`           DATETIME,
  `updated_at`           DATETIME
);

-- ─── TABLE : types_operations ────────────────────────────────────────────
-- Catalogue des types d'opérations disponibles.
-- Codes standards : DEPOT, RETRAIT, TRANSFERT
CREATE TABLE IF NOT EXISTS `types_operations` (
  `id`          INTEGER PRIMARY KEY AUTOINCREMENT,
  `code`        VARCHAR(20) NOT NULL UNIQUE,
  `libelle`     VARCHAR(100) NOT NULL,
  `description` TEXT,
  `actif`       INTEGER DEFAULT 1,
  `created_at`  DATETIME,
  `updated_at`  DATETIME
);

-- ─── TABLE : baremes_frais ───────────────────────────────────────────────
-- Grille tarifaire par tranche de montant, liée à un type d'opération.
-- Les frais sont un montant fixe en Ar (pas un pourcentage).
-- La commission pour autres opérateurs est gérée via la table configurations.
CREATE TABLE IF NOT EXISTS `baremes_frais` (
  `id`                INTEGER PRIMARY KEY AUTOINCREMENT,
  `type_operation_id` INTEGER NOT NULL,
  `montant_min`       REAL NOT NULL DEFAULT 0,
  `montant_max`       REAL NOT NULL,
  `frais`             REAL NOT NULL DEFAULT 0,
  `created_at`        DATETIME,
  `updated_at`        DATETIME,
  FOREIGN KEY (`type_operation_id`) REFERENCES `types_operations`(`id`) 
);

-- ─── TABLE : comptes ────────────────────────────────────────────────────
-- Comptes clients. Créés automatiquement à la première connexion via téléphone.
CREATE TABLE IF NOT EXISTS `comptes` (
  `id`         INTEGER PRIMARY KEY AUTOINCREMENT,
  `telephone`  VARCHAR(20) NOT NULL UNIQUE,
  `nom`        VARCHAR(100),
  `prenom`     VARCHAR(100),
  `solde`      REAL NOT NULL DEFAULT 0,
  `pourcentage_epargne`      REAL NOT NULL DEFAULT 0,
  `statut`     VARCHAR(20) DEFAULT 'actif',  -- actif | suspendu | fermé
  `created_at` DATETIME,
  `updated_at` DATETIME
);

-- ─── TABLE : transactions ────────────────────────────────────────────────
-- Historique de toutes les opérations (dépôts, retraits, transferts).
-- Pour les transferts sortants : compte_destinataire_id est renseigné.
-- Les entrées "reçu" ont une référence préfixée REC-.
CREATE TABLE IF NOT EXISTS `transactions` (
  `id`                          INTEGER PRIMARY KEY AUTOINCREMENT,
  `reference`                   VARCHAR(30) NOT NULL UNIQUE,
  `compte_id`                   INTEGER NOT NULL,
  `type_operation_id`           INTEGER NOT NULL,
  `montant`                     REAL NOT NULL,
  -- `pourcentage_epargne`                 REAL NOT NULL DEFAULT 0,
  `frais`                       REAL NOT NULL DEFAULT 0,
  `commission_autre_operateur`  REAL DEFAULT 0,   -- [V2] Commission prélevée sur transferts externes
  `compte_destinataire_id`      INTEGER,           -- NULL sauf pour transferts
  `statut`                      VARCHAR(20) DEFAULT 'success',
  `note`                        TEXT,
  `created_at`                  DATETIME,
  `updated_at`                  DATETIME,
  FOREIGN KEY (`compte_id`)            REFERENCES `comptes`(`id`),
  FOREIGN KEY (`type_operation_id`)    REFERENCES `types_operations`(`id`),
  FOREIGN KEY (`compte_destinataire_id`) REFERENCES `comptes`(`id`)
);

-- ─── TABLE : configurations ─────────────────────────────────────────────
-- [V2] Paramètres globaux de l'application (clé-valeur).
-- Clé : commission_transfert_externe → % de commission sur transferts vers autres opérateurs
CREATE TABLE IF NOT EXISTS `configurations` (
  `cle`        VARCHAR(50) PRIMARY KEY,
  `valeur`     VARCHAR(255),
  `created_at` DATETIME,
  `updated_at` DATETIME
);

-- =======================================================================
-- DONNÉES INITIALES (miroir du MainSeeder.php)
-- =======================================================================

-- ─── Préfixes ──────────────────────────────────────────────────────────
INSERT INTO `prefixes` (`prefixe`, `description`, `actif`, `est_autre_operateur`) VALUES
  ('032', 'Telma - 032 (Autre Operateur)', 1, 1),
  ('033', 'Airtel - 033',                  1, 0),
  ('034', 'Telma - 034 (Autre Operateur)', 1, 1),
  ('037', 'Orange Madagascar - 037',       1, 0);

-- ─── Types d'opérations ────────────────────────────────────────────────
INSERT INTO `types_operations` (`code`, `libelle`, `description`, `actif`) VALUES
  ('DEPOT',     'Dépôt',     'Dépôt d''argent sur le compte',            1),
  ('RETRAIT',   'Retrait',   'Retrait d''argent du compte',              1),
  ('TRANSFERT', 'Transfert', 'Transfert d''argent vers un autre compte', 1);

-- ─── Barèmes de frais (RETRAIT) ─────────────────────────────────────────
INSERT INTO `baremes_frais` (`type_operation_id`, `montant_min`, `montant_max`, `frais`) VALUES
  (2,    100,     1000,    50),
  (2,   1001,     5000,    50),
  (2,   5001,    10000,   100),
  (2,  10001,    25000,   200),
  (2,  25001,    50000,   400),
  (2,  50001,   100000,   800),
  (2, 100001,   250000,  1500),
  (2, 250001,   500000,  1500),
  (2, 500001,  1000000,  2500),
  (2,1000001,  2000000,  3000);

-- ─── Barèmes de frais (TRANSFERT) ────────────────────────────────────────
INSERT INTO `baremes_frais` (`type_operation_id`, `montant_min`, `montant_max`, `frais`) VALUES
  (3,    100,     1000,    50),
  (3,   1001,     5000,    50),
  (3,   5001,    10000,   100),
  (3,  10001,    25000,   200),
  (3,  25001,    50000,   400),
  (3,  50001,   100000,   800),
  (3, 100001,   250000,  1500),
  (3, 250001,   500000,  1500),
  (3, 500001,  1000000,  2500),
  (3,1000001,  2000000,  3000);

INSERT INTO `comptes` (`telephone`, `nom`, `prenom`, `solde`, `statut`) VALUES
  ('0334263', 'RAKOTO', 'Jean',  150000.00, 'actif'),
  ('0334021', 'RABE',   'Marie',  75000.00, 'actif');

INSERT INTO `configurations` (`cle`, `valeur`) VALUES
  ('commission_transfert_externe', '5');  -- 5% par défaut
