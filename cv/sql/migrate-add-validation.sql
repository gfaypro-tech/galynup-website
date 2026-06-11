-- Ajout de la colonne validation_result pour stocker le résultat de la checklist d'alignement stratégique
ALTER TABLE cv_applications
  ADD COLUMN validation_result LONGTEXT NULL AFTER cv_content;
