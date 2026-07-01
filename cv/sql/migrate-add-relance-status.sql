-- Migration : ajout du statut 'relance' dans le suivi de candidature
ALTER TABLE cv_applications
  MODIFY COLUMN hiring_status
    ENUM('non_envoye','envoye','repondu','relance','entretien','offre','refuse','abandon')
    DEFAULT 'non_envoye';
