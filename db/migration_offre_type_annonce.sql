-- À exécuter une fois si la base existait avant l’ajout de type_annonce (offre déjà créée sans cette colonne).
ALTER TABLE offre
  ADD COLUMN type_annonce VARCHAR(30) NOT NULL DEFAULT 'stage' AFTER titre;
