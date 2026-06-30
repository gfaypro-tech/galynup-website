-- Migration : ajout de la valeur 'abandon' dans hiring_status
-- À exécuter via phpMyAdmin sur OVH (onglet SQL)

ALTER TABLE cv_applications
  MODIFY COLUMN hiring_status
    ENUM('non_envoye','envoye','repondu','entretien','offre','refuse','abandon')
    NOT NULL DEFAULT 'non_envoye';
