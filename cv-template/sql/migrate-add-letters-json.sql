-- CV Builder — Migration : ajout colonne letters_json
-- À exécuter UNE SEULE FOIS dans phpMyAdmin sur la base OVH
-- Stocke les formats LinkedIn et Malt générés en JSON

ALTER TABLE cv_applications
  ADD COLUMN letters_json LONGTEXT DEFAULT NULL
  AFTER letter_content;
