SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS wishlist, candidature, evaluation, offre_competence, competence, offre, entreprise, utilisateur;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE utilisateur (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom          VARCHAR(100) NOT NULL,
  prenom       VARCHAR(100) NOT NULL,
  email        VARCHAR(255) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,
  role         ENUM('admin','pilote','etudiant') NOT NULL DEFAULT 'etudiant',
  id_pilote    INT UNSIGNED NULL,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_pilote) REFERENCES utilisateur(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE entreprise (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom         VARCHAR(200) NOT NULL,
  description TEXT,
  email       VARCHAR(255),
  telephone   VARCHAR(20),
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE offre (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titre         VARCHAR(200) NOT NULL,
  type_annonce  VARCHAR(30) NOT NULL DEFAULT 'stage',
  description   TEXT NOT NULL,
  remuneration  DECIMAL(8,2) UNSIGNED,
  date_debut    DATE,
  date_fin      DATE,
  duree_mois    TINYINT UNSIGNED,
  id_entreprise INT UNSIGNED NOT NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_entreprise) REFERENCES entreprise(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE competence (
  id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE offre_competence (
  id_offre      INT UNSIGNED NOT NULL,
  id_competence INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_offre, id_competence),
  FOREIGN KEY (id_offre)      REFERENCES offre(id)      ON DELETE CASCADE,
  FOREIGN KEY (id_competence) REFERENCES competence(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE candidature (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_offre          INT UNSIGNED NOT NULL,
  id_etudiant       INT UNSIGNED NOT NULL,
  lettre_motivation TEXT,
  cv_path           VARCHAR(500),
  date_candidature  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_candidature (id_offre, id_etudiant),
  FOREIGN KEY (id_offre)    REFERENCES offre(id)        ON DELETE CASCADE,
  FOREIGN KEY (id_etudiant) REFERENCES utilisateur(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE evaluation (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_entreprise INT UNSIGNED NOT NULL,
  id_etudiant   INT UNSIGNED NOT NULL,
  note          TINYINT UNSIGNED NOT NULL CHECK (note BETWEEN 1 AND 5),
  commentaire   TEXT,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_evaluation (id_entreprise, id_etudiant),
  FOREIGN KEY (id_entreprise) REFERENCES entreprise(id)   ON DELETE CASCADE,
  FOREIGN KEY (id_etudiant)   REFERENCES utilisateur(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE wishlist (
  id_offre    INT UNSIGNED NOT NULL,
  id_etudiant INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_offre, id_etudiant),
  FOREIGN KEY (id_offre)    REFERENCES offre(id)       ON DELETE CASCADE,
  FOREIGN KEY (id_etudiant) REFERENCES utilisateur(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

