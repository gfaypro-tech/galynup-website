-- Migration : ajout du statut 'direct' pour les candidatures saisies manuellement
-- (sans passer par le générateur de CV)
ALTER TABLE cv_applications
  MODIFY COLUMN status
    ENUM('draft','analysis','matching','dialogue','generating','completed','direct')
    DEFAULT NULL;
