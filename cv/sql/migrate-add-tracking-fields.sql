-- Migration : ajout des champs de suivi avancé
-- À exécuter après migrate-add-direct-status.sql et migrate-add-relance-status.sql

ALTER TABLE cv_applications
  ADD COLUMN avancement ENUM('en_cours','a_relancer','cloture') NOT NULL DEFAULT 'en_cours' AFTER hiring_status,
  ADD COLUMN date_candidature DATE NULL AFTER avancement,
  ADD COLUMN commentaire_relance TEXT NULL AFTER date_candidature;
